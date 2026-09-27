<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->ensureDatabaseTables();
    }

    /**
     * Automatically ensure all required database tables and columns exist
     */
    protected function ensureDatabaseTables(): void
    {
        try {
            $connection = \Cake\Datasource\ConnectionManager::get('default');
            $schemaCollection = $connection->getSchemaCollection();
            $tables = $schemaCollection->listTables();

            // 1. Add last_seen_at column to users table if missing
            try {
                if (in_array('users', $tables)) {
                    $userCols = $schemaCollection->describe('users')->columns();
                    if (!in_array('last_seen_at', $userCols)) {
                        $connection->execute("ALTER TABLE `users` ADD COLUMN `last_seen_at` DATETIME NULL AFTER `modified`");
                    }
                }
            } catch (\Throwable $e) {}

            // 2. Create chat tables if missing
            if (!in_array('chat_conversations', $tables) || !in_array('chat_requests', $tables)) {
                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_requests` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `sender_id` INT NOT NULL,
                  `receiver_id` INT NOT NULL,
                  `initial_message` TEXT NULL,
                  `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
                  `accepted_at` DATETIME NULL,
                  `rejected_at` DATETIME NULL,
                  `created` DATETIME NOT NULL,
                  `modified` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_cr_sender_id` (`sender_id`),
                  KEY `idx_cr_receiver_id` (`receiver_id`),
                  KEY `idx_cr_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_conversations` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `created_by` INT NOT NULL,
                  `type` ENUM('direct', 'group') NOT NULL DEFAULT 'direct',
                  `title` VARCHAR(255) NULL,
                  `description` TEXT NULL,
                  `icon` VARCHAR(255) NULL,
                  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
                  `created` DATETIME NOT NULL,
                  `modified` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_cc_created_by` (`created_by`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_conversation_users` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `conversation_id` INT NOT NULL,
                  `user_id` INT NOT NULL,
                  `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
                  `joined_at` DATETIME NOT NULL,
                  `last_read_at` DATETIME NULL,
                  `cleared_at` DATETIME NULL,
                  `typing_at` DATETIME NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uq_ccu_conv_user` (`conversation_id`, `user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_messages` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `conversation_id` INT NOT NULL,
                  `sender_id` INT NOT NULL,
                  `message` TEXT NULL,
                  `message_type` VARCHAR(20) NOT NULL DEFAULT 'text',
                  `attachment_path` VARCHAR(255) NULL,
                  `attachment_name` VARCHAR(255) NULL,
                  `attachment_size` INT NULL DEFAULT 0,
                  `attachment_type` VARCHAR(50) NULL,
                  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
                  `is_edited` TINYINT(1) NOT NULL DEFAULT 0,
                  `edited_at` DATETIME NULL,
                  `created` DATETIME NOT NULL,
                  `modified` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_cm_conversation_created` (`conversation_id`, `created`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_message_attachments` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `message_id` INT NOT NULL,
                  `file_path` VARCHAR(255) NOT NULL,
                  `file_name` VARCHAR(255) NOT NULL,
                  `file_size` INT NOT NULL DEFAULT 0,
                  `file_type` VARCHAR(50) NOT NULL,
                  `created` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `idx_cma_message_id` (`message_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                $connection->execute("CREATE TABLE IF NOT EXISTS `chat_message_reactions` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `message_id` INT NOT NULL,
                  `user_id` INT NOT NULL,
                  `reaction` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                  `created` DATETIME NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uq_msg_user` (`message_id`, `user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }
        } catch (\Throwable $e) {
            // Silently continue
        }
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');

        // Allow public access to login, signup, forgot password, and Google auth endpoints
        if ($controller === 'Users' && in_array($action, ['login', 'signup', 'forgotPassword', 'verifyResetOtp', 'resetPassword', 'googleLogin', 'googleAuth', 'googleCallback'], true)) {
            return null;
        }

        // Require authentication for all other pages and AJAX endpoints
        $authUser = $this->getAuthUser();
        if (!$authUser) {
            if ($this->request->is('json') || $this->request->is('ajax') || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response
                    ->withStatus(401)
                    ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                    ->withHeader('Pragma', 'no-cache')
                    ->withHeader('Expires', '0')
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'require_login' => true,
                        'message' => 'Please login to access this feature.',
                    ]));
            }

            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        return null;
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        // Prevent browser caching for ALL responses (HTML, JSON, AJAX)
        // Ensures user always gets fresh date, fresh workpad, and valid CSRF token without hard refresh
        $this->response = $this->response
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');


        $currentUser = $this->request->getSession()->read('AuthUser');
        if ($currentUser && !empty($currentUser['id'])) {
            if (empty($currentUser['picture']) || empty($currentUser['gender'])) {
                $usersTable = $this->fetchTable('Users');
                $dbUser = $usersTable->find()->where(['id' => $currentUser['id']])->first();
                if ($dbUser) {
                    $gender = !empty($dbUser->gender) ? $dbUser->gender : 'male';
                    $picture = !empty($dbUser->picture) ? $dbUser->picture : \App\Controller\UsersController::getRandomAvatarPath($gender);
                    if ($dbUser->picture !== $picture || $dbUser->gender !== $gender) {
                        $dbUser->gender = $gender;
                        $dbUser->picture = $picture;
                        $usersTable->save($dbUser);
                    }
                    $currentUser['gender'] = $gender;
                    $currentUser['picture'] = $picture;
                    $this->request->getSession()->write('AuthUser', $currentUser);
                }
            }
        }

        // Calculate global chat notifications count (pending requests + unread messages)
        $globalNotificationCount = 0;
        if (!empty($currentUser['id'])) {
            try {
                $uid = (int)$currentUser['id'];
                $chatReqTable = $this->fetchTable('ChatRequests');
                $pendingRequests = $chatReqTable->find()
                    ->where(['receiver_id' => $uid, 'status' => 'pending'])
                    ->count();

                $convUsersTable = $this->fetchTable('ChatConversationUsers');
                $chatMessagesTable = $this->fetchTable('ChatMessages');
                $userConvs = $convUsersTable->find()->where(['user_id' => $uid])->all();

                $unreadMessages = 0;
                foreach ($userConvs as $row) {
                    $q = $chatMessagesTable->find()->where([
                        'conversation_id' => $row->conversation_id,
                        'sender_id !=' => $uid,
                        'is_deleted' => 0,
                    ]);
                    if ($row->last_read_at) {
                        $q->where(['created >' => $row->last_read_at]);
                    }
                    $unreadMessages += $q->count();
                }
                $globalNotificationCount = $pendingRequests + $unreadMessages;
            } catch (\Throwable $e) {
                $globalNotificationCount = 0;
            }
        }

        $this->set(compact('currentUser', 'globalNotificationCount'));
    }

    /**
     * Check if user is authenticated (Session + Remember Me Cookie)
     */
    protected function getAuthUser(): ?array
    {
        $sessionAuth = $this->request->getSession()->read('AuthUser');
        if ($sessionAuth) {
            return $sessionAuth;
        }

        $cookieToken = $this->request->getCookie('remember_user');
        if ($cookieToken) {
            $decoded = base64_decode($cookieToken, true);
            if ($decoded && str_contains($decoded, ':')) {
                [$userId, $tokenHash] = explode(':', $decoded, 2);
                $usersTable = $this->fetchTable('Users');
                $user = $usersTable->find()->where(['id' => (int)$userId])->first();
                if ($user && hash('sha256', $user->email . $user->password) === $tokenHash) {
                    $gender = !empty($user->gender) ? $user->gender : 'male';
                    $picture = !empty($user->picture) ? $user->picture : \App\Controller\UsersController::getRandomAvatarPath($gender);
                    if ($user->picture !== $picture || $user->gender !== $gender) {
                        $user->gender = $gender;
                        $user->picture = $picture;
                        $usersTable->save($user);
                    }
                    $userData = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'gender' => $gender,
                        'picture' => $picture,
                    ];
                    $this->request->getSession()->write('AuthUser', $userData);
                    return $userData;
                }
            }
        }

        return null;
    }
}

