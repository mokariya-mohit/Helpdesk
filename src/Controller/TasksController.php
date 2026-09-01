<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\I18n\DateTime;

class TasksController extends AppController
{
    protected ?\App\Model\Table\WorkLogsTable $WorkLogs = null;
    protected ?\App\Model\Table\ProjectsTable $Projects = null;
    protected ?\App\Model\Table\ClientsTable $Clients = null;
    protected ?\App\Model\Table\UsersTable $Users = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->WorkLogs = $this->fetchTable('WorkLogs');
        $this->Projects = $this->fetchTable('Projects');
        $this->Clients = $this->fetchTable('Clients');
        $this->Users = $this->fetchTable('Users');
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
     * Main Daily Work Notepad UI
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();

        $projects = $this->Projects->find('all')
            ->where(['user_id' => $userId])
            ->orderBy(['is_default' => 'DESC', 'name' => 'ASC'])
            ->all();

        $defaultProject = $this->Projects->find()
            ->where(['user_id' => $userId, 'is_default' => 1])
            ->first() ?? $projects->first();

        $today = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $todayFormatted = $today->format('d-m-Y');
        $todayIso = $today->format('Y-m-d');

        // Fetch today's log for this user if exists
        $todayLog = $this->WorkLogs->find()
            ->where([
                'WorkLogs.user_id' => $userId,
                'WorkLogs.log_date' => $todayIso,
            ])
            ->contain(['Projects', 'Clients'])
            ->first();

        if ($todayLog && $todayLog->project) {
            $defaultProject = $todayLog->project;
        }

        $clients = $this->Clients->find('all')
            ->where(['user_id' => $userId])
            ->orderBy(['is_default' => 'DESC', 'name' => 'ASC'])
            ->all();

        // 1. Priority from URL query param if present
        $queryClientName = $this->request->getQuery('client_name') ?? $this->request->getQuery('client');
        $defaultClient = null;
        if ($queryClientName) {
            $defaultClient = $this->Clients->find()->where(['user_id' => $userId, 'name' => trim($queryClientName)])->first();
        }

        // 2. Priority from today's saved work log client
        if (!$defaultClient && $todayLog && $todayLog->client) {
            $defaultClient = $todayLog->client;
        }

        // 3. Priority from session last selected client
        $lastClientId = $this->request->getSession()->read('Auth.User.last_client_id');
        if (!$defaultClient && $lastClientId) {
            $defaultClient = $this->Clients->find()->where(['user_id' => $userId, 'id' => $lastClientId])->first();
        }

        // 4. Fallback: is_default client or first client
        if (!$defaultClient) {
            $defaultClient = $this->Clients->find()
                ->where(['user_id' => $userId, 'is_default' => 1])
                ->first() ?? $clients->first();
        }

        $userEntity = $this->Users->find()->where(['id' => $userId])->first();
        $userApiKey = ($userEntity && !empty($userEntity->api_key)) ? \App\Model\Entity\User::decryptString($userEntity->api_key) : '';

        $this->set(compact('projects', 'defaultProject', 'clients', 'defaultClient', 'todayFormatted', 'todayIso', 'todayLog', 'userApiKey'));
    }

    /**
     * Helper to parse task count from content string
     */
    private function calculateTaskCount(?string $text): int
    {
        if (empty($text) || trim($text) === '') {
            return 0;
        }

        $lines = explode("\n", $text);
        $count = 0;
        $hasBullet = false;

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if (preg_match('/^[-*\x{2022}]/u', $line)) {
                $after = preg_replace('/^[-*\x{2022}]+\s*/u', '', $line);
                if (mb_strlen(trim($after)) > 0) {
                    $count++;
                    $hasBullet = true;
                }
            }
        }

        if ($hasBullet) {
            return $count;
        }

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^\d{2}-\d{2}-\d{4}/', $line) || preg_match('/^[-=]{3,}$/', $line) || str_ends_with($line, ':')) {
                continue;
            }
            $count++;
        }

        return $count;
    }

    /**
     * Convert DD-MM-YYYY to YYYY-MM-DD
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
     * AJAX Endpoint: Get Note by Date and Project for Logged-In User
     */
    public function getLog()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();
        $dateInput = $this->request->getQuery('date') ?? $this->request->getData('date');
        $projectId = $this->request->getQuery('project_id') ?? $this->request->getData('project_id');
        $projectName = $this->request->getQuery('project_name') ?? $this->request->getData('project_name');

        if (!$dateInput) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Date is required']));
        }

        $isoDate = $this->parseDateToIso($dateInput);

        // Find or match project by name if no valid ID
        if (!$projectId && $projectName) {
            $proj = $this->Projects->find()->where(['user_id' => $userId, 'name' => $projectName])->first();
            if ($proj) {
                $projectId = $proj->id;
            }
        }

        if (!$projectId) {
            $default = $this->Projects->find()->where(['user_id' => $userId, 'is_default' => 1])->first();
            $projectId = $default ? $default->id : 1;
        }

        $log = $this->WorkLogs->find()
            ->where([
                'WorkLogs.user_id' => $userId,
                'WorkLogs.log_date' => $isoDate,
            ])
            ->contain(['Projects', 'Clients'])
            ->first();

        if ($log) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'found' => true,
                    'data' => [
                        'id' => $log->id,
                        'content' => $log->content,
                        'task_count' => $log->task_count,
                        'log_date' => $log->log_date ? $log->log_date->format('d-m-Y') : $dateInput,
                        'iso_date' => $isoDate,
                        'project_id' => $log->project_id,
                        'project_name' => $log->project ? $log->project->name : '',
                        'client_id' => $log->client_id,
                        'client_name' => $log->client ? $log->client->name : '',
                        'modified' => $log->modified ? $log->modified->format('d-m-Y H:i') : null,
                    ],
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'found' => false,
                'data' => [
                    'content' => '',
                    'task_count' => 0,
                    'log_date' => $dateInput,
                    'iso_date' => $isoDate,
                    'project_id' => $projectId,
                    'client_id' => null,
                    'client_name' => '',
                ],
            ]));
    }

    /**
     * AJAX Endpoint: Save Note to Database for Logged-In User
     */
    public function saveLog()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();

        $dateInput = $this->request->getData('date');
        $projectId = $this->request->getData('project_id');
        $projectName = $this->request->getData('project_name');
        $clientId = $this->request->getData('client_id');
        $clientName = $this->request->getData('client_name');
        $content = $this->request->getData('content') ?? '';

        if (!$dateInput) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Date is required']));
        }

        $isoDate = $this->parseDateToIso($dateInput);

        // Resolve Project
        if (!$projectId && $projectName) {
            $proj = $this->Projects->find()->where(['user_id' => $userId, 'name' => $projectName])->first();
            if (!$proj) {
                $proj = $this->Projects->newEntity(['user_id' => $userId, 'name' => $projectName, 'is_default' => 0]);
                $this->Projects->save($proj);
            }
            $projectId = $proj->id;
        }

        // Resolve Client
        if (!$clientId && $clientName) {
            $cl = $this->Clients->find()->where(['user_id' => $userId, 'name' => $clientName])->first();
            if (!$cl) {
                $cl = $this->Clients->newEntity(['user_id' => $userId, 'name' => $clientName, 'is_default' => 0]);
                $this->Clients->save($cl);
            }
            $clientId = $cl->id;
        }

        // Find existing for this date or create new
        $log = $this->WorkLogs->find()
            ->where([
                'user_id' => $userId,
                'log_date' => $isoDate,
            ])
            ->first();

        if (!$log) {
            $log = $this->WorkLogs->newEmptyEntity();
            $log->user_id = $userId;
            $log->log_date = new \Cake\I18n\Date($isoDate);
        }

        if (!empty($projectId)) {
            $log->project_id = (int)$projectId;
        }
        $log->content = $content;
        $log->task_count = $this->calculateTaskCount($content);
        if (!empty($clientId)) {
            $log->client_id = (int)$clientId;
            $this->request->getSession()->write('Auth.User.last_client_id', (int)$clientId);
        }

        if ($this->WorkLogs->save($log)) {
            $istTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Note saved successfully to database',
                    'saved_at' => $istTime->format('h:i:s A'),
                    'task_count' => $log->task_count,
                    'log_id' => $log->id,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save note',
                'errors' => $log->getErrors(),
            ]));
    }

    /**
     * AJAX Endpoint: Clear / Delete Note for Logged-In User
     */
    public function clearLog()
    {
        $this->request->allowMethod(['post', 'delete']);
        $userId = $this->getCurrentUserId();

        $dateInput = $this->request->getData('date');

        if (!$dateInput) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Date is required']));
        }

        $isoDate = $this->parseDateToIso($dateInput);

        $conditions = ['user_id' => $userId, 'log_date' => $isoDate];

        $log = $this->WorkLogs->find()->where($conditions)->first();
        if ($log) {
            $this->WorkLogs->delete($log);
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'Note cleared from database',
            ]));
    }

    /**
     * AJAX Endpoint: Get list of notes for a specific month/year for Logged-In User
     */
    public function getMonthLogs()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();

        $year = (int)($this->request->getQuery('year') ?? $this->request->getData('year') ?? date('Y'));
        $month = (int)($this->request->getQuery('month') ?? $this->request->getData('month') ?? date('m'));

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $conditions = [
            'WorkLogs.user_id' => $userId,
            'WorkLogs.log_date >=' => $startDate,
            'WorkLogs.log_date <=' => $endDate,
        ];

        $logs = $this->WorkLogs->find()
            ->where($conditions)
            ->contain(['Projects'])
            ->orderBy(['WorkLogs.log_date' => 'DESC'])
            ->all();

        $result = [];
        foreach ($logs as $l) {
            $dateFormatted = $l->log_date ? $l->log_date->format('d-m-Y') : '';
            $isoDate = $l->log_date ? $l->log_date->format('Y-m-d') : '';
            $dayName = $l->log_date ? $l->log_date->format('D') : '';

            $result[] = [
                'id' => $l->id,
                'date_formatted' => $dateFormatted,
                'iso_date' => $isoDate,
                'day_name' => $dayName,
                'project_id' => $l->project_id,
                'project_name' => $l->project ? $l->project->name : '8.bloqs',
                'task_count' => $l->task_count,
                'preview' => mb_substr(trim($l->content ?? ''), 0, 80),
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'logs' => $result,
                'count' => count($result),
            ]));
    }

    /**
     * AJAX Endpoint: Global Search in User's Work Logs
     */
    public function globalSearch()
    {
        $this->request->allowMethod(['get', 'post']);
        $userId = $this->getCurrentUserId();

        $query = trim($this->request->getQuery('q') ?? $this->request->getData('q') ?? '');

        if ($query === '') {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true, 'results' => []]));
        }

        $logs = $this->WorkLogs->find()
            ->where([
                'WorkLogs.user_id' => $userId,
                'WorkLogs.content LIKE' => '%' . $query . '%',
            ])
            ->contain(['Projects'])
            ->orderBy(['WorkLogs.log_date' => 'DESC'])
            ->limit(30)
            ->all();

        $results = [];
        foreach ($logs as $l) {
            $results[] = [
                'id' => $l->id,
                'date_formatted' => $l->log_date ? $l->log_date->format('d-m-Y') : '',
                'iso_date' => $l->log_date ? $l->log_date->format('Y-m-d') : '',
                'project_id' => $l->project_id,
                'project_name' => $l->project ? $l->project->name : '8.bloqs',
                'content' => $l->content,
                'task_count' => $l->task_count,
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'query' => $query,
                'results' => $results,
            ]));
    }

    /**
     * AJAX: Save Gemini API Key to User Profile in MySQL Database
     */
    public function saveGeminiKey()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $apiKey = trim(trim((string)$this->request->getData('api_key'), "\"'` \t\n\r"));

        if (empty($apiKey)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter a valid API key.']));
        }

        // Fast validation with Google API before saving
        $testUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($apiKey);
        $chTest = curl_init($testUrl);
        curl_setopt($chTest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chTest, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chTest, CURLOPT_TIMEOUT, 8);
        $testResp = curl_exec($chTest);
        curl_close($chTest);

        if ($testResp) {
            $testData = json_decode($testResp, true);
            if (!empty($testData['error']['message'])) {
                $err = $testData['error']['message'];
                if (str_contains($err, 'API key not valid') || str_contains($err, 'API_KEY_INVALID') || str_contains($err, 'PERMISSION_DENIED')) {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => false,
                            'message' => 'Invalid Google Gemini API Key. Please verify and enter a valid API key from Google AI Studio.',
                        ]));
                }
            }
        }

        $user = $this->Users->get($userId);
        $user->api_key = \App\Model\Entity\User::encryptString($apiKey);

        if ($this->Users->save($user)) {
            $sessionUser = $this->request->getSession()->read('Auth.User');
            if ($sessionUser) {
                $sessionUser['api_key'] = $user->api_key;
                $this->request->getSession()->write('Auth.User', $sessionUser);
            }
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Gemini API Key validated and saved to database!',
                    'api_key' => $apiKey,
                ]));
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => 'Failed to save API Key to database.']));
    }

    /**
     * AJAX: Gemini AI Grammar & Sentence Polish (Backend Proxy)
     */
    public function aiPolish()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $content = trim($this->request->getData('content') ?? '');
        $apiKey = trim(trim((string)$this->request->getData('api_key'), "\"'` \t\n\r"));

        if (empty($content)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter note content to polish.']));
        }

        // If not provided in POST, load from logged in user's database record and decrypt
        if (empty($apiKey)) {
            $userEntity = $this->Users->find()->where(['id' => $userId])->first();
            $apiKey = ($userEntity && !empty($userEntity->api_key)) ? \App\Model\Entity\User::decryptString($userEntity->api_key) : '';
        }

        if (empty($apiKey)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'is_key_error' => true,
                    'message' => 'Gemini API Key not found. Please enter and save your API key in database.',
                ]));
        }

        $promptText = "You are an assistant that polishes daily software engineering work task notes.\n\n" .
            "Key Instructions:\n" .
            "1. PRESERVE ORIGINAL MEANING: Do NOT over-rewrite, exaggerate, or change the original meaning of any task. Keep all tasks accurate to what the user intended.\n" .
            "2. SIMPLE & EASY ENGLISH: Use simple, clean, natural, and easy-to-understand everyday English words. Do NOT use overly complex, fancy, or artificial vocabulary.\n" .
            "3. FOR TASKS ALREADY IN ENGLISH: If a task is already written in English, ONLY fix grammar mistakes, spelling typos, punctuation, and slight awkward phrasing. Keep the sentence structure close and true to the original.\n" .
            "4. TRANSLATE GUJARATI / GUJLISH: If a task or phrase is written in Gujarati (ગુજરાતી) or Romanized Gujarati / Gujlish / Hinglish (e.g. 'aa feature banavyu', 'bug solve karyo', 'api call ma issue hato te fix karyo', 'design complete kari'), translate it into simple, direct, natural English (e.g. 'Created the feature', 'Fixed the bug', 'Resolved the issue in API call', 'Completed the design').\n" .
            "5. PRESERVE FORMATTING & HEADERS: Keep date headers ('DD-MM-YYYY ProjectName', '-------------------'), category headers ('Backend:', 'Frontend:', 'Design:', etc.), ticket IDs, URLs, and bullet points exactly intact.\n" .
            "6. STRICT OUTPUT: Output ONLY the final polished task notes text. Do NOT add any conversational comments, explanations, thought steps, or markdown code fences.\n\n" .
            "Work Notes to Polish:\n" . $content;

        // 1. Try known popular models first
        $candidateModels = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-2.0-flash',
            'gemini-2.0-flash-exp',
            'gemini-1.5-pro-latest',
            'gemini-pro',
        ];

        // 2. Discover available models directly from user's account if needed
        $listUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($apiKey);
        $chList = curl_init($listUrl);
        curl_setopt($chList, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chList, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chList, CURLOPT_TIMEOUT, 10);
        $listResp = curl_exec($chList);
        curl_close($chList);

        if ($listResp) {
            $listData = json_decode($listResp, true);
            if (!empty($listData['models'])) {
                $discovered = [];
                foreach ($listData['models'] as $m) {
                    if (!empty($m['supportedGenerationMethods']) && in_array('generateContent', $m['supportedGenerationMethods'])) {
                        $mName = preg_replace('#^models/#', '', $m['name']);
                        $discovered[] = $mName;
                    }
                }
                if (!empty($discovered)) {
                    $candidateModels = array_values(array_unique(array_merge($discovered, $candidateModels)));
                }
            } elseif (!empty($listData['error']['message'])) {
                $errMsg = $listData['error']['message'];
                if (str_contains($errMsg, 'API key not valid') || str_contains($errMsg, 'API_KEY_INVALID') || str_contains($errMsg, 'PERMISSION_DENIED')) {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => false,
                            'is_key_error' => true,
                            'message' => 'Invalid Gemini API Key. Please check and enter a valid API key from Google AI Studio.',
                        ]));
                }
            }
        }

        $lastError = 'Unknown error communicating with Gemini API';

        foreach ($candidateModels as $model) {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode($apiKey);

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => 'You are a clear daily work log editor. Use simple, natural English words. Correct grammar and spelling, translate Gujarati/Gujlish if present, and never over-rewrite or alter the original meaning. Output only the final notes text directly.']
                    ]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $promptText]]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($response && $httpCode === 200) {
                $data = json_decode($response, true);
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $rawText = trim($data['candidates'][0]['content']['parts'][0]['text']);
                    $polished = $this->cleanAiOutput($rawText);

                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => true,
                            'polished' => $polished,
                            'model_used' => $model,
                        ]));
                }
            } elseif ($response) {
                $errData = json_decode($response, true);
                if (!empty($errData['error']['message'])) {
                    $lastError = $errData['error']['message'];
                    if (str_contains($lastError, 'API key not valid') || str_contains($lastError, 'API_KEY_INVALID') || str_contains($lastError, 'PERMISSION_DENIED')) {
                        return $this->response->withType('application/json')
                            ->withStringBody(json_encode([
                                'success' => false,
                                'is_key_error' => true,
                                'message' => 'Invalid Gemini API Key. Please check and enter a valid API key from Google AI Studio.',
                            ]));
                    }
                }
            } elseif ($curlErr) {
                $lastError = 'Connection error: ' . $curlErr;
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => $lastError,
            ]));
    }

    /**
     * Clean and strip any conversational thoughts, rules, or metadata from AI response
     */
    private function cleanAiOutput(string $text): string
    {
        // 1. Remove markdown code block fences
        $text = preg_replace('/^```[a-z]*\s*\n/i', '', $text);
        $text = preg_replace('/\n\s*```$/i', '', $text);

        // 2. Filter out internal thought/meta lines
        $lines = explode("\n", $text);
        $cleanLines = [];
        $thoughtPattern = '/^\s*(\*|-)?\s*(Input:|Task:|Goal:|Constraints:|Rules:|Date:|Header:|Category:|Task\s+\d+:|Role:|Self-Correction:|The input|I will|No markdown|No explanations|Only the text|Wait|Since|Actually|Note:|Note\s*\()/i';

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (preg_match($thoughtPattern, $trimmed)) {
                continue;
            }
            if (preg_match('/^\s*\d+\.\s*(Translate|Fix grammar|Use professional|Preserve layout|Strict output)/i', $trimmed)) {
                continue;
            }
            if (preg_match('/^\s*\((No markdown|No chat|No explanations|Already professional)/i', $trimmed)) {
                continue;
            }

            // Remove leading indentation so Backend: / Frontend: / tasks align with column 0
            $line = preg_replace('/^[ \t]+/', '', $line);

            // Keep ONLY the date on the date header line (strip project name from header)
            if (preg_match('/^\d{2}-\d{2}-\d{4}/', $line)) {
                $line = preg_replace('/^(\d{2}-\d{2}-\d{4})\b.*$/', '$1', $line);
            }

            // Remove any "Create Project" placeholder anywhere in task note
            $line = preg_replace('/\bCreate\s+Project\b/i', '', $line);

            $cleanLines[] = $line;
        }
        $cleaned = trim(implode("\n", $cleanLines));

        // 3. If date header is duplicated, take the last block
        if (preg_match_all('/(\d{2}-\d{2}-\d{4})/', $cleaned, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($matches[0]);
            $lastPos = $lastMatch[1];
            $cleaned = trim(substr($cleaned, $lastPos));
        }

        return $cleaned;
    }
}
