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

        if ($this->request->is('ajax') || $this->request->is('json') || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $this->response = $this->response
                ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                ->withHeader('Pragma', 'no-cache')
                ->withHeader('Expires', '0');
        }

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
}

