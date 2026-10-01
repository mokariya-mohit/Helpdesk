<?php
/**
 * Shared Modals Element
 * Available across all pages (Tasks, Chats, DailyUpdates) for logged-in users.
 * 
 * @var \App\View\AppView $this
 * @var array|null $currentUser
 */
?>

<!-- Manage Projects Modal -->
<div id="manageProjectModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 520px;">
        <div class="modal-header">
            <div class="modal-title">
                <div style="width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35); flex-shrink: 0;">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
                <div>
                    <span style="display: block; font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800; color: var(--text-headline); line-height: 1.2;">Database Project Manager</span>
                    <span style="display: block; font-size: 11px; font-weight: 500; color: var(--text-muted); margin-top: 2px;">Manage and switch active projects in database</span>
                </div>
            </div>
            <button type="button" id="btnCloseProjectModal" class="btn-close-modal" title="Close">&times;</button>
        </div>
        <div class="modal-body" style="gap: 18px; padding: 22px 24px;">
            <!-- Add New Project Form -->
            <div class="modal-create-card">
                <label class="modal-section-label">
                    <i class="fa-solid fa-plus-circle" style="color: #6366f1;"></i> Add New Project to Database
                </label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="newProjectInput" class="modal-form-input" placeholder="project_name (e.g. 8.bloqs, internal-crm)" style="flex: 1;">
                    <button type="button" id="btnSaveNewProject" class="btn btn-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important; color: #ffffff !important; border: none !important; border-radius: 12px !important; font-weight: 700 !important; font-size: 13px !important; padding: 10px 22px !important; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4) !important; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); white-space: nowrap;">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
                <div id="newProjectNameError" class="modal-input-error" style="display:none;">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <!-- Existing Projects List -->
            <div>
                <label class="modal-section-label" style="margin-bottom: 10px;">
                    <i class="fa-solid fa-layer-group" style="color: #818cf8;"></i> All Projects in Database
                </label>
                <div id="modalProjectList" class="modal-project-list">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Clients Modal -->
<div id="manageClientModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 520px;">
        <div class="modal-header">
            <div class="modal-title">
                <div style="width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35); flex-shrink: 0;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <span style="display: block; font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800; color: var(--text-headline); line-height: 1.2;">Database Client Manager</span>
                    <span style="display: block; font-size: 11px; font-weight: 500; color: var(--text-muted); margin-top: 2px;">Manage client assignments and email contacts</span>
                </div>
            </div>
            <button type="button" id="btnCloseClientModal" class="btn-close-modal" title="Close">&times;</button>
        </div>
        <div class="modal-body" style="gap: 18px; padding: 22px 24px;">
            <!-- Add New Client Form -->
            <div class="modal-create-card">
                <label class="modal-section-label">
                    <i class="fa-solid fa-user-plus" style="color: #0284c7;"></i> Add New Client to Database
                </label>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <input type="text" id="newClientInput" class="modal-form-input" placeholder="Client Name (e.g. John Doe, Example Company)">
                        <div id="newClientNameError" class="modal-input-error" style="display:none;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                    <div>
                        <div class="add-client-inputs-row" style="display: flex; gap: 10px; align-items: center;">
                            <input type="email" id="newClientEmailInput" class="modal-form-input" placeholder="Client Email (e.g. client@company.com)" style="flex: 1; min-width: 0;">
                            <button type="button" id="btnSaveNewClient" class="btn btn-primary btn-add-client-submit" style="background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%) !important; color: #ffffff !important; border: none !important; border-radius: 12px !important; font-weight: 700 !important; font-size: 13px !important; white-space: nowrap; padding: 10px 20px !important; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4) !important; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                                <i class="fa-solid fa-plus"></i> Add Client
                            </button>
                        </div>
                        <div id="newClientEmailError" class="modal-input-error" style="display:none;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Clients List -->
            <div>
                <label class="modal-section-label" style="margin-bottom: 10px;">
                    <i class="fa-solid fa-users" style="color: #38bdf8;"></i> All Clients in Database
                </label>
                <div id="modalClientList" class="modal-project-list">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Profile Modal (Centered, Theme-Matched) -->
<div id="userProfileModal" class="modal-overlay">
    <div class="modal-card profile-modal-card">
        <div class="profile-modal-header">
            <div class="profile-header-title">
                <i class="fa-solid fa-id-badge" style="color: var(--primary);"></i>
                <span>User Profile &amp; Account Settings</span>
            </div>
            <button type="button" class="btn-close-modal" id="btnCloseProfileModal" title="Close">&times;</button>
        </div>

        <div class="profile-modal-body">
            <div id="profileAlert" style="display: none; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;"></div>

            <!-- User Info Card (Theme-Matched with Big 3D Pixar Avatar) -->
            <div class="profile-banner-card">
                <div class="profile-avatar-wrapper">
                    <div id="profileAvatarBig" class="profile-avatar-circle">
                        <?php
                            $pGender = !empty($currentUser['gender']) ? $currentUser['gender'] : 'male';
                            $pAvatar = !empty($currentUser['picture']) ? $currentUser['picture'] : "img/avatars/{$pGender}/{$pGender}_1.png";
                            $pAvatarUrl = str_starts_with($pAvatar, 'http') ? $pAvatar : $this->Url->build('/' . ltrim($pAvatar, '/'));

                            $userId = (int)($currentUser['id'] ?? 1);
                            $maleIdx = (($userId - 1) % 7) + 1;
                            $femaleIdx = (($userId - 1) % 6) + 1;
                            $initMalePic = ($pGender === 'male' && !empty($currentUser['picture'])) ? $currentUser['picture'] : "img/avatars/male/male_{$maleIdx}.png";
                            $initFemalePic = ($pGender === 'female' && !empty($currentUser['picture'])) ? $currentUser['picture'] : "img/avatars/female/female_{$femaleIdx}.png";
                            $initMaleUrl = $this->Url->build('/' . ltrim($initMalePic, '/'));
                            $initFemaleUrl = $this->Url->build('/' . ltrim($initFemalePic, '/'));
                        ?>
                        <img src="<?= h($pAvatarUrl) ?>" alt="Avatar" id="profileBigAvatarImg" class="profile-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                        <span class="profile-avatar-fallback" style="display:none;"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></span>
                    </div>
                    <input type="hidden" id="profSelectedPicture" value="<?= h($pAvatar) ?>" data-male-pic="<?= h($initMalePic) ?>" data-male-url="<?= h($initMaleUrl) ?>" data-female-pic="<?= h($initFemalePic) ?>" data-female-url="<?= h($initFemaleUrl) ?>">
                </div>
                <div class="profile-banner-meta">
                    <div id="profileBannerName" class="profile-banner-name"><?= h($currentUser['name'] ?? 'User') ?></div>
                    <div class="profile-banner-sub">
                        <span><i class="fa-regular fa-envelope" style="margin-right: 3px;"></i> <span id="profileBannerEmail"><?= h($currentUser['email'] ?? '') ?></span></span>
                        <span><i class="fa-regular fa-calendar-check" style="margin-right: 3px;"></i> Member Since: <strong id="profileMemberSince">Loading...</strong></span>
                        <span class="profile-gender-badge" id="profileGenderBadge">
                            <i class="fa-solid <?= ($currentUser['gender'] ?? 'male') === 'female' ? 'fa-venus' : 'fa-mars' ?>" style="color: <?= ($currentUser['gender'] ?? 'male') === 'female' ? '#ec4899' : '#3b82f6' ?>; margin-right: 3px;"></i>
                            <span id="profileGenderText"><?= ucfirst($currentUser['gender'] ?? 'male') ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 1: Personal Details -->
            <div>
                <div class="profile-section-title">Personal Information</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label for="profInputName" class="profile-field-label">Full Name <span style="color: #ef4444;">*</span> :</label>
                        <input type="text" id="profInputName" class="profile-input-field" placeholder="Your Name" value="<?= h($currentUser['name'] ?? '') ?>" autocomplete="off" required>
                        <div id="profNameError" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:3px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                    <div>
                        <label for="profInputEmail" class="profile-field-label">Email Address <span style="color: #ef4444;">*</span> :</label>
                        <input type="email" id="profInputEmail" class="profile-input-field" placeholder="user@domain.com" value="<?= h($currentUser['email'] ?? '') ?>" autocomplete="off" required>
                        <div id="profEmailError" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:3px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 12px;">
                    <label class="profile-field-label">Gender <span style="color: #ef4444;">*</span> :</label>
                    <div class="profile-gender-toggle-group">
                        <label class="profile-gender-toggle-opt <?= ($currentUser['gender'] ?? 'male') === 'male' ? 'active' : '' ?>" id="labelGenderMale">
                            <input type="radio" name="profGender" id="profGenderMale" value="male" <?= ($currentUser['gender'] ?? 'male') === 'male' ? 'checked' : '' ?>>
                            <i class="fa-solid fa-mars" style="color: #3b82f6; font-size: 15px;"></i>
                            <span>Male</span>
                        </label>
                        <label class="profile-gender-toggle-opt <?= ($currentUser['gender'] ?? 'male') === 'female' ? 'active' : '' ?>" id="labelGenderFemale">
                            <input type="radio" name="profGender" id="profGenderFemale" value="female" <?= ($currentUser['gender'] ?? 'male') === 'female' ? 'checked' : '' ?>>
                            <i class="fa-solid fa-venus" style="color: #ec4899; font-size: 15px;"></i>
                            <span>Female</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Security & Password -->
            <div class="profile-box-panel">
                <div class="profile-section-title" style="margin-bottom: 0;">
                    <span><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 4px;"></i> Change Password (Optional)</span>
                    <span style="font-size: 11px; text-transform: none; font-weight: 500; color: var(--text-muted); font-style: italic;">Leave empty to keep current password</span>
                </div>

                <div id="profGoogleAccountNotice" style="display:none; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2); border-radius:6px; padding:6px 10px; font-size:11.5px; color:var(--text-muted);">
                    <i class="fa-brands fa-google" style="color:#4285F4; margin-right:4px;"></i> You signed in via Google. You can set a direct login password below without entering a current password.
                </div>

                <div id="profCurrentPassWrapper">
                    <label for="profCurrentPass" class="profile-field-label">Current Password :</label>
                    <div style="position: relative;">
                        <input type="password" id="profCurrentPass" class="profile-input-field" placeholder="Enter current password if changing password" style="padding-right: 36px;">
                        <button type="button" class="btn-toggle-password" data-target="#profCurrentPass" title="Show/Hide Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div id="profCurrentPassError" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:3px;">
                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label for="profNewPass" class="profile-field-label">New Password :</label>
                        <div style="position: relative;">
                            <input type="password" id="profNewPass" class="profile-input-field" placeholder="Min 6 characters" style="padding-right: 36px;">
                            <button type="button" class="btn-toggle-password" data-target="#profNewPass" title="Show/Hide Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div id="profNewPassError" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:3px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                    <div>
                        <label for="profConfirmPass" class="profile-field-label">Confirm New Password :</label>
                        <div style="position: relative;">
                            <input type="password" id="profConfirmPass" class="profile-input-field" placeholder="Repeat new password" style="padding-right: 36px;">
                            <button type="button" class="btn-toggle-password" data-target="#profConfirmPass" title="Show/Hide Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div id="profConfirmPassError" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:3px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-modal-footer">
            <button type="button" class="btn-prof-cancel" id="btnCancelProfileModal">Cancel</button>
            <button type="button" class="btn-prof-save" id="btnSaveProfileModal">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Dedicated System Settings Modal (AI Engine & Google SMTP) -->
<div id="settingsModal" class="modal-overlay">
    <div class="modal-card settings-modal-card">
        <div class="settings-modal-header">
            <div class="settings-header-title">
                <div class="settings-header-icon">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div>
                    <span class="settings-title-text">System Settings</span>
                    <span class="settings-subtitle-text">Configure AI Engine (Gemini / ChatGPT / Free AI) &amp; Google SMTP</span>
                </div>
            </div>
            <button type="button" class="btn-close-modal" id="btnCloseSettingsModal" title="Close">&times;</button>
        </div>

        <!-- Navigation Tabs -->
        <div class="settings-tabs-nav">
            <button type="button" class="settings-tab-btn active" data-tab="geminiTab">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Gemini AI
            </button>
            <button type="button" class="settings-tab-btn" data-tab="smtpTab">
                <i class="fa-solid fa-envelope"></i> Google SMTP
            </button>
        </div>

        <div class="settings-modal-body">
            <div id="settingsAlert" style="display: none; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500; margin-bottom: 12px;"></div>

            <!-- Tab Pane 1: Google Gemini AI -->
            <div class="settings-tab-pane active" id="geminiTab">
                <div class="settings-pane-card">
                    <div class="settings-pane-header">
                        <div class="settings-pane-title">
                            <i class="fa-solid fa-wand-magic-sparkles" style="color: #6366f1;"></i> Google Gemini AI API Key
                        </div>
                        <span class="settings-badge-encrypted">
                            <i class="fa-solid fa-shield-halved"></i> Encrypted in MySQL
                        </span>
                    </div>
                    <p class="settings-pane-desc">
                        Powers <strong>AI Polish</strong>, grammar correction, and Gujarati to English translation using Google Gemini AI.
                    </p>
                    <div style="margin-bottom: 12px;">
                        <label for="settingsGeminiKey" class="profile-field-label">Google Gemini API Key :</label>
                        <div style="position: relative;">
                            <input type="password" id="settingsGeminiKey" class="profile-input-field" placeholder="AIzaSy... (leave blank to keep current key)" style="padding-right: 36px;">
                            <button type="button" class="btn-toggle-password" data-target="#settingsGeminiKey" title="Show/Hide Key">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                            <span style="font-size: 11px; color: var(--text-muted);">Status: <strong id="settingsGeminiStatus">Checking...</strong></span>
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" style="font-size: 11px; color: #4f46e5; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                <i class="fa-solid fa-key"></i> Get free Gemini API Key <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Pane 2: Google SMTP -->
            <div class="settings-tab-pane" id="smtpTab">
                <div class="settings-pane-card">
                    <div class="settings-pane-header">
                        <div class="settings-pane-title">
                            <i class="fa-solid fa-envelope" style="color: #0284c7;"></i> Google SMTP Email App Password
                        </div>
                        <span class="settings-badge-encrypted">
                            <i class="fa-solid fa-shield-halved"></i> Encrypted in MySQL
                        </span>
                    </div>
                    <p class="settings-pane-desc">
                        Required to dispatch daily updates and work reports directly from your Gmail account via authenticated SMTP.
                    </p>
                    <div style="margin-bottom: 12px;">
                        <label for="settingsSmtpPass" class="profile-field-label">16-Digit Google App Password :</label>
                        <div style="position: relative;">
                            <input type="password" id="settingsSmtpPass" class="profile-input-field" placeholder="xxxx xxxx xxxx xxxx (leave blank to keep current password)" style="padding-right: 36px;">
                            <button type="button" class="btn-toggle-password" data-target="#settingsSmtpPass" title="Show/Hide Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                            <span style="font-size: 11px; color: var(--text-muted);">Status: <strong id="settingsSmtpStatus">Checking...</strong></span>
                            <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer" style="font-size: 11px; color: #0284c7; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                <i class="fa-brands fa-google"></i> Get Google App Password <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-modal-footer">
            <button type="button" class="btn-confirm-cancel" id="btnCancelSettingsModal">Close</button>
            <button type="button" class="btn-confirm-action" id="btnSaveSettingsModal">
                <i class="fa-solid fa-check"></i> Save Settings
            </button>
        </div>
    </div>
</div>
