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
            $password = $data['password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';

            if (empty($data['name']) || empty($data['email']) || empty($password)) {
                $this->Flash->error('Please fill in all required fields.');
            } elseif ($password !== $confirmPassword) {
                $this->Flash->error('Passwords do not match. Please try again.');
            } elseif (strlen($password) < 6) {
                $this->Flash->error('Password must be at least 6 characters long.');
            } else {
                $existing = $this->Users->find()->where(['email' => trim($data['email'])])->first();
                if ($existing) {
                    $this->Flash->error('An account with this email already exists. Please log in.');
                } else {
                    $user = $this->Users->patchEntity($user, [
                        'name' => trim($data['name']),
                        'email' => strtolower(trim($data['email'])),
                        'password' => $password,
                    ]);

                    if ($this->Users->save($user)) {
                        // Clear any old flash messages
                        $this->request->getSession()->delete('Flash');

                        // Automatically log in
                        $this->request->getSession()->write('AuthUser', [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ]);

                        $this->Flash->success('Welcome, ' . $user->name . '! Your account has been created successfully.');
                        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
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
            } else {
                $user = $this->Users->find()->where(['email' => $email])->first();

                if ($user && password_verify($password, $user->password)) {
                    // Clear old flash messages
                    $this->request->getSession()->delete('Flash');

                    // Successful login
                    $this->request->getSession()->write('AuthUser', [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]);

                    if ($rememberMe) {
                        $token = base64_encode($user->id . ':' . hash('sha256', $user->email . $user->password));
                        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', $token, (new \DateTime('+30 days')), '/', '', false, true);
                        $this->response = $this->response->withCookie($cookie);
                    } else {
                        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', '', (new \DateTime('-1 day')), '/');
                        $this->response = $this->response->withCookie($cookie);
                    }

                    $this->Flash->success('Welcome back, ' . $user->name . '!');
                    return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
                } else {
                    $this->Flash->error('Invalid email or password. Please try again.');
                }
            }
        }
    }

    /**
     * User Logout
     */
    public function logout()
    {
        $this->request->getSession()->delete('Flash');
        $this->request->getSession()->delete('AuthUser');
        $cookie = new \Cake\Http\Cookie\Cookie('remember_user', '', (new \DateTime('-1 day')), '/');
        $this->response = $this->response->withCookie($cookie);
        $this->Flash->success('You have been logged out successfully.');
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
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
            $user->name = trim($data['name'] ?? $user->name);

            $newPassword = $data['new_password'] ?? '';
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $this->Flash->error('New password must be at least 6 characters.');
                } else {
                    $user->password = $newPassword;
                }
            }

            if ($this->Users->save($user)) {
                $this->request->getSession()->write('AuthUser', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
                $this->Flash->success('Profile updated successfully.');
                return $this->redirect(['action' => 'profile']);
            } else {
                $this->Flash->error('Could not update profile.');
            }
        }

        $this->set(compact('user'));
    }
}
