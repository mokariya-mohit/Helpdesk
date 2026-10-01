<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User|null $currentUserEntity
 * @var int $pendingRequestsCount
 */

$this->assign('title', 'Team Chat - Helpdesk');
$currentUser = $this->request->getSession()->read('AuthUser');
$currentUserId = $currentUser ? (int)$currentUser['id'] : 0;
?>

<?= $this->Html->css('messaging.css?v=' . (file_exists(WWW_ROOT . 'css' . DS . 'messaging.css') ? filemtime(WWW_ROOT . 'css' . DS . 'messaging.css') : time())) ?>

<!-- Radiant Ambient Glowing Mesh Orbs -->
<div class="ambient-glow-orb orb-1"></div>
<div class="ambient-glow-orb orb-2"></div>
<div class="ambient-glow-orb orb-3"></div>

<div class="app-container">
    <!-- Top Header Bar (Rulse-Inspired Glassmorphic Header matching Tasks page) -->
    <header class="top-header-bar">
        <div class="header-left-cluster">
            <a href="<?= $this->Url->build('/') ?>" class="header-brand" title="Helpdesk Daily Work Journal">
                <h1 class="header-brand-logo" style="margin:0; font-size:inherit; font-weight:inherit; display:inline-flex; align-items:center;">
                    <i class="fa-solid fa-layer-group"></i> HELPDESK
                </h1>
            </a>

            <!-- Mode Switcher Pill (Matching Image 1: Mode: Sunrise ▾ / Mode: Dark ▾) -->
            <button type="button" class="mode-switcher-pill" id="btnHeaderModeToggle" title="Click to switch Sunrise / Dark mode">
                <span id="modeSwitcherIcon"><i class="fa-solid fa-sun" style="color: #f59e0b;"></i></span>
                <span id="modeSwitcherText">Mode: Sunrise</span>
                <i class="fa-solid fa-chevron-down text-xs" style="opacity: 0.6; margin-left: 2px;"></i>
            </button>
        </div>

        <div class="header-center-cluster">
            <div class="header-chat-title-badge">
                <i class="fa-solid fa-comments"></i>
                <span>Team Chat &amp; Messaging</span>
            </div>
        </div>

        <div class="header-right-cluster">
            <!-- Notification Icon with Dynamic Badge -->
            <?php
                $globalCount = $globalNotificationCount ?? 0;
                $hasBadge = ($globalCount > 0);
            ?>
            <button type="button" class="btn-header-circle" id="btnHeaderNotification" title="Notifications" style="position: relative;">
                <i class="fa-regular fa-bell"></i>
                <span class="header-notification-badge <?= $hasBadge ? 'show' : 'd-none' ?>" id="headerNotificationBadge" data-count="<?= $globalCount ?>" style="<?= $hasBadge ? 'display: inline-flex;' : 'display: none;' ?>"><?= $hasBadge ? ($globalCount > 99 ? '99+' : $globalCount) : '' ?></span>
            </button>

            <!-- Settings Menu Gear Icon (Opens Settings Modal directly) -->
            <button type="button" class="btn-header-circle" id="btnHeaderSettings" title="System Settings (Gemini AI & Google SMTP)">
                <i class="fa-solid fa-gear"></i>
            </button>

            <?php if (!empty($currentUser)): ?>
                <?php
                    $gender = !empty($currentUser['gender']) ? $currentUser['gender'] : 'male';
                    $headerAvatar = !empty($currentUser['picture']) ? $currentUser['picture'] : "img/avatars/{$gender}/{$gender}_1.png";
                    $headerAvatarUrl = str_starts_with($headerAvatar, 'http') ? $headerAvatar : $this->Url->build('/' . ltrim($headerAvatar, '/'));
                    $userInitial = strtoupper(substr($currentUser['name'] ?? 'U', 0, 1));
                ?>
                <!-- User Profile Avatar (Matching Tasks page) -->
                <button type="button" class="header-user-avatar-btn" id="btnHeaderProfile" title="User Profile & Account Settings">
                    <img src="<?= h($headerAvatarUrl) ?>" alt="<?= h($currentUser['name'] ?? 'User') ?>" class="header-user-avatar-img" id="headerProfileAvatarImg" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <span class="header-user-avatar-fallback" style="display:none;"><?= $userInitial ?></span>
                </button>
            <?php else: ?>
                <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>" class="btn" style="background:#1c201e; color:#ffffff; border-color:#1c201e;">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Workspace Layout with Left Motion Dock & Chat Workspace Card -->
    <div class="app-main-layout">
        <!-- Left Floating Motion Primitives Dock Sidebar (macOS Wave Magnification) -->
        <aside class="motion-dock-sidebar" id="motionDockSidebar" aria-label="Application Dock">
            <div class="dock-item-list" id="dockItemList">
                <!-- 1. Work Logs Toggle Icon -->
                <a href="<?= $this->Url->build('/') ?>" class="dock-item" id="btnDockWorkLogs" data-title="Work Logs" style="text-decoration:none;">
                    <div class="dock-item-icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="dock-label-tooltip">Work Logs</span>
                </a>

                <!-- 2. Projects Manager Modal -->
                <div class="dock-item" id="btnDockProjects" role="button" tabindex="0" data-title="Projects">
                    <div class="dock-item-icon">
                        <i class="fa-solid fa-folder-tree"></i>
                    </div>
                    <span class="dock-label-tooltip">Projects</span>
                </div>

                <!-- 3. Clients Manager Modal -->
                <div class="dock-item" id="btnDockClients" role="button" tabindex="0" data-title="Clients">
                    <div class="dock-item-icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <span class="dock-label-tooltip">Clients</span>
                </div>

                <div class="dock-divider"></div>

                <!-- 4. Team Chat (Active) -->
                <a href="<?= $this->Url->build('/messages') ?>" class="dock-item active dock-item-active" id="btnDockChat" data-title="Team Chat" style="text-decoration:none;">
                    <div class="dock-item-icon">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <span class="dock-label-tooltip">Team Chat</span>
                </a>

                <!-- 5. Logout Trigger with Custom Confirmation -->
                <div class="dock-item" id="btnDockLogout" role="button" tabindex="0" data-title="Logout" data-url="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout', 'plugin' => false]) ?>">
                    <div class="dock-item-icon" style="color: #ef4444;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </div>
                    <span class="dock-label-tooltip">Logout</span>
                </div>
            </div>
        </aside>

        <!-- Right Main Panel: Glassmorphic Chat Workspace Container -->
        <main class="chat-workspace-card" id="chatAppWrapper">

        <!-- Left Panel: Conversations & Pending Requests -->
        <section class="chat-sidebar-panel" id="chatSidebarPanel">
            <div class="chat-sidebar-header">
                <div class="chat-sidebar-title">
                    <i class="fa-solid fa-comments"></i>
                    <span>Team Chat</span>
                </div>
                <div class="chat-sidebar-actions" style="display: flex; gap: 6px; align-items: center;">
                    <button type="button" class="btn-new-group" id="btnOpenNewGroupModal" title="Create a new team group">
                        <i class="fa-solid fa-users"></i>
                        <span>Group</span>
                    </button>
                    <button type="button" class="btn-new-chat" id="btnOpenNewChatModal" title="Find teammate and send message request">
                        <i class="fa-solid fa-plus"></i>
                        <span>New Chat</span>
                    </button>
                </div>
            </div>

            <div class="chat-search-wrap">
                <div class="chat-search-inner">
                    <i class="fa-solid fa-magnifying-glass chat-search-icon"></i>
                    <input type="text" id="chatFilterInput" class="chat-search-input" placeholder="Search conversations..." autocomplete="off">
                </div>
            </div>

            <!-- Tab Navigation (Chats vs Requests) -->
            <div class="chat-tabs-bar">
                <button type="button" class="chat-tab-btn active" id="tabBtnChats">
                    <i class="fa-regular fa-message"></i> Chats
                    <span class="chat-tab-badge secondary" id="chatsTabCount">0</span>
                </button>
                <button type="button" class="chat-tab-btn" id="tabBtnRequests">
                    <i class="fa-solid fa-user-clock"></i> Requests
                    <span class="chat-tab-badge d-none" id="requestsTabBadge" style="display: none;"></span>
                </button>
            </div>

            <!-- Tab 1: Conversations List -->
            <div class="chat-list-scroll" id="conversationsListContainer">
                <div style="text-align: center; padding: 30px 10px; color: var(--text-muted); font-size: 12.5px;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 18px; margin-bottom: 8px; color: var(--primary);"></i>
                    <div>Loading conversations...</div>
                </div>
            </div>

            <!-- Tab 2: Requests List (Hidden by default) -->
            <div class="chat-list-scroll" id="requestsListContainer" style="display: none;">
                <!-- Populated dynamically via AJAX -->
            </div>
        </section>

        <!-- Middle Panel: Active Conversation or Empty View -->
        <main class="chat-main-panel" id="chatMainPanel">
            <!-- State A: Empty View (No chat selected) -->
            <div class="chat-empty-view" id="chatEmptyView">
                <div class="chat-empty-icon">
                    <i class="fa-regular fa-comments"></i>
                </div>
                <h2 class="chat-empty-title">Select a Conversation</h2>
                <p class="chat-empty-desc">
                    Connect and collaborate in real-time with your teammates. Select an active conversation from the left or click "New Chat" to send a chat request to a colleague.
                </p>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button type="button" class="btn-new-chat" id="btnEmptyStartChat" style="padding: 10px 20px; font-size: 13.5px;">
                        <i class="fa-solid fa-user-plus"></i> Start a Conversation
                    </button>
                    <button type="button" class="btn-new-group" id="btnEmptyCreateGroup" style="padding: 10px 20px; font-size: 13.5px;">
                        <i class="fa-solid fa-users"></i> Create Group
                    </button>
                </div>
            </div>

            <!-- State B: Active Chat View -->
            <div id="chatActiveView" style="display: none; height: 100%; flex-direction: column;">
                <!-- Conversation Header -->
                <div class="chat-active-header">
                    <div class="chat-header-user">
                        <button type="button" class="btn-chat-back-mobile" id="btnBackToChatList" title="Back to chats">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <div class="chat-avatar-wrap">
                            <img src="" alt="Avatar" class="chat-avatar-img" id="activeUserAvatar">
                            <div class="chat-online-dot" id="activeUserOnlineDot"></div>
                        </div>
                        <div class="chat-header-info">
                            <div class="chat-header-name" id="activeUserName">Loading...</div>
                            <div class="chat-header-status" id="activeUserStatusText">
                                <span class="chat-status-pill" id="activeStatusPill"></span>
                                <span id="activeStatusLabel">Offline</span>
                            </div>
                        </div>
                    </div>

                    <div class="chat-header-actions">
                        <button type="button" class="btn-header-action" id="btnToggleInfoPanel" title="Contact Details & Info">
                            <i class="fa-solid fa-circle-info"></i>
                        </button>
                    </div>
                </div>

                <!-- Messages Thread Scroll Container -->
                <div class="chat-messages-scroll" id="chatMessagesScroll">
                    <div id="chatMessagesList" style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
                        <!-- Dynamic Message Elements -->
                    </div>
                </div>

                <!-- Floating Scroll to Bottom Button -->
                <button type="button" class="chat-scroll-bottom-btn" id="btnScrollToBottom" style="display: none;">
                    <i class="fa-solid fa-arrow-down"></i> New messages
                </button>

                <!-- Floating Live Typing Indicator -->
                <div class="chat-typing-indicator" id="chatTypingIndicator" style="display: none;">
                    <div class="typing-bubble">
                        <div class="typing-dots">
                            <span class="typing-dot"></span>
                            <span class="typing-dot"></span>
                            <span class="typing-dot"></span>
                        </div>
                        <span class="typing-text"><strong id="typingUserSpan">Someone</strong> is typing...</span>
                    </div>
                </div>

                <!-- Message Compose Area -->
                <div class="chat-input-area" style="position: relative;">
                    <!-- Multi-Attachment Preview Container (Supports up to 10 files) -->
                    <div class="chat-attachment-preview-container" id="chatAttachmentPreview" style="display: none;">
                        <div class="chat-attachment-preview-header">
                            <span class="chat-attachment-count-label" id="chatAttachmentCountLabel"><i class="fa-solid fa-paperclip"></i> Attached files (0/10)</span>
                            <div class="chat-attachment-header-actions">
                                <button type="button" class="btn-add-more-attachments" id="btnAddMoreAttachments" title="Add more files (max 10)">
                                    <i class="fa-solid fa-plus"></i> Add Files
                                </button>
                                <button type="button" class="btn-clear-all-attachments" id="btnClearAllAttachments" title="Remove all files">
                                    <i class="fa-solid fa-trash-can"></i> Clear All
                                </button>
                            </div>
                        </div>
                        <div class="chat-attachment-preview-items" id="chatAttachmentItems">
                            <!-- Populated with attached file items -->
                        </div>
                    </div>

                    <div class="chat-input-box">
                        <button type="button" class="btn-chat-input-tool" id="btnToggleEmoji" title="Insert Emoji">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>
                        <button type="button" class="btn-chat-input-tool" id="btnAttachFile" title="Attach files or images (max 10)">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        <input type="file" id="chatFileInput" style="display: none;" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.csv" />
                        <textarea id="chatMessageInput" class="chat-textarea" placeholder="Type a message..." rows="1" maxlength="4000"></textarea>
                        <button type="button" class="btn-send-message" id="btnSendMessage" title="Send message" disabled>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>

                    <!-- Emoji Popover (iPhone iOS Full Library) -->
                    <div class="chat-emoji-popover" id="chatEmojiPopover" style="display: none;">
                        <div class="chat-emoji-header">
                            <div class="chat-emoji-search-wrap">
                                <i class="fa-solid fa-magnifying-glass chat-emoji-search-icon"></i>
                                <input type="text" class="chat-emoji-search-input" id="chatEmojiSearchInput" placeholder="Search emojis..." autocomplete="off" />
                                <button type="button" class="btn-clear-emoji-search" id="btnClearEmojiSearch" style="display: none;">&times;</button>
                            </div>
                            <div class="chat-emoji-tabs">
                                <button type="button" class="emoji-tab-btn active" data-category="smileys" title="Smileys & Emotion">😀</button>
                                <button type="button" class="emoji-tab-btn" data-category="people" title="People & Gestures">🖐️</button>
                                <button type="button" class="emoji-tab-btn" data-category="nature" title="Animals & Nature">🐶</button>
                                <button type="button" class="emoji-tab-btn" data-category="food" title="Food & Drink">🍔</button>
                                <button type="button" class="emoji-tab-btn" data-category="activities" title="Activities & Sports">⚽</button>
                                <button type="button" class="emoji-tab-btn" data-category="travel" title="Travel & Places">🚀</button>
                                <button type="button" class="emoji-tab-btn" data-category="objects" title="Objects & Tech">💡</button>
                                <button type="button" class="emoji-tab-btn" data-category="symbols" title="Symbols & Flags">💖</button>
                            </div>
                        </div>
                        <div class="chat-emoji-list" id="chatEmojiList">
                            <!-- Populated with categorized emojis -->
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Side Panel: Contact / Group Info & Details (Collapsible) -->
        <aside class="chat-info-panel hidden" id="chatInfoPanel">
            <div class="chat-info-header">
                <span id="infoPanelTitle">Contact Details</span>
                <button type="button" class="btn-header-action" id="btnCloseInfoPanel" style="width: 26px; height: 26px;" title="Close Info">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- 1. Direct 1-on-1 Contact View -->
            <div class="chat-info-body" id="infoPanelDirectView">
                <div class="chat-info-avatar-wrap">
                    <img src="" alt="User" class="chat-info-avatar-img" id="infoPanelAvatar">
                    <div class="chat-online-dot" id="infoPanelOnlineDot" style="width: 14px; height: 14px; bottom: 2px; right: 2px;"></div>
                </div>
                <div class="chat-info-name" id="infoPanelName">-</div>
                <div class="chat-info-email" id="infoPanelEmail">
                    <i class="fa-regular fa-envelope"></i> <span>-</span>
                </div>

                <div class="chat-info-meta-card">
                    <div class="chat-info-meta-row">
                        <span class="chat-info-meta-label">Status</span>
                        <span class="chat-info-meta-val" id="infoPanelStatus">Offline</span>
                    </div>
                    <div class="chat-info-meta-row">
                        <span class="chat-info-meta-label">Gender</span>
                        <span class="chat-info-meta-val" id="infoPanelGender">Male</span>
                    </div>
                    <div class="chat-info-meta-row">
                        <span class="chat-info-meta-label">Member Since</span>
                        <span class="chat-info-meta-val" id="infoPanelMemberSince">-</span>
                    </div>
                </div>

                <div class="chat-info-actions-section">
                    <button type="button" class="btn-chat-info-action btn-clear-chat-danger" id="btnClearChatHistoryBtnDirect" title="Clear chat history for yourself">
                        <i class="fa-solid fa-broom"></i>
                        <span>Clear Chat</span>
                    </button>
                </div>
            </div>

            <!-- 2. Group Conversation View -->
            <div class="chat-info-body" id="infoPanelGroupView" style="display: none;">
                <div class="chat-info-avatar-wrap">
                    <img src="" alt="Group" class="chat-info-avatar-img" id="groupInfoAvatar">
                </div>
                <div class="chat-info-name" id="groupInfoName">-</div>
                <div class="chat-info-desc" id="groupInfoDesc"></div>

                <div class="chat-info-meta-card" style="margin-top: 10px;">
                    <div class="chat-info-meta-row">
                        <span class="chat-info-meta-label">Members</span>
                        <span class="chat-info-meta-val" id="groupInfoMemberCount">0</span>
                    </div>
                    <div class="chat-info-meta-row">
                        <span class="chat-info-meta-label">Created</span>
                        <span class="chat-info-meta-val" id="groupInfoCreatedDate">-</span>
                    </div>
                </div>

                <!-- Group Members List -->
                <div class="group-members-container">
                    <div class="group-members-header">
                        <span>Group Members</span>
                        <button type="button" class="btn-add-group-member" id="btnOpenAddGroupMemberModal" title="Add members to group">
                            <i class="fa-solid fa-user-plus"></i> Add
                        </button>
                    </div>
                    <div class="group-members-list" id="groupMembersListContainer">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Group Actions -->
                <div class="chat-info-actions-section" style="margin-top: 14px; width: 100%;">
                    <button type="button" class="btn-chat-info-action btn-clear-chat-danger" id="btnClearChatHistoryBtnGroup" title="Clear chat history for yourself">
                        <i class="fa-solid fa-broom"></i>
                        <span>Clear Chat</span>
                    </button>
                    <button type="button" class="btn-chat-info-action btn-leave-group-danger" id="btnLeaveGroupTrigger" title="Leave this team group" style="margin-top: 8px;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Leave Group</span>
                    </button>
                </div>
            </div>
        </aside>
        </main>
    </div>
</div>

<!-- Modal: New Chat / Send Message Request -->
<div class="chat-modal-overlay" id="modalNewChat">
    <div class="chat-modal-card">
        <div class="chat-modal-header">
            <div class="chat-modal-title">
                <i class="fa-solid fa-user-plus" style="color: var(--primary);"></i>
                <span>Start a New Conversation</span>
            </div>
            <button type="button" class="btn-header-action" id="btnCloseNewChatModal" style="width: 28px; height: 28px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-modal-body">
            <div style="font-size: 12.5px; color: var(--text-muted);">
                Search for an existing teammate by their full name or email address to send a direct message request.
            </div>

            <div class="chat-modal-search-wrap">
                <i class="fa-solid fa-magnifying-glass chat-modal-search-icon"></i>
                <input type="text" id="inputSearchTeammates" class="chat-modal-search-input" placeholder="Search by name or email (e.g. John, Amit)..." autocomplete="off">
            </div>

            <div id="newChatSearchResults" style="display: flex; flex-direction: column; gap: 8px; max-height: 240px; overflow-y: auto;">
                <div style="text-align: center; color: var(--text-muted); padding: 18px; font-size: 12px;">
                    Type a name or email to search teammates.
                </div>
            </div>

            <!-- Selected Teammate Compose Area (Hidden until a user without conversation is picked) -->
            <div id="newChatComposeArea" class="chat-modal-selected-box" style="display: none;">
                <div class="chat-modal-selected-header">
                    <div class="chat-modal-selected-user">
                        <img src="" id="selectedUserAvatar" class="chat-modal-selected-avatar">
                        <strong id="selectedUserName"></strong>
                    </div>
                    <button type="button" id="btnDeselectUser" class="btn-deselect-user" title="Pick another colleague">
                        <i class="fa-solid fa-xmark"></i> Change
                    </button>
                </div>
                <textarea id="inputInitialMessage" class="chat-modal-note-input" placeholder="Add an optional greeting or note (e.g. 'Hi, need help regarding today\'s ticket')..."></textarea>
            </div>
        </div>

        <div class="chat-modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelNewChat">Cancel</button>
            <button type="button" class="btn-modal-submit" id="btnSendRequestSubmit" disabled>
                <i class="fa-solid fa-paper-plane"></i>
                <span>Send Request</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Create New Group -->
<div class="chat-modal-overlay" id="modalNewGroup">
    <div class="chat-modal-card">
        <div class="chat-modal-header">
            <div class="chat-modal-title">
                <i class="fa-solid fa-users" style="color: var(--primary);"></i>
                <span>Create New Team Group</span>
            </div>
            <button type="button" class="btn-header-action" id="btnCloseNewGroupModal" style="width: 28px; height: 28px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-modal-body">
            <div style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px;">
                Create a team chat channel for projects, departments, or quick collaboration.
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--text-headline);">Group Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="inputNewGroupTitle" class="form-control" placeholder="e.g. Frontend Team, Helpdesk Support, Sprint Q3..." maxlength="100" autocomplete="off" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 13px; background: var(--bg-card); color: var(--text-primary);">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--text-headline);">Description <span style="font-weight: 400; color: var(--text-muted);">(optional)</span></label>
                <textarea id="inputNewGroupDesc" class="form-control" rows="2" placeholder="Brief purpose or topic for this group..." style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 12.5px; background: var(--bg-card); color: var(--text-primary); resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-headline);">Select Members <span style="color: #ef4444;">*</span></label>
                <span id="newGroupSelectedCount" style="font-size: 11.5px; font-weight: 600; color: var(--primary);">0 selected</span>
            </div>

            <div class="chat-modal-search-wrap" style="margin-bottom: 8px;">
                <i class="fa-solid fa-magnifying-glass chat-modal-search-icon"></i>
                <input type="text" id="inputSearchGroupMembers" class="chat-modal-search-input" placeholder="Filter teammates by name..." autocomplete="off">
            </div>

            <div id="newGroupTeammatesList" class="group-members-picker-list">
                <div style="text-align: center; color: var(--text-muted); padding: 18px; font-size: 12px;">
                    <i class="fa-solid fa-spinner fa-spin" style="margin-right: 6px;"></i> Loading teammates...
                </div>
            </div>
        </div>

        <div class="chat-modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelNewGroup">Cancel</button>
            <button type="button" class="btn-modal-submit" id="btnSubmitCreateGroup">
                <i class="fa-solid fa-check"></i>
                <span>Create Group</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add Members to Existing Group -->
<div class="chat-modal-overlay" id="modalAddGroupMembers">
    <div class="chat-modal-card">
        <div class="chat-modal-header">
            <div class="chat-modal-title">
                <i class="fa-solid fa-user-plus" style="color: var(--primary);"></i>
                <span>Add Members to Group</span>
            </div>
            <button type="button" class="btn-header-action" id="btnCloseAddMembersModal" style="width: 28px; height: 28px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-modal-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; color: var(--text-muted);">Choose teammates to add into this group:</span>
                <span id="addMembersSelectedCount" style="font-size: 11.5px; font-weight: 600; color: var(--primary);">0 selected</span>
            </div>

            <div class="chat-modal-search-wrap" style="margin-bottom: 8px;">
                <i class="fa-solid fa-magnifying-glass chat-modal-search-icon"></i>
                <input type="text" id="inputSearchAddMembers" class="chat-modal-search-input" placeholder="Search teammates..." autocomplete="off">
            </div>

            <div id="addMembersTeammatesList" class="group-members-picker-list">
                <div style="text-align: center; color: var(--text-muted); padding: 18px; font-size: 12px;">
                    <i class="fa-solid fa-spinner fa-spin" style="margin-right: 6px;"></i> Loading teammates...
                </div>
            </div>
        </div>

        <div class="chat-modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelAddMembers">Cancel</button>
            <button type="button" class="btn-modal-submit" id="btnSubmitAddMembers">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Selected</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Confirm Clear Chat -->
<div class="chat-modal-overlay" id="modalClearChatConfirm">
    <div class="chat-modal-card" style="max-width: 400px;">
        <div class="chat-modal-header" style="border-bottom-color: rgba(239, 68, 68, 0.15);">
            <div class="chat-modal-title" style="color: #ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Clear Chat History?</span>
            </div>
            <button type="button" class="btn-header-action" id="btnCloseClearChatModal" style="width: 28px; height: 28px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-modal-body" style="font-size: 13px; line-height: 1.5; color: var(--text-primary);">
            <p style="margin: 0 0 10px 0;">
                Are you sure you want to clear your chat history for this conversation?
            </p>
            <div style="background: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; padding: 10px 12px; border-radius: 6px; font-size: 12px; color: var(--text-muted);">
                <strong>Note:</strong> Messages will be cleared from your view only. Other conversation participants will not be affected.
            </div>
        </div>

        <div class="chat-modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelClearChat">Cancel</button>
            <button type="button" class="btn-modal-submit" id="btnConfirmClearChatAction" style="background: #ef4444; border-color: #ef4444;">
                <i class="fa-solid fa-broom"></i>
                <span>Yes, Clear Chat</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Confirm Leave Group -->
<div class="chat-modal-overlay" id="modalLeaveGroupConfirm">
    <div class="chat-modal-card" style="max-width: 400px;">
        <div class="chat-modal-header" style="border-bottom-color: rgba(239, 68, 68, 0.15);">
            <div class="chat-modal-title" style="color: #ef4444;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Leave Group?</span>
            </div>
            <button type="button" class="btn-header-action" id="btnCloseLeaveGroupModal" style="width: 28px; height: 28px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-modal-body" style="font-size: 13px; line-height: 1.5; color: var(--text-primary);">
            <p style="margin: 0 0 10px 0;">
                Are you sure you want to leave this group?
            </p>
            <div style="background: rgba(245, 158, 11, 0.08); border-left: 3px solid #f59e0b; padding: 10px 12px; border-radius: 6px; font-size: 12px; color: var(--text-muted);">
                You will no longer receive new messages or updates from this group conversation.
            </div>
        </div>

        <div class="chat-modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelLeaveGroup">Cancel</button>
            <button type="button" class="btn-modal-submit" id="btnConfirmLeaveGroupAction" style="background: #ef4444; border-color: #ef4444;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Yes, Leave</span>
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var CURRENT_USER_ID = <?= (int)$currentUserId ?>;
    var activeConversationId = 0;
    var lastKnownMessageId = 0;
    var isPolling = false;
    var pollIntervalTimer = null;
    var conversationsCache = [];
    var pendingRequestsCache = [];
    var selectedTargetUserId = 0;
    var activeOtherUserCache = null;
    var activeIsGroupCache = false;
    var activeGroupDataCache = null;

    // Helper: Escape HTML to strictly prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Helper: Show Toast
    function showToast(msg, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(msg, type);
            return;
        }
        var toast = $('#toastNotification');
        $('#toastMessage').text(msg);
        toast.addClass('show');
        setTimeout(function() { toast.removeClass('show'); }, 3000);
    }

    // Helper: Scroll to bottom of message thread
    function scrollToBottom(force) {
        var el = document.getElementById('chatMessagesScroll');
        if (!el) return;
        var threshold = 180;
        var isNearBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) <= threshold;

        if (force || isNearBottom) {
            el.scrollTop = el.scrollHeight;
            $('#btnScrollToBottom').hide();
        } else {
            $('#btnScrollToBottom').show();
        }
    }

    $('#btnScrollToBottom').click(function() {
        scrollToBottom(true);
    });

    $('#chatMessagesScroll').on('scroll', function() {
        var el = this;
        var threshold = 180;
        var isNearBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) <= threshold;
        if (isNearBottom) {
            $('#btnScrollToBottom').hide();
        }
    });

    // Parse URL params for direct navigation from desktop notifications (?c=123 or ?tab=requests)
    var urlParams = new URLSearchParams(window.location.search);
    var targetConvId = parseInt(urlParams.get('c'), 10) || 0;
    var targetTab = urlParams.get('tab');

    // 1. Tab Navigation: Chats vs Requests
    $('#tabBtnChats').click(function() {
        $(this).addClass('active');
        $('#tabBtnRequests').removeClass('active');
        $('#conversationsListContainer').show();
        $('#requestsListContainer').hide();
    });

    $('#tabBtnRequests').click(function() {
        $(this).addClass('active');
        $('#tabBtnChats').removeClass('active');
        $('#conversationsListContainer').hide();
        $('#requestsListContainer').show();
        loadRequests();
    });

    if (targetTab === 'requests') {
        $('#tabBtnRequests').click();
    }

    // 2. Fetch Requests
    function loadRequests() {
        $.ajax({
            url: window.APP_BASE + 'messages/get-requests',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    pendingRequestsCache = res.incoming || [];
                    renderRequestsList(res.incoming, res.outgoing);
                    updateRequestsBadge(res.pending_count);
                }
            }
        });
    }

    function updateRequestsBadge(count) {
        var badge = $('#requestsTabBadge');
        if (count > 0) {
            badge.text(count > 99 ? '99+' : count)
                .attr('data-count', count)
                .removeClass('d-none')
                .addClass('show')
                .css('display', 'inline-flex');
        } else {
            badge.empty()
                .attr('data-count', '0')
                .removeClass('show')
                .addClass('d-none')
                .css('display', 'none');
        }
    }

    function renderRequestsList(incoming, outgoing) {
        var container = $('#requestsListContainer');
        var html = '';

        if ((!incoming || incoming.length === 0) && (!outgoing || outgoing.length === 0)) {
            container.html('<div style="text-align:center; padding:40px 14px; color:var(--text-muted); font-size:12.5px;">' +
                '<i class="fa-regular fa-envelope-open" style="font-size:26px; margin-bottom:8px; opacity:0.6;"></i>' +
                '<div>No pending chat requests.</div>' +
            '</div>');
            return;
        }

        if (incoming && incoming.length > 0) {
            html += '<div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em; margin:6px 4px 8px 4px;">Incoming Requests (' + incoming.length + ')</div>';
            incoming.forEach(function(req) {
                html += '<div class="chat-req-card" data-req-id="' + req.id + '">';
                html += '  <div class="chat-req-header">';
                html += '    <img src="' + escapeHtml(req.avatar_url) + '" class="chat-avatar-img" style="width:34px; height:34px;">';
                html += '    <div class="chat-req-user">';
                html += '      <div class="chat-req-name">' + escapeHtml(req.name) + '</div>';
                html += '      <div class="chat-req-time">' + escapeHtml(req.time_ago) + '</div>';
                html += '    </div>';
                html += '  </div>';
                if (req.initial_message) {
                    html += '  <div class="chat-req-message">"' + escapeHtml(req.initial_message) + '"</div>';
                }
                html += '  <div class="chat-req-actions">';
                html += '    <button type="button" class="btn-req-accept" data-req-id="' + req.id + '"><i class="fa-solid fa-check"></i> Accept</button>';
                html += '    <button type="button" class="btn-req-reject" data-req-id="' + req.id + '"><i class="fa-solid fa-xmark"></i> Reject</button>';
                html += '  </div>';
                html += '</div>';
            });
        }

        if (outgoing && outgoing.length > 0) {
            html += '<div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em; margin:16px 4px 8px 4px;">Sent Requests Pending (' + outgoing.length + ')</div>';
            outgoing.forEach(function(req) {
                html += '<div class="chat-req-card" style="opacity: 0.85;">';
                html += '  <div class="chat-req-header">';
                html += '    <img src="' + escapeHtml(req.avatar_url) + '" class="chat-avatar-img" style="width:34px; height:34px;">';
                html += '    <div class="chat-req-user">';
                html += '      <div class="chat-req-name">' + escapeHtml(req.name) + '</div>';
                html += '      <div class="chat-req-time">Sent ' + escapeHtml(req.time_ago) + ' &bull; Pending approval</div>';
                html += '    </div>';
                html += '  </div>';
                if (req.initial_message) {
                    html += '  <div class="chat-req-message">"' + escapeHtml(req.initial_message) + '"</div>';
                }
                html += '</div>';
            });
        }

        container.html(html);
    }

    // Accept Request Action
    $(document).on('click', '.btn-req-accept', function(e) {
        e.stopPropagation();
        var reqId = $(this).data('req-id');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.APP_BASE + 'messages/accept-request',
            type: 'POST',
            data: { request_id: reqId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    loadRequests();
                    loadConversationsAndPoll(true);
                    if (res.conversation_id) {
                        $('#tabBtnChats').click();
                        openConversation(res.conversation_id);
                    }
                } else {
                    showToast(res.message || 'Failed to accept request', 'error');
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Accept');
                }
            },
            error: function() {
                showToast('Network error while accepting request', 'error');
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Accept');
            }
        });
    });

    // Reject Request Action
    $(document).on('click', '.btn-req-reject', function(e) {
        e.stopPropagation();
        var reqId = $(this).data('req-id');

        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                title: 'Reject Chat Request?',
                message: 'Are you sure you want to decline this chat request? You can always accept requests later if re-sent.',
                type: 'danger',
                icon: 'fa-solid fa-user-xmark',
                confirmText: 'Yes, Reject',
                cancelText: 'Cancel',
                onConfirm: function() {
                    executeRejectRequest(reqId);
                }
            });
        } else if (confirm('Are you sure you want to reject this chat request?')) {
            executeRejectRequest(reqId);
        }
    });

    function executeRejectRequest(reqId) {
        $.ajax({
            url: window.APP_BASE + 'messages/reject-request',
            type: 'POST',
            data: { request_id: reqId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'info');
                    loadRequests();
                } else {
                    showToast(res.message || 'Failed to reject request', 'error');
                }
            },
            error: function() {
                showToast('Network error while rejecting request', 'error');
            }
        });
    }

    // 3. Render Conversations List
    function renderConversationsList(conversations) {
        var container = $('#conversationsListContainer');
        var filter = ($('#chatFilterInput').val() || '').toLowerCase().trim();

        var filtered = conversations.filter(function(c) {
            if (!filter) return true;
            var name = (c.user.name || '').toLowerCase();
            var email = (c.user.email || '').toLowerCase();
            return name.includes(filter) || email.includes(filter);
        });

        $('#chatsTabCount').text(conversations.length);

        if (filtered.length === 0) {
            if (filter) {
                container.html('<div style="text-align:center; padding:30px 10px; color:var(--text-muted); font-size:12.5px;">No conversations match "' + escapeHtml(filter) + '".</div>');
            } else {
                container.html('<div style="text-align:center; padding:40px 14px; color:var(--text-muted); font-size:12.5px;">' +
                    '<i class="fa-regular fa-comment-dots" style="font-size:26px; margin-bottom:8px; opacity:0.6;"></i>' +
                    '<div>No active chats yet.</div>' +
                    '<div style="font-size:11.5px; margin-top:4px;">Click <strong>+ New Chat</strong> to message a teammate.</div>' +
                '</div>');
            }
            return;
        }

        var html = '';
        filtered.forEach(function(c) {
            var isActive = (c.conversation_id === activeConversationId);
            var isUnread = (c.unread_count > 0);
            var isOnline = c.user.is_online;
            var isGroup = c.is_group || false;
            var lastSnippet = c.last_message ? (c.last_message.is_self ? 'You: ' : '') + c.last_message.text : 'Conversation started';

            html += '<div class="chat-conv-item ' + (isActive ? 'active ' : '') + (isUnread ? 'unread ' : '') + '" data-conv-id="' + c.conversation_id + '">';
            html += '  <div class="chat-avatar-wrap">';
            html += '    <img src="' + escapeHtml(c.user.avatar_url) + '" alt="' + escapeHtml(c.user.name) + '" class="chat-avatar-img">';
            if (isGroup) {
                html += '    <div class="chat-group-indicator" title="Team Group"><i class="fa-solid fa-users" style="font-size: 8px;"></i></div>';
            } else {
                html += '    <div class="chat-online-dot ' + (isOnline ? '' : 'offline') + '" title="' + (isOnline ? 'Active now' : 'Offline') + '"></div>';
            }
            html += '  </div>';
            html += '  <div class="chat-conv-meta">';
            html += '    <div class="chat-conv-top">';
            html += '      <span class="chat-conv-name">' + (isGroup ? '<i class="fa-solid fa-users" style="font-size: 11px; margin-right: 4px; color: var(--primary);"></i>' : '') + escapeHtml(c.user.name) + '</span>';
            html += '      <span class="chat-conv-time">' + escapeHtml(c.last_message ? c.last_message.time_formatted : '') + '</span>';
            html += '    </div>';
            html += '    <div class="chat-conv-bottom">';
            html += '      <span class="chat-conv-lastmsg">' + escapeHtml(lastSnippet) + '</span>';
            if (isUnread) {
                html += '    <span class="chat-unread-badge">' + c.unread_count + '</span>';
            }
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });

        container.html(html);

        // Auto-open conversation:
        // Priority 1: URL param (?c=123)
        // Priority 2: Last opened conversation from localStorage for this user
        // Priority 3: First available conversation in list if not on requests tab
        if (!activeConversationId && targetTab !== 'requests') {
            var convToAutoOpen = 0;
            if (targetConvId > 0 && $('.chat-conv-item[data-conv-id="' + targetConvId + '"]').length) {
                convToAutoOpen = targetConvId;
            } else {
                var savedConvId = parseInt(localStorage.getItem('hd_last_active_conv_' + CURRENT_USER_ID), 10);
                if (savedConvId > 0 && $('.chat-conv-item[data-conv-id="' + savedConvId + '"]').length) {
                    convToAutoOpen = savedConvId;
                } else if (filtered.length > 0 && !filter) {
                    convToAutoOpen = filtered[0].conversation_id;
                }
            }
            if (convToAutoOpen > 0) {
                targetConvId = 0;
                openConversation(convToAutoOpen);
            }
        }
    }

    $('#chatFilterInput').on('input', function() {
        renderConversationsList(conversationsCache);
    });

    // 4. Open a Conversation
    $(document).on('click', '.chat-conv-item', function() {
        var convId = $(this).data('conv-id');
        openConversation(convId);
    });

    function openConversation(convId) {
        if (!convId) return;

        // Reset typing status on previous conversation if user was typing
        if (isUserTyping && activeConversationId && activeConversationId !== convId) {
            notifyTypingStatus(activeConversationId, false);
            isUserTyping = false;
            clearTimeout(userTypingTimeout);
        }

        activeConversationId = convId;
        lastKnownMessageId = 0;

        // Save last opened conversation in localStorage for persistent recall
        localStorage.setItem('hd_last_active_conv_' + CURRENT_USER_ID, convId);
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', window.APP_BASE + 'messages?c=' + convId);
        }

        $('#chatTypingIndicator').hide();

        $('.chat-conv-item').removeClass('active');
        $('.chat-conv-item[data-conv-id="' + convId + '"]').addClass('active').removeClass('unread').find('.chat-unread-badge').remove();

        // Switch to conversation on mobile view
        $('#chatAppWrapper').addClass('mobile-conversation-active');

        $('#chatEmptyView').hide();
        $('#chatActiveView').css('display', 'flex');
        $('#chatMessagesList').html('<div style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin" style="font-size:20px; color:var(--primary);"></i><div style="margin-top:8px;">Loading messages...</div></div>');

        // Fetch Conversation History
        $.ajax({
            url: window.APP_BASE + 'messages/get-list',
            type: 'GET',
            data: { conversation_id: convId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    activeIsGroupCache = res.is_group || false;
                    activeGroupDataCache = res.group || null;
                    activeOtherUserCache = res.other_user;
                    updateActiveChatHeader(res.other_user, res.is_group, res.group);
                    renderMessagesHistory(res.messages, true);
                    if (res.other_user_last_read_timestamp) {
                        updateSeenStatuses(res.other_user_last_read_timestamp);
                    }
                    $('#chatMessageInput').focus();
                } else {
                    showToast(res.message || 'Unable to open conversation', 'error');
                }
            },
            error: function() {
                showToast('Failed to load conversation messages', 'error');
            }
        });
    }

    $('#btnBackToChatList').click(function() {
        $('#chatAppWrapper').removeClass('mobile-conversation-active');
    });

    function updateActiveChatHeader(user, isGroup, group) {
        if (!user) return;
        $('#activeUserName').text(user.name);
        $('#activeUserAvatar').attr('src', user.avatar_url);

        if (isGroup && group) {
            $('#activeUserOnlineDot').hide();
            $('#activeStatusPill').removeClass('offline');
            $('#activeStatusLabel').text(group.member_count + ' members').css('color', 'var(--text-muted)');

            // Update Info Panel for Group
            $('#infoPanelTitle').text('Group Details');
            $('#infoPanelDirectView').hide();
            $('#infoPanelGroupView').show();

            $('#groupInfoAvatar').attr('src', group.avatar_url);
            $('#groupInfoName').text(group.title);
            $('#groupInfoDesc').text(group.description || 'No description provided');
            $('#groupInfoMemberCount').text(group.member_count + (group.member_count === 1 ? ' member' : ' members'));
            $('#groupInfoCreatedDate').text(group.created || '-');

            // Render group members list
            var mHtml = '';
            if (group.members && group.members.length) {
                group.members.forEach(function(m) {
                    mHtml += '<div class="group-member-item">';
                    mHtml += '  <div class="group-member-avatar-wrap">';
                    mHtml += '    <img src="' + escapeHtml(m.avatar_url) + '" alt="' + escapeHtml(m.name) + '" class="group-member-avatar">';
                    mHtml += '    <div class="chat-online-dot ' + (m.is_online ? '' : 'offline') + '" title="' + (m.is_online ? 'Online' : 'Offline') + '"></div>';
                    mHtml += '  </div>';
                    mHtml += '  <div class="group-member-info">';
                    mHtml += '    <div class="group-member-name-row">';
                    mHtml += '      <span class="group-member-name">' + escapeHtml(m.name) + (m.is_self ? ' <span class="group-member-you-badge">You</span>' : '') + '</span>';
                    if (m.role === 'admin') {
                        mHtml += '    <span class="group-role-badge admin">Admin</span>';
                    }
                    mHtml += '    </div>';
                    mHtml += '    <div class="group-member-email">' + escapeHtml(m.email) + '</div>';
                    mHtml += '  </div>';
                    mHtml += '</div>';
                });
            } else {
                mHtml = '<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:10px;">No members found.</div>';
            }
            $('#groupMembersListContainer').html(mHtml);
        } else {
            // Direct 1-on-1 Chat
            $('#activeUserOnlineDot').show();
            if (user.is_online) {
                $('#activeUserOnlineDot').removeClass('offline');
                $('#activeStatusPill').removeClass('offline');
                $('#activeStatusLabel').text('Active now').css('color', '#10b981');
            } else {
                $('#activeUserOnlineDot').addClass('offline');
                $('#activeStatusPill').addClass('offline');
                $('#activeStatusLabel').text(user.last_seen_formatted ? 'Last seen ' + user.last_seen_formatted : 'Offline').css('color', 'var(--text-muted)');
            }

            // Update Info Panel for Direct Contact
            $('#infoPanelTitle').text('Contact Details');
            $('#infoPanelGroupView').hide();
            $('#infoPanelDirectView').show();

            $('#infoPanelAvatar').attr('src', user.avatar_url);
            $('#infoPanelName').text(user.name);
            $('#infoPanelEmail span').text(user.email);
            $('#infoPanelGender').text(user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : 'Male');
            $('#infoPanelMemberSince').text(user.member_since || '-');
            $('#infoPanelStatus').text(user.is_online ? 'Online' : 'Offline').css('color', user.is_online ? '#10b981' : 'var(--text-muted)');
        }
    }

    // 5. Render Message History
    function renderMessagesHistory(messages, shouldScrollBottom) {
        var container = $('#chatMessagesList');
        if (!messages || messages.length === 0) {
            container.html('<div style="text-align:center; padding:40px 10px; color:var(--text-muted); font-size:12.5px;">' +
                '<i class="fa-regular fa-paper-plane" style="font-size:24px; margin-bottom:8px; opacity:0.5;"></i>' +
                '<div>No messages yet in this conversation.</div>' +
                '<div style="font-size:11.5px; margin-top:3px;">Say hello and start collaborating!</div>' +
            '</div>');
            return;
        }

        var html = '';
        var lastDate = '';

        messages.forEach(function(m) {
            if (m.id > lastKnownMessageId) {
                lastKnownMessageId = m.id;
            }

            // Date Separator
            if (m.date_display && m.date_display !== lastDate) {
                html += '<div class="chat-date-separator"><span class="chat-date-pill">' + escapeHtml(m.date_display) + '</span></div>';
                lastDate = m.date_display;
            }

            html += buildMessageHtml(m);
        });

        container.html(html);

        if (shouldScrollBottom) {
            setTimeout(function() { scrollToBottom(true); }, 50);
        }
    }

    function renderReactionsHtml(reactions, msgId) {
        if (!reactions || reactions.length === 0) return '';
        var html = '';
        reactions.forEach(function(r) {
            var activeClass = r.has_reacted ? 'has-my-reaction' : '';
            var tooltip = (r.users && r.users.length) ? escapeHtml(r.users.join(', ')) : '';
            html += '<button type="button" class="chat-reaction-pill ' + activeClass + '" data-msg-id="' + msgId + '" data-reaction="' + escapeHtml(r.reaction) + '" title="' + tooltip + '">';
            html += '  <span class="reaction-emoji">' + escapeHtml(r.reaction) + '</span>';
            html += '  <span class="reaction-count">' + r.count + '</span>';
            html += '</button>';
        });
        return html;
    }

    function buildMessageHtml(m) {
        if (m.message_type === 'system') {
            return '<div class="chat-system-msg-row" data-msg-id="' + m.id + '">' +
                   '  <span class="chat-system-pill">' + escapeHtml(m.message) + '</span>' +
                   '</div>';
        }

        var rowClass = m.is_self ? 'outgoing' : 'incoming';
        var isDeleted = m.is_deleted;
        var html = '';

        html += '<div class="chat-msg-row ' + rowClass + '" data-msg-id="' + m.id + '">';

        if (!m.is_self) {
            html += '<img src="' + escapeHtml(m.sender_avatar) + '" class="chat-msg-avatar" alt="' + escapeHtml(m.sender_name) + '">';
        }

        html += '  <div class="chat-msg-content-wrap">';

        if (!m.is_self) {
            html += '    <span class="chat-msg-sender-name">' + escapeHtml(m.sender_name) + '</span>';
        }

        html += '    <div class="chat-bubble-wrap">';
        html += '      <div class="chat-bubble ' + (isDeleted ? 'deleted' : '') + '">';
        if (isDeleted) {
            html += '<i class="fa-solid fa-ban" style="margin-right:4px;"></i> <em>This message was deleted</em>';
        } else {
            // Render attachments (either array of attachments or single attachment fallback)
            var attList = (m.attachments && m.attachments.length) ? m.attachments : [];
            if (attList.length === 0 && m.attachment_url) {
                attList.push({
                    id: 0,
                    file_url: m.attachment_url,
                    file_name: m.attachment_name,
                    file_size: m.attachment_size,
                    file_size_formatted: m.attachment_size_formatted,
                    file_type: m.attachment_type || 'file'
                });
            }

            if (attList.length > 0) {
                var images = attList.filter(function(a) { return a.file_type === 'image'; });
                var files = attList.filter(function(a) { return a.file_type !== 'image'; });

                if (images.length === 1) {
                    var img = images[0];
                    html += '<div class="chat-bubble-attachment-image">';
                    html += '  <a href="' + escapeHtml(img.file_url) + '" target="_blank" rel="noopener noreferrer" class="chat-img-link">';
                    html += '    <img src="' + escapeHtml(img.file_url) + '" alt="' + escapeHtml(img.file_name || 'Photo') + '" class="chat-bubble-img" loading="lazy" />';
                    html += '  </a>';
                    html += '</div>';
                } else if (images.length > 1) {
                    var gridClass = 'grid-' + Math.min(images.length, 4);
                    if (images.length > 4) gridClass = 'grid-many';
                    html += '<div class="chat-bubble-attachment-grid ' + gridClass + '">';
                    images.forEach(function(img) {
                        html += '<a href="' + escapeHtml(img.file_url) + '" target="_blank" rel="noopener noreferrer" class="chat-img-link">';
                        html += '  <img src="' + escapeHtml(img.file_url) + '" alt="' + escapeHtml(img.file_name || 'Photo') + '" class="chat-bubble-img" loading="lazy" />';
                        html += '</a>';
                    });
                    html += '</div>';
                }

                if (files.length > 0) {
                    files.forEach(function(f) {
                        html += '<div class="chat-bubble-attachment-file">';
                        html += '  <div class="chat-file-icon"><i class="fa-solid fa-file-lines"></i></div>';
                        html += '  <div class="chat-file-info">';
                        html += '    <span class="chat-file-name" title="' + escapeHtml(f.file_name) + '">' + escapeHtml(f.file_name) + '</span>';
                        html += '    <span class="chat-file-size">' + escapeHtml(f.file_size_formatted) + '</span>';
                        html += '  </div>';
                        html += '  <a href="' + escapeHtml(f.file_url) + '" download="' + escapeHtml(f.file_name) + '" class="btn-file-download" title="Download"><i class="fa-solid fa-download"></i></a>';
                        html += '</div>';
                    });
                }
            }

            if (m.message && m.message.trim() !== '') {
                html += '<span class="chat-msg-text">' + escapeHtml(m.message) + '</span>';
            }
        }
        html += '      </div>'; // end chat-bubble

        // Microsoft Teams Action Toolbar (Reactions + Edit + Delete)
        if (!isDeleted) {
            html += '      <div class="chat-msg-actions">';
            html += '        <div class="chat-teams-reactions-bar">';
            html += '          <button type="button" class="btn-react-quick" data-msg-id="' + m.id + '" data-reaction="👍" title="Like">👍</button>';
            html += '          <button type="button" class="btn-react-quick" data-msg-id="' + m.id + '" data-reaction="❤️" title="Love">❤️</button>';
            html += '          <button type="button" class="btn-react-quick" data-msg-id="' + m.id + '" data-reaction="😂" title="Laugh">😂</button>';
            html += '          <button type="button" class="btn-react-quick" data-msg-id="' + m.id + '" data-reaction="😮" title="Surprised">😮</button>';
            html += '          <button type="button" class="btn-react-quick" data-msg-id="' + m.id + '" data-reaction="👏" title="Applause">👏</button>';
            html += '          <button type="button" class="btn-react-custom" data-msg-id="' + m.id + '" title="More reactions"><i class="fa-regular fa-face-smile"></i></button>';
            html += '        </div>';
            if (m.is_self) {
                html += '        <div class="chat-msg-action-divider"></div>';
                html += '        <div class="chat-msg-ops-bar">';
                html += '          <button type="button" class="btn-msg-edit" data-msg-id="' + m.id + '" title="Edit message"><i class="fa-solid fa-pen"></i></button>';
                html += '          <button type="button" class="btn-msg-delete" data-msg-id="' + m.id + '" title="Delete message"><i class="fa-regular fa-trash-can"></i></button>';
                html += '        </div>';
            }
            html += '      </div>';
        }
        html += '    </div>'; // end chat-bubble-wrap

        // Message Reactions Container
        html += '    <div class="chat-msg-reactions" data-msg-id="' + m.id + '">';
        if (!isDeleted && m.reactions && m.reactions.length) {
            html += renderReactionsHtml(m.reactions, m.id);
        }
        html += '    </div>';

        html += '    <div class="chat-bubble-footer">';
        if (m.is_edited && !isDeleted) {
            html += '      <span class="chat-msg-edited" title="' + (m.edited_at ? 'Edited at ' + m.edited_at : 'Edited') + '"><i class="fa-solid fa-pencil" style="font-size: 8px; margin-right: 2px;"></i>Edited</span>';
        }
        html += '      <span class="chat-msg-time">' + escapeHtml(m.time) + '</span>';
        if (m.is_self && !isDeleted) {
            var msgTs = m.timestamp || 0;
            if (m.is_seen) {
                html += '    <span class="chat-seen-status seen" title="Seen" data-msg-timestamp="' + msgTs + '"><i class="fa-solid fa-check-double"></i></span>';
            } else {
                html += '    <span class="chat-seen-status delivered" title="Delivered" data-msg-timestamp="' + msgTs + '"><i class="fa-solid fa-check"></i></span>';
            }
        }
        html += '    </div>';

        html += '  </div>';
        html += '</div>';

        return html;
    }

    // 6. Inline Message Edit Handlers
    $(document).on('click', '.btn-msg-edit', function(e) {
        e.stopPropagation();
        var msgId = $(this).data('msg-id');
        var $row = $('.chat-msg-row[data-msg-id="' + msgId + '"]');
        var $bubble = $row.find('.chat-bubble');
        if ($row.hasClass('is-editing') || $bubble.hasClass('deleted')) return;

        // Cancel any other inline edits
        $('.chat-msg-row.is-editing').each(function() {
            cancelInlineEdit($(this).data('msg-id'));
        });

        var currentText = $row.find('.chat-msg-text').text() || '';
        $row.addClass('is-editing');
        $row.data('original-text', currentText);

        var editHtml = '<div class="chat-inline-editor">' +
            '<textarea class="chat-inline-edit-input" maxlength="4000">' + escapeHtml(currentText) + '</textarea>' +
            '<div class="chat-inline-edit-actions">' +
            '  <button type="button" class="btn-inline-cancel" data-msg-id="' + msgId + '">Cancel</button>' +
            '  <button type="button" class="btn-inline-save" data-msg-id="' + msgId + '"><i class="fa-solid fa-check"></i> Save</button>' +
            '</div>' +
            '</div>';

        $bubble.data('cached-html', $bubble.html());
        $bubble.html(editHtml);

        var $textarea = $bubble.find('.chat-inline-edit-input');
        $textarea.focus();
        var val = $textarea.val();
        $textarea.val('').val(val);
        $textarea.css('height', 'auto').css('height', Math.min($textarea[0].scrollHeight, 120) + 'px');
    });

    $(document).on('click', '.btn-inline-cancel', function(e) {
        e.stopPropagation();
        cancelInlineEdit($(this).data('msg-id'));
    });

    $(document).on('click', '.btn-inline-save', function(e) {
        e.stopPropagation();
        saveInlineEdit($(this).data('msg-id'));
    });

    $(document).on('keydown', '.chat-inline-edit-input', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            var msgId = $(this).closest('.chat-msg-row').data('msg-id');
            saveInlineEdit(msgId);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            var msgId = $(this).closest('.chat-msg-row').data('msg-id');
            cancelInlineEdit(msgId);
        }
    });

    function cancelInlineEdit(msgId) {
        var $row = $('.chat-msg-row[data-msg-id="' + msgId + '"]');
        var $bubble = $row.find('.chat-bubble');
        var cached = $bubble.data('cached-html');
        if (cached) {
            $bubble.html(cached);
        }
        $row.removeClass('is-editing');
    }

    function saveInlineEdit(msgId) {
        var $row = $('.chat-msg-row[data-msg-id="' + msgId + '"]');
        var $bubble = $row.find('.chat-bubble');
        var newText = ($bubble.find('.chat-inline-edit-input').val() || '').trim();
        var originalText = $row.data('original-text') || '';

        if (!newText) {
            showToast('Message cannot be empty', 'warning');
            return;
        }

        if (newText === originalText) {
            cancelInlineEdit(msgId);
            return;
        }

        var $saveBtn = $bubble.find('.btn-inline-save');
        $saveBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.APP_BASE + 'messages/edit-msg',
            type: 'POST',
            data: {
                message_id: msgId,
                message: newText
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $row.removeClass('is-editing');
                    $bubble.html('<span class="chat-msg-text">' + escapeHtml(res.message) + '</span>');
                    var $footer = $row.find('.chat-bubble-footer');
                    if ($footer.find('.chat-msg-edited').length === 0) {
                        $footer.prepend('<span class="chat-msg-edited" title="Edited"><i class="fa-solid fa-pencil" style="font-size: 8px; margin-right: 2px;"></i>Edited</span>');
                    }
                    showToast('Message edited successfully', 'success');
                    loadConversationsAndPoll(false);
                } else {
                    $saveBtn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Save');
                    showToast(res.message || 'Failed to edit message', 'error');
                }
            },
            error: function(xhr) {
                $saveBtn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Save');
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error updating message';
                showToast(err, 'error');
            }
        });
    }

    // 7. Delete Message Action
    $(document).on('click', '.btn-msg-delete', function(e) {
        e.stopPropagation();
        var msgId = $(this).data('msg-id');

        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                title: 'Delete Message?',
                message: 'Are you sure you want to delete this message? This action will mark it as deleted for everyone.',
                type: 'danger',
                icon: 'fa-regular fa-trash-can',
                confirmText: 'Yes, Delete',
                cancelText: 'Cancel',
                onConfirm: function() {
                    executeDeleteMessage(msgId);
                }
            });
        } else if (confirm('Are you sure you want to delete this message?')) {
            executeDeleteMessage(msgId);
        }
    });

    function executeDeleteMessage(msgId) {
        $.ajax({
            url: window.APP_BASE + 'messages/delete-msg',
            type: 'POST',
            data: { message_id: msgId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var $row = $('.chat-msg-row[data-msg-id="' + msgId + '"]');
                    $row.find('.chat-bubble').addClass('deleted').html('<i class="fa-solid fa-ban" style="margin-right:4px;"></i> <em>This message was deleted</em>');
                    $row.find('.chat-msg-actions').remove();
                    showToast('Message deleted', 'info');
                } else {
                    showToast(res.message || 'Failed to delete message', 'error');
                }
            },
            error: function() {
                showToast('Network error while deleting message', 'error');
            }
        });
    }

    // ==========================================
    // iPhone iOS Style Full Emoji Picker System
    // ==========================================
    var emojiCategories = {
        smileys: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','🫠','😉','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','😋','😛','😜','🤪','😝','🤑','🤗','🫣','🤭','🫢','🤫','🤔','🫡','🤐','🤨','😐','😑','😶','🫥','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🤧','🥵','🥶','🥴','😵','😵‍💫','🤯','🤠','🥳','🥸','😎','🤓','🧐','😕','🫤','😟','🙁','😮','😯','😲','😳','🥺','🥹','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖'],
        people: ['👋','🤚','🖐️','✋','🖖','🫱','🫲','🫳','🫴','🫷','🫸','👌','🤌','🤏','✌️','🤞','🫰','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','🫵','👍','👎','✊','👊','🤛','🤜','👏','🙌','🫶','👐','🤲','🤝','🙏','✍️','💅','🤳','💪','🦾','🦿','🦵','🦶','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁️','👅','👄','🫦','👶','🧒','👦','👧','🧑','👱','👨','🧔','🧔‍♂️','🧔‍♀️','👩','🧓','👴','👵','🙍','🙎','🙅','🙆','💁','🙋','🧏','🙇','🤦','🤷','👮','🕵️','💂','👷','🤴','👸','👳','👲','🧕','🤵','👰','🤰','🤱','👼','🎅','🤶','🦸','🦹','🧙','🧚','🧛','🧜','🧝','🧞','🧟'],
        nature: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐻‍❄️','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🪳','🦟','🦗','🕷️','🕸️','🦂','🐢','🐍','🦎','🦖','🦕','🐙','🦑','🦐','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐅','🐆','🦓','🦍','🦧','🦣','🐘','🦛','🦏','🐪','🐫','🦒','🦘','🦬','🐃','🐂','🐄','🐎','🐖','🐏','🐑','🦙','🐐','🦌','🐕','🐩','🐈','🐈‍⬛','🐓','🦃','🦚','🦜','🦢','🦩','🕊️','🐇','🦝','🦨','🦡','🦫','🦦','🦥','🐁','🐀','🐿️','🦔','🌲','🌳','🌴','🌵','🌾','🌿','☘️','🍀','🍁','🍂','🍃','🍄','🌸','🏵️','🌹','🥀','🌺','🌻','🌼','🌷','🪷','🌱','🪴','☀️','🌤️','⛅','🌥️','☁️','🌦️','🌧️','⛈️','🌩️','🌨️','❄️','☃️','⛄','🌬️','💨','🌪️','🌫️','🌈','⭐','🌟','✨','⚡','💥','🔥','💧','🌊'],
        food: ['🍏','🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🫒','🧄','🧅','🥔','🍠','🥐','🥯','🍞','🥖','🥨','🧀','🥚','🍳','🧈','🥞','🧇','🥓','🥩','🍗','🍖','🌭','🍔','🍟','🍕','🫓','🥪','🥙','🧆','🌮','🌯','🫔','🥗','🥘','🫕','🥫','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🦪','🍤','🍙','🍚','🍘','🍥','🥠','🥮','🍢','🍡','🍧','🍨','🍦','🥧','🧁','🍰','🎂','🍮','🍭','🍬','🍫','🍿','🍩','🍪','🌰','🥜','🍯','🥛','🍼','☕','🫖','🍵','🧃','🥤','🧋','🍶','🍺','🍻','🥂','🍷','🥃','🍸','🍹','🧉','🍾','🧊'],
        activities: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🥏','🎱','🪀','🏓','🏸','🏒','🏑','🥍','🏏','🪃','🥅','⛳','🪁','🏹','🎣','🤿','🥊','🥋','🎽','🛹','🛼','🛷','⛸️','🥌','🎿','⛷️','🏂','🪂','🏋️','🤼','🤸','⛹️','🤺','🤾','🏌️','🏇','🧘','🏄','🏊','🤽','🚣','🧗','🚵','🚴','🏆','🥇','🥈','🥉','🏅','🎖️','🏵️','🎗️','🎫','🎟️','🎪','🤹','🎭','🩰','🎨','🎬','🎤','🎧','🎼','🎹','🥁','🎷','🎺','🎸','🪕','🎻','🎲','♟️','🎯','🎳','🎮','🎰'],
        travel: ['🚗','🚕','🚙','🚌','🚎','🏎️','🚓','🚑','🚒','🚐','🛻','🚚','🚛','🚜','🛴','🚲','🛵','🏍️','🛺','🚨','🚔','🚍','🚘','🚖','🚡','🚠','🚟','🚃','🚋','🚞','🚝','🚄','🚅','🚈','🚂','🚆','🚇','🚊','🚉','🚁','🛩️','✈️','🛫','🛬','💺','🛰️','🚀','🛸','⛵','🛶','🚤','🛥️','🛳️','⛴️','🚢','⚓','🛟','⛽','🚦','🚥','🗺️','🗿','🗽','🗼','🏰','🏯','🏟️','🎡','🎢','🎠','⛲','⛱️','🏖️','🏝️','🏜️','🌋','⛰️','🏔️','🗻','🏕️','⛺','🛖','🏠','🏡'],
        objects: ['⌚','📱','📲','💻','⌨️','🖥️','🖨️','🖱️','🕹️','💽','💾','💿','📀','📼','📷','📸','📹','🎥','📽️','🎞️','📞','☎️','📟','📠','📺','📻','🎙️','🎚️','🎛️','🧭','⏱️','⏲️','⏰','🕰️','⌛','⏳','📡','🔋','🪫','🔌','💡','🔦','🕯️','🧯','💸','💵','💴','💶','💷','🪙','💰','💳','💎','⚖️','🪜','🧰','🪛','🔧','🔨','⚒️','🛠️','⛏️','🪚','🔩','⚙️','🧱','⛓️','🧲','🔫','💣','🧨','🪓','🔪','🗡️','⚔️','🛡️','🚬','⚰️','🪦','⚱️','🏺','🔮','📿','🧿','💈','⚗️','🔭','🔬','🩹','🩺','💊','💉','🩸','🧬','🦠','🧫','🧪','🌡️','🧹','🪠','🪣','🧴','🧻','🧼','🧽','🪥','🪒','🔑','🗝️','🚪','🪑','🛌','🛏️','🛋️','🚿','🛁','🛀','🚽','🪞','🪟','🧳','📦','✉️','📩','📨','📧','💌','📮','🗳️','✏️','✒️','🖋️','🖊️','🖌️','🖍️','📝','💼','📁','📂','🗂️','📅','📆','🗒️','🗓️','📇','📈','📉','📊','📋','📌','📍','📎','🖇️','📏','📐','✂️','🗃️','🗄️','🗑️','🔒','🔓','🔏','🔐'],
        symbols: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','🛐','⛎','♈','♉','♊','♋','♌','♍','♎','♏','♐','♑','♒','♓','🆔','⚛️','🈳','🈹','☢️','☣️','📴','📳','🈶','🈚','🈸','🈺','🈷️','✴️','🆚','💮','🉐','㊙️','㊗️','🈴','🈵','🈲','🅰️','🅱️','🆎','🆑','🅾️','🆘','❌','⭕','🛑','⛔','📛','🚫','💯','💢','♨️','🚷','🚯','🚳','🚱','🔞','📵','🚭','❗','❕','❓','❔','‼️','⁉️','🔅','🔆','〽️','⚠️','🚸','🔱','⚜️','🔰','♻️','✅','🈯','💹','❇️','✳️','❎','🌐','💠','Ⓜ️','🌀','💤','🏧','🚾','♿','🅿️','🈳','🈂️','🛂','🛃','🛄','🛅','🚹','🚺','🚼','⚧️','🚻','🚮','🎦','📶','🈁','🆖','🆗','🆙','🆒','🆕','🆓','🔟','🔢','#️⃣','*️⃣','0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔴','🟠','🟡','🟢','🔵','🟣','🟤','⚫','⚪','🟥','🟧','🟨','🟩','🟦','🟪','🟫','⬛','⬜','🏁','🚩','🎌','🏴','🏳️','🏳️‍🌈','🏳️‍⚧️','🏴‍☠️']
    };

    var activeEmojiReactionMsgId = null;

    function renderEmojis(categoryOrList) {
        var list = [];
        if (Array.isArray(categoryOrList)) {
            list = categoryOrList;
        } else {
            list = emojiCategories[categoryOrList] || emojiCategories.smileys;
        }
        var html = '';
        if (list.length === 0) {
            html = '<div style="grid-column: span 8; text-align: center; padding: 20px 0; color: var(--text-muted); font-size: 12px;">No emojis found</div>';
        } else {
            list.forEach(function(em) {
                html += '<span class="emoji-item" data-emoji="' + em + '">' + em + '</span>';
            });
        }
        $('#chatEmojiList').html(html);
    }
    renderEmojis('smileys');

    $('#btnToggleEmoji').click(function(e) {
        e.stopPropagation();
        activeEmojiReactionMsgId = null;
        $('#chatEmojiSearchInput').val('');
        $('#btnClearEmojiSearch').hide();
        $('.emoji-tab-btn').removeClass('active');
        $('.emoji-tab-btn[data-category="smileys"]').addClass('active');
        renderEmojis('smileys');
        $('#chatEmojiPopover').toggle();
        if ($('#chatEmojiPopover').is(':visible')) {
            $('#chatEmojiSearchInput').focus();
        }
    });

    $(document).on('click', '.emoji-tab-btn', function(e) {
        e.stopPropagation();
        $('#chatEmojiSearchInput').val('');
        $('#btnClearEmojiSearch').hide();
        $('.emoji-tab-btn').removeClass('active');
        $(this).addClass('active');
        renderEmojis($(this).data('category'));
    });

    $('#chatEmojiSearchInput').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        if (q.length > 0) {
            $('#btnClearEmojiSearch').show();
            $('.emoji-tab-btn').removeClass('active');
            var matched = [];
            Object.keys(emojiCategories).forEach(function(cat) {
                emojiCategories[cat].forEach(function(em) {
                    if (em.indexOf(q) !== -1 || matched.length < 96) {
                        matched.push(em);
                    }
                });
            });
            matched = Array.from(new Set(matched));
            renderEmojis(matched);
        } else {
            $('#btnClearEmojiSearch').hide();
            var activeCat = $('.emoji-tab-btn.active').data('category') || 'smileys';
            renderEmojis(activeCat);
        }
    });

    $('#btnClearEmojiSearch').click(function(e) {
        e.stopPropagation();
        $('#chatEmojiSearchInput').val('').focus();
        $(this).hide();
        $('.emoji-tab-btn[data-category="smileys"]').click();
    });

    $(document).on('click', '.emoji-item', function(e) {
        e.stopPropagation();
        var emoji = $(this).data('emoji');

        if (activeEmojiReactionMsgId) {
            executeToggleReaction(activeEmojiReactionMsgId, emoji);
            activeEmojiReactionMsgId = null;
            $('#chatEmojiPopover').hide();
            return;
        }

        var $input = $('#chatMessageInput');
        var val = $input.val();
        var pos = $input[0].selectionStart !== undefined ? $input[0].selectionStart : val.length;
        var newVal = val.slice(0, pos) + emoji + val.slice(pos);
        $input.val(newVal).focus();
        $input[0].setSelectionRange(pos + emoji.length, pos + emoji.length);
        $input.trigger('input');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#chatEmojiPopover, #btnToggleEmoji, .btn-react-custom').length) {
            $('#chatEmojiPopover').hide();
            activeEmojiReactionMsgId = null;
        }
    });

    // ==========================================
    // Microsoft Teams Style Message Reactions
    // ==========================================
    $(document).on('click', '.btn-react-quick, .chat-reaction-pill', function(e) {
        e.stopPropagation();
        var msgId = $(this).data('msg-id');
        var reaction = $(this).data('reaction');
        executeToggleReaction(msgId, reaction);
    });

    $(document).on('click', '.btn-react-custom', function(e) {
        e.stopPropagation();
        var msgId = $(this).data('msg-id');
        activeEmojiReactionMsgId = msgId;
        $('#chatEmojiSearchInput').val('');
        $('#btnClearEmojiSearch').hide();
        $('.emoji-tab-btn').removeClass('active');
        $('.emoji-tab-btn[data-category="smileys"]').addClass('active');
        renderEmojis('smileys');
        $('#chatEmojiPopover').show();
        $('#chatEmojiSearchInput').focus();
    });

    function executeToggleReaction(msgId, reaction) {
        if (!msgId || !reaction) return;
        $.ajax({
            url: window.APP_BASE + 'messages/toggle-reaction',
            type: 'POST',
            data: {
                message_id: msgId,
                reaction: reaction
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var $row = $('.chat-msg-row[data-msg-id="' + msgId + '"]');
                    var $reactionsContainer = $row.find('.chat-msg-reactions');
                    $reactionsContainer.html(renderReactionsHtml(res.reactions, msgId));
                } else if (res.message) {
                    showToast(res.message, 'warning');
                }
            },
            error: function() {
                showToast('Unable to update reaction', 'error');
            }
        });
    }

    // ==========================================
    // Multi-File Attachment System (Up to 10 files)
    // ==========================================
    var selectedAttachmentFiles = [];

    function updateAttachmentPreview() {
        var $previewContainer = $('#chatAttachmentPreview');
        var $itemsContainer = $('#chatAttachmentItems');
        var count = selectedAttachmentFiles.length;

        $('#chatAttachmentCountLabel').html('<i class="fa-solid fa-paperclip"></i> Attached files (' + count + '/10)');

        if (count === 0) {
            $previewContainer.slideUp(150);
            $itemsContainer.empty();
            checkSendButtonState();
            return;
        }

        $previewContainer.slideDown(150);
        $itemsContainer.empty();

        selectedAttachmentFiles.forEach(function(item) {
            var f = item.file;
            var isImg = f.type.startsWith('image/');
            var sizeStr = (f.size >= 1048576) ? (f.size / 1048576).toFixed(1) + ' MB' : Math.round(f.size / 1024) + ' KB';

            var itemHtml = '' +
                '<div class="chat-attachment-item" data-id="' + item.id + '">' +
                '  <div class="chat-attachment-item-thumb">';

            if (isImg && item.previewUrl) {
                itemHtml += '<img src="' + item.previewUrl + '" class="chat-attachment-item-img" alt="preview" />';
            } else {
                itemHtml += '<i class="fa-solid fa-file-lines chat-attachment-item-icon"></i>';
            }

            itemHtml += '  </div>' +
                '  <div class="chat-attachment-item-meta">' +
                '    <div class="chat-attachment-item-name" title="' + escapeHtml(f.name) + '">' + escapeHtml(f.name) + '</div>' +
                '    <div class="chat-attachment-item-size">' + sizeStr + '</div>' +
                '  </div>' +
                '  <button type="button" class="btn-remove-attachment-item" data-id="' + item.id + '" title="Remove file">&times;</button>' +
                '</div>';

            $itemsContainer.append(itemHtml);
        });

        checkSendButtonState();
    }

    function addFilesToAttachment(fileList) {
        if (!fileList || fileList.length === 0) return;

        var availableSlots = 10 - selectedAttachmentFiles.length;
        if (availableSlots <= 0) {
            showToast('Maximum limit of 10 files reached.', 'warning');
            return;
        }

        var filesArray = Array.from(fileList);
        if (filesArray.length > availableSlots) {
            showToast('Only ' + availableSlots + ' file(s) added. Maximum 10 files allowed.', 'warning');
            filesArray = filesArray.slice(0, availableSlots);
        }

        filesArray.forEach(function(file) {
            if (file.size > 25 * 1024 * 1024) {
                showToast("File '" + file.name + "' is larger than 25MB.", 'warning');
                return;
            }

            var item = {
                id: 'att_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6),
                file: file,
                previewUrl: null
            };

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    item.previewUrl = e.target.result;
                    updateAttachmentPreview();
                };
                reader.readAsDataURL(file);
            }

            selectedAttachmentFiles.push(item);
        });

        updateAttachmentPreview();
    }

    $('#btnAttachFile, #btnAddMoreAttachments').click(function() {
        if (selectedAttachmentFiles.length >= 10) {
            showToast('Maximum 10 files can be attached at a time.', 'warning');
            return;
        }
        $('#chatFileInput').click();
    });

    $('#chatFileInput').on('change', function() {
        addFilesToAttachment(this.files);
        $(this).val('');
    });

    $(document).on('click', '.btn-remove-attachment-item', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        selectedAttachmentFiles = selectedAttachmentFiles.filter(function(item) {
            return item.id !== id;
        });
        updateAttachmentPreview();
    });

    $('#btnClearAllAttachments').click(function(e) {
        e.stopPropagation();
        clearAllAttachments();
    });

    function clearAllAttachments() {
        selectedAttachmentFiles = [];
        $('#chatFileInput').val('');
        updateAttachmentPreview();
    }

    function checkSendButtonState() {
        var len = ($('#chatMessageInput').val() || '').trim().length;
        $('#btnSendMessage').prop('disabled', len === 0 && selectedAttachmentFiles.length === 0);
    }

    // ==========================================
    // Live Typing Notification & Throttling
    // ==========================================
    var isUserTyping = false;
    var userTypingTimeout = null;

    function notifyTypingStatus(convId, isTyping) {
        if (!convId) return;
        $.ajax({
            url: window.APP_BASE + 'messages/set-typing',
            type: 'POST',
            data: {
                conversation_id: convId,
                is_typing: isTyping ? 1 : 0
            },
            dataType: 'json'
        });
    }

    // ==========================================
    // Send Message (Text, Emojis, and Up to 10 Files)
    // ==========================================
    function sendMessage() {
        var $input = $('#chatMessageInput');
        var text = ($input.val() || '').trim();

        if ((!text && selectedAttachmentFiles.length === 0) || activeConversationId <= 0) return;

        // Instantly stop typing indicator on send
        clearTimeout(userTypingTimeout);
        if (isUserTyping) {
            isUserTyping = false;
            notifyTypingStatus(activeConversationId, false);
        }

        var $btn = $('#btnSendMessage');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        var formData = new FormData();
        formData.append('conversation_id', activeConversationId);
        formData.append('message', text);

        selectedAttachmentFiles.forEach(function(item) {
            formData.append('attachments[]', item.file);
        });

        $.ajax({
            url: window.APP_BASE + 'messages/post-msg',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i>');
                if (res.success && res.message) {
                    $input.val('').trigger('input');
                    clearAllAttachments();
                    appendIncomingMessage(res.message);
                    scrollToBottom(true);
                    loadConversationsAndPoll(false);
                } else {
                    showToast(res.message || 'Unable to send message', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i>');
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to send message';
                showToast(err, 'error');
            }
        });
    }

    $('#btnSendMessage').click(sendMessage);

    $('#chatMessageInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    $('#chatMessageInput').on('input keydown', function(e) {
        checkSendButtonState();

        // Auto-expand textarea up to 130px
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 130) + 'px';

        if (!activeConversationId) return;
        if (e.key === 'Enter' && !e.shiftKey) return;

        var textLen = ($(this).val() || '').trim().length;
        if (textLen > 0) {
            if (!isUserTyping) {
                isUserTyping = true;
                notifyTypingStatus(activeConversationId, true);
            }

            clearTimeout(userTypingTimeout);
            userTypingTimeout = setTimeout(function() {
                if (isUserTyping) {
                    isUserTyping = false;
                    notifyTypingStatus(activeConversationId, false);
                }
            }, 3000);
        } else if (isUserTyping) {
            isUserTyping = false;
            clearTimeout(userTypingTimeout);
            notifyTypingStatus(activeConversationId, false);
        }
    });

    // Dynamic Live Seen Status Updater
    function updateSeenStatuses(otherLastReadTs) {
        if (!otherLastReadTs || otherLastReadTs <= 0) return;
        $('#chatMessagesList .chat-seen-status.delivered').each(function() {
            var msgTs = parseInt($(this).attr('data-msg-timestamp'), 10) || 0;
            if (msgTs > 0 && msgTs <= otherLastReadTs) {
                $(this).removeClass('delivered').addClass('seen')
                    .attr('title', 'Seen')
                    .html('<i class="fa-solid fa-check-double"></i>');
            }
        });
    }

    function appendIncomingMessage(m) {
        if (m.id <= lastKnownMessageId) return;
        lastKnownMessageId = m.id;

        var container = $('#chatMessagesList');
        if (container.find('.fa-paper-plane').length > 0) {
            container.empty();
        }

        if (m.date_display) {
            var lastDatePill = container.find('.chat-date-pill').last().text().trim();
            if (lastDatePill !== m.date_display) {
                container.append('<div class="chat-date-separator"><span class="chat-date-pill">' + escapeHtml(m.date_display) + '</span></div>');
            }
        }

        container.append(buildMessageHtml(m));
        scrollToBottom(false);
    }

    // 8. Lightweight Live AJAX Polling (Runs every 2s without reloading page)
    function loadConversationsAndPoll(isManual) {
        if (isPolling) return;
        isPolling = true;

        $.ajax({
            url: window.APP_BASE + 'messages/get-updates',
            type: 'GET',
            data: {
                conversation_id: activeConversationId,
                last_message_id: lastKnownMessageId
            },
            dataType: 'json',
            success: function(res) {
                isPolling = false;
                if (res.success) {
                    conversationsCache = res.conversations || [];
                    renderConversationsList(conversationsCache);
                    updateRequestsBadge(res.pending_requests_count);

                    // Check if other conversations received new unread messages
                    var prevUnreadTotal = window.lastTotalUnreadCount;
                    window.lastTotalUnreadCount = res.total_unread_count || 0;
                    if (prevUnreadTotal !== undefined && res.total_unread_count > prevUnreadTotal) {
                        if (typeof window.playNotificationSound === 'function') {
                            window.playNotificationSound();
                        }
                        if (res.conversations && res.conversations.length) {
                            var otherUnreadConv = res.conversations.find(function(c) {
                                return c.unread_count > 0 && c.conversation_id !== activeConversationId;
                            });
                            if (otherUnreadConv) {
                                if (typeof window.showInAppTeamsToast === 'function') {
                                    window.showInAppTeamsToast({
                                        conversation_id: otherUnreadConv.conversation_id,
                                        sender_name: otherUnreadConv.user ? otherUnreadConv.user.name : 'Teammate',
                                        sender_avatar: otherUnreadConv.user ? otherUnreadConv.user.avatar_url : null,
                                        message: otherUnreadConv.last_message ? otherUnreadConv.last_message.text : 'New message received'
                                    });
                                }
                            }
                        }
                    }

                    // Auto-refresh requests list if count changed or Requests tab is currently open
                    var prevReqCount = window.lastPendingRequestsCount;
                    window.lastPendingRequestsCount = res.pending_requests_count;
                    if (prevReqCount !== undefined && res.pending_requests_count > prevReqCount) {
                        if (typeof window.playNotificationSound === 'function') {
                            window.playNotificationSound();
                        }
                        if (typeof window.showInAppTeamsToast === 'function') {
                            window.showInAppTeamsToast({
                                is_request: true,
                                sender_name: 'Chat Request',
                                message: 'You have a new incoming chat request.'
                            });
                        }
                        loadRequests();
                    } else if (prevReqCount !== undefined && prevReqCount !== res.pending_requests_count) {
                        loadRequests();
                    } else if ($('#tabBtnRequests').hasClass('active') && (!window.lastReqListRefresh || (Date.now() - window.lastReqListRefresh > 4000))) {
                        window.lastReqListRefresh = Date.now();
                        loadRequests();
                    }

                    // Update Top Header Notification Badge (ONLY in top header, NEVER on left dock)
                    var totalBadge = (res.pending_requests_count || 0) + (res.total_unread_count || 0);
                    var $headerBadge = $('#headerNotificationBadge');
                    $('#dockChatBadge').empty().attr('data-count', '0').removeClass('show').addClass('d-none').css('display', 'none');
                    if (totalBadge > 0) {
                        $headerBadge.text(totalBadge > 99 ? '99+' : totalBadge)
                            .attr('data-count', totalBadge)
                            .removeClass('d-none')
                            .addClass('show')
                            .css('display', 'inline-flex');
                    } else {
                        $headerBadge.empty()
                            .attr('data-count', '0')
                            .removeClass('show')
                            .addClass('d-none')
                            .css('display', 'none');
                    }

                    // Append new messages if conversation is open
                    if (res.new_messages && res.new_messages.length > 0) {
                        res.new_messages.forEach(function(msg) {
                            appendIncomingMessage(msg);

                            // Trigger Teams chime & In-App Helpdesk Toast on incoming message
                            if (!msg.is_self) {
                                if (typeof window.playNotificationSound === 'function') {
                                    window.playNotificationSound();
                                }
                                var isHidden = document.hidden || !document.hasFocus();
                                if (isHidden) {
                                    if (typeof window.showInAppTeamsToast === 'function') {
                                        window.showInAppTeamsToast({
                                            conversation_id: msg.conversation_id,
                                            sender_name: msg.sender_name,
                                            sender_avatar: msg.sender_avatar,
                                            message: msg.message || 'Sent an attachment'
                                        });
                                    }
                                }
                            }
                        });
                    }

                    // Live update edited/deleted messages without refresh
                    if (res.updated_messages && res.updated_messages.length > 0) {
                        res.updated_messages.forEach(function(msg) {
                            var $row = $('.chat-msg-row[data-msg-id="' + msg.id + '"]');
                            if ($row.length && !$row.hasClass('is-editing')) {
                                var $bubble = $row.find('.chat-bubble');
                                if (msg.is_deleted) {
                                    $bubble.addClass('deleted').html('<i class="fa-solid fa-ban" style="margin-right:4px;"></i> <em>This message was deleted</em>');
                                    $row.find('.chat-msg-actions').remove();
                                } else {
                                    $bubble.find('.chat-msg-text').text(msg.message);
                                    if (msg.is_edited) {
                                        var $footer = $row.find('.chat-bubble-footer');
                                        if ($footer.find('.chat-msg-edited').length === 0) {
                                            $footer.prepend('<span class="chat-msg-edited" title="' + (msg.edited_at ? 'Edited at ' + msg.edited_at : 'Edited') + '"><i class="fa-solid fa-pencil" style="font-size: 8px; margin-right: 2px;"></i>Edited</span>');
                                        }
                                    }
                                    // Live update message reactions
                                    var $reactionsContainer = $row.find('.chat-msg-reactions');
                                    if ($reactionsContainer.length) {
                                        $reactionsContainer.html(renderReactionsHtml(msg.reactions, msg.id));
                                    }
                                }
                            }
                        });
                    }

                    // Live update seen receipts for delivered messages
                    if (res.other_user_last_read_timestamp && res.other_user_last_read_timestamp > 0) {
                        updateSeenStatuses(res.other_user_last_read_timestamp);
                    }

                    // Live Typing Status Indicator Updater
                    if (res.is_typing && res.typing_user_name) {
                        $('#typingUserSpan').text(res.typing_user_name);
                        if (!$('#chatTypingIndicator').is(':visible')) {
                            $('#chatTypingIndicator').stop(true, true).fadeIn(150);
                        }
                        $('#activeStatusLabel').html('<span style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-pen-nib fa-bounce" style="font-size:10px; margin-right:4px;"></i>typing...</span>');
                        $('#activeStatusPill').addClass('typing');
                    } else {
                        if ($('#chatTypingIndicator').is(':visible')) {
                            $('#chatTypingIndicator').stop(true, true).fadeOut(150);
                        }
                        $('#activeStatusPill').removeClass('typing');
                        if (activeOtherUserCache && !activeIsGroupCache) {
                            $('#activeStatusLabel').text(activeOtherUserCache.is_online ? 'Active now' : 'Offline').css('color', '');
                            if (activeOtherUserCache.is_online) {
                                $('#activeStatusPill').removeClass('offline');
                            } else {
                                $('#activeStatusPill').addClass('offline');
                            }
                        } else if (activeIsGroupCache && activeGroupDataCache) {
                            $('#activeStatusLabel').text(activeGroupDataCache.member_count + ' members').css('color', 'var(--text-muted)');
                            $('#activeStatusPill').removeClass('offline');
                        }
                    }
                }
            },
            error: function() {
                isPolling = false;
            }
        });
    }

    // Polling lifecycle: 2s interval when active, 3s interval when minimized/hidden in background
    function startPolling() {
        stopPolling();
        var delay = (document.visibilityState === 'visible') ? 2000 : 3000;
        pollIntervalTimer = setInterval(function() {
            loadConversationsAndPoll(false);
        }, delay);
    }

    function stopPolling() {
        if (pollIntervalTimer) {
            clearInterval(pollIntervalTimer);
            pollIntervalTimer = null;
        }
    }

    document.addEventListener('visibilitychange', function() {
        startPolling();
        if (document.visibilityState === 'visible') {
            loadConversationsAndPoll(true);
        }
    });

    // Initial Load & Start Polling
    loadConversationsAndPoll(true);
    startPolling();

    // 9. Info Panel Toggle
    $('#btnToggleInfoPanel, #btnCloseInfoPanel').click(function() {
        $('#chatInfoPanel').toggleClass('hidden');
    });

    // 10. New Chat Modal Logic & Search
    $('#btnOpenNewChatModal, #btnEmptyStartChat').click(function() {
        $('#modalNewChat').addClass('active');
        $('#inputSearchTeammates').val('').focus();
        $('#newChatComposeArea').hide();
        $('#btnSendRequestSubmit').prop('disabled', true);
        selectedTargetUserId = 0;
        searchTeammates('');
    });

    $('#btnCloseNewChatModal, #btnCancelNewChat').click(function() {
        $('#modalNewChat').removeClass('active');
    });

    $('#modalNewChat').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    var searchTimer = null;
    $('#inputSearchTeammates').on('input', function() {
        var query = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            searchTeammates(query);
        }, 220);
    });

    function searchTeammates(q) {
        var container = $('#newChatSearchResults');
        container.html('<div style="text-align:center; padding:16px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Searching...</div>');

        $.ajax({
            url: window.APP_BASE + 'messages/search-users',
            type: 'GET',
            data: { q: q },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    renderSearchResults(res.users);
                }
            }
        });
    }

    function renderSearchResults(users) {
        var container = $('#newChatSearchResults');
        if (!users || users.length === 0) {
            container.html('<div style="text-align:center; padding:20px; color:var(--text-muted); font-size:12.5px;">No teammates found.</div>');
            return;
        }

        var html = '';
        users.forEach(function(u) {
            if (u.status === 'active' && u.conversation_id) {
                html += '<div class="chat-search-user-item has-active-conv btn-select-existing-conv" data-conv-id="' + u.conversation_id + '">';
                html += '  <div class="chat-search-user-info">';
                html += '    <img src="' + escapeHtml(u.avatar_url) + '" class="chat-search-user-avatar" alt="' + escapeHtml(u.name) + '">';
                html += '    <div>';
                html += '      <div class="chat-search-user-name">' + escapeHtml(u.name) + '</div>';
                html += '      <div class="chat-search-user-email">' + escapeHtml(u.email) + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <button type="button" class="btn-select-existing-conv-pill" data-conv-id="' + u.conversation_id + '">';
                html += '    <i class="fa-regular fa-comment-dots"></i> Open Chat';
                html += '  </button>';
                html += '</div>';
            } else if (u.status === 'pending') {
                html += '<div class="chat-search-user-item is-pending">';
                html += '  <div class="chat-search-user-info">';
                html += '    <img src="' + escapeHtml(u.avatar_url) + '" class="chat-search-user-avatar" alt="' + escapeHtml(u.name) + '">';
                html += '    <div>';
                html += '      <div class="chat-search-user-name">' + escapeHtml(u.name) + '</div>';
                html += '      <div class="chat-search-user-email">' + escapeHtml(u.email) + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <span class="chat-search-pending-badge">';
                html += '    <i class="fa-solid fa-clock"></i> Pending';
                html += '  </span>';
                html += '</div>';
            } else {
                html += '<div class="chat-search-user-item is-pickable btn-pick-teammate" data-user-id="' + u.id + '" data-user-name="' + escapeHtml(u.name) + '" data-user-avatar="' + escapeHtml(u.avatar_url) + '">';
                html += '  <div class="chat-search-user-info">';
                html += '    <img src="' + escapeHtml(u.avatar_url) + '" class="chat-search-user-avatar" alt="' + escapeHtml(u.name) + '">';
                html += '    <div>';
                html += '      <div class="chat-search-user-name">' + escapeHtml(u.name) + '</div>';
                html += '      <div class="chat-search-user-email">' + escapeHtml(u.email) + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <button type="button" class="btn-pick-teammate-pill">';
                html += '    Select';
                html += '  </button>';
                html += '</div>';
            }
        });

        container.html(html);
    }

    $(document).on('click', '.btn-select-existing-conv', function() {
        var convId = $(this).data('conv-id');
        $('#modalNewChat').removeClass('active');
        $('#tabBtnChats').click();
        openConversation(convId);
    });

    $(document).on('click', '.btn-pick-teammate', function() {
        $('.chat-search-user-item.is-pickable').removeClass('selected');
        $(this).addClass('selected');
        selectedTargetUserId = $(this).data('user-id');
        var name = $(this).data('user-name');
        var avatar = $(this).data('user-avatar');

        $('#selectedUserName').text(name);
        $('#selectedUserAvatar').attr('src', avatar);
        $('#newChatComposeArea').css('display', 'flex');
        $('#btnSendRequestSubmit').prop('disabled', false);
        $('#inputInitialMessage').focus();
    });

    $('#btnDeselectUser').click(function() {
        selectedTargetUserId = 0;
        $('.chat-search-user-item.is-pickable').removeClass('selected');
        $('#newChatComposeArea').hide();
        $('#btnSendRequestSubmit').prop('disabled', true);
    });

    $('#btnSendRequestSubmit').click(function() {
        if (!selectedTargetUserId) return;
        var initMsg = ($('#inputInitialMessage').val() || '').trim();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: window.APP_BASE + 'messages/send-request',
            type: 'POST',
            data: {
                receiver_id: selectedTargetUserId,
                initial_message: initMsg
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Send Request');
                if (res.success) {
                    showToast(res.message, 'success');
                    $('#modalNewChat').removeClass('active');
                    loadRequests();
                    loadConversationsAndPoll(true);
                    if (res.conversation_id) {
                        openConversation(res.conversation_id);
                    }
                } else {
                    showToast(res.message || 'Unable to send request', 'warning');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Send Request');
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error while sending request';
                showToast(err, 'error');
            }
        });
    });

    // 11. User Profile Modal Handlers
    $('#btnHeaderProfile').click(function(e) {
        e.preventDefault();
        $('#userProfileModal').addClass('active');
    });

    $('#btnCloseProfileModal, #btnCancelProfileModal').click(function() {
        $('#userProfileModal').removeClass('active');
    });

    $('#userProfileModal').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    // 12. System Settings Modal Handlers
    $('#btnHeaderSettings').click(function(e) {
        e.preventDefault();
        $('#settingsModal').addClass('active');
    });

    $('#btnCloseSettingsModal, #btnCancelSettingsModal').click(function() {
        $('#settingsModal').removeClass('active');
    });

    $('#settingsModal').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $(document).on('click', '.settings-tab-btn', function(e) {
        e.preventDefault();
        var tabTarget = $(this).data('tab');
        $('.settings-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.settings-tab-pane').hide();
        $('#' + tabTarget).show();
    });
    // 13. Clear Chat Handlers
    $('#btnClearChatHistoryBtnDirect, #btnClearChatHistoryBtnGroup').click(function() {
        if (!activeConversationId) {
            showToast('No active conversation selected', 'warning');
            return;
        }
        $('#modalClearChatConfirm').addClass('active');
    });

    $('#btnCloseClearChatModal, #btnCancelClearChat').click(function() {
        $('#modalClearChatConfirm').removeClass('active');
    });

    $('#modalClearChatConfirm').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $('#btnConfirmClearChatAction').click(function() {
        if (!activeConversationId) return;
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Clearing...');

        $.ajax({
            url: window.APP_BASE + 'messages/clear-history',
            type: 'POST',
            data: { conversation_id: activeConversationId },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-broom"></i> Yes, Clear Chat');
                if (res.success) {
                    $('#modalClearChatConfirm').removeClass('active');
                    showToast('Chat history cleared successfully', 'success');

                    // Reset messages display
                    $('#chatMessagesList').html('<div class="chat-system-msg-row"><span class="chat-system-pill"><i class="fa-solid fa-broom" style="margin-right: 4px;"></i> Chat history cleared</span></div>');

                    // Update cached conversation
                    var cached = conversationsCache.find(function(item) { return item.conversation_id === activeConversationId; });
                    if (cached) {
                        cached.last_message = null;
                        cached.unread_count = 0;
                        renderConversationsList(conversationsCache);
                    }
                } else {
                    showToast(res.message || 'Unable to clear chat', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-broom"></i> Yes, Clear Chat');
                showToast('Failed to clear chat history', 'error');
            }
        });
    });

    // 14. Create Group Handlers
    var groupTeammatesCache = [];
    var selectedGroupMemberIds = new Set();

    $('#btnOpenNewGroupModal, #btnEmptyCreateGroup').click(function() {
        $('#inputNewGroupTitle').val('');
        $('#inputNewGroupDesc').val('');
        $('#inputSearchGroupMembers').val('');
        selectedGroupMemberIds.clear();
        updateSelectedGroupCount();
        $('#modalNewGroup').addClass('active');

        $('#newGroupTeammatesList').html('<div style="text-align:center; color:var(--text-muted); padding:18px; font-size:12px;"><i class="fa-solid fa-spinner fa-spin" style="margin-right:6px;"></i> Loading teammates...</div>');

        $.ajax({
            url: window.APP_BASE + 'messages/get-teammates',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    groupTeammatesCache = res.users || [];
                    renderGroupTeammatesPicker(groupTeammatesCache, '#newGroupTeammatesList', 'new-group-picker');
                } else {
                    $('#newGroupTeammatesList').html('<div style="text-align:center; color:#ef4444; padding:14px; font-size:12px;">Failed to load teammates.</div>');
                }
            },
            error: function() {
                $('#newGroupTeammatesList').html('<div style="text-align:center; color:#ef4444; padding:14px; font-size:12px;">Server error loading teammates.</div>');
            }
        });
    });

    function updateSelectedGroupCount() {
        var count = selectedGroupMemberIds.size;
        $('#newGroupSelectedCount').text(count + ' selected');
    }

    function renderGroupTeammatesPicker(users, containerSelector, namePrefix) {
        var $cont = $(containerSelector);
        if (!users || users.length === 0) {
            $cont.html('<div style="text-align:center; color:var(--text-muted); padding:16px; font-size:12px;">No teammates available.</div>');
            return;
        }

        var html = '';
        users.forEach(function(u) {
            var isChecked = selectedGroupMemberIds.has(u.id) ? 'checked' : '';
            html += '<label class="group-member-picker-item" for="' + namePrefix + '_user_' + u.id + '">';
            html += '  <input type="checkbox" id="' + namePrefix + '_user_' + u.id + '" class="group-member-checkbox" data-user-id="' + u.id + '" ' + isChecked + '>';
            html += '  <div class="group-picker-avatar-wrap">';
            html += '    <img src="' + escapeHtml(u.avatar_url) + '" alt="' + escapeHtml(u.name) + '" class="group-picker-avatar">';
            html += '    <div class="chat-online-dot ' + (u.is_online ? '' : 'offline') + '"></div>';
            html += '  </div>';
            html += '  <div class="group-picker-info">';
            html += '    <div class="group-picker-name">' + escapeHtml(u.name) + '</div>';
            html += '    <div class="group-picker-email">' + escapeHtml(u.email) + '</div>';
            html += '  </div>';
            html += '</label>';
        });
        $cont.html(html);
    }

    $(document).on('change', '#newGroupTeammatesList .group-member-checkbox', function() {
        var uId = parseInt($(this).data('user-id'), 10);
        if ($(this).is(':checked')) {
            selectedGroupMemberIds.add(uId);
        } else {
            selectedGroupMemberIds.delete(uId);
        }
        updateSelectedGroupCount();
    });

    $('#inputSearchGroupMembers').on('input', function() {
        var q = ($(this).val() || '').toLowerCase().trim();
        var filtered = groupTeammatesCache.filter(function(u) {
            return (u.name || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q);
        });
        renderGroupTeammatesPicker(filtered, '#newGroupTeammatesList', 'new-group-picker');
    });

    $('#btnCloseNewGroupModal, #btnCancelNewGroup').click(function() {
        $('#modalNewGroup').removeClass('active');
    });

    $('#modalNewGroup').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $('#btnSubmitCreateGroup').click(function() {
        var title = ($('#inputNewGroupTitle').val() || '').trim();
        var desc = ($('#inputNewGroupDesc').val() || '').trim();
        var memberIds = Array.from(selectedGroupMemberIds);

        if (!title) {
            showToast('Please enter a group name', 'warning');
            $('#inputNewGroupTitle').focus();
            return;
        }

        if (memberIds.length === 0) {
            showToast('Please select at least 1 teammate to add', 'warning');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Creating...');

        $.ajax({
            url: window.APP_BASE + 'messages/create-group',
            type: 'POST',
            data: {
                title: title,
                description: desc,
                member_ids: memberIds
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Create Group');
                if (res.success) {
                    $('#modalNewGroup').removeClass('active');
                    showToast('Group created successfully!', 'success');
                    $('#tabBtnChats').click();
                    pollUpdates(true);
                    if (res.conversation_id) {
                        openConversation(res.conversation_id);
                    }
                } else {
                    showToast(res.message || 'Failed to create group', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Create Group');
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error creating group';
                showToast(err, 'error');
            }
        });
    });

    // 15. Add Members to Group Handlers
    var addMembersTeammatesCache = [];
    var selectedAddMemberIds = new Set();

    $('#btnOpenAddGroupMemberModal').click(function() {
        if (!activeConversationId) return;
        $('#inputSearchAddMembers').val('');
        selectedAddMemberIds.clear();
        $('#addMembersSelectedCount').text('0 selected');
        $('#modalAddGroupMembers').addClass('active');

        $('#addMembersTeammatesList').html('<div style="text-align:center; color:var(--text-muted); padding:18px; font-size:12px;"><i class="fa-solid fa-spinner fa-spin" style="margin-right:6px;"></i> Loading teammates...</div>');

        $.ajax({
            url: window.APP_BASE + 'messages/get-teammates',
            type: 'GET',
            data: { conversation_id: activeConversationId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    addMembersTeammatesCache = res.users || [];
                    renderAddMembersPicker(addMembersTeammatesCache);
                } else {
                    $('#addMembersTeammatesList').html('<div style="text-align:center; color:#ef4444; padding:14px; font-size:12px;">Failed to load teammates.</div>');
                }
            },
            error: function() {
                $('#addMembersTeammatesList').html('<div style="text-align:center; color:#ef4444; padding:14px; font-size:12px;">Server error loading teammates.</div>');
            }
        });
    });

    function renderAddMembersPicker(users) {
        var $cont = $('#addMembersTeammatesList');
        if (!users || users.length === 0) {
            $cont.html('<div style="text-align:center; color:var(--text-muted); padding:16px; font-size:12px;">All teammates are already in this group!</div>');
            return;
        }

        var html = '';
        users.forEach(function(u) {
            var isChecked = selectedAddMemberIds.has(u.id) ? 'checked' : '';
            html += '<label class="group-member-picker-item" for="add_user_' + u.id + '">';
            html += '  <input type="checkbox" id="add_user_' + u.id + '" class="add-member-checkbox" data-user-id="' + u.id + '" ' + isChecked + '>';
            html += '  <div class="group-picker-avatar-wrap">';
            html += '    <img src="' + escapeHtml(u.avatar_url) + '" alt="' + escapeHtml(u.name) + '" class="group-picker-avatar">';
            html += '    <div class="chat-online-dot ' + (u.is_online ? '' : 'offline') + '"></div>';
            html += '  </div>';
            html += '  <div class="group-picker-info">';
            html += '    <div class="group-picker-name">' + escapeHtml(u.name) + '</div>';
            html += '    <div class="group-picker-email">' + escapeHtml(u.email) + '</div>';
            html += '  </div>';
            html += '</label>';
        });
        $cont.html(html);
    }

    $(document).on('change', '#addMembersTeammatesList .add-member-checkbox', function() {
        var uId = parseInt($(this).data('user-id'), 10);
        if ($(this).is(':checked')) {
            selectedAddMemberIds.add(uId);
        } else {
            selectedAddMemberIds.delete(uId);
        }
        $('#addMembersSelectedCount').text(selectedAddMemberIds.size + ' selected');
    });

    $('#inputSearchAddMembers').on('input', function() {
        var q = ($(this).val() || '').toLowerCase().trim();
        var filtered = addMembersTeammatesCache.filter(function(u) {
            return (u.name || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q);
        });
        renderAddMembersPicker(filtered);
    });

    $('#btnCloseAddMembersModal, #btnCancelAddMembers').click(function() {
        $('#modalAddGroupMembers').removeClass('active');
    });

    $('#modalAddGroupMembers').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $('#btnSubmitAddMembers').click(function() {
        var memberIds = Array.from(selectedAddMemberIds);
        if (memberIds.length === 0) {
            showToast('Please select at least 1 member to add', 'warning');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Adding...');

        $.ajax({
            url: window.APP_BASE + 'messages/add-group-members',
            type: 'POST',
            data: {
                conversation_id: activeConversationId,
                member_ids: memberIds
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-user-plus"></i> Add Selected');
                if (res.success) {
                    $('#modalAddGroupMembers').removeClass('active');
                    showToast(res.message, 'success');
                    openConversation(activeConversationId);
                    pollUpdates(true);
                } else {
                    showToast(res.message || 'Unable to add members', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-user-plus"></i> Add Selected');
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error adding members';
                showToast(err, 'error');
            }
        });
    });

    // 16. Leave Group Handlers
    $('#btnLeaveGroupTrigger').click(function() {
        if (!activeConversationId) return;
        $('#modalLeaveGroupConfirm').addClass('active');
    });

    $('#btnCloseLeaveGroupModal, #btnCancelLeaveGroup').click(function() {
        $('#modalLeaveGroupConfirm').removeClass('active');
    });

    $('#modalLeaveGroupConfirm').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $('#btnConfirmLeaveGroupAction').click(function() {
        if (!activeConversationId) return;
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Leaving...');

        $.ajax({
            url: window.APP_BASE + 'messages/leave-group',
            type: 'POST',
            data: { conversation_id: activeConversationId },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-right-from-bracket"></i> Yes, Leave');
                if (res.success) {
                    $('#modalLeaveGroupConfirm').removeClass('active');
                    showToast(res.message, 'info');

                    activeConversationId = 0;
                    $('#chatActiveView').hide();
                    $('#chatEmptyView').css('display', 'flex');
                    $('#chatAppWrapper').removeClass('mobile-conversation-active');
                    pollUpdates(true);
                } else {
                    showToast(res.message || 'Unable to leave group', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-right-from-bracket"></i> Yes, Leave');
                showToast('Server error while leaving group', 'error');
            }
        });
    });
});
</script>
