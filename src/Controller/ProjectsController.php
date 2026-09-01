<?php
declare(strict_types=1);

namespace App\Controller;

class ProjectsController extends AppController
{
    protected ?\App\Model\Table\ProjectsTable $Projects = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->Projects = $this->fetchTable('Projects');
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
     * AJAX: Get all projects for logged-in user
     */
    public function getProjects()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();

        $projects = $this->Projects->find('all')
            ->where(['user_id' => $userId])
            ->orderBy(['is_default' => 'DESC', 'name' => 'ASC'])
            ->all();

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'projects' => $projects,
            ]));
    }

    /**
     * AJAX: Add project for logged-in user
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $name = trim($this->request->getData('name') ?? '');

        if ($name === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Project name cannot be empty']));
        }

        $existing = $this->Projects->find()
            ->where(['user_id' => $userId, 'name' => $name])
            ->first();

        if ($existing) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Project already exists',
                    'project' => $existing,
                ]));
        }

        $project = $this->Projects->newEntity([
            'user_id' => $userId,
            'name' => $name,
            'is_default' => 0,
        ]);
        $project->user_id = $userId;

        if ($this->Projects->save($project)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Project created successfully',
                    'project' => $project,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save project',
                'errors' => $project->getErrors(),
            ]));
    }

    /**
     * AJAX: Delete project (only user's own project)
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userId = $this->getCurrentUserId();
        $id = $id ?? $this->request->getData('id');

        if (!$id) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Project ID required']));
        }

        $project = $this->Projects->find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();

        if (!$project) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Project not found or unauthorized']));
        }

        if ($project->is_default) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Cannot delete the default project']));
        }

        if ($this->Projects->delete($project)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Project deleted successfully',
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Could not delete project',
            ]));
    }

    /**
     * AJAX: Edit / Rename project (only user's own project)
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        $userId = $this->getCurrentUserId();
        $id = $id ?? $this->request->getData('id');
        $newName = trim($this->request->getData('name') ?? '');

        if (!$id || $newName === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Project ID and new name required']));
        }

        $project = $this->Projects->find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->first();

        if (!$project) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Project not found or unauthorized']));
        }

        $project->name = $newName;
        if ($this->Projects->save($project)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Project renamed successfully',
                    'project' => $project,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to update project name',
            ]));
    }
}
