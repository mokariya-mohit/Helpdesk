<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\DateTime;

class DailyUpdatesController extends AppController
{
    protected ?\App\Model\Table\DailyUpdatesTable $DailyUpdates = null;
    protected ?\App\Model\Table\WorkLogsTable $WorkLogs = null;
    protected ?\App\Model\Table\ProjectsTable $Projects = null;
    protected ?\App\Model\Table\ClientsTable $Clients = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->DailyUpdates = $this->fetchTable('DailyUpdates');
        $this->WorkLogs = $this->fetchTable('WorkLogs');
        $this->Projects = $this->fetchTable('Projects');
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
     * Convert DD-MM-YYYY or any date string to YYYY-MM-DD
     */
    private function parseDateToIso(string $dateStr): string
    {
        $dateStr = trim($dateStr);
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateStr)) {
            $parts = explode('-', $dateStr);
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $dateStr;
    }

    /**
     * Clean raw work note text to extract only task bullet points
     */
    private function extractTaskLines(?string $text): string
    {
        if (empty($text) || trim($text) === '') {
            return '';
        }

        $lines = explode("\n", trim($text));
        $startIndex = 0;

        if (count($lines) > 0 && preg_match('/^\d{2}-\d{2}-\d{4}/', trim($lines[0]))) {
            $startIndex = 1;
            if (count($lines) > 1 && preg_match('/^[-=]{3,}/', trim($lines[1]))) {
                $startIndex = 2;
            }
        }

        $extracted = array_slice($lines, $startIndex);
        $clean = [];
        foreach ($extracted as $line) {
            $clean[] = preg_replace('/^[ \t]+/', '', $line);
        }

        return trim(implode("\n", $clean));
    }

    /**
     * Main Daily Update Generator UI for Logged-In User
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();

        $clients = $this->Clients->find('all')->where(['user_id' => $userId])->orderBy(['name' => 'ASC'])->all();
        $projects = $this->Projects->find('all')
            ->where(['user_id' => $userId])
            ->orderBy(['is_default' => 'DESC', 'name' => 'ASC'])
            ->all();

        $queryProjectId = $this->request->getQuery('project_id');
        $queryProjectName = $this->request->getQuery('project_name') ?? $this->request->getQuery('project');
        $queryClientName = $this->request->getQuery('client_name') ?? $this->request->getQuery('client');

        $activeProject = null;
        if ($queryProjectId) {
            $activeProject = $this->Projects->find()->where(['user_id' => $userId, 'id' => (int)$queryProjectId])->first();
        }
        if (!$activeProject && $queryProjectName) {
            $activeProject = $this->Projects->find()->where(['user_id' => $userId, 'name' => trim($queryProjectName)])->first();
        }
        if (!$activeProject) {
            $activeProject = $this->Projects->find()
                ->where(['user_id' => $userId, 'is_default' => 1])
                ->first() ?? $projects->first();
        }
        $defaultProject = $activeProject;

        $today = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $todayIso = $today->format('Y-m-d');
        $todayFormatted = $today->format('d-m-Y');

        $detectedProjectName = $queryProjectName ?: ($defaultProject ? $defaultProject->name : '');
        $detectedClientName = $queryClientName ?: '';

        // Check if there is an existing saved Daily Update for today for this user
        $savedUpdate = null;
        if ($defaultProject) {
            $savedUpdate = $this->DailyUpdates->find()
                ->where(['user_id' => $userId, 'update_date' => $todayIso, 'project_id' => $defaultProject->id])
                ->first();
        }

        // Auto-fetch completed tasks from today's Work Log for this user
        $autoDoneTasks = '';
        $workLogConditions = ['WorkLogs.user_id' => $userId, 'WorkLogs.log_date' => $todayIso];
        if ($defaultProject) {
            $workLogConditions['WorkLogs.project_id'] = $defaultProject->id;
        }

        $todayWorkLog = $this->WorkLogs->find()
            ->where($workLogConditions)
            ->contain(['Projects', 'Clients'])
            ->orderBy(['WorkLogs.modified' => 'DESC'])
            ->first();

        if ($todayWorkLog) {
            $autoDoneTasks = $this->extractTaskLines($todayWorkLog->content);
            if (!$detectedProjectName && $todayWorkLog->project) {
                $detectedProjectName = $todayWorkLog->project->name;
            }
            if (!$detectedClientName && $todayWorkLog->client) {
                $detectedClientName = $todayWorkLog->client->name;
            }
        }

        $this->set(compact('clients', 'projects', 'defaultProject', 'todayIso', 'todayFormatted', 'savedUpdate', 'autoDoneTasks', 'detectedProjectName', 'detectedClientName'));
    }

    /**
     * AJAX Endpoint: Fetch Saved Update for Logged-In User
     */
    public function getUpdate()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();
        $dateInput = $this->request->getQuery('date') ?? $this->request->getData('date') ?? date('Y-m-d');
        $projectId = $this->request->getQuery('project_id') ?? $this->request->getData('project_id');

        $isoDate = $this->parseDateToIso($dateInput);
        $conditions = ['user_id' => $userId, 'update_date' => $isoDate];
        if ($projectId) {
            $conditions['project_id'] = $projectId;
        }

        $update = $this->DailyUpdates->find()->where($conditions)->first();

        if ($update) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'found' => true,
                    'data' => [
                        'id' => $update->id,
                        'update_date' => $update->update_date ? $update->update_date->format('d-m-Y') : $dateInput,
                        'client_name' => $update->client_name,
                        'project_name' => $update->project_name,
                        'tl_name' => $update->tl_name,
                        'done_tasks' => $update->done_tasks,
                        'progress_tasks' => $update->progress_tasks,
                        'remaining_tasks' => $update->remaining_tasks,
                        'queries' => $update->queries,
                        'notes' => $update->notes,
                    ],
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'found' => false,
                'message' => 'No saved update for this date',
            ]));
    }

    /**
     * AJAX Endpoint: Save Daily Update for Logged-In User
     */
    public function saveUpdate()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();

        $dateInput = $this->request->getData('update_date') ?? date('Y-m-d');
        $isoDate = $this->parseDateToIso($dateInput);

        $projectName = trim($this->request->getData('project_name') ?? '8.bloqs');
        $projectId = $this->request->getData('project_id');

        if (!$projectId) {
            $proj = $this->Projects->find()->where(['user_id' => $userId, 'name' => $projectName])->first();
            if ($proj) {
                $projectId = $proj->id;
            } else {
                $default = $this->Projects->find()->where(['user_id' => $userId, 'is_default' => 1])->first();
                $projectId = $default ? $default->id : 1;
            }
        }

        $update = $this->DailyUpdates->find()
            ->where(['user_id' => $userId, 'update_date' => $isoDate, 'project_id' => $projectId])
            ->first();

        if (!$update) {
            $update = $this->DailyUpdates->newEmptyEntity();
            $update->user_id = $userId;
            $update->update_date = new \Cake\I18n\Date($isoDate);
            $update->project_id = $projectId;
        }

        $update->client_name = $this->request->getData('client_name') ?? 'Hitesh sir';
        $update->project_name = $projectName;
        $update->tl_name = $this->request->getData('tl_name') ?? '';
        $update->done_tasks = $this->request->getData('done_tasks') ?? '';
        $update->progress_tasks = $this->request->getData('progress_tasks') ?? '';
        $update->remaining_tasks = $this->request->getData('remaining_tasks') ?? '';
        $update->queries = $this->request->getData('queries') ?? '';
        $update->notes = $this->request->getData('notes') ?? '';

        if ($this->DailyUpdates->save($update)) {
            $clientNameInput = $this->request->getData('client_name');
            if ($clientNameInput) {
                $cl = $this->Clients->find()->where(['user_id' => $userId, 'name' => trim($clientNameInput)])->first();
                if ($cl) {
                    $this->request->getSession()->write('Auth.User.last_client_id', $cl->id);
                }
            }

            $istTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Daily update saved successfully to database',
                    'saved_at' => $istTime->format('h:i:s A'),
                    'id' => $update->id,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save daily update',
                'errors' => $update->getErrors(),
            ]));
    }

    /**
     * AJAX Endpoint: Get Today's Work Log tasks for Logged-In User
     */
    public function getTodayTasks()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();
        $dateInput = $this->request->getQuery('date') ?? $this->request->getData('date') ?? date('Y-m-d');
        $isoDate = $this->parseDateToIso($dateInput);
        $projectId = $this->request->getQuery('project_id') ?? $this->request->getData('project_id');

        $conditions = ['WorkLogs.user_id' => $userId, 'WorkLogs.log_date' => $isoDate];
        if ($projectId) {
            $conditions['WorkLogs.project_id'] = $projectId;
        }

        $workLog = $this->WorkLogs->find()
            ->where($conditions)
            ->contain(['Projects', 'Clients'])
            ->orderBy(['WorkLogs.modified' => 'DESC'])
            ->first();

        $tasks = '';
        $projName = '';
        $clientName = '';
        if ($workLog) {
            $tasks = $this->extractTaskLines($workLog->content);
            if ($workLog->project) {
                $projName = $workLog->project->name;
            }
            if ($workLog->client) {
                $clientName = $workLog->client->name;
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'found' => ($workLog !== null),
                'tasks' => $tasks,
                'project_name' => $projName,
                'client_name' => $clientName,
            ]));
    }
}
