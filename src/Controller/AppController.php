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
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $currentUser = $this->request->getSession()->read('AuthUser');
        $this->set(compact('currentUser'));
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
                    $userData = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                    $this->request->getSession()->write('AuthUser', $userData);
                    return $userData;
                }
            }
        }

        return null;
    }

    /**
     * Require authentication for protected actions
     */
    protected function requireAuth()
    {
        if (!$this->getAuthUser()) {
            $this->Flash->error('Please login to access this page.');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        return null;
    }
}
