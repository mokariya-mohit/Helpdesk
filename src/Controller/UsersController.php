<?php
declare(strict_types=1);

namespace App\Controller;

class UsersController extends AppController
{
    protected ?\App\Model\Table\UsersTable $Users = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->Users = $this->fetchTable('Users');
    }

    /**
     * Get assigned 3D Pixar avatar file path for male or female deterministically based on user ID
     */
    public static function getAssignedAvatarPath(?int $userId, string $gender = 'male'): string
    {
        $g = strtolower(trim($gender)) === 'female' ? 'female' : 'male';
        $max = ($g === 'female') ? 6 : 7;
        $id = ($userId !== null && $userId > 0) ? $userId : 1;
        $index = (($id - 1) % $max) + 1;
        return "img/avatars/{$g}/{$g}_{$index}.png";
    }

    /**
     * Get random 3D Pixar avatar file path for male or female
     */
    public static function getRandomAvatarPath(string $gender = 'male'): string
    {
        $g = strtolower(trim($gender)) === 'female' ? 'female' : 'male';
        $dir = WWW_ROOT . 'img' . DS . 'avatars' . DS . $g;
        if (is_dir($dir)) {
            $files = glob($dir . DS . '*.png');
            if (!empty($files)) {
                $chosen = basename($files[array_rand($files)]);
                return "img/avatars/{$g}/{$chosen}";
            }
        }
        return "img/avatars/{$g}/{$g}_1.png";
    }

    /**
     * Resolve avatar path or URL into a web-accessible URL
     */
    public static function getAvatarUrl(?string $picture, ?string $gender = 'male', string $webroot = '/'): string
    {
        if (!empty($picture)) {
            if (str_starts_with($picture, 'http://') || str_starts_with($picture, 'https://')) {
                return $picture;
            }
            return rtrim($webroot, '/') . '/' . ltrim($picture, '/');
        }
        $g = strtolower(trim((string)$gender)) === 'female' ? 'female' : 'male';
        return rtrim($webroot, '/') . "/img/avatars/{$g}/{$g}_1.png";
    }

    /**
     * AJAX: Get a random avatar path and url based on gender
     */
    public function getRandomAvatar()
    {
        $this->request->allowMethod(['get', 'post']);
        $gender = strtolower(trim((string)$this->request->getQuery('gender', $this->request->getData('gender', 'male'))));
        $gender = $gender === 'female' ? 'female' : 'male';

        $avatarPath = self::getRandomAvatarPath($gender);
        $webroot = $this->request->getAttribute('webroot') ?? '/';
        $avatarUrl = self::getAvatarUrl($avatarPath, $gender, $webroot);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'gender' => $gender,
                'avatar_path' => $avatarPath,
                'avatar_url' => $avatarUrl,
            ]));
    }

    /**
     * User Signup (Register)
     */
    public function signup()
    {
        // If already logged in, redirect to tasks
        if ($this->getAuthUser()) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $name = trim($data['name'] ?? '');
            $email = strtolower(trim($data['email'] ?? ''));
            $password = $data['password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                $this->Flash->error('Please fill in all required fields.');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->Flash->error('Please enter a valid email address.');
            } elseif ($password !== $confirmPassword) {
                $this->Flash->error('Passwords do not match. Please try again.');
            } elseif (strlen($password) < 6) {
                $this->Flash->error('Password must be at least 6 characters long.');
            } else {
                $existing = $this->Users->find()->where(['email' => $email])->first();
                if ($existing) {
                    $this->Flash->error('An account with this email already exists. Please log in.');
                } else {
                    $gender = strtolower(trim((string)($data['gender'] ?? 'male')));
                    if (!in_array($gender, ['male', 'female'], true)) {
                        $gender = 'male';
                    }
                    $picture = self::getRandomAvatarPath($gender);

                    $user = $this->Users->newEntity([
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'gender' => $gender,
                        'picture' => $picture,
                    ]);

                    if ($this->Users->save($user)) {
                        // Clear any old flash messages
                        $this->request->getSession()->delete('Flash');

                        // Automatically log in
                        $this->request->getSession()->write('AuthUser', [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'gender' => $user->gender,
                            'picture' => $user->picture,
                        ]);

                        $this->Flash->success('Welcome, ' . $user->name . '! Your account has been created successfully.');
                        $token = base64_encode($user->id . ':' . hash('sha256', $user->email . $user->password));
                        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', $token, (new \DateTime('+30 days')), '/', '', false, true);

                        return $this->redirect(['controller' => 'Tasks', 'action' => 'index'])
                            ->withCookie($cookie);
                    } else {
                        $this->Flash->error('Failed to create account. Please check the form errors.');
                    }
                }
            }
        }

        $this->set(compact('user'));
    }

    /**
     * User Login
     */
    public function login()
    {
        // If already logged in, redirect to tasks
        if ($this->getAuthUser()) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        if ($this->request->is('post')) {
            $email = strtolower(trim($this->request->getData('email') ?? ''));
            $password = $this->request->getData('password') ?? '';
            $rememberMe = (bool)$this->request->getData('remember_me');

            if (empty($email) || empty($password)) {
                $this->Flash->error('Please enter both email and password.');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->Flash->error('Please enter a valid email address.');
            } else {
                $user = $this->Users->find()->where(['email' => $email])->first();

                if (!$user) {
                    $this->Flash->error('No account found with this email address. Please sign up first.');
                } elseif (!password_verify($password, $user->password)) {
                    $this->Flash->error('Incorrect password. Please try again.');
                } else {
                    // Clear old flash messages
                    $this->request->getSession()->delete('Flash');

                    $needsSave = false;
                    if (empty($user->gender)) {
                        $user->gender = 'male';
                        $needsSave = true;
                    }
                    if (empty($user->picture)) {
                        $user->picture = self::getRandomAvatarPath($user->gender);
                        $needsSave = true;
                    }
                    if ($needsSave) {
                        $this->Users->save($user);
                    }

                    // Successful login
                    $this->request->getSession()->write('AuthUser', [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'gender' => $user->gender,
                        'picture' => $user->picture,
                    ]);

                    $this->Flash->success('Welcome back, ' . $user->name . '!');

                    $token = base64_encode($user->id . ':' . hash('sha256', $user->email . $user->password));
                    $cookie = new \Cake\Http\Cookie\Cookie('remember_user', $token, (new \DateTime('+30 days')), '/', '', false, true);

                    return $this->redirect(['controller' => 'Tasks', 'action' => 'index'])
                        ->withCookie($cookie);
                }
            }
        }
    }

    /**
     * User Logout
     */
    /**
     * AJAX: Send Password Reset OTP
     */
    public function forgotPassword()
    {
        $this->request->allowMethod(['post']);
        $email = strtolower(trim((string)$this->request->getData('email')));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                ]));
        }

        $user = $this->Users->find()->where(['email' => $email])->first();
        if (!$user) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'This email address is not registered in our system. Please check your email or register.',
                ]));
        }

        // Generate secure 6-digit OTP
        $otp = (string)random_int(100000, 999999);
        $this->request->getSession()->write('PasswordReset', [
            'email' => $email,
            'otp' => $otp,
            'expires' => time() + 900, // 15 mins
            'verified' => false,
        ]);

        // Send OTP email
        $emailService = new \App\Service\EmailService();
        $mailResult = $emailService->sendOtpEmail($email, $user->name, $otp);

        if (empty($mailResult['sent'])) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Could not send OTP email: ' . ($mailResult['error'] ?? 'Please check SMTP credentials or server mail connection.'),
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'Verification code (OTP) sent to ' . $email . '. Please check your inbox.',
                'email' => $email,
            ]));
    }

    /**
     * AJAX: Verify Password Reset OTP
     */
    public function verifyResetOtp()
    {
        $this->request->allowMethod(['post']);
        $email = strtolower(trim((string)$this->request->getData('email')));
        $otp = trim((string)$this->request->getData('otp'));

        $reset = $this->request->getSession()->read('PasswordReset');
        if (empty($reset) || empty($reset['email']) || $reset['email'] !== $email) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Password reset session expired or invalid. Please request a new OTP.',
                ]));
        }

        if (time() > (int)($reset['expires'] ?? 0)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'The OTP has expired. Please request a new code.',
                ]));
        }

        if ($reset['otp'] !== $otp) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Invalid OTP entered. Please check the 6-digit code in your email.',
                ]));
        }

        // Mark OTP as verified
        $reset['verified'] = true;
        $this->request->getSession()->write('PasswordReset', $reset);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'OTP verified successfully! Please enter your new password.',
                'email' => $email,
            ]));
    }

    /**
     * AJAX: Reset Password with New Credentials
     */
    public function resetPassword()
    {
        $this->request->allowMethod(['post']);
        $email = strtolower(trim((string)$this->request->getData('email')));
        $password = (string)$this->request->getData('password');
        $confirmPassword = (string)$this->request->getData('confirm_password');

        $reset = $this->request->getSession()->read('PasswordReset');
        if (empty($reset) || empty($reset['verified']) || $reset['verified'] !== true || $reset['email'] !== $email) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized or expired session. Please verify OTP first.',
                ]));
        }

        if (strlen($password) < 6) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'New password must be at least 6 characters long.',
                ]));
        }

        if ($password !== $confirmPassword) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'New password and confirm password do not match.',
                ]));
        }

        $user = $this->Users->find()->where(['email' => $email])->first();
        if (!$user) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'User account not found.',
                ]));
        }

        $user->password = $password;
        if ($this->Users->save($user)) {
            $this->request->getSession()->delete('PasswordReset');

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Password reset successfully! You can now sign in with your new password.',
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to update password. Please try again.',
            ]));
    }

    /**
     * User Logout
     */
    public function logout()
    {
        $this->request->getSession()->delete('Flash');
        $this->request->getSession()->delete('AuthUser');
        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', '', (new \DateTime('-1 day')), '/');
        $this->Flash->success('You have been logged out successfully.');
        return $this->redirect(['controller' => 'Users', 'action' => 'login'])
            ->withCookie($cookie);
    }

    /**
     * User Profile
     */
    public function profile()
    {
        $auth = $this->getAuthUser();
        if (!$auth) {
            $this->Flash->error('Please login first.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $user = $this->Users->get($auth['id']);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $name = trim($data['name'] ?? '');

            if (empty($name)) {
                $this->Flash->error('Name cannot be empty.');
            } else {
                $user->name = $name;
                $newPassword = $data['new_password'] ?? '';
                $hasError = false;

                if (!empty($newPassword)) {
                    if (strlen($newPassword) < 6) {
                        $this->Flash->error('New password must be at least 6 characters.');
                        $hasError = true;
                    } else {
                        $user->password = $newPassword;
                    }
                }

                if (!$hasError) {
                    if ($this->Users->save($user)) {
                        $this->request->getSession()->write('AuthUser', [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'gender' => $user->gender ?: 'male',
                            'picture' => $user->picture ?: self::getRandomAvatarPath($user->gender ?: 'male'),
                        ]);
                        $this->Flash->success('Profile updated successfully.');
                        return $this->redirect(['action' => 'profile']);
                    } else {
                        $this->Flash->error('Could not update profile.');
                    }
                }
            }
        }

        $this->set(compact('user'));
    }

    /**
     * AJAX: Get profile details for logged-in user
     */
    public function getProfile()
    {
        $this->request->allowMethod(['get']);
        $auth = $this->getAuthUser();
        if (!$auth) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized']));
        }

        $user = $this->Users->get($auth['id']);
        $apiKey = !empty($user->api_key) ? \App\Model\Entity\User::decryptString($user->api_key) : '';
        $smtpPassword = !empty($user->smtp_password) ? \App\Model\Entity\User::decryptString($user->smtp_password) : '';
        $needsPasswordSetup = (!empty($user->google_id) && empty($user->password_set));

        $gender = !empty($user->gender) ? $user->gender : 'male';
        $picture = (!empty($user->picture) && str_contains($user->picture, "avatars/{$gender}/")) ? $user->picture : self::getAssignedAvatarPath($user->id, $gender);
        if ($user->gender !== $gender || $user->picture !== $picture) {
            $user->gender = $gender;
            $user->picture = $picture;
            $this->Users->save($user);
        }
        $webroot = $this->request->getAttribute('webroot') ?? '/';
        $avatarUrl = self::getAvatarUrl($picture, $gender, $webroot);

        // Precompute assigned avatars for both genders for this user
        $maleAvatar = ($gender === 'male') ? $picture : self::getAssignedAvatarPath($user->id, 'male');
        $femaleAvatar = ($gender === 'female') ? $picture : self::getAssignedAvatarPath($user->id, 'female');
        $maleAvatarUrl = self::getAvatarUrl($maleAvatar, 'male', $webroot);
        $femaleAvatarUrl = self::getAvatarUrl($femaleAvatar, 'female', $webroot);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $gender,
                    'picture' => $picture,
                    'avatar_url' => $avatarUrl,
                    'male_avatar' => $maleAvatar,
                    'male_avatar_url' => $maleAvatarUrl,
                    'female_avatar' => $femaleAvatar,
                    'female_avatar_url' => $femaleAvatarUrl,
                    'is_google_user' => !empty($user->google_id),
                    'needs_password_setup' => $needsPasswordSetup,
                    'api_key' => $apiKey,
                    'smtp_password' => $smtpPassword,
                    'has_smtp_password' => !empty($smtpPassword),
                    'created_formatted' => $user->created ? $user->created->format('d M Y, h:i A') : 'N/A',
                ],
            ]));
    }

    /**
     * AJAX: Update profile details for logged-in user
     */
    public function updateProfile()
    {
        $this->request->allowMethod(['post', 'put']);
        $auth = $this->getAuthUser();
        if (!$auth) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized']));
        }

        $user = $this->Users->get($auth['id']);
        $data = $this->request->getData();

        $name = trim((string)($data['name'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));

        // User can update gender (male or female)
        $newGender = strtolower(trim((string)($data['gender'] ?? '')));
        if ($newGender === 'male' || $newGender === 'female') {
            $user->gender = $newGender;
            $clientPic = trim((string)($data['picture'] ?? ''));
            // If client sent the assigned picture for this gender and it exists, save it!
            if (!empty($clientPic) && str_contains($clientPic, "avatars/{$newGender}/") && file_exists(WWW_ROOT . ltrim($clientPic, '/'))) {
                $user->picture = $clientPic;
            } elseif (empty($user->picture) || !str_contains((string)$user->picture, "avatars/{$newGender}/")) {
                $user->picture = self::getAssignedAvatarPath($user->id, $newGender);
            }
        } elseif (empty($user->gender)) {
            $user->gender = 'male';
            $user->picture = self::getAssignedAvatarPath($user->id, 'male');
        }

        $currentPassword = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');
        $hasApiKey = array_key_exists('api_key', $data);
        $apiKey = trim((string)($data['api_key'] ?? ''));
        $hasSmtpPassword = array_key_exists('smtp_password', $data);
        $smtpPassword = trim((string)($data['smtp_password'] ?? ''));
        $needsPasswordSetup = (!empty($user->google_id) && empty($user->password_set));

        if ($name === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'field' => 'name', 'message' => 'Full Name cannot be empty.']));
        }

        if (mb_strlen($name) > 100) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'field' => 'name', 'message' => 'Full Name cannot exceed 100 characters.']));
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'field' => 'email', 'message' => 'Please enter a valid email address.']));
        }

        // Check if email already used by another user
        $dup = $this->Users->find()->where(['email' => $email, 'id !=' => $user->id])->first();
        if ($dup) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'field' => 'email', 'message' => 'This email address is already in use by another account.']));
        }

        // Password change validation
        if ($newPassword !== '') {
            if (!$needsPasswordSetup) {
                if ($currentPassword === '') {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'field' => 'current_password', 'message' => 'Please enter your current password to set a new password.']));
                }
                if (!password_verify($currentPassword, $user->password)) {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'field' => 'current_password', 'message' => 'Current password is incorrect.']));
                }
            }
            if (strlen($newPassword) < 6) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'field' => 'new_password', 'message' => 'New password must be at least 6 characters.']));
            }
            if ($newPassword !== $confirmPassword) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'field' => 'confirm_password', 'message' => 'New passwords do not match.']));
            }
            $user->password = $newPassword;
            $user->password_set = 1;
        }

        $user->name = $name;
        $user->email = $email;

        if ($hasApiKey) {
            $user->api_key = !empty($apiKey) ? \App\Model\Entity\User::encryptString($apiKey) : null;
        }

        if ($hasSmtpPassword) {
            $user->smtp_password = !empty($smtpPassword) ? \App\Model\Entity\User::encryptString($smtpPassword) : null;
        }

        if ($this->Users->save($user)) {
            // Update session
            $this->request->getSession()->write('AuthUser', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'picture' => $user->picture,
            ]);

            $webroot = $this->request->getAttribute('webroot') ?? '/';
            $avatarUrl = self::getAvatarUrl($user->picture, $user->gender, $webroot);
            $resApiKey = !empty($user->api_key) ? \App\Model\Entity\User::decryptString($user->api_key) : '';
            $resSmtpPassword = !empty($user->smtp_password) ? \App\Model\Entity\User::decryptString($user->smtp_password) : '';

            $maleAvatar = ($user->gender === 'male') ? $user->picture : self::getAssignedAvatarPath($user->id, 'male');
            $femaleAvatar = ($user->gender === 'female') ? $user->picture : self::getAssignedAvatarPath($user->id, 'female');
            $maleAvatarUrl = self::getAvatarUrl($maleAvatar, 'male', $webroot);
            $femaleAvatarUrl = self::getAvatarUrl($femaleAvatar, 'female', $webroot);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'gender' => $user->gender,
                        'picture' => $user->picture,
                        'avatar_url' => $avatarUrl,
                        'male_avatar' => $maleAvatar,
                        'male_avatar_url' => $maleAvatarUrl,
                        'female_avatar' => $femaleAvatar,
                        'female_avatar_url' => $femaleAvatarUrl,
                        'api_key' => $resApiKey,
                        'smtp_password' => $resSmtpPassword,
                        'has_smtp_password' => !empty($resSmtpPassword),
                    ],
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save profile changes.',
                'errors' => $user->getErrors(),
            ]));
    }

    /**
     * AJAX: Save System Settings (Gemini AI API Key and/or Google SMTP credentials)
     */
    public function saveSettings()
    {
        $this->request->allowMethod(['post', 'put']);
        $auth = $this->getAuthUser();
        if (!$auth) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized']));
        }

        $user = $this->Users->get($auth['id']);
        $data = $this->request->getData();

        if (array_key_exists('api_key', $data)) {
            $apiKey = trim((string)$data['api_key']);
            $user->api_key = !empty($apiKey) ? \App\Model\Entity\User::encryptString($apiKey) : null;
        }

        if (array_key_exists('smtp_password', $data)) {
            $smtpPassword = trim((string)$data['smtp_password']);
            $user->smtp_password = !empty($smtpPassword) ? \App\Model\Entity\User::encryptString($smtpPassword) : null;
        }

        if ($this->Users->save($user)) {
            $currentApiKey = !empty($user->api_key) ? \App\Model\Entity\User::decryptString($user->api_key) : '';
            $currentSmtp = !empty($user->smtp_password) ? \App\Model\Entity\User::decryptString($user->smtp_password) : '';
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'System settings saved successfully!',
                    'api_key' => $currentApiKey,
                    'has_api_key' => !empty($currentApiKey),
                    'has_smtp_password' => !empty($currentSmtp),
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save settings.',
                'errors' => $user->getErrors(),
            ]));
    }

    /**
     * Google Sign-In via GIS Credential (POST JWT token)
     */
    public function googleLogin()
    {
        $this->request->allowMethod(['post']);
        $credential = $this->request->getData('credential');

        if (empty($credential)) {
            $jsonData = $this->request->input('json_decode', true);
            $credential = $jsonData['credential'] ?? null;
        }

        if (empty($credential)) {
            $this->Flash->error('Invalid Google Sign-In response. Please try again.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        // Verify ID Token with Google tokeninfo endpoint
        $verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode((string)$credential);
        $ch = curl_init($verifyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$resp) {
            // Local fallback: Decode JWT payload directly from Google credential token
            $parts = explode('.', (string)$credential);
            if (count($parts) === 3) {
                $jwtBody = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                if (!empty($jwtBody['sub']) && !empty($jwtBody['email'])) {
                    $payload = $jwtBody;
                }
            }

            if (empty($payload)) {
                $this->Flash->error('Failed to verify Google token with Google servers. Please try again.');
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }
        } else {
            $payload = json_decode($resp, true);
        }

        if (empty($payload['sub']) || empty($payload['email'])) {
            $this->Flash->error('Google authentication did not provide valid user information.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $googleId = (string)$payload['sub'];
        $email = strtolower(trim((string)$payload['email']));
        $name = trim((string)($payload['name'] ?? ''));
        $picture = (string)($payload['picture'] ?? '');

        $result = $this->handleGoogleUser($googleId, $email, $name, $picture);

        if ($result['success']) {
            $user = $result['user'];
            $isNew = !empty($result['is_new']);
            $welcomeMsg = $isNew 
                ? 'Welcome to Helpdesk, ' . $user->name . '! Your account was created successfully with Google.'
                : 'Welcome back, ' . $user->name . '!';
            
            $this->Flash->success($welcomeMsg);

            return $this->redirect(['controller' => 'Tasks', 'action' => 'index'])
                ->withCookie($result['cookie']);
        }

        $this->Flash->error('Failed to authenticate with Google: ' . ($result['message'] ?? 'Unknown error'));
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    /**
     * Start Google OAuth 2.0 Authorization Flow (Redirect to Google)
     */
    public function googleAuth()
    {
        $clientId = (string)\Cake\Core\Configure::read('Google.clientId');
        $redirectUri = \Cake\Routing\Router::url(['controller' => 'Users', 'action' => 'googleCallback'], true);

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        return $this->redirect($authUrl);
    }

    /**
     * Google OAuth 2.0 Callback
     */
    public function googleCallback()
    {
        $code = $this->request->getQuery('code');
        $error = $this->request->getQuery('error');

        if ($error || empty($code)) {
            $this->Flash->error('Google Sign-In was cancelled or failed.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $clientId = (string)\Cake\Core\Configure::read('Google.clientId');
        $clientSecret = (string)\Cake\Core\Configure::read('Google.clientSecret');
        $redirectUri = \Cake\Routing\Router::url(['controller' => 'Users', 'action' => 'googleCallback'], true);

        // Exchange Authorization Code for Access Token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $tokenResp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$tokenResp) {
            $this->Flash->error('Failed to exchange authorization code with Google.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $tokenData = json_decode($tokenResp, true);
        $accessToken = $tokenData['access_token'] ?? null;
        $idToken = $tokenData['id_token'] ?? null;

        // Fetch User Info from Google
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $userResp = curl_exec($ch);
        $userHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($userHttpCode !== 200 || !$userResp) {
            $this->Flash->error('Failed to retrieve user profile from Google.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $userData = json_decode($userResp, true);
        $googleId = (string)($userData['sub'] ?? '');
        $email = strtolower(trim((string)($userData['email'] ?? '')));
        $name = trim((string)($userData['name'] ?? ''));
        $picture = (string)($userData['picture'] ?? '');

        if (empty($googleId) || empty($email)) {
            $this->Flash->error('Google account details are incomplete.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $result = $this->handleGoogleUser($googleId, $email, $name, $picture);

        if ($result['success']) {
            $user = $result['user'];
            $isNew = !empty($result['is_new']);
            $welcomeMsg = $isNew 
                ? 'Welcome to Helpdesk, ' . $user->name . '! Your account was created successfully with Google.'
                : 'Welcome back, ' . $user->name . '!';

            $this->Flash->success($welcomeMsg);

            return $this->redirect(['controller' => 'Tasks', 'action' => 'index'])
                ->withCookie($result['cookie']);
        }

        $this->Flash->error('Failed to authenticate with Google: ' . ($result['message'] ?? 'Unknown error'));
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    /**
     * Helper: Find or Create User from Google Authentication Data with Non-Blank Secure Password
     */
    protected function handleGoogleUser(string $googleId, string $email, string $name, ?string $picture): array
    {
        $isNew = false;

        // 1. Check if user exists by google_id
        $user = $this->Users->find()->where(['google_id' => $googleId])->first();

        // 2. If not found by google_id, check if user exists with matching email
        if (!$user) {
            $user = $this->Users->find()->where(['email' => $email])->first();
            if ($user) {
                // Link Google ID and update picture if empty
                $user->google_id = $googleId;
                if (!empty($picture) && empty($user->picture)) {
                    $user->picture = $picture;
                }
                $this->Users->save($user);
            }
        }

        // 3. If user is brand new, create new user entity with a strong random password (NEVER BLANK)
        if (!$user) {
            $isNew = true;
            $displayName = !empty($name) ? $name : explode('@', $email)[0];
            
            // Generate 32-character cryptographically secure random password
            $randomSecurePassword = bin2hex(random_bytes(16));

            $gender = 'male';
            $userPicture = !empty($picture) ? $picture : self::getRandomAvatarPath($gender);

            $user = $this->Users->newEntity([
                'name' => $displayName,
                'email' => $email,
                'password' => $randomSecurePassword, // Will be securely hashed by User::_setPassword()
                'password_set' => 0, // Needs first-time password setup
                'google_id' => $googleId,
                'gender' => $gender,
                'picture' => $userPicture,
            ]);

            if (!$this->Users->save($user)) {
                return [
                    'success' => false,
                    'message' => 'Could not create user account in database.',
                    'errors' => $user->getErrors(),
                ];
            }
        }

        // 4. Set Session AuthUser
        $this->request->getSession()->delete('Flash');
        $this->request->getSession()->write('AuthUser', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender ?: 'male',
            'picture' => $user->picture ?: self::getRandomAvatarPath($user->gender ?: 'male'),
        ]);

        // 5. Generate 30-day Remember User Cookie
        $token = base64_encode($user->id . ':' . hash('sha256', $user->email . $user->password));
        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', $token, (new \DateTime('+30 days')), '/', '', false, true);

        return [
            'success' => true,
            'user' => $user,
            'is_new' => $isNew,
            'cookie' => $cookie,
        ];
    }
}

