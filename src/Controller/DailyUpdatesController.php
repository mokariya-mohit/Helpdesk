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
        return $auth ? (int)$auth['id'] : 0;
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
     * Clean raw work note text to extract only task bullet points (preserving all user indentation)
     */
    private function extractTaskLines(?string $text): string
    {
        if (empty($text) || trim($text) === '') {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        $startIndex = 0;

        if (count($lines) > 0 && preg_match('/^\d{2}-\d{2}-\d{4}/', trim($lines[0]))) {
            $startIndex = 1;
            if (count($lines) > 1 && preg_match('/^[-=]{3,}/', trim($lines[1]))) {
                $startIndex = 2;
            }
        }

        $extracted = array_slice($lines, $startIndex);
        while (!empty($extracted) && trim($extracted[0]) === '') {
            array_shift($extracted);
        }
        while (!empty($extracted) && trim(end($extracted)) === '') {
            array_pop($extracted);
        }

        return implode("\n", $extracted);
    }

    /**
     * Format work log content from done tasks preserving headers and exact sub-point indentation
     */
    private function formatWorkLogContentFromDoneTasks(?string $currentContent, string $doneTasksRaw, string $dateFormatted, string $projectName): string
    {
        $currentContent = (string)$currentContent;
        $currentContent = str_replace(["\r\n", "\r"], "\n", $currentContent);
        $doneTasksRaw = str_replace(["\r\n", "\r"], "\n", $doneTasksRaw);

        $lines = explode("\n", $currentContent);
        $headerLines = [];

        if (count($lines) > 0 && preg_match('/^\d{2}-\d{2}-\d{4}/', trim($lines[0]))) {
            $headerLines[] = trim($lines[0]);
            if (count($lines) > 1 && preg_match('/^[-=]{3,}/', trim($lines[1]))) {
                $headerLines[] = trim($lines[1]);
            } else {
                $headerLines[] = '-------------------';
            }
        } else {
            $headerLines[] = $dateFormatted . '  ' . ($projectName ?: 'General');
            $headerLines[] = '-------------------';
        }

        $taskLines = explode("\n", $doneTasksRaw);
        while (!empty($taskLines) && trim(end($taskLines)) === '') {
            array_pop($taskLines);
        }
        $cleanedTasks = implode("\n", $taskLines);

        $result = implode("\n", $headerLines);
        if ($cleanedTasks !== '') {
            $result .= "\n" . $cleanedTasks;
        }

        return $result;
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

        $queryDate = $this->request->getQuery('date');
        if ($queryDate) {
            $currentIso = $this->parseDateToIso($queryDate);
            $currentDateObj = new DateTime($currentIso, new \DateTimeZone('Asia/Kolkata'));
            $todayIso = $currentDateObj->format('Y-m-d');
            $todayFormatted = $currentDateObj->format('d-m-Y');
        } else {
            $today = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $todayIso = $today->format('Y-m-d');
            $todayFormatted = $today->format('d-m-Y');
        }

        $detectedProjectName = $queryProjectName ?: ($defaultProject ? $defaultProject->name : '');
        $detectedClientName = $queryClientName ?: '';

        // Check if there is an existing saved Daily Update for this date for this user
        $savedUpdate = $this->DailyUpdates->find()
            ->where(['user_id' => $userId, 'update_date' => $todayIso])
            ->first();

        // Auto-fetch completed tasks from Work Log for this user & date
        $autoDoneTasks = '';
        $todayWorkLog = $this->WorkLogs->find()
            ->where(['WorkLogs.user_id' => $userId, 'WorkLogs.log_date' => $todayIso])
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

        // Determine initial done tasks: Prioritize Work Log if it has tasks, otherwise use saved update
        if (!empty($autoDoneTasks)) {
            $initialDoneTasks = $autoDoneTasks;
        } elseif ($savedUpdate && !empty($savedUpdate->done_tasks)) {
            $initialDoneTasks = $savedUpdate->done_tasks;
        } else {
            $initialDoneTasks = '';
        }

        $currentUserEntity = $this->fetchTable('Users')->find()->where(['id' => $userId])->first();
        $hasSmtpConfigured = $currentUserEntity && !empty($currentUserEntity->email) && !empty($currentUserEntity->smtp_password);

        $this->set(compact('clients', 'projects', 'defaultProject', 'todayIso', 'todayFormatted', 'savedUpdate', 'autoDoneTasks', 'initialDoneTasks', 'detectedProjectName', 'detectedClientName', 'hasSmtpConfigured'));
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

        if (!$projectId && $projectName) {
            $proj = $this->Projects->find()->where(['user_id' => $userId, 'name' => $projectName])->first();
            if ($proj) {
                $projectId = $proj->id;
            }
        }

        $update = $this->DailyUpdates->find()
            ->where(['user_id' => $userId, 'update_date' => $isoDate])
            ->first();

        if (!$update) {
            $update = $this->DailyUpdates->newEmptyEntity();
            $update->user_id = $userId;
            $update->update_date = new \Cake\I18n\Date($isoDate);
        }

        if (!empty($projectId)) {
            $update->project_id = (int)$projectId;
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

            // Two-Way Synchronization: Automatically sync done tasks back into Today's Work Log (WorkLogs)
            $newDoneTasks = trim($update->done_tasks ?? '');
            if (!empty($newDoneTasks)) {
                $workLog = $this->WorkLogs->find()
                    ->where(['WorkLogs.user_id' => $userId, 'WorkLogs.log_date' => $isoDate])
                    ->first();

                $currentContent = $workLog ? $workLog->content : '';
                $formattedDate = (new \DateTime($isoDate))->format('d-m-Y');
                $updatedContent = $this->formatWorkLogContentFromDoneTasks($currentContent, $newDoneTasks, $formattedDate, $projectName);

                if (!$workLog) {
                    $workLog = $this->WorkLogs->newEmptyEntity();
                    $workLog->user_id = $userId;
                    $workLog->log_date = new \Cake\I18n\Date($isoDate);
                }

                $workLog->content = $updatedContent;
                if (!empty($projectId)) {
                    $workLog->project_id = (int)$projectId;
                }
                $this->WorkLogs->save($workLog);
            }

            $istTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Daily update and Work Log synced successfully',
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
                'found' => ($workLog !== null && trim($tasks) !== ''),
                'tasks' => $tasks,
                'project_name' => $projName,
                'client_name' => $clientName,
            ]));
    }

    /**
     * AJAX Endpoint: Send Daily Update Email (Delegated to EmailService)
     */
    public function sendEmail()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $userEntity = $this->fetchTable('Users')->find()->where(['id' => $userId])->first();
        if (!$userEntity || empty($userEntity->email) || empty($userEntity->smtp_password)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Please configure your Email and Email App / SMTP Password in your Profile first to send emails.'
                ]));
        }

        $authUser = $this->getAuthUser();

        $fromEmail = trim((string)$userEntity->email);
        $fromName = trim((string)$userEntity->name) ?: trim((string)($authUser['name'] ?? ''));

        $to = (string)$this->request->getData('to');
        $cc = (string)$this->request->getData('cc');
        $bcc = (string)$this->request->getData('bcc');
        $subject = (string)$this->request->getData('subject');
        if (trim($to) === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter at least one recipient email address in the "To" field.']));
        }

        if (trim($subject) === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Email subject cannot be empty.']));
        }

        $bodyHtml = (string)$this->request->getData('body_html');
        // Strip any buttons or interactive preview controls
        $bodyHtml = preg_replace('/<button\b[^>]*>(.*?)<\/button>/is', '', $bodyHtml);
        $bodyHtml = preg_replace('/<a\b[^>]*class=["\'][^"\']*\bbtn-copy\b[^"\']*["\'][^>]*>(.*?)<\/a>/is', '', $bodyHtml);

        $smtpPass = '';
        if ($userEntity && !empty($userEntity->smtp_password)) {
            $smtpPass = \App\Model\Entity\User::decryptString($userEntity->smtp_password);
        }

        $emailService = new \App\Service\EmailService();
        $result = $emailService->sendDailyUpdateEmail(
            to: $to,
            subject: $subject,
            contentHtml: $bodyHtml,
            cc: $cc,
            bcc: $bcc,
            sender: [
                'email' => $fromEmail,
                'name' => $fromName,
                'smtp_password' => $smtpPass,
            ]
        );

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}
