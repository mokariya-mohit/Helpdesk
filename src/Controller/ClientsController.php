<?php
declare(strict_types=1);

namespace App\Controller;

class ClientsController extends AppController
{
    protected ?\App\Model\Table\ClientsTable $Clients = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->Clients = $this->fetchTable('Clients');
    }

    /**
     * Helper to get user ID safely
     */
    protected function getCurrentUserId(): int
    {
        $auth = $this->getAuthUser();
        return $auth ? (int)$auth['id'] : 0;
    }

    /**
     * AJAX: Get all clients for logged-in user
     */
    public function getClients()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();

        $clients = $this->Clients->find('all')
            ->where(['user_id' => $userId])
            ->orderBy(['is_default' => 'DESC', 'name' => 'ASC'])
            ->all();

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'clients' => $clients,
            ]));
    }

    /**
     * AJAX: Add client for logged-in user
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $name = trim((string)($this->request->getData('name') ?? ''));
        $email = strtolower(trim((string)($this->request->getData('email') ?? '')));

        if ($name === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client name cannot be empty']));
        }

        if (mb_strlen($name) > 100) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client name cannot exceed 100 characters']));
        }

        if ($email !== '') {
            if (mb_strlen($email) > 255) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Email address cannot exceed 255 characters']));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/', $email)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter a valid email address (e.g. client@company.com)']));
            }

            // Check if another client already uses this email
            $duplicateClient = $this->Clients->find()
                ->where(['user_id' => $userId, 'email' => $email])
                ->first();

            if ($duplicateClient && strcasecmp($duplicateClient->name, $name) !== 0) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => "Client '{$duplicateClient->name}' is already registered with this email address.",
                    ]));
            }
        }

        $existing = $this->Clients->find()
            ->where(['user_id' => $userId, 'name' => $name])
            ->first();

        if ($existing) {
            if ($email !== '' && $existing->email !== $email) {
                $existing->email = $email;
                $this->Clients->save($existing);
            }
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Client already exists',
                    'client' => $existing,
                ]));
        }

        $client = $this->Clients->newEntity([
            'user_id' => $userId,
            'name' => $name,
            'email' => $email !== '' ? $email : null,
            'is_default' => 0,
        ]);
        $client->user_id = $userId;
        $client->email = $email !== '' ? $email : null;

        if ($this->Clients->save($client)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Client created successfully',
                    'client' => $client,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save client',
                'errors' => $client->getErrors(),
            ]));
    }

    /**
     * AJAX: Delete client (only user's own client)
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userId = $this->getCurrentUserId();
        $id = $id ?? $this->request->getData('id');

        if (!$id) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client ID required']));
        }

        $client = $this->Clients->find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();

        if (!$client) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client not found or unauthorized']));
        }

        if ($this->Clients->delete($client)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Client deleted successfully',
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Could not delete client',
            ]));
    }

    /**
     * AJAX: Edit / Rename client & email (only user's own client)
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $userId = $this->getCurrentUserId();
        $id = $id ?? $this->request->getData('id');
        $newName = trim((string)($this->request->getData('name') ?? ''));
        $hasEmail = $this->request->getData('email') !== null;
        $newEmail = strtolower(trim((string)($this->request->getData('email') ?? '')));

        if (!$id || $newName === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client ID and new name required']));
        }

        if (mb_strlen($newName) > 100) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client name cannot exceed 100 characters']));
        }

        if ($hasEmail && $newEmail !== '') {
            if (mb_strlen($newEmail) > 255) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Email address cannot exceed 255 characters']));
            }

            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/', $newEmail)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter a valid email address (e.g. client@company.com)']));
            }

            // Check if another client already uses this email
            $duplicateClient = $this->Clients->find()
                ->where(['user_id' => $userId, 'email' => $newEmail, 'id !=' => $id])
                ->first();

            if ($duplicateClient) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => "Client '{$duplicateClient->name}' is already registered with this email address.",
                    ]));
            }
        }

        $client = $this->Clients->find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();

        if (!$client) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client not found or unauthorized']));
        }

        $client->name = $newName;
        if ($hasEmail) {
            $client->email = $newEmail !== '' ? $newEmail : null;
        }

        if ($this->Clients->save($client)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Client updated successfully',
                    'client' => $client,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to update client',
                'errors' => $client->getErrors(),
            ]));
    }
}
