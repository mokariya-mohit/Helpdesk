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
        return $auth ? (int)$auth['id'] : 1;
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
        $name = trim($this->request->getData('name') ?? '');

        if ($name === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client name cannot be empty']));
        }

        $existing = $this->Clients->find()
            ->where(['user_id' => $userId, 'name' => $name])
            ->first();

        if ($existing) {
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
            'is_default' => 0,
        ]);
        $client->user_id = $userId;

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
     * AJAX: Edit / Rename client (only user's own client)
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $userId = $this->getCurrentUserId();
        $id = $id ?? $this->request->getData('id');
        $newName = trim($this->request->getData('name') ?? '');

        if (!$id || $newName === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client ID and new name required']));
        }

        $client = $this->Clients->find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();

        if (!$client) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Client not found or unauthorized']));
        }

        $client->name = $newName;
        if ($this->Clients->save($client)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Client renamed successfully',
                    'client' => $client,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to update client name',
            ]));
    }
}
