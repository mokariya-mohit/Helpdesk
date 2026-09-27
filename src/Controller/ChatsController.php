<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\I18n\DateTime;

class ChatsController extends AppController
{
    protected ?\App\Model\Table\UsersTable $Users = null;
    protected ?\App\Model\Table\ChatRequestsTable $ChatRequests = null;
    protected ?\App\Model\Table\ChatConversationsTable $ChatConversations = null;
    protected ?\App\Model\Table\ChatConversationUsersTable $ChatConversationUsers = null;
    protected ?\App\Model\Table\ChatMessagesTable $ChatMessages = null;
    protected ?\App\Model\Table\ChatMessageAttachmentsTable $ChatMessageAttachments = null;
    protected ?\App\Model\Table\ChatMessageReactionsTable $ChatMessageReactions = null;

    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
        $this->ChatRequests = $this->fetchTable('ChatRequests');
        $this->ChatConversations = $this->fetchTable('ChatConversations');
        $this->ChatConversationUsers = $this->fetchTable('ChatConversationUsers');
        $this->ChatMessages = $this->fetchTable('ChatMessages');
        $this->ChatMessageAttachments = $this->fetchTable('ChatMessageAttachments');
        $this->ChatMessageReactions = $this->fetchTable('ChatMessageReactions');
    }

    /**
     * Helper to get current authenticated user ID safely
     */
    protected function getCurrentUserId(): int
    {
        $auth = $this->getAuthUser();
        return $auth ? (int)$auth['id'] : 0;
    }

    /**
     * Helper to format user avatar URL with fallback
     */
    protected function getUserAvatarUrl(?string $picture, ?string $gender): string
    {
        $webroot = $this->request->getAttribute('webroot') ?? '/';
        return \App\Controller\UsersController::getAvatarUrl($picture, $gender, $webroot);
    }

    /**
     * Helper to generate a crisp SVG avatar data-URL for groups
     */
    protected function getGroupAvatarUrl(?string $title): string
    {
        $words = preg_split('/\s+/', trim($title ?: 'Group'));
        $initials = '';
        if (count($words) >= 2) {
            $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
        } else {
            $initials = mb_substr($words[0], 0, 2);
        }
        $initials = strtoupper($initials);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">'
            . '<defs>'
            . '<linearGradient id="grpGrad" x1="0%" y1="0%" x2="100%" y2="100%">'
            . '<stop offset="0%" stop-color="#4f46e5"/>'
            . '<stop offset="50%" stop-color="#7c3aed"/>'
            . '<stop offset="100%" stop-color="#06b6d4"/>'
            . '</linearGradient>'
            . '</defs>'
            . '<rect width="64" height="64" rx="32" fill="url(#grpGrad)"/>'
            . '<text x="50%" y="54%" font-family="-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif" font-size="22" font-weight="700" fill="#ffffff" dominant-baseline="middle" text-anchor="middle">'
            . htmlspecialchars($initials)
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    /**
     * Helper to touch user's last_seen_at timestamp
     */
    protected function touchUserLastSeen(int $userId): void
    {
        if ($userId <= 0) return;
        try {
            $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $this->Users->updateAll(
                ['last_seen_at' => $now],
                ['id' => $userId]
            );
        } catch (\Throwable $e) {
            // Silently continue if last_seen_at column not available
        }
    }

    /**
     * Verify if the current user is a participant of the given conversation
     */
    protected function isUserInConversation(int $conversationId, int $userId): bool
    {
        if ($conversationId <= 0 || $userId <= 0) return false;

        $count = $this->ChatConversationUsers->find()
            ->where([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
            ])
            ->count();

        return $count > 0;
    }

    /**
     * Main Team Chat Interface
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();
        $this->touchUserLastSeen($userId);

        $currentUserEntity = $this->Users->find()->where(['id' => $userId])->first();

        // Get initial count of pending incoming requests
        $pendingRequestsCount = $this->ChatRequests->find()
            ->where([
                'receiver_id' => $userId,
                'status' => 'pending',
            ])
            ->count();

        $this->set(compact('currentUserEntity', 'pendingRequestsCount'));
    }

    /**
     * AJAX: Search Users to start new chat
     * Method: GET
     */
    public function searchUsers()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();
        $query = trim((string)$this->request->getQuery('q', ''));

        $usersQuery = $this->Users->find()
            ->where(['id !=' => $userId]);

        if ($query !== '') {
            $usersQuery->where([
                'OR' => [
                    'name LIKE' => '%' . $query . '%',
                    'email LIKE' => '%' . $query . '%',
                ],
            ]);
        }

        $users = $usersQuery
            ->orderBy(['name' => 'ASC'])
            ->limit(20)
            ->all();

        $results = [];
        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        foreach ($users as $u) {
            // Check if there is an existing active conversation
            $existingConvId = $this->findExistingConversationBetween($userId, $u->id);

            // Check if there is a pending request between them
            $pendingRequest = $this->ChatRequests->find()
                ->where([
                    'status' => 'pending',
                    'OR' => [
                        ['sender_id' => $userId, 'receiver_id' => $u->id],
                        ['sender_id' => $u->id, 'receiver_id' => $userId],
                    ],
                ])
                ->first();

            $status = 'none';
            $isIncoming = false;
            if ($existingConvId) {
                $status = 'active';
            } elseif ($pendingRequest) {
                $status = 'pending';
                $isIncoming = ($pendingRequest->receiver_id === $userId);
            }

            // Check if online (active within last 3 minutes)
            $isOnline = false;
            if ($u->last_seen_at) {
                $diffSec = $now->getTimestamp() - $u->last_seen_at->getTimestamp();
                $isOnline = ($diffSec <= 180);
            }

            $results[] = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar_url' => $this->getUserAvatarUrl($u->picture, $u->gender),
                'status' => $status,
                'is_incoming_request' => $isIncoming,
                'pending_request_id' => $pendingRequest ? $pendingRequest->id : null,
                'conversation_id' => $existingConvId,
                'is_online' => $isOnline,
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'users' => $results,
            ]));
    }

    /**
     * Helper to find existing active conversation between two users
     */
    protected function findExistingConversationBetween(int $userA, int $userB): ?int
    {
        $convsA = $this->ChatConversationUsers->find()
            ->select(['conversation_id'])
            ->where(['user_id' => $userA])
            ->all()
            ->extract('conversation_id')
            ->toArray();

        if (empty($convsA)) {
            return null;
        }

        $shared = $this->ChatConversationUsers->find()
            ->select(['conversation_id'])
            ->where([
                'conversation_id IN' => $convsA,
                'user_id' => $userB,
            ])
            ->first();

        return $shared ? (int)$shared->conversation_id : null;
    }

    /**
     * AJAX: Send a Chat Request to another user
     * Method: POST
     */
    public function sendRequest()
    {
        $this->request->allowMethod(['post']);
        $senderId = $this->getCurrentUserId();
        $receiverId = (int)$this->request->getData('receiver_id');
        $initialMessage = trim((string)$this->request->getData('initial_message', ''));

        if ($receiverId <= 0) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid user selected.']));
        }

        if ($senderId === $receiverId) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'You cannot message yourself.']));
        }

        $receiver = $this->Users->find()->where(['id' => $receiverId])->first();
        if (!$receiver) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Target user does not exist.']));
        }

        // 1. Check if conversation already exists
        $existingConvId = $this->findExistingConversationBetween($senderId, $receiverId);
        if ($existingConvId) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'already_exists' => true,
                    'conversation_id' => $existingConvId,
                    'message' => 'Active conversation already exists with ' . $receiver->name . '.',
                ]));
        }

        // 2. Check if sender already sent a pending request
        $existingPending = $this->ChatRequests->find()
            ->where([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'status' => 'pending',
            ])
            ->first();

        if ($existingPending) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'already_pending' => true,
                    'message' => 'Chat request already pending with ' . $receiver->name . '.',
                ]));
        }

        // 3. Check if receiver already sent a request to sender (Reverse Request): Auto-accept!
        $reversePending = $this->ChatRequests->find()
            ->where([
                'sender_id' => $receiverId,
                'receiver_id' => $senderId,
                'status' => 'pending',
            ])
            ->first();

        if ($reversePending) {
            $convId = $this->createConversationBetween($receiverId, $senderId, $reversePending->initial_message);
            $reversePending->status = 'accepted';
            $reversePending->accepted_at = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $this->ChatRequests->save($reversePending);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'auto_accepted' => true,
                    'conversation_id' => $convId,
                    'message' => $receiver->name . ' had also requested to chat. Conversation is now active!',
                ]));
        }

        // 4. Create new pending chat request
        $chatReq = $this->ChatRequests->newEntity([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'initial_message' => $initialMessage !== '' ? mb_substr($initialMessage, 0, 500) : null,
            'status' => 'pending',
        ]);

        if ($this->ChatRequests->save($chatReq)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'request_id' => $chatReq->id,
                    'message' => 'Chat request sent to ' . $receiver->name . ' successfully!',
                ]));
        }

        return $this->response->withStatus(500)->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => 'Unable to send request. Please try again.']));
    }

    /**
     * AJAX: Get Pending Incoming and Outgoing Requests
     * Method: GET
     */
    public function getRequests()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();

        $incoming = $this->ChatRequests->find()
            ->where(['receiver_id' => $userId, 'status' => 'pending'])
            ->contain(['Sender'])
            ->orderBy(['ChatRequests.created' => 'DESC'])
            ->all();

        $incomingList = [];
        foreach ($incoming as $r) {
            $s = $r->sender;
            $incomingList[] = [
                'id' => $r->id,
                'sender_id' => $r->sender_id,
                'name' => $s ? $s->name : 'Unknown User',
                'email' => $s ? $s->email : '',
                'avatar_url' => $this->getUserAvatarUrl($s ? $s->picture : null, $s ? $s->gender : null),
                'initial_message' => $r->initial_message ?? '',
                'created_formatted' => $r->created ? $r->created->i18nFormat('dd MMM, h:mm a') : '',
                'time_ago' => $r->created ? $r->created->timeAgoInWords() : '',
            ];
        }

        $outgoing = $this->ChatRequests->find()
            ->where(['sender_id' => $userId, 'status' => 'pending'])
            ->contain(['Receiver'])
            ->orderBy(['ChatRequests.created' => 'DESC'])
            ->all();

        $outgoingList = [];
        foreach ($outgoing as $r) {
            $rec = $r->receiver;
            $outgoingList[] = [
                'id' => $r->id,
                'receiver_id' => $r->receiver_id,
                'name' => $rec ? $rec->name : 'Unknown User',
                'email' => $rec ? $rec->email : '',
                'avatar_url' => $this->getUserAvatarUrl($rec ? $rec->picture : null, $rec ? $rec->gender : null),
                'initial_message' => $r->initial_message ?? '',
                'created_formatted' => $r->created ? $r->created->i18nFormat('dd MMM, h:mm a') : '',
                'time_ago' => $r->created ? $r->created->timeAgoInWords() : '',
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'incoming' => $incomingList,
                'outgoing' => $outgoingList,
                'pending_count' => count($incomingList),
            ]));
    }

    /**
     * AJAX: Accept a Chat Request
     * Method: POST
     */
    public function acceptRequest()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $requestId = (int)$this->request->getData('request_id');

        $req = $this->ChatRequests->find()
            ->where([
                'ChatRequests.id' => $requestId,
                'ChatRequests.receiver_id' => $userId,
                'ChatRequests.status' => 'pending',
            ])
            ->contain(['Sender'])
            ->first();

        if (!$req) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Pending chat request not found.']));
        }

        $conn = $this->ChatRequests->getConnection();
        $conn->begin();

        try {
            $senderId = $req->sender_id;
            $convId = $this->createConversationBetween($senderId, $userId, $req->initial_message);

            $req->status = 'accepted';
            $req->accepted_at = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $this->ChatRequests->saveOrFail($req);

            $conn->commit();

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'conversation_id' => $convId,
                    'message' => 'Chat request accepted! You can now message ' . ($req->sender ? $req->sender->name : 'each other') . '.',
                ]));
        } catch (\Throwable $e) {
            $conn->rollback();
            return $this->response->withStatus(500)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Failed to accept request: ' . $e->getMessage()]));
        }
    }

    /**
     * AJAX: Reject a Chat Request
     * Method: POST
     */
    public function rejectRequest()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $requestId = (int)$this->request->getData('request_id');

        $req = $this->ChatRequests->find()
            ->where([
                'ChatRequests.id' => $requestId,
                'ChatRequests.receiver_id' => $userId,
                'ChatRequests.status' => 'pending',
            ])
            ->first();

        if (!$req) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Pending chat request not found.']));
        }

        $req->status = 'rejected';
        $req->rejected_at = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        if ($this->ChatRequests->save($req)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Chat request rejected.',
                ]));
        }

        return $this->response->withStatus(500)->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => 'Unable to reject request.']));
    }

    /**
     * Helper to create a new conversation and associate participants
     */
    protected function createConversationBetween(int $creatorId, int $otherUserId, ?string $initialMessage = null): int
    {
        // Re-check if one already exists
        $existing = $this->findExistingConversationBetween($creatorId, $otherUserId);
        if ($existing) {
            return $existing;
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        $conversation = $this->ChatConversations->newEntity([
            'created_by' => $creatorId,
            'status' => 'active',
        ]);
        $this->ChatConversations->saveOrFail($conversation);
        $convId = $conversation->id;

        // Add both participants
        $p1 = $this->ChatConversationUsers->newEntity([
            'conversation_id' => $convId,
            'user_id' => $creatorId,
            'joined_at' => $now,
            'last_read_at' => $now,
        ]);
        $p2 = $this->ChatConversationUsers->newEntity([
            'conversation_id' => $convId,
            'user_id' => $otherUserId,
            'joined_at' => $now,
            'last_read_at' => null,
        ]);
        $this->ChatConversationUsers->saveOrFail($p1);
        $this->ChatConversationUsers->saveOrFail($p2);

        // Optional initial message
        if ($initialMessage && trim($initialMessage) !== '') {
            $msg = $this->ChatMessages->newEntity([
                'conversation_id' => $convId,
                'sender_id' => $creatorId,
                'message' => trim($initialMessage),
                'message_type' => 'text',
                'is_deleted' => 0,
            ]);
            $this->ChatMessages->saveOrFail($msg);
        }

        return $convId;
    }

    /**
     * AJAX: Lightweight Polling Endpoint
     * Fetches new messages for current active conversation, unread counts, and pending requests
     * Method: GET
     */
    public function getUpdates()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();
        $this->touchUserLastSeen($userId);

        $activeConvId = (int)$this->request->getQuery('conversation_id', 0);
        $lastMessageId = (int)$this->request->getQuery('last_message_id', 0);

        // 1. Pending incoming requests count
        $pendingRequestsCount = $this->ChatRequests->find()
            ->where(['receiver_id' => $userId, 'status' => 'pending'])
            ->count();

        // 2. User's active conversations summary
        $userConvRows = $this->ChatConversationUsers->find()
            ->where(['user_id' => $userId])
            ->all();

        $convMap = [];
        $totalUnreadCount = 0;
        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        foreach ($userConvRows as $row) {
            $cId = (int)$row->conversation_id;
            $lastReadAt = $row->last_read_at;
            $clearedAt = $row->cleared_at;

            $conv = $this->ChatConversations->find()
                ->where(['id' => $cId, 'status' => 'active'])
                ->first();
            if (!$conv) continue;

            $isGroup = ($conv->type === 'group');

            if ($isGroup) {
                $memberCount = $this->ChatConversationUsers->find()
                    ->where(['conversation_id' => $cId])
                    ->count();

                $unreadQuery = $this->ChatMessages->find()
                    ->where([
                        'ChatMessages.conversation_id' => $cId,
                        'ChatMessages.sender_id !=' => $userId,
                        'ChatMessages.is_deleted' => 0,
                    ]);
                if ($lastReadAt) {
                    $unreadQuery->where(['ChatMessages.created >' => $lastReadAt]);
                }
                if ($clearedAt) {
                    $unreadQuery->where(['ChatMessages.created >' => $clearedAt]);
                }
                $unreadCount = $unreadQuery->count();
                $totalUnreadCount += $unreadCount;

                $latestQuery = $this->ChatMessages->find()
                    ->where(['ChatMessages.conversation_id' => $cId])
                    ->contain(['Sender', 'ChatAttachments'])
                    ->orderBy(['ChatMessages.id' => 'DESC']);
                if ($clearedAt) {
                    $latestQuery->where(['ChatMessages.created >' => $clearedAt]);
                }
                $latestMsg = $latestQuery->first();

                $lastMsgText = '';
                if ($latestMsg) {
                    if ($latestMsg->is_deleted) {
                        $lastMsgText = 'This message was deleted';
                    } elseif ($latestMsg->message_type === 'system') {
                        $lastMsgText = $latestMsg->message;
                    } else {
                        $msgContent = $latestMsg->message;
                        if (empty($msgContent)) {
                            $attCount = !empty($latestMsg->chat_attachments) ? count($latestMsg->chat_attachments) : (!empty($latestMsg->attachment_name) ? 1 : 0);
                            $msgContent = $attCount > 1 ? "📎 {$attCount} Files" : "📎 Attachment";
                        }
                        if ($latestMsg->sender_id === $userId) {
                            $lastMsgText = 'You: ' . $msgContent;
                        } else {
                            $senderName = $latestMsg->sender ? explode(' ', $latestMsg->sender->name)[0] : 'Member';
                            $lastMsgText = $senderName . ': ' . $msgContent;
                        }
                    }
                }

                $convMap[] = [
                    'conversation_id' => $cId,
                    'is_group' => true,
                    'title' => $conv->title ?: 'Group Chat',
                    'description' => $conv->description ?? '',
                    'member_count' => $memberCount,
                    'user' => [
                        'id' => 0,
                        'name' => $conv->title ?: 'Group Chat',
                        'email' => $memberCount . ($memberCount === 1 ? ' member' : ' members'),
                        'avatar_url' => $this->getGroupAvatarUrl($conv->title),
                        'is_online' => false,
                        'last_seen_formatted' => $memberCount . ' members',
                        'is_group' => true,
                    ],
                    'last_message' => $latestMsg ? [
                        'id' => $latestMsg->id,
                        'sender_id' => $latestMsg->sender_id,
                        'is_self' => ($latestMsg->sender_id === $userId),
                        'text' => $lastMsgText,
                        'raw_text' => $latestMsg->message,
                        'is_deleted' => (bool)$latestMsg->is_deleted,
                        'time_formatted' => $latestMsg->created ? $latestMsg->created->i18nFormat('h:mm a') : '',
                        'timestamp' => $latestMsg->created ? $latestMsg->created->getTimestamp() : 0,
                    ] : null,
                    'unread_count' => $unreadCount,
                ];
            } else {
                // Direct 1-on-1 Conversation
                $otherParticipantRow = $this->ChatConversationUsers->find()
                    ->where(['conversation_id' => $cId, 'user_id !=' => $userId])
                    ->contain(['Users'])
                    ->first();

                $otherUser = $otherParticipantRow ? $otherParticipantRow->user : null;
                if (!$otherUser) continue;

                // Unread count: messages sent by other user after last_read_at AND after cleared_at
                $unreadQuery = $this->ChatMessages->find()
                    ->where([
                        'ChatMessages.conversation_id' => $cId,
                        'ChatMessages.sender_id !=' => $userId,
                        'ChatMessages.is_deleted' => 0,
                    ]);

                if ($lastReadAt) {
                    $unreadQuery->where(['ChatMessages.created >' => $lastReadAt]);
                }
                if ($clearedAt) {
                    $unreadQuery->where(['ChatMessages.created >' => $clearedAt]);
                }
                $unreadCount = $unreadQuery->count();
                $totalUnreadCount += $unreadCount;

                // Latest message in conversation after cleared_at
                $latestQuery = $this->ChatMessages->find()
                    ->where(['ChatMessages.conversation_id' => $cId])
                    ->contain(['Sender', 'ChatAttachments'])
                    ->orderBy(['ChatMessages.id' => 'DESC']);
                if ($clearedAt) {
                    $latestQuery->where(['ChatMessages.created >' => $clearedAt]);
                }
                $latestMsg = $latestQuery->first();

                $isOnline = false;
                if ($otherUser->last_seen_at) {
                    $diffSec = $now->getTimestamp() - $otherUser->last_seen_at->getTimestamp();
                    $isOnline = ($diffSec <= 180);
                }

                $convMap[] = [
                    'conversation_id' => $cId,
                    'is_group' => false,
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'avatar_url' => $this->getUserAvatarUrl($otherUser->picture, $otherUser->gender),
                        'is_online' => $isOnline,
                        'last_seen_formatted' => $otherUser->last_seen_at ? $otherUser->last_seen_at->timeAgoInWords() : 'offline',
                        'is_group' => false,
                    ],
                    'last_message' => $latestMsg ? [
                        'id' => $latestMsg->id,
                        'sender_id' => $latestMsg->sender_id,
                        'is_self' => ($latestMsg->sender_id === $userId),
                        'text' => $latestMsg->is_deleted ? 'This message was deleted' : ($latestMsg->message ?? '📎 Attachment'),
                        'raw_text' => $latestMsg->message,
                        'is_deleted' => (bool)$latestMsg->is_deleted,
                        'time_formatted' => $latestMsg->created ? $latestMsg->created->i18nFormat('h:mm a') : '',
                        'timestamp' => $latestMsg->created ? $latestMsg->created->getTimestamp() : 0,
                    ] : null,
                    'unread_count' => $unreadCount,
                ];
            }
        }

        // Sort conversations by latest message timestamp descending
        usort($convMap, function($a, $b) {
            $tA = ($a['last_message'] && isset($a['last_message']['timestamp'])) ? $a['last_message']['timestamp'] : 0;
            $tB = ($b['last_message'] && isset($b['last_message']['timestamp'])) ? $b['last_message']['timestamp'] : 0;
            return $tB <=> $tA;
        });

        // 3. New messages and live typing status for currently open conversation
        $newMessages = [];
        $otherLastReadTimestamp = 0;
        $isTyping = false;
        $typingUserName = '';

        if ($activeConvId > 0 && $this->isUserInConversation($activeConvId, $userId)) {
            // Live typing detection within the last 4 seconds
            $typingThreshold = (clone $now)->subSeconds(4);
            $typingRows = $this->ChatConversationUsers->find()
                ->where([
                    'ChatConversationUsers.conversation_id' => $activeConvId,
                    'ChatConversationUsers.user_id !=' => $userId,
                    'ChatConversationUsers.typing_at >=' => $typingThreshold,
                ])
                ->contain(['Users'])
                ->all();

            $typers = [];
            foreach ($typingRows as $tr) {
                if ($tr->user) {
                    $typers[] = $tr->user->name;
                }
            }
            if (!empty($typers)) {
                $isTyping = true;
                $typingUserName = implode(', ', $typers);
            }

            $currentUserConv = $this->ChatConversationUsers->find()
                ->where(['conversation_id' => $activeConvId, 'user_id' => $userId])
                ->first();
            $userClearedAt = $currentUserConv ? $currentUserConv->cleared_at : null;

            $otherParticipantRow = $this->ChatConversationUsers->find()
                ->where(['conversation_id' => $activeConvId, 'user_id !=' => $userId])
                ->first();
            $otherLastReadAt = $otherParticipantRow ? $otherParticipantRow->last_read_at : null;
            if ($otherLastReadAt) {
                $otherLastReadTimestamp = $otherLastReadAt->getTimestamp();
            }

            $msgQuery = $this->ChatMessages->find()
                ->where(['ChatMessages.conversation_id' => $activeConvId])
                ->contain(['Sender', 'ChatAttachments', 'ChatReactions.Users'])
                ->orderBy(['ChatMessages.id' => 'ASC']);

            if ($userClearedAt) {
                $msgQuery->where(['ChatMessages.created >' => $userClearedAt]);
            }

            if ($lastMessageId > 0) {
                $msgQuery->where(['ChatMessages.id >' => $lastMessageId]);
            }

            $fetched = $msgQuery->all();
            foreach ($fetched as $m) {
                $newMessages[] = $this->formatMessageData($m, $userId, $otherLastReadAt);
            }

            // Check if any existing messages in active conversation were modified recently (edited, deleted, or reactions added)
            $updatedMessages = [];
            if ($lastMessageId > 0) {
                $recentMod = (clone $now)->subSeconds(60);
                $modQuery = $this->ChatMessages->find()
                    ->where([
                        'ChatMessages.conversation_id' => $activeConvId,
                        'ChatMessages.id <=' => $lastMessageId,
                        'ChatMessages.modified >=' => $recentMod,
                    ])
                    ->contain(['Sender', 'ChatAttachments', 'ChatReactions.Users']);

                if ($userClearedAt) {
                    $modQuery->where(['ChatMessages.created >' => $userClearedAt]);
                }

                $modFetched = $modQuery->all();
                foreach ($modFetched as $m) {
                    $updatedMessages[] = $this->formatMessageData($m, $userId, $otherLastReadAt);
                }
            }

            // If user is actively in conversation, mark conversation as read
            if (!empty($newMessages) || $lastMessageId == 0) {
                $this->ChatConversationUsers->updateAll(
                    ['last_read_at' => $now],
                    ['conversation_id' => $activeConvId, 'user_id' => $userId]
                );
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'user_id' => $userId,
                'conversations' => $convMap,
                'new_messages' => $newMessages,
                'updated_messages' => $updatedMessages ?? [],
                'pending_requests_count' => $pendingRequestsCount,
                'total_unread_count' => $totalUnreadCount,
                'other_user_last_read_timestamp' => $otherLastReadTimestamp,
                'is_typing' => $isTyping,
                'typing_user_name' => $typingUserName,
            ]));
    }

    /**
     * AJAX: Get Message History for a Conversation with Pagination
     * Method: GET
     */
    public function getMessages()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getQuery('conversation_id');
        $beforeId = (int)$this->request->getQuery('before_id', 0);

        if (!$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $conv = $this->ChatConversations->find()->where(['id' => $convId])->first();
        if (!$conv) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Conversation not found.']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $isGroup = ($conv->type === 'group');

        // Current user membership
        $currentUserRow = $this->ChatConversationUsers->find()
            ->where(['conversation_id' => $convId, 'user_id' => $userId])
            ->first();
        $userClearedAt = $currentUserRow ? $currentUserRow->cleared_at : null;

        // Fetch up to 40 messages
        $query = $this->ChatMessages->find()
            ->where(['ChatMessages.conversation_id' => $convId])
            ->contain(['Sender', 'ChatAttachments', 'ChatReactions.Users'])
            ->orderBy(['ChatMessages.id' => 'DESC'])
            ->limit(40);

        if ($userClearedAt) {
            $query->where(['ChatMessages.created >' => $userClearedAt]);
        }

        if ($beforeId > 0) {
            $query->where(['ChatMessages.id <' => $beforeId]);
        }

        $rawMessages = $query->all()->toArray();
        $hasMore = count($rawMessages) === 40;
        $rawMessages = array_reverse($rawMessages);

        $otherLastReadAt = null;
        $otherLastReadTimestamp = 0;
        $groupData = null;
        $otherUser = null;

        if ($isGroup) {
            $membersRows = $this->ChatConversationUsers->find()
                ->where(['conversation_id' => $convId])
                ->contain(['Users'])
                ->orderBy(['ChatConversationUsers.role' => 'ASC', 'ChatConversationUsers.id' => 'ASC'])
                ->all();

            $groupMembers = [];
            $isAdmin = false;
            foreach ($membersRows as $mr) {
                $u = $mr->user;
                if (!$u) continue;
                $isMemberOnline = false;
                if ($u->last_seen_at) {
                    $diffSec = $now->getTimestamp() - $u->last_seen_at->getTimestamp();
                    $isMemberOnline = ($diffSec <= 180);
                }
                if ($u->id === $userId && $mr->role === 'admin') {
                    $isAdmin = true;
                }
                $groupMembers[] = [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar_url' => $this->getUserAvatarUrl($u->picture, $u->gender),
                    'role' => $mr->role,
                    'is_online' => $isMemberOnline,
                    'is_self' => ($u->id === $userId),
                ];
            }

            $groupData = [
                'title' => $conv->title ?: 'Group Chat',
                'description' => $conv->description ?? '',
                'avatar_url' => $this->getGroupAvatarUrl($conv->title),
                'member_count' => count($groupMembers),
                'members' => $groupMembers,
                'is_admin' => $isAdmin,
                'created' => $conv->created ? $conv->created->i18nFormat('dd MMM, yyyy') : '',
            ];

            $otherUser = [
                'id' => 0,
                'name' => $conv->title ?: 'Group Chat',
                'email' => count($groupMembers) . ' members',
                'avatar_url' => $this->getGroupAvatarUrl($conv->title),
                'is_online' => false,
                'last_seen_formatted' => count($groupMembers) . ' members',
                'is_group' => true,
            ];
        } else {
            // Other participant in 1-on-1
            $otherRow = $this->ChatConversationUsers->find()
                ->where(['conversation_id' => $convId, 'user_id !=' => $userId])
                ->contain(['Users'])
                ->first();

            $targetUser = $otherRow ? $otherRow->user : null;
            $otherLastReadAt = $otherRow ? $otherRow->last_read_at : null;
            if ($otherLastReadAt) {
                $otherLastReadTimestamp = $otherLastReadAt->getTimestamp();
            }

            $isOnline = false;
            if ($targetUser && $targetUser->last_seen_at) {
                $diffSec = $now->getTimestamp() - $targetUser->last_seen_at->getTimestamp();
                $isOnline = ($diffSec <= 180);
            }

            if ($targetUser) {
                $otherUser = [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'gender' => $targetUser->gender ?? 'male',
                    'avatar_url' => $this->getUserAvatarUrl($targetUser->picture, $targetUser->gender),
                    'is_online' => $isOnline,
                    'last_seen_formatted' => $targetUser->last_seen_at ? $targetUser->last_seen_at->timeAgoInWords() : 'offline',
                    'member_since' => $targetUser->created ? $targetUser->created->i18nFormat('MMMM yyyy') : '',
                    'is_group' => false,
                ];
            }
        }

        $messages = [];
        foreach ($rawMessages as $m) {
            $messages[] = $this->formatMessageData($m, $userId, $otherLastReadAt);
        }

        // Mark as read
        $this->ChatConversationUsers->updateAll(
            ['last_read_at' => $now],
            ['conversation_id' => $convId, 'user_id' => $userId]
        );

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'conversation_id' => $convId,
                'is_group' => $isGroup,
                'group' => $groupData,
                'other_user_last_read_timestamp' => $otherLastReadTimestamp,
                'other_user' => $otherUser,
                'messages' => $messages,
                'has_more' => $hasMore,
            ]));
    }

    /**
     * AJAX: Send a Message (Supports Text, Emojis, and up to 10 Attachments)
     * Method: POST
     */
    public function sendMessage()
    {
        $this->request->allowMethod(['post']);
        $senderId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');
        $rawMessage = (string)$this->request->getData('message', '');
        $message = trim($rawMessage);

        if (!$this->isUserInConversation($convId, $senderId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        // Handle attachments (up to 10 files)
        $processedAttachments = [];
        $targetDir = WWW_ROOT . 'uploads' . DS . 'chat' . DS;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'csv'];

        // Gather all uploaded files from PSR-7 or fallback $_FILES
        $rawUploadedFiles = [];
        $psrFiles = $this->request->getUploadedFiles();

        if (!empty($psrFiles['attachments'])) {
            $attList = $psrFiles['attachments'];
            if (is_array($attList)) {
                $rawUploadedFiles = $attList;
            } else {
                $rawUploadedFiles[] = $attList;
            }
        } elseif (!empty($psrFiles['attachment'])) {
            $rawUploadedFiles[] = $psrFiles['attachment'];
        } elseif (!empty($_FILES['attachments'])) {
            $f = $_FILES['attachments'];
            if (is_array($f['name'])) {
                for ($i = 0; $i < count($f['name']); $i++) {
                    if (isset($f['error'][$i]) && $f['error'][$i] === UPLOAD_ERR_OK) {
                        $rawUploadedFiles[] = [
                            'name' => $f['name'][$i],
                            'size' => $f['size'][$i],
                            'tmp_name' => $f['tmp_name'][$i],
                        ];
                    }
                }
            } elseif ($f['error'] === UPLOAD_ERR_OK) {
                $rawUploadedFiles[] = [
                    'name' => $f['name'],
                    'size' => $f['size'],
                    'tmp_name' => $f['tmp_name'],
                ];
            }
        } elseif (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $rawUploadedFiles[] = [
                'name' => $_FILES['attachment']['name'],
                'size' => $_FILES['attachment']['size'],
                'tmp_name' => $_FILES['attachment']['tmp_name'],
            ];
        }

        if (count($rawUploadedFiles) > 10) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Maximum 10 files can be attached at a time.']));
        }

        foreach ($rawUploadedFiles as $upFile) {
            $clientFilename = '';
            $clientSize = 0;
            $tmpPath = null;
            $isPsr = false;

            if ($upFile instanceof \Psr\Http\Message\UploadedFileInterface) {
                if ($upFile->getError() !== UPLOAD_ERR_OK) continue;
                $clientFilename = $upFile->getClientFilename() ?? '';
                $clientSize = (int)$upFile->getSize();
                $isPsr = true;
            } elseif (is_array($upFile) && isset($upFile['tmp_name'])) {
                $clientFilename = $upFile['name'];
                $clientSize = (int)$upFile['size'];
                $tmpPath = $upFile['tmp_name'];
            } else {
                continue;
            }

            if (!$clientFilename) continue;

            $ext = strtolower(pathinfo($clientFilename, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                return $this->response->withStatus(400)->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => "File '{$clientFilename}' is not supported. Allowed: images, PDF, documents, ZIP."]));
            }

            if ($clientSize > 25 * 1024 * 1024) {
                return $this->response->withStatus(400)->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => "File '{$clientFilename}' exceeds maximum limit of 25MB."]));
            }

            $safeFilename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $targetDir . $safeFilename;

            if ($isPsr) {
                $upFile->moveTo($destPath);
            } else {
                move_uploaded_file($tmpPath, $destPath);
            }

            $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            $processedAttachments[] = [
                'file_path' => 'uploads/chat/' . $safeFilename,
                'file_name' => $clientFilename,
                'file_size' => $clientSize,
                'file_type' => $isImg ? 'image' : 'file',
            ];
        }

        if ($message === '' && empty($processedAttachments)) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message or attachment cannot be empty.']));
        }

        if (mb_strlen($message) > 4000) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message is too long (maximum 4000 characters).']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        $messageType = 'text';
        if (!empty($processedAttachments)) {
            $hasImg = false;
            $hasDoc = false;
            foreach ($processedAttachments as $att) {
                if ($att['file_type'] === 'image') $hasImg = true;
                else $hasDoc = true;
            }
            if ($hasImg && $hasDoc) {
                $messageType = 'mixed';
            } elseif ($hasImg) {
                $messageType = 'image';
            } else {
                $messageType = 'file';
            }
        }

        $primary = !empty($processedAttachments) ? $processedAttachments[0] : null;

        $msg = $this->ChatMessages->newEntity([
            'conversation_id' => $convId,
            'sender_id' => $senderId,
            'message' => $message !== '' ? $message : null,
            'message_type' => $messageType,
            'attachment_path' => $primary ? $primary['file_path'] : null,
            'attachment_name' => $primary ? $primary['file_name'] : null,
            'attachment_size' => $primary ? $primary['file_size'] : 0,
            'attachment_type' => $primary ? $primary['file_type'] : null,
            'is_deleted' => 0,
        ]);

        if ($this->ChatMessages->save($msg)) {
            // Save all attachments to chat_message_attachments table
            foreach ($processedAttachments as $attData) {
                $attEntity = $this->ChatMessageAttachments->newEntity([
                    'message_id' => $msg->id,
                    'file_path' => $attData['file_path'],
                    'file_name' => $attData['file_name'],
                    'file_size' => $attData['file_size'],
                    'file_type' => $attData['file_type'],
                ]);
                $this->ChatMessageAttachments->save($attEntity);
            }

            // Update conversation modified time
            $this->ChatConversations->updateAll(['modified' => $now], ['id' => $convId]);

            // Update sender's last_read_at so own message is never counted as unread and clear typing status
            $this->ChatConversationUsers->getConnection()->execute(
                "UPDATE chat_conversation_users SET last_read_at = ?, typing_at = NULL WHERE conversation_id = ? AND user_id = ?",
                [$now->format('Y-m-d H:i:s'), $convId, $senderId]
            );

            // Re-load with sender info, attachments, and reactions
            $saved = $this->ChatMessages->get($msg->id, contain: ['Sender', 'ChatAttachments', 'ChatReactions.Users']);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => $this->formatMessageData($saved, $senderId),
                ]));
        }

        $errs = $msg->getErrors();
        $errMsg = 'Unable to send message. Please try again.';
        if (!empty($errs)) {
            $first = reset($errs);
            if (is_array($first)) {
                $errMsg = reset($first);
            }
        }

        return $this->response->withStatus(500)->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => $errMsg]));
    }

    /**
     * AJAX: Soft-Delete a Message
     * Method: POST
     */
    public function deleteMessage()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $messageId = (int)$this->request->getData('message_id');

        $msg = $this->ChatMessages->find()
            ->where(['id' => $messageId])
            ->first();

        if (!$msg) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message not found.']));
        }

        if ($msg->sender_id !== $userId) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'You can only delete your own messages.']));
        }

        $msg->is_deleted = 1;
        if ($this->ChatMessages->save($msg)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message_id' => $msg->id,
                    'message' => 'Message deleted.',
                ]));
        }

        return $this->response->withStatus(500)->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => 'Failed to delete message.']));
    }

    /**
     * AJAX: Edit a Message
     * Method: POST
     */
    public function editMessage()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $messageId = (int)$this->request->getData('message_id');
        $rawMessage = (string)$this->request->getData('message', '');
        $newText = trim($rawMessage);

        if ($userId <= 0) {
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized.']));
        }

        if ($newText === '') {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message cannot be empty.']));
        }

        if (mb_strlen($newText) > 4000) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message cannot exceed 4000 characters.']));
        }

        $msg = $this->ChatMessages->find()
            ->where(['id' => $messageId])
            ->first();

        if (!$msg) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message not found.']));
        }

        if ($msg->sender_id !== $userId) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'You can only edit your own messages.']));
        }

        if ($msg->is_deleted) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Deleted messages cannot be edited.']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $msg->message = $newText;
        $msg->is_edited = 1;
        $msg->edited_at = $now;
        $msg->modified = $now;

        if ($this->ChatMessages->save($msg)) {
            // Update conversation modified time
            $this->ChatConversations->updateAll(['modified' => $now], ['id' => $msg->conversation_id]);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'message_id' => $msg->id,
                    'message' => $newText,
                    'is_edited' => true,
                    'edited_at_formatted' => $now->i18nFormat('h:mm a'),
                ]));
        }

        return $this->response->withStatus(500)->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => 'Failed to save edited message.']));
    }

    /**
     * AJAX: Mark Conversation as Read
     * Method: POST
     */
    public function markRead()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');

        if (!$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $this->ChatConversationUsers->updateAll(
            ['last_read_at' => $now],
            ['conversation_id' => $convId, 'user_id' => $userId]
        );

        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['success' => true]));
    }

    /**
     * AJAX: Set Typing Status for user in conversation
     * Method: POST
     */
    public function setTyping()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');
        $isTyping = (int)$this->request->getData('is_typing', 0);

        if (!$convId || !$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        if ($isTyping === 1) {
            $this->ChatConversationUsers->getConnection()->execute(
                "UPDATE chat_conversation_users SET typing_at = NOW() WHERE conversation_id = ? AND user_id = ?",
                [$convId, $userId]
            );
        } else {
            $this->ChatConversationUsers->getConnection()->execute(
                "UPDATE chat_conversation_users SET typing_at = NULL WHERE conversation_id = ? AND user_id = ?",
                [$convId, $userId]
            );
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['success' => true]));
    }

    /**
     * AJAX: Global badge counter for dock sidebar across all pages
     * Method: GET
     */
    public function getGlobalBadge()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true, 'total' => 0]));
        }

        $this->touchUserLastSeen($userId);

        $pendingRequests = $this->ChatRequests->find()
            ->where(['receiver_id' => $userId, 'status' => 'pending'])
            ->count();

        // Calculate unread messages count and locate latest unread message across all conversations
        $userConvRows = $this->ChatConversationUsers->find()
            ->where(['user_id' => $userId])
            ->all();

        $unreadMessages = 0;
        $latestUnreadMsg = null;
        $latestUnreadMsgId = 0;

        foreach ($userConvRows as $row) {
            $cId = (int)$row->conversation_id;
            $lastReadAt = $row->last_read_at;
            $clearedAt = $row->cleared_at;

            $conv = $this->ChatConversations->find()
                ->where(['id' => $cId, 'status' => 'active'])
                ->first();
            if (!$conv) continue;

            $isGroup = ($conv->type === 'group');

            $q = $this->ChatMessages->find()
                ->where([
                    'ChatMessages.conversation_id' => $cId,
                    'ChatMessages.sender_id !=' => $userId,
                    'ChatMessages.is_deleted' => 0,
                ]);

            if ($lastReadAt) {
                $q->where(['ChatMessages.created >' => $lastReadAt]);
            }
            if ($clearedAt) {
                $q->where(['ChatMessages.created >' => $clearedAt]);
            }
            $count = $q->count();
            $unreadMessages += $count;

            if ($count > 0) {
                $latest = (clone $q)->contain(['Sender'])->orderBy(['ChatMessages.id' => 'DESC'])->first();
                if ($latest && $latest->id > $latestUnreadMsgId) {
                    $latestUnreadMsgId = $latest->id;
                    $sender = $latest->sender;
                    $snippet = $latest->message;
                    if (empty($snippet)) {
                        $attCount = $this->ChatMessageAttachments->find()->where(['message_id' => $latest->id])->count();
                        if ($attCount > 1) {
                            $snippet = "📎 {$attCount} Files attached";
                        } elseif (!empty($latest->attachment_name)) {
                            $snippet = "📎 " . $latest->attachment_name;
                        } else {
                            $snippet = "📎 Sent an attachment";
                        }
                    }

                    $senderDisplayName = $sender ? $sender->name : 'New Message';
                    $senderAvatar = $this->getUserAvatarUrl($sender ? $sender->picture : null, $sender ? $sender->gender : null);

                    if ($isGroup) {
                        $senderDisplayName = ($sender ? $sender->name : 'Someone') . ' (' . ($conv->title ?: 'Group') . ')';
                        $senderAvatar = $this->getGroupAvatarUrl($conv->title);
                    }

                    $latestUnreadMsg = [
                        'id' => $latest->id,
                        'conversation_id' => $cId,
                        'sender_id' => $latest->sender_id,
                        'sender_name' => $senderDisplayName,
                        'sender_avatar' => $senderAvatar,
                        'message' => $snippet,
                        'time' => $latest->created ? $latest->created->i18nFormat('h:mm a') : '',
                        'created_timestamp' => $latest->created ? $latest->created->getTimestamp() : 0,
                    ];
                }
            }
        }

        $latestPendingRequest = null;
        if ($pendingRequests > 0) {
            $latestReq = $this->ChatRequests->find()
                ->where(['receiver_id' => $userId, 'status' => 'pending'])
                ->contain(['Sender'])
                ->orderBy(['ChatRequests.id' => 'DESC'])
                ->first();
            if ($latestReq) {
                $reqSender = $latestReq->sender;
                $latestPendingRequest = [
                    'id' => $latestReq->id,
                    'sender_name' => $reqSender ? $reqSender->name : 'Someone',
                    'sender_avatar' => $this->getUserAvatarUrl($reqSender ? $reqSender->picture : null, $reqSender ? $reqSender->gender : null),
                    'message' => $latestReq->message,
                    'time' => $latestReq->created ? $latestReq->created->i18nFormat('h:mm a') : '',
                    'created_timestamp' => $latestReq->created ? $latestReq->created->getTimestamp() : 0,
                ];
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'user_id' => $userId,
                'pending_requests' => $pendingRequests,
                'unread_messages' => $unreadMessages,
                'total' => ($pendingRequests + $unreadMessages),
                'latest_unread_msg' => $latestUnreadMsg,
                'latest_pending_request' => $latestPendingRequest,
            ]));
    }

    /**
     * AJAX: Toggle an emoji reaction on a message (Microsoft Teams Style)
     * Method: POST
     */
    public function toggleReaction()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $messageId = (int)$this->request->getData('message_id');
        $reaction = trim((string)$this->request->getData('reaction', ''));

        if ($userId <= 0) {
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized.']));
        }

        if ($reaction === '' || mb_strlen($reaction) > 32) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid emoji reaction.']));
        }

        $msg = $this->ChatMessages->find()
            ->where(['id' => $messageId])
            ->first();

        if (!$msg) {
            return $this->response->withStatus(404)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Message not found.']));
        }

        if (!$this->isUserInConversation($msg->conversation_id, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $existing = $this->ChatMessageReactions->find()
            ->where(['message_id' => $messageId, 'user_id' => $userId])
            ->first();

        $action = 'added';
        if ($existing) {
            if ($existing->reaction === $reaction) {
                // Same reaction clicked again -> toggle OFF
                $this->ChatMessageReactions->delete($existing);
                $action = 'removed';
            } else {
                // Different reaction clicked -> switch reaction
                $existing->reaction = $reaction;
                $this->ChatMessageReactions->save($existing);
                $action = 'updated';
            }
        } else {
            // New reaction -> add
            $newReact = $this->ChatMessageReactions->newEntity([
                'message_id' => $messageId,
                'user_id' => $userId,
                'reaction' => $reaction,
            ]);
            $this->ChatMessageReactions->save($newReact);
            $action = 'added';
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $this->ChatMessages->updateAll(['modified' => $now], ['id' => $messageId]);
        $this->ChatConversations->updateAll(['modified' => $now], ['id' => $msg->conversation_id]);

        $reactionsData = $this->formatMessageReactions($messageId, $userId);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'action' => $action,
                'message_id' => $messageId,
                'reactions' => $reactionsData['reactions'],
                'my_reaction' => $reactionsData['my_reaction'],
            ]));
    }

    /**
     * Helper to group and format message reactions
     */
    protected function formatMessageReactions(int $messageId, int $currentUserId): array
    {
        $allReactions = $this->ChatMessageReactions->find()
            ->where(['message_id' => $messageId])
            ->contain(['Users'])
            ->all();

        $grouped = [];
        $myReaction = null;

        foreach ($allReactions as $r) {
            $emoji = $r->reaction;
            $userName = $r->user ? $r->user->name : 'User';
            $isMe = ($r->user_id === $currentUserId);

            if ($isMe) {
                $myReaction = $emoji;
            }

            if (!isset($grouped[$emoji])) {
                $grouped[$emoji] = [
                    'reaction' => $emoji,
                    'count' => 0,
                    'has_reacted' => false,
                    'users' => [],
                ];
            }

            $grouped[$emoji]['count']++;
            $grouped[$emoji]['users'][] = $userName;
            if ($isMe) {
                $grouped[$emoji]['has_reacted'] = true;
            }
        }

        return [
            'reactions' => array_values($grouped),
            'my_reaction' => $myReaction,
        ];
    }

    /**
     * Helper to format file size cleanly
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024) . ' KB';
        } elseif ($bytes > 0) {
            return $bytes . ' B';
        }
        return '';
    }

    /**
     * Helper to serialize a chat message entity into a secure, safe array
     */
    protected function formatMessageData(\App\Model\Entity\ChatMessage $m, int $currentUserId, ?\DateTimeInterface $otherUserLastReadAt = null): array
    {
        $sender = $m->sender;
        $isSelf = ($m->sender_id === $currentUserId);
        $dateFormatted = $m->created ? $m->created->i18nFormat('yyyy-MM-dd') : '';
        $dateDisplay = '';
        if ($m->created) {
            if ($m->created->isToday()) {
                $dateDisplay = 'Today';
            } elseif ($m->created->isYesterday()) {
                $dateDisplay = 'Yesterday';
            } else {
                $dateDisplay = $m->created->i18nFormat('dd MMM, yyyy');
            }
        }

        $isSeen = false;
        if ($isSelf && $otherUserLastReadAt && $m->created) {
            $isSeen = ($m->created->getTimestamp() <= $otherUserLastReadAt->getTimestamp());
        }

        $webroot = $this->request->getAttribute('webroot') ?? '/';

        // Gather all attachments
        $attachments = [];
        if (!empty($m->chat_attachments)) {
            foreach ($m->chat_attachments as $att) {
                $attachments[] = [
                    'id' => $att->id,
                    'file_url' => rtrim($webroot, '/') . '/' . ltrim($att->file_path, '/'),
                    'file_name' => $att->file_name,
                    'file_size' => (int)$att->file_size,
                    'file_size_formatted' => $this->formatFileSize((int)$att->file_size),
                    'file_type' => $att->file_type,
                ];
            }
        } elseif (!empty($m->attachment_path)) {
            $attachments[] = [
                'id' => 0,
                'file_url' => rtrim($webroot, '/') . '/' . ltrim($m->attachment_path, '/'),
                'file_name' => $m->attachment_name ?: 'Attachment',
                'file_size' => (int)$m->attachment_size,
                'file_size_formatted' => $this->formatFileSize((int)$m->attachment_size),
                'file_type' => $m->attachment_type ?: 'file',
            ];
        }

        // Primary attachment properties for backwards compatibility
        $primaryAtt = !empty($attachments) ? $attachments[0] : null;

        // Group message reactions
        $reactionsData = $this->formatMessageReactions($m->id, $currentUserId);

        return [
            'id' => $m->id,
            'conversation_id' => $m->conversation_id,
            'sender_id' => $m->sender_id,
            'sender_name' => $sender ? $sender->name : 'User',
            'sender_avatar' => $this->getUserAvatarUrl($sender ? $sender->picture : null, $sender ? $sender->gender : null),
            'is_self' => $isSelf,
            'is_seen' => $isSeen,
            'is_edited' => !empty($m->is_edited),
            'edited_at' => $m->edited_at ? $m->edited_at->i18nFormat('h:mm a') : null,
            'message' => $m->is_deleted ? 'This message was deleted' : ($m->message ?? ''),
            'message_type' => $m->message_type,
            'attachments' => $m->is_deleted ? [] : $attachments,
            'reactions' => $m->is_deleted ? [] : $reactionsData['reactions'],
            'my_reaction' => $m->is_deleted ? null : $reactionsData['my_reaction'],
            'attachment_url' => ($m->is_deleted || !$primaryAtt) ? null : $primaryAtt['file_url'],
            'attachment_name' => ($m->is_deleted || !$primaryAtt) ? null : $primaryAtt['file_name'],
            'attachment_size' => ($m->is_deleted || !$primaryAtt) ? 0 : $primaryAtt['file_size'],
            'attachment_size_formatted' => ($m->is_deleted || !$primaryAtt) ? '' : $primaryAtt['file_size_formatted'],
            'attachment_type' => ($m->is_deleted || !$primaryAtt) ? null : $primaryAtt['file_type'],
            'is_deleted' => (bool)$m->is_deleted,
            'time' => $m->created ? $m->created->i18nFormat('h:mm a') : '',
            'date_raw' => $dateFormatted,
            'date_display' => $dateDisplay,
            'timestamp' => $m->created ? $m->created->getTimestamp() : 0,
        ];
    }

    /**
     * AJAX: Clear Chat history for the current user in a conversation
     * Method: POST
     */
    public function clearChat()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');

        if ($userId <= 0) {
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized.']));
        }

        if ($convId <= 0 || !$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $this->ChatConversationUsers->updateAll(
            ['cleared_at' => $now],
            ['conversation_id' => $convId, 'user_id' => $userId]
        );

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'conversation_id' => $convId,
                'message' => 'Chat history cleared successfully.',
            ]));
    }

    /**
     * AJAX: Get teammates list for creating a new group or adding members
     * Method: GET
     */
    public function getTeammatesForGroup()
    {
        $this->request->allowMethod(['get']);
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized.']));
        }

        $excludeConvId = (int)$this->request->getQuery('conversation_id', 0);
        $excludeUserIds = [$userId];

        if ($excludeConvId > 0) {
            $existingMemberIds = $this->ChatConversationUsers->find()
                ->select(['user_id'])
                ->where(['conversation_id' => $excludeConvId])
                ->all()
                ->extract('user_id')
                ->toArray();
            $excludeUserIds = array_unique(array_merge($excludeUserIds, $existingMemberIds));
        }

        $users = $this->Users->find()
            ->where(['id NOT IN' => $excludeUserIds])
            ->orderBy(['name' => 'ASC'])
            ->all();

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $list = [];
        foreach ($users as $u) {
            $isOnline = false;
            if ($u->last_seen_at) {
                $diffSec = $now->getTimestamp() - $u->last_seen_at->getTimestamp();
                $isOnline = ($diffSec <= 180);
            }
            $list[] = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar_url' => $this->getUserAvatarUrl($u->picture, $u->gender),
                'role' => $u->role ?? 'member',
                'is_online' => $isOnline,
            ];
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'users' => $list,
            ]));
    }

    /**
     * AJAX: Create a new Team Group
     * Method: POST
     */
    public function createGroup()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized.']));
        }

        $title = trim((string)$this->request->getData('title', ''));
        $description = trim((string)$this->request->getData('description', ''));
        $rawMembers = $this->request->getData('member_ids');

        if (empty($title)) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Please provide a group name.']));
        }

        $memberIds = [];
        if (is_array($rawMembers)) {
            foreach ($rawMembers as $id) {
                $id = (int)$id;
                if ($id > 0 && $id !== $userId) {
                    $memberIds[] = $id;
                }
            }
        } elseif (is_string($rawMembers)) {
            $parts = explode(',', $rawMembers);
            foreach ($parts as $id) {
                $id = (int)trim($id);
                if ($id > 0 && $id !== $userId) {
                    $memberIds[] = $id;
                }
            }
        }
        $memberIds = array_unique($memberIds);

        if (empty($memberIds)) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Please select at least 1 teammate to add to the group.']));
        }

        $creator = $this->Users->find()->where(['id' => $userId])->first();
        $creatorName = $creator ? $creator->name : 'Admin';

        $conn = $this->ChatConversations->getConnection();
        $conn->begin();

        try {
            $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

            $conv = $this->ChatConversations->newEntity([
                'created_by' => $userId,
                'type' => 'group',
                'title' => $title,
                'description' => $description,
                'status' => 'active',
            ]);
            $this->ChatConversations->saveOrFail($conv);
            $convId = $conv->id;

            // Add Creator as admin
            $adminUser = $this->ChatConversationUsers->newEntity([
                'conversation_id' => $convId,
                'user_id' => $userId,
                'role' => 'admin',
                'joined_at' => $now,
                'last_read_at' => $now,
            ]);
            $this->ChatConversationUsers->saveOrFail($adminUser);

            // Add teammates as members
            foreach ($memberIds as $mId) {
                $memberRow = $this->ChatConversationUsers->newEntity([
                    'conversation_id' => $convId,
                    'user_id' => $mId,
                    'role' => 'member',
                    'joined_at' => $now,
                    'last_read_at' => null,
                ]);
                $this->ChatConversationUsers->saveOrFail($memberRow);
            }

            // Post Initial System Message
            $sysMsg = $this->ChatMessages->newEntity([
                'conversation_id' => $convId,
                'sender_id' => $userId,
                'message' => "{$creatorName} created group \"{$title}\"",
                'message_type' => 'system',
                'is_deleted' => 0,
            ]);
            $this->ChatMessages->saveOrFail($sysMsg);

            $conn->commit();

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'conversation_id' => $convId,
                    'title' => $title,
                    'message' => 'Group created successfully!',
                ]));
        } catch (\Throwable $e) {
            $conn->rollback();
            return $this->response->withStatus(500)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()]));
        }
    }

    /**
     * AJAX: Add members to an existing group
     * Method: POST
     */
    public function addGroupMembers()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');
        $rawMembers = $this->request->getData('member_ids');

        if (!$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $conv = $this->ChatConversations->find()->where(['id' => $convId, 'type' => 'group'])->first();
        if (!$conv) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid group conversation.']));
        }

        $memberIds = [];
        if (is_array($rawMembers)) {
            foreach ($rawMembers as $id) {
                $id = (int)$id;
                if ($id > 0) $memberIds[] = $id;
            }
        } elseif (is_string($rawMembers)) {
            $parts = explode(',', $rawMembers);
            foreach ($parts as $id) {
                $id = (int)trim($id);
                if ($id > 0) $memberIds[] = $id;
            }
        }
        $memberIds = array_unique($memberIds);

        if (empty($memberIds)) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'No members selected to add.']));
        }

        $currentMemberIds = $this->ChatConversationUsers->find()
            ->select(['user_id'])
            ->where(['conversation_id' => $convId])
            ->all()
            ->extract('user_id')
            ->toArray();

        $toAdd = array_diff($memberIds, $currentMemberIds);
        if (empty($toAdd)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true, 'message' => 'Selected members are already in the group.']));
        }

        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $addedUsers = $this->Users->find()->where(['id IN' => $toAdd])->all();
        $addedNames = [];

        foreach ($addedUsers as $u) {
            $addedNames[] = $u->name;
            $newRow = $this->ChatConversationUsers->newEntity([
                'conversation_id' => $convId,
                'user_id' => $u->id,
                'role' => 'member',
                'joined_at' => $now,
                'last_read_at' => null,
            ]);
            $this->ChatConversationUsers->saveOrFail($newRow);
        }

        $actor = $this->Users->find()->where(['id' => $userId])->first();
        $actorName = $actor ? $actor->name : 'Someone';
        $namesStr = implode(', ', $addedNames);

        // Post System Message
        $sysMsg = $this->ChatMessages->newEntity([
            'conversation_id' => $convId,
            'sender_id' => $userId,
            'message' => "{$actorName} added {$namesStr} to the group",
            'message_type' => 'system',
            'is_deleted' => 0,
        ]);
        $this->ChatMessages->saveOrFail($sysMsg);

        $this->ChatConversations->updateAll(['modified' => $now], ['id' => $convId]);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'Added ' . count($addedNames) . ' member(s) to the group.',
            ]));
    }

    /**
     * AJAX: Leave a group conversation
     * Method: POST
     */
    public function leaveGroup()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->getCurrentUserId();
        $convId = (int)$this->request->getData('conversation_id');

        if (!$this->isUserInConversation($convId, $userId)) {
            return $this->response->withStatus(403)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Unauthorized conversation access.']));
        }

        $conv = $this->ChatConversations->find()->where(['id' => $convId, 'type' => 'group'])->first();
        if (!$conv) {
            return $this->response->withStatus(400)->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid group conversation.']));
        }

        $actor = $this->Users->find()->where(['id' => $userId])->first();
        $actorName = $actor ? $actor->name : 'Member';
        $now = new DateTime('now', new \DateTimeZone('Asia/Kolkata'));

        // Delete user's membership
        $this->ChatConversationUsers->deleteAll([
            'conversation_id' => $convId,
            'user_id' => $userId,
        ]);

        // Post System Message
        $sysMsg = $this->ChatMessages->newEntity([
            'conversation_id' => $convId,
            'sender_id' => $userId,
            'message' => "{$actorName} left the group",
            'message_type' => 'system',
            'is_deleted' => 0,
        ]);
        $this->ChatMessages->saveOrFail($sysMsg);

        // Check remaining members
        $remainingCount = $this->ChatConversationUsers->find()
            ->where(['conversation_id' => $convId])
            ->count();

        if ($remainingCount === 0) {
            $conv->status = 'archived';
            $this->ChatConversations->save($conv);
        } else {
            // If no admin left, promote the oldest member
            $hasAdmin = $this->ChatConversationUsers->find()
                ->where(['conversation_id' => $convId, 'role' => 'admin'])
                ->count() > 0;

            if (!$hasAdmin) {
                $oldest = $this->ChatConversationUsers->find()
                    ->where(['conversation_id' => $convId])
                    ->orderBy(['id' => 'ASC'])
                    ->first();
                if ($oldest) {
                    $oldest->role = 'admin';
                    $this->ChatConversationUsers->save($oldest);
                }
            }
            $this->ChatConversations->updateAll(['modified' => $now], ['id' => $convId]);
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'You left the group.',
            ]));
    }
}
