<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Project> $projects
 * @var \App\Model\Entity\Project|null $defaultProject
 * @var string $todayFormatted
 * @var string $todayIso
 * @var \App\Model\Entity\WorkLog|null $todayLog
 */
$this->assign('title', 'Write your todays task - Helpdesk');
?>

<style>
    :root {
        --bg-main: #f7f6f0;
        --bg-card: #ffffff;
        --bg-secondary: #f0eee6;
        --bg-editor: #faf9f5;
        --border-color: #e5e3d7;
        --border-hover: #cfcbc0;
        --text-main: #1c201e;
        --text-muted: #6e7570;
        --text-light: #9ba19c;
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --accent-amber: #d97706;
        --shadow-sm: 0 2px 8px rgba(90, 85, 60, 0.04);
        --shadow-md: 0 8px 24px rgba(90, 85, 60, 0.07);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--bg-main);
        color: var(--text-main);
        line-height: 1.5;
        padding: 16px 20px;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .app-container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
        height: calc(100vh - 36px);
    }

    /* Top Header */
    header {
        background-color: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 12px 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: 40px;
        height: 40px;
        background: #1c201e;
        color: #f7f6f0;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .brand-title {
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    .brand-subtitle {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Header Date Selector */
    .date-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-secondary);
        padding: 4px 6px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
    }

    .btn-nav {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-nav:hover {
        color: var(--text-main);
        background: #ffffff;
    }

    .date-picker {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: var(--radius-sm);
        outline: none;
        cursor: pointer;
    }

    .action-btns {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 7px 14px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn:hover {
        background: var(--bg-secondary);
        border-color: var(--border-hover);
        color: var(--text-main);
    }

    .btn-primary {
        background: #1c201e;
        color: #ffffff;
        border-color: #1c201e;
    }

    .btn-primary:hover {
        background: #343a37;
        color: #ffffff;
    }

    .btn-logout-pill:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    /* Main Workspace Layout */
    .workspace {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 14px;
        flex: 1;
        min-height: 0;
    }

    /* Sidebar: Past Journal Logs */
    .sidebar {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: var(--shadow-sm);
        min-height: 0;
    }

    .sidebar-header {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--bg-secondary);
    }

    /* Month Filter Bar */
    .month-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 4px 8px;
    }

    .btn-month-nav {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.15s ease;
    }

    .btn-month-nav:hover {
        background: #ffffff;
        color: var(--text-main);
    }

    .month-label {
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
    }

    .search-box {
        position: relative;
    }

    .search-input {
        width: 100%;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 6px 10px 6px 28px;
        font-size: 12px;
        color: var(--text-main);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        font-size: 11px;
    }

    .log-list {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .log-item {
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid transparent;
    }

    .log-item:hover {
        background: var(--bg-secondary);
    }

    .log-item.active {
        background: var(--bg-secondary);
        border-color: var(--border-color);
        font-weight: 700;
    }

    .log-date {
        font-size: 12px;
        color: var(--text-main);
        font-family: 'Fira Code', monospace;
        white-space: nowrap;
    }

    .log-badge {
        font-size: 10px;
        color: var(--text-muted);
        background: var(--bg-card);
        padding: 1px 6px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        white-space: nowrap;
    }

    /* Editor Container */
    .editor-container {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        min-height: 0;
        overflow: hidden;
    }

    .editor-toolbar {
        padding: 10px 16px;
        background: #faf9f5;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .editor-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .editor-title {
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Custom Project Dropdown & Management Styling */
    .custom-project-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        padding: 3px 6px 3px 10px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .custom-project-wrapper:hover {
        border-color: var(--border-hover);
        box-shadow: var(--shadow-sm);
    }

    .custom-project-trigger {
        background: transparent;
        border: none;
        outline: none;
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        font-weight: 700;
        color: #1c201e;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 2px 4px;
    }

    .custom-project-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        min-width: 220px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        padding: 6px;
        z-index: 1500;
        display: none;
        flex-direction: column;
        gap: 4px;
    }

    .custom-project-menu.active {
        display: flex;
    }

    .custom-menu-header {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 6px 8px 4px 8px;
        border-bottom: 1px solid var(--bg-secondary);
    }

    .custom-menu-list {
        display: flex;
        flex-direction: column;
        gap: 2px;
        max-height: 200px;
        overflow-y: auto;
    }

    .custom-menu-item {
        padding: 8px 10px;
        border-radius: 6px;
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
    }

    .custom-menu-item:hover {
        background: var(--bg-secondary);
    }

    .custom-menu-item.active {
        background: #f0fdf4;
        color: #15803d;
        font-weight: 700;
    }

    .btn-add-project {
        background: var(--bg-secondary);
        color: #4f46e5;
        border: 1px solid var(--border-color);
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-add-project:hover {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
    }

    .editor-textarea {
        flex: 1;
        width: 100%;
        padding: 20px;
        background: var(--bg-editor);
        border: none;
        outline: none;
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        line-height: 1.7;
        color: #1a1e1c;
        resize: none;
        tab-size: 2;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
        overflow-y: auto;
    }

    .editor-footer {
        padding: 8px 16px;
        background: #faf9f5;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        margin-right: 4px;
    }

    .status-dot.saving {
        background: #f59e0b;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.3; }
        100% { opacity: 1; }
    }

    /* Global Search Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(28, 32, 30, 0.45);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 2000;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 680px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #faf9f5;
    }

    .modal-title {
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 20px;
        color: var(--text-muted);
        cursor: pointer;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .btn-close-modal:hover {
        background: var(--bg-secondary);
        color: var(--text-main);
    }

    .modal-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow-y: auto;
        flex: 1;
    }

    .global-search-input-box {
        position: relative;
    }

    .global-search-input {
        width: 100%;
        padding: 12px 16px 12px 38px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        color: var(--text-main);
        outline: none;
        transition: all 0.2s ease;
    }

    .global-search-input:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .search-results-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 440px;
        overflow-y: auto;
    }

    .result-card {
        background: #faf9f5;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .result-card:hover {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
        transform: translateY(-1px);
    }

    .result-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .result-date {
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        font-weight: 700;
        color: var(--primary);
    }

    .result-project {
        font-size: 11px;
        font-weight: 600;
        background: #e0e7ff;
        color: #3730a3;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .result-snippet {
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        color: var(--text-main);
        line-height: 1.5;
        white-space: pre-wrap;
        background: #ffffff;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    mark.highlight {
        background: #fef08a;
        color: #1e1b4b;
        font-weight: 700;
        padding: 0 2px;
        border-radius: 2px;
    }

    /* Modal Project Item List */
    .modal-project-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 220px;
        overflow-y: auto;
    }

    .modal-project-item {
        background: #faf9f5;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .modal-project-name {
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-icon {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        transition: all 0.15s ease;
    }

    .btn-action-icon:hover {
        background: #ffffff;
        color: var(--primary);
    }

    .btn-action-icon.delete:hover {
        color: #e11d48;
    }

    /* Toast notification */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #1c201e;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 8px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.25s ease;
        z-index: 3000;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 900px) {
        .workspace {
            grid-template-columns: 1fr;
        }
        .sidebar {
            max-height: 220px;
        }
        .app-container {
            height: auto;
        }
    }
</style>

<div class="app-container">
    <!-- Header -->
    <header>
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-note-sticky"></i>
            </div>
            <div>
                <h1 class="brand-title">Write your todays task</h1>
                <p class="brand-subtitle">CakePHP 5 & MySQL Work Log Journal</p>
            </div>
        </div>

        <!-- Date Controls -->
        <div class="date-controls">
            <button type="button" class="btn-nav" id="btnPrevDay" title="Previous Day">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <input type="date" id="datePicker" class="date-picker" value="<?= h($todayIso) ?>" max="<?= h($todayIso) ?>">
            <button type="button" class="btn-nav" id="btnToday">Today</button>
            <button type="button" class="btn-nav" id="btnNextDay" title="Next Day">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Header Actions -->
        <div class="action-btns">
            <button type="button" class="btn" id="btnOpenGlobalSearch" title="Search across all past notes in Database">
                <i class="fa-solid fa-magnifying-glass text-indigo-600"></i> Global Search
            </button>
            <button type="button" class="btn btn-primary" id="btnInsertTemplate" title="Insert Standard Daily Template">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Insert Template
            </button>

            <?php if (!empty($currentUser)): ?>
                <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>" class="btn-logout-pill" title="Click to Logout (<?= h($currentUser['name']) ?>)" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-secondary); border:1px solid var(--border-color); padding:5px 12px; border-radius:var(--radius-sm); text-decoration:none; cursor:pointer; transition:all 0.2s ease;">
                    <i class="fa-solid fa-user-circle text-indigo-600" style="font-size:15px;"></i>
                    <span style="font-size:12px; font-weight:700; color:var(--text-main);"><?= h($currentUser['name']) ?></span>
                    <i class="fa-solid fa-right-from-bracket" style="color:#ef4444; font-size:12px; margin-left:2px;" title="Logout"></i>
                </a>
            <?php else: ?>
                <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>" class="btn" style="background:#1c201e; color:#ffffff; border-color:#1c201e;">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Workspace Grid -->
    <div class="workspace">
        <!-- Left Sidebar: Past Daily Logs from Database -->
        <div class="sidebar">
            <div class="sidebar-header">
                <span><i class="fa-solid fa-database mr-1" style="color: #4f46e5;"></i> &nbsp; Work Logs</span>
                <span id="logCountBadge" style="font-size: 11px; color: var(--text-muted);">0</span>
            </div>

            <!-- Month & Year Filter Bar -->
            <div class="month-filter-bar">
                <button type="button" id="btnPrevMonth" class="btn-month-nav" title="Previous Month">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <span id="currentMonthDisplay" class="month-label"><?= date('M Y') ?></span>
                <button type="button" id="btnNextMonth" class="btn-month-nav" title="Next Month">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Filter this month...">
            </div>

            <div id="logListContainer" class="log-list">
                <!-- Dynamically populated from MySQL -->
            </div>
        </div>

        <!-- Right Main Panel: Clean Text Editor -->
        <div class="editor-container">
            <div class="editor-toolbar">
                <div class="editor-title-group">
                    <div class="editor-title">
                        <i class="fa-solid fa-calendar-day text-slate-500"></i>
                        <span id="activeDateTitle"><?= h($todayFormatted) ?></span>
                    </div>

                    <!-- Custom Project Selection Dropdown -->
                    <div class="custom-project-wrapper">
                        <button type="button" id="btnProjectDropdownToggle" class="custom-project-trigger" title="Select Active Project">
                            <i class="fa-solid fa-folder-open text-indigo-600"></i>
                            <span id="activeProjectDisplay" class="font-bold"><?= h($defaultProject ? $defaultProject->name : 'No Project') ?></span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <button type="button" id="btnAddProject" class="btn-add-project" title="Manage Projects">
                            <i class="fa-solid fa-plus"></i> Project
                        </button>

                        <!-- Custom Popover Menu -->
                        <div id="projectDropdownMenu" class="custom-project-menu">
                            <div class="custom-menu-header">
                                <span>Select Project (MySQL)</span>
                            </div>
                            <div id="projectMenuList" class="custom-menu-list">
                                <!-- Dynamically populated from Database -->
                            </div>
                        </div>
                    </div>

                    <!-- Custom Client Selection Dropdown -->
                    <div class="custom-project-wrapper">
                        <button type="button" id="btnClientDropdownToggle" class="custom-project-trigger" title="Select Active Client" style="border-color:#bae6fd;">
                            <i class="fa-solid fa-user-tie" style="color:#0284c7;"></i>
                            <span id="activeClientDisplay" class="font-bold"><?= h($defaultClient ? $defaultClient->name : 'Select Client') ?></span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <button type="button" id="btnAddClient" class="btn-add-project" title="Manage Clients" style="color:#0284c7;">
                            <i class="fa-solid fa-plus"></i> Client
                        </button>

                        <!-- Custom Client Popover Menu -->
                        <div id="clientDropdownMenu" class="custom-project-menu">
                            <div class="custom-menu-header">
                                <span>Select Client (MySQL)</span>
                            </div>
                            <div id="clientMenuList" class="custom-menu-list">
                                <!-- Dynamically populated from Database -->
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; align-items: center;">
                    <button type="button" class="btn" id="btnAiPolish" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; border: none; font-weight: 600; padding: 5px 12px; font-size: 11px; display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; box-shadow: 0 2px 5px rgba(99, 102, 241, 0.25); cursor: pointer;" title="Fix grammar and make sentences professional with Gemini AI">
                        <i class="fa-solid fa-pen-to-square"></i> AI Polish
                    </button>
                    <button type="button" class="btn" id="btnClearCurrent" style="font-size: 11px; padding: 4px 8px;">
                        <i class="fa-solid fa-trash text-rose-500"></i> Clear Note
                    </button>
                </div>
            </div>

            <!-- No Projects Alert Banner -->
            <div id="noProjectWarningBanner" style="display: <?= empty($projects) || count($projects) === 0 ? 'flex' : 'none' ?>; align-items:center; justify-content:space-between; background:#fef3c7; border-bottom:1px solid #fde68a; padding:10px 16px; color:#92400e; font-size:12px; font-weight:500;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:14px; color:#d97706;"></i>
                    <span>No projects created yet. Please create a project first before writing tasks.</span>
                </div>
                <button type="button" id="btnBannerCreateProj" class="btn btn-primary" style="font-size:11px; padding:4px 12px;">
                    <i class="fa-solid fa-plus"></i> Create Project
                </button>
            </div>

            <!-- Main Monospace Editor -->
            <textarea id="workNotesEditor" class="editor-textarea" placeholder="Type or paste your daily task points here..."><?= h($todayLog ? $todayLog->content : '') ?></textarea>

            <div class="editor-footer">
                <div>
                    <span class="status-dot" id="saveStatusDot"></span>
                    <span id="saveStatusText"><?= $todayLog && $todayLog->modified ? 'Last saved at ' . $todayLog->modified->format('h:i:s A') : 'All changes saved' ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <span id="charCount">0 characters | 0 tasks</span>
                    <a href="#" id="btnContinueToEmail" class="btn" style="background:#6366f1; color:#ffffff; border-color:#6366f1; font-weight:600; text-decoration:none; padding: 6px 14px; border-radius: 6px; display:inline-flex; align-items:center; gap:6px; font-size:12px;" title="Continue to Daily Update Generator">
                        Continue to Email <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <footer style="text-align:center; font-size:11px; color:var(--text-muted); padding:4px 0; font-weight:500; opacity:0.85; flex-shrink:0;">
        &copy; <?= date('Y') ?> Mohit Mokariya. All Rights Reserved. Powered by CakePHP 5 & MySQL Database.
    </footer>
</div>

<!-- Global Search Modal -->
<div id="globalSearchModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-magnifying-glass text-indigo-600"></i>
                <span>Global Database Notes Search</span>
            </div>
            <button type="button" id="btnCloseSearchModal" class="btn-close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="global-search-input-box">
                <i class="fa-solid fa-search search-icon" style="left: 14px; font-size: 14px;"></i>
                <input type="text" id="globalSearchInput" class="global-search-input" placeholder="Search across ALL historical dates and notes in Database..." autofocus>
            </div>
            <div id="globalSearchResults" class="search-results-list">
                <div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Type any keyword above to search MySQL database.</div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Projects Modal -->
<div id="manageProjectModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-folder-tree text-indigo-600"></i>
                <span>Database Project Manager</span>
            </div>
            <button type="button" id="btnCloseProjectModal" class="btn-close-modal">&times;</button>
        </div>
        <div class="modal-body" style="gap: 16px; padding: 20px;">
            <!-- Add New Project Form -->
            <div>
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 6px;">Add New Project to Database</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="newProjectInput" class="global-search-input" placeholder="e.g. client_portal, seo_reports..." style="padding-left: 14px; flex: 1;">
                    <button type="button" id="btnSaveNewProject" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add</button>
                </div>
            </div>

            <!-- Existing Projects List -->
            <div>
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 8px;">All Projects in Database</label>
                <div id="modalProjectList" class="modal-project-list">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Clients Modal -->
<div id="manageClientModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-user-tie" style="color:#0284c7;"></i>
                <span>Database Client Manager</span>
            </div>
            <button type="button" id="btnCloseClientModal" class="btn-close-modal">&times;</button>
        </div>
        <div class="modal-body" style="gap: 16px; padding: 20px;">
            <!-- Add New Client Form -->
            <div>
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 6px;">Add New Client to Database</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="newClientInput" class="global-search-input" placeholder="e.g. Hitesh sir, Suresh sir, Acme Corp..." style="padding-left: 14px; flex: 1;">
                    <button type="button" id="btnSaveNewClient" class="btn" style="background:#0284c7; color:#fff; border:none; font-weight:600;"><i class="fa-solid fa-plus"></i> Add</button>
                </div>
            </div>

            <!-- Existing Clients List -->
            <div>
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 8px;">All Clients in Database</label>
                <div id="modalClientList" class="modal-project-list">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gemini AI API Key Settings Modal -->
<div id="geminiKeyModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <div class="modal-title" style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i>
                <span>Gemini AI Configuration</span>
            </div>
            <button type="button" id="btnCloseGeminiKeyModal" class="btn-close-modal">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px; display:flex; flex-direction:column; gap:14px;">
            <div id="geminiKeyErrorMsg" style="display:none; padding:8px 12px; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#b91c1c; font-size:12px; line-height:1.4;"></div>
            <p style="font-size:12px; color:var(--text-muted); line-height:1.5;">
                Enter your <strong>Google Gemini API Key</strong> to enable AI Grammar Polish, Sentence Correction, and Professional Phrasing.
            </p>
            <div>
                <label style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-muted); display:block; margin-bottom:6px;">Gemini API Key</label>
                <div style="position:relative; display:flex; align-items:center;">
                    <input type="password" id="geminiApiKeyInput" class="global-search-input" placeholder="AIzaSy..." style="padding-left:14px; padding-right:38px; width:100%;">
                    <button type="button" id="btnToggleApiKeyVis" style="position:absolute; right:10px; background:none; border:none; color:var(--text-muted); cursor:pointer;" title="Toggle Visibility">
                        <i class="fa-solid fa-eye" id="apiKeyEyeIcon"></i>
                    </button>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" style="font-size:11px; color:#4f46e5; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Get free Gemini API Key
                    </a>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:6px;">
                <button type="button" id="btnSaveGeminiKey" class="btn btn-primary" style="padding:6px 16px;">
                    <i class="fa-solid fa-save"></i> Save Key & Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Gemini AI Polish Preview Modal -->
<div id="aiPolishModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 980px; width: 94%; max-height: 92vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="flex-shrink: 0;">
            <div class="modal-title" style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                <span>AI Polished Output</span>
            </div>
            <button type="button" id="btnCloseAiPolishModal" class="btn-close-modal">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px; display:flex; flex-direction:column; flex: 1; min-height: 0; gap: 12px; overflow-y: auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-shrink: 0;">
                <span style="font-size:13px; color:var(--text-muted); font-weight:600;">Grammar & sentences improved professionally:</span>
                <button type="button" id="btnChangeAiKey" style="background:none; border:none; font-size:11px; color:var(--primary); cursor:pointer; text-decoration:underline;">
                    <i class="fa-solid fa-gear"></i> Change API Key
                </button>
            </div>
            <textarea id="aiPolishedTextarea" class="editor-textarea" style="flex: 1; min-height: 520px; max-height: 70vh; font-size: 14px; line-height: 1.65; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 16px; resize: vertical; overflow-y: auto;"></textarea>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px; flex-shrink: 0;">
                <button type="button" id="btnCopyAiPolish" class="btn" style="font-size:12px; padding:7px 16px;">
                    <i class="fa-regular fa-copy"></i> Copy Text
                </button>
                <div style="display:flex; gap:8px;">
                    <button type="button" id="btnCancelAiPolish" class="btn" style="font-size:12px; padding:7px 16px;">Cancel</button>
                    <button type="button" id="btnApplyAiPolish" class="btn btn-primary" style="font-size:12px; padding:7px 18px; background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border:none;">
                        <i class="fa-solid fa-check"></i> Apply to Editor
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastNotification" class="toast">
    <i class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="toastMessage">Saved to MySQL database!</span>
</div>
<script>
$(document).ready(function() {
    // Initial State injected by CakePHP
    var currentDateIso = '<?= h($todayIso) ?>';
    var currentDateFormatted = '<?= h($todayFormatted) ?>';
    var activeProjectId = <?= $defaultProject ? (int)$defaultProject->id : 0 ?>;
    var activeProjectName = '<?= h($defaultProject ? $defaultProject->name : '') ?>';
    var activeClientId = <?= $defaultClient ? (int)$defaultClient->id : 0 ?>;
    var activeClientName = '<?= h($defaultClient ? $defaultClient->name : '') ?>';
    var currentFilterYear = new Date().getFullYear();
    var currentFilterMonth = new Date().getMonth() + 1;

    var monthLogsCache = [];
    var autoSaveTimer = null;

    // Helper functions
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function isoToFormatted(iso) {
        if (!iso) return '';
        var parts = iso.split('-');
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    function showToast(msg) {
        $('#toastMessage').text(msg);
        $('#toastNotification').addClass('show');
        setTimeout(function() {
            $('#toastNotification').removeClass('show');
        }, 2500);
    }

    function countTasks(text) {
        if (!text || !text.trim()) return 0;
        var lines = text.split('\n');
        var count = 0;
        var hasBullet = false;

        lines.forEach(function(raw) {
            var line = raw.trim();
            if (/^[-*\u2022]/.test(line)) {
                var after = line.replace(/^[-*\u2022]+\s*/, '');
                if (after.length > 0) {
                    count++;
                    hasBullet = true;
                }
            }
        });

        if (hasBullet) return count;

        lines.forEach(function(raw) {
            var line = raw.trim();
            if (line === '') return;
            if (/^\d{2}-\d{2}-\d{4}/.test(line) || /^[-=]{3,}$/.test(line) || line.endsWith(':')) return;
            count++;
        });
        return count;
    }

    function updateCharCount() {
        var val = $('#workNotesEditor').val();
        var len = val.length;
        var tasks = countTasks(val);
        $('#charCount').text(len + ' characters | ' + tasks + (tasks === 1 ? ' task' : ' tasks'));
    }

    // --- Database AJAX Operations (Zero LocalStorage) ---

    // 1. Fetch note from MySQL
    function loadNoteFromDatabase(isoDate, projId) {
        $('#saveStatusDot').addClass('saving');
        $('#saveStatusText').text('Loading note...');

        $.ajax({
            url: window.APP_BASE + 'tasks/get-log',
            type: 'GET',
            data: { date: isoDate, project_id: projId },
            dataType: 'json',
            success: function(res) {
                $('#saveStatusDot').removeClass('saving');
                if (res.success && res.found) {
                    $('#workNotesEditor').val(res.data.content || '');
                    $('#saveStatusText').text(res.data.modified ? 'Last saved at ' + res.data.modified : 'All changes saved');
                    if (res.data.project_id) {
                        activeProjectId = res.data.project_id;
                        activeProjectName = res.data.project_name || '';
                        $('#activeProjectDisplay').text(activeProjectName || 'No Project');
                        renderProjectsDropdown();
                    }
                    if (res.data.client_id) {
                        activeClientId = res.data.client_id;
                        activeClientName = res.data.client_name || '';
                        $('#activeClientDisplay').text(activeClientName || 'Select Client');
                        renderClientsDropdown();
                    }
                } else {
                    $('#workNotesEditor').val('');
                    $('#saveStatusText').text('Ready (New note)');
                }
                updateCharCount();
            },
            error: function() {
                $('#saveStatusDot').removeClass('saving');
                $('#saveStatusText').text('Connection error');
            }
        });
    }

    // 2. Save note to MySQL (Debounced Auto-Save)
    function saveNoteToDatabase() {
        var content = $('#workNotesEditor').val();
        $('#saveStatusDot').addClass('saving');
        $('#saveStatusText').text('Saving...');

        $.ajax({
            url: window.APP_BASE + 'tasks/save-log',
            type: 'POST',
            data: {
                date: currentDateIso,
                project_id: activeProjectId,
                project_name: activeProjectName,
                client_id: activeClientId,
                client_name: activeClientName,
                content: content
            },
            dataType: 'json',
            success: function(res) {
                $('#saveStatusDot').removeClass('saving');
                if (res.success) {
                    $('#saveStatusText').text('Saved at ' + res.saved_at);
                    loadSidebarLogs();
                } else {
                    $('#saveStatusText').text('Save failed');
                }
            },
            error: function() {
                $('#saveStatusDot').removeClass('saving');
                $('#saveStatusText').text('Connection error');
            }
        });
    }

    function triggerAutoSave() {
        $('#saveStatusDot').addClass('saving');
        $('#saveStatusText').text('Unsaved changes...');
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(saveNoteToDatabase, 600);
    }

    // 3. Clear note from MySQL
    function clearNoteFromDatabase() {
        if (!confirm('Are you sure you want to clear this note from Database?')) return;
        $('#workNotesEditor').val('');
        updateCharCount();
        saveNoteToDatabase();
        showToast('Note cleared from database');
    }

    // 4. Load Sidebar Journal Logs for current month from MySQL
    function loadSidebarLogs() {
        $.ajax({
            url: window.APP_BASE + 'tasks/get-month-logs',
            type: 'GET',
            data: {
                year: currentFilterYear,
                month: currentFilterMonth
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    monthLogsCache = res.logs || [];
                    renderSidebarLogs();
                }
            }
        });
    }

    function renderSidebarLogs() {
        var container = $('#logListContainer');
        var countBadge = $('#logCountBadge');
        var searchVal = $('#searchInput').val().toLowerCase().trim();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $('#currentMonthDisplay').text(monthNames[currentFilterMonth - 1] + ' ' + currentFilterYear);

        var filtered = monthLogsCache.filter(function(log) {
            if (!searchVal) return true;
            return log.date_formatted.toLowerCase().includes(searchVal) ||
                   (log.preview && log.preview.toLowerCase().includes(searchVal)) ||
                   (log.project_name && log.project_name.toLowerCase().includes(searchVal));
        });

        countBadge.text(filtered.length);

        if (filtered.length === 0) {
            container.html('<div class="empty-state">No notes found for this month in Database.</div>');
            return;
        }

        var html = '';
        filtered.forEach(function(item) {
            var isCurrent = (item.iso_date === currentDateIso);
            html += '<div class="log-item ' + (isCurrent ? 'active' : '') + '" data-iso="' + item.iso_date + '" data-project-id="' + item.project_id + '" data-project-name="' + escapeHtml(item.project_name) + '">';
            html += '  <span class="log-date">' + item.date_formatted + '</span>';
            html += '  <span class="log-badge">' + item.task_count + (item.task_count === 1 ? ' task' : ' tasks') + '</span>';
            html += '</div>';
        });

        container.html(html);

        // Sidebar click item
        $('.log-item').click(function() {
            var iso = $(this).attr('data-iso');
            var pid = $(this).attr('data-project-id');
            var pname = $(this).attr('data-project-name');

            if (pid && pid != activeProjectId) {
                activeProjectId = pid;
                activeProjectName = pname || '';
                $('#activeProjectDisplay').text(activeProjectName || 'No Project');
                renderProjectsDropdown();
            }

            setDate(iso);
        });
    }

    // 5. Load Project List from MySQL
    var projectsCache = [];
    function loadProjects() {
        $.ajax({
            url: window.APP_BASE + 'projects/get-projects',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    projectsCache = res.projects || [];
                    if (projectsCache.length === 0) {
                        $('#noProjectWarningBanner').css('display', 'flex');
                        activeProjectId = 0;
                        activeProjectName = '';
                        $('#activeProjectDisplay').text('No Project');
                    } else {
                        $('#noProjectWarningBanner').hide();
                        if (activeProjectId == 0) {
                            var def = projectsCache.find(function(x) { return x.is_default; }) || projectsCache[0];
                            if (def) {
                                activeProjectId = def.id;
                                activeProjectName = def.name;
                                $('#activeProjectDisplay').text(activeProjectName);
                            }
                        }
                    }
                    renderProjectsDropdown();
                    renderModalProjectsList();
                }
            }
        });
    }

    function renderProjectsDropdown() {
        var html = '';
        if (projectsCache.length === 0) {
            html += '<div style="padding:12px; font-size:12px; color:var(--text-muted); text-align:center;">';
            html += '  No projects yet.<br>';
            html += '  <button type="button" class="btn btn-primary" id="btnDropdownCreateProj" style="margin-top:8px; font-size:11px; padding:4px 10px;"><i class="fa-solid fa-plus"></i> Create Project</button>';
            html += '</div>';
        } else {
            projectsCache.forEach(function(p) {
                var isActive = (p.id == activeProjectId || p.name === activeProjectName);
                html += '<div class="custom-menu-item ' + (isActive ? 'active' : '') + '" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '">';
                html += '  <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-folder"></i> ' + escapeHtml(p.name) + '</span>';
                if (isActive) {
                    html += '  <i class="fa-solid fa-check text-xs"></i>';
                }
                html += '</div>';
            });
        }
        $('#projectMenuList').html(html);

        $('#btnDropdownCreateProj').click(function() {
            $('#projectDropdownMenu').removeClass('active');
            $('#manageProjectModal').addClass('active');
            $('#newProjectInput').val('').focus();
        });

        $('#projectMenuList .custom-menu-item').click(function(e) {
            e.stopPropagation();
            activeProjectId = $(this).attr('data-id');
            activeProjectName = $(this).attr('data-name');
            $('#activeProjectDisplay').text(activeProjectName);
            $('#projectDropdownMenu').removeClass('active');
            renderProjectsDropdown();
            saveNoteToDatabase();
            showToast('Active project set to: ' + activeProjectName);
        });
    }

    function renderModalProjectsList() {
        var html = '';
        if (projectsCache.length === 0) {
            html = '<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:16px;">No projects in database. Create one above!</div>';
        } else {
            projectsCache.forEach(function(p) {
                html += '<div class="modal-project-item" id="projRow-' + p.id + '" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" style="padding:8px 10px; border-radius:6px; background:var(--bg-secondary); margin-bottom:8px; border:1px solid var(--border-color);">';
                
                // View Mode
                html += '  <div class="proj-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%;">';
                html += '    <span class="modal-project-name" style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;">';
                html += '      <i class="fa-solid fa-folder text-indigo-600"></i>';
                html += '      <span class="proj-name-text">' + escapeHtml(p.name) + '</span>';
                if (p.is_default) {
                    html += '  <span style="font-size:10px; color:#15803d; background:#dcfce7; padding:2px 8px; border-radius:10px; font-weight:700;">Default</span>';
                }
                html += '    </span>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn-action-icon btn-start-edit-proj" data-id="' + p.id + '" title="Rename Project" style="padding:5px 9px; font-size:11px;"><i class="fa-solid fa-pen"></i></button>';
                if (!p.is_default) {
                    html += '    <button type="button" class="btn-action-icon delete btn-delete-project" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" title="Delete Project" style="padding:5px 9px; font-size:11px;"><i class="fa-solid fa-trash"></i></button>';
                }
                html += '    </div>';
                html += '  </div>';

                // Inline Edit Mode
                html += '  <div class="proj-edit-mode" style="display:none; align-items:center; justify-content:space-between; width:100%; gap:8px;">';
                html += '    <div style="display:flex; align-items:center; gap:8px; flex:1;">';
                html += '      <i class="fa-solid fa-folder text-indigo-600"></i>';
                html += '      <input type="text" class="proj-inline-input" value="' + escapeHtml(p.name) + '" placeholder="Project name" style="padding:5px 10px; font-size:13px; flex:1; border:1px solid var(--primary); border-radius:6px; background:#ffffff; color:var(--text-main); outline:none;">';
                html += '    </div>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn btn-primary btn-save-inline-proj" data-id="' + p.id + '" title="Save Changes" style="padding:4px 10px; font-size:11px; height:30px;"><i class="fa-solid fa-check"></i> Save</button>';
                html += '      <button type="button" class="btn btn-cancel-inline-proj" data-id="' + p.id + '" title="Cancel" style="padding:4px 8px; font-size:11px; height:30px; background:#ffffff; color:#71717a; border:1px solid var(--border-color);"><i class="fa-solid fa-xmark"></i></button>';
                html += '    </div>';
                html += '  </div>';

                html += '</div>';
            });
        }
        $('#modalProjectList').html(html);

        // Click Edit Icon -> Turn into input field
        $('.btn-start-edit-proj').click(function() {
            var row = $(this).closest('.modal-project-item');
            row.find('.proj-view-mode').hide();
            var editMode = row.find('.proj-edit-mode');
            editMode.css('display', 'flex');
            var input = editMode.find('.proj-inline-input');
            input.focus().select();
        });

        // Click Cancel -> Revert back to text
        $('.btn-cancel-inline-proj').click(function() {
            var row = $(this).closest('.modal-project-item');
            var origName = row.attr('data-name');
            row.find('.proj-inline-input').val(origName);
            row.find('.proj-edit-mode').hide();
            row.find('.proj-view-mode').css('display', 'flex');
        });

        // Save Function
        function saveInlineProject(row) {
            var editId = row.attr('data-id');
            var origName = row.attr('data-name');
            var input = row.find('.proj-inline-input');
            var newName = input.val().trim();

            if (!newName) {
                showToast('⚠️ Project name cannot be empty');
                input.focus();
                return;
            }

            if (newName === origName) {
                row.find('.proj-edit-mode').hide();
                row.find('.proj-view-mode').css('display', 'flex');
                return;
            }

            $.ajax({
                url: window.APP_BASE + 'projects/edit/' + editId,
                type: 'POST',
                data: { name: newName },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showToast('✅ Project renamed to: ' + newName);
                        row.attr('data-name', newName);
                        row.find('.proj-name-text').text(newName);
                        row.find('.proj-edit-mode').hide();
                        row.find('.proj-view-mode').css('display', 'flex');

                        // Update cache & active display
                        var pItem = projectsCache.find(function(x) { return x.id == editId; });
                        if (pItem) pItem.name = newName;
                        if (activeProjectId == editId) {
                            activeProjectName = newName;
                            $('#activeProjectDisplay').text(activeProjectName);
                        }
                        renderProjectsDropdown();
                    } else {
                        showToast('❌ ' + (res.message || 'Failed to rename'));
                    }
                },
                error: function() {
                    showToast('❌ Network error while saving');
                }
            });
        }

        // Save on Save Button click
        $('.btn-save-inline-proj').click(function() {
            var row = $(this).closest('.modal-project-item');
            saveInlineProject(row);
        });

        // Save on Enter key / Cancel on Escape key
        $('.proj-inline-input').on('keydown', function(e) {
            var row = $(this).closest('.modal-project-item');
            if (e.key === 'Enter') {
                e.preventDefault();
                saveInlineProject(row);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                row.find('.btn-cancel-inline-proj').click();
            }
        });

        // Delete Project handler
        $('.btn-delete-project').click(function() {
            var row = $(this).closest('.modal-project-item');
            var delId = row.attr('data-id');
            var delName = row.attr('data-name');

            $.ajax({
                url: window.APP_BASE + 'projects/delete/' + delId,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showToast('🗑️ Project "' + delName + '" deleted');
                        loadProjects();
                        if (activeProjectId == delId) {
                            var def = projectsCache.find(function(x) { return x.is_default; }) || projectsCache[0];
                            if (def) {
                                activeProjectId = def.id;
                                activeProjectName = def.name;
                                $('#activeProjectDisplay').text(activeProjectName);
                                loadNoteFromDatabase(currentDateIso, activeProjectId);
                            } else {
                                activeProjectId = 0;
                                activeProjectName = '';
                                $('#activeProjectDisplay').text('No Project');
                            }
                        }
                    } else {
                        showToast('❌ ' + (res.message || 'Could not delete project'));
                    }
                },
                error: function() {
                    showToast('❌ Network error while deleting');
                }
            });
        });
    }

    // 6. Load Client List from MySQL
    var clientsCache = [];
    function loadClients() {
        $.ajax({
            url: window.APP_BASE + 'clients/get-clients',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    clientsCache = res.clients || [];
                    if (clientsCache.length > 0 && activeClientId == 0) {
                        var def = clientsCache.find(function(x) { return x.is_default; }) || clientsCache[0];
                        if (def) {
                            activeClientId = def.id;
                            activeClientName = def.name;
                            $('#activeClientDisplay').text(activeClientName);
                        }
                    }
                    renderClientsDropdown();
                    renderModalClientsList();
                }
            }
        });
    }

    function renderClientsDropdown() {
        var html = '';
        if (clientsCache.length === 0) {
            html += '<div style="padding:12px; font-size:12px; color:var(--text-muted); text-align:center;">';
            html += '  No clients yet.<br>';
            html += '  <button type="button" class="btn" id="btnDropdownCreateClient" style="margin-top:8px; font-size:11px; padding:4px 10px; background:#0284c7; color:#fff; border:none;"><i class="fa-solid fa-plus"></i> Add Client</button>';
            html += '</div>';
        } else {
            clientsCache.forEach(function(c) {
                var isActive = (c.id == activeClientId || c.name === activeClientName);
                html += '<div class="custom-menu-item ' + (isActive ? 'active' : '') + '" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '">';
                html += '  <span style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-user-tie" style="color:#0284c7;"></i> ' + escapeHtml(c.name) + '</span>';
                if (isActive) {
                    html += '  <i class="fa-solid fa-check text-xs"></i>';
                }
                html += '</div>';
            });
        }
        $('#clientMenuList').html(html);

        $('#btnDropdownCreateClient').click(function() {
            $('#clientDropdownMenu').removeClass('active');
            $('#manageClientModal').addClass('active');
            $('#newClientInput').val('').focus();
        });

        $('#clientMenuList .custom-menu-item').click(function(e) {
            e.stopPropagation();
            activeClientId = $(this).attr('data-id');
            activeClientName = $(this).attr('data-name');
            $('#activeClientDisplay').text(activeClientName);
            $('#clientDropdownMenu').removeClass('active');
            renderClientsDropdown();
            saveNoteToDatabase();
            showToast('Active client set to: ' + activeClientName);
        });
    }

    function renderModalClientsList() {
        var html = '';
        if (clientsCache.length === 0) {
            html = '<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:16px;">No clients in database. Add one above!</div>';
        } else {
            clientsCache.forEach(function(c) {
                html += '<div class="modal-project-item" id="clientRow-' + c.id + '" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" style="padding:8px 10px; border-radius:6px; background:var(--bg-secondary); margin-bottom:8px; border:1px solid var(--border-color);">';
                
                // View Mode
                html += '  <div class="client-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%;">';
                html += '    <span class="modal-project-name" style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;">';
                html += '      <i class="fa-solid fa-user-tie" style="color:#0284c7;"></i>';
                html += '      <span class="client-name-text">' + escapeHtml(c.name) + '</span>';
                if (c.is_default) {
                    html += '  <span style="font-size:10px; color:#15803d; background:#dcfce7; padding:2px 8px; border-radius:10px; font-weight:700;">Default</span>';
                }
                html += '    </span>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn-action-icon btn-start-edit-client" data-id="' + c.id + '" title="Rename Client" style="padding:5px 9px; font-size:11px;"><i class="fa-solid fa-pen"></i></button>';
                if (!c.is_default) {
                    html += '    <button type="button" class="btn-action-icon delete btn-delete-client" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" title="Delete Client" style="padding:5px 9px; font-size:11px;"><i class="fa-solid fa-trash"></i></button>';
                }
                html += '    </div>';
                html += '  </div>';

                // Inline Edit Mode
                html += '  <div class="client-edit-mode" style="display:none; align-items:center; justify-content:space-between; width:100%; gap:8px;">';
                html += '    <div style="display:flex; align-items:center; gap:8px; flex:1;">';
                html += '      <i class="fa-solid fa-user-tie" style="color:#0284c7;"></i>';
                html += '      <input type="text" class="client-inline-input" value="' + escapeHtml(c.name) + '" placeholder="Client name" style="padding:5px 10px; font-size:13px; flex:1; border:1px solid #0284c7; border-radius:6px; background:#ffffff; color:var(--text-main); outline:none;">';
                html += '    </div>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn btn-save-inline-client" data-id="' + c.id + '" title="Save Changes" style="padding:4px 10px; font-size:11px; height:30px; background:#0284c7; color:#fff; border:none;"><i class="fa-solid fa-check"></i> Save</button>';
                html += '      <button type="button" class="btn btn-cancel-inline-client" data-id="' + c.id + '" title="Cancel" style="padding:4px 8px; font-size:11px; height:30px; background:#ffffff; color:#71717a; border:1px solid var(--border-color);"><i class="fa-solid fa-xmark"></i></button>';
                html += '    </div>';
                html += '  </div>';

                html += '</div>';
            });
        }
        $('#modalClientList').html(html);

        // Click Edit Icon
        $('.btn-start-edit-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            row.find('.client-view-mode').hide();
            var editMode = row.find('.client-edit-mode');
            editMode.css('display', 'flex');
            var input = editMode.find('.client-inline-input');
            input.focus().select();
        });

        // Click Cancel
        $('.btn-cancel-inline-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            var origName = row.attr('data-name');
            row.find('.client-inline-input').val(origName);
            row.find('.client-edit-mode').hide();
            row.find('.client-view-mode').css('display', 'flex');
        });

        function saveInlineClient(row) {
            var editId = row.attr('data-id');
            var origName = row.attr('data-name');
            var input = row.find('.client-inline-input');
            var newName = input.val().trim();

            if (!newName) {
                showToast('Client name cannot be empty');
                input.focus();
                return;
            }

            if (newName === origName) {
                row.find('.client-edit-mode').hide();
                row.find('.client-view-mode').css('display', 'flex');
                return;
            }

            $.ajax({
                url: window.APP_BASE + 'clients/edit/' + editId,
                type: 'POST',
                data: { name: newName },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showToast('Client renamed to: ' + newName);
                        row.attr('data-name', newName);
                        row.find('.client-name-text').text(newName);
                        row.find('.client-edit-mode').hide();
                        row.find('.client-view-mode').css('display', 'flex');

                        var cItem = clientsCache.find(function(x) { return x.id == editId; });
                        if (cItem) cItem.name = newName;
                        if (activeClientId == editId) {
                            activeClientName = newName;
                            $('#activeClientDisplay').text(activeClientName);
                        }
                        renderClientsDropdown();
                    } else {
                        showToast(res.message || 'Failed to rename');
                    }
                },
                error: function() {
                    showToast('Network error while saving');
                }
            });
        }

        $('.btn-save-inline-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            saveInlineClient(row);
        });

        $('.client-inline-input').on('keydown', function(e) {
            var row = $(this).closest('.modal-project-item');
            if (e.key === 'Enter') {
                e.preventDefault();
                saveInlineClient(row);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                row.find('.btn-cancel-inline-client').click();
            }
        });

        // Delete Client
        $('.btn-delete-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            var delId = row.attr('data-id');
            var delName = row.attr('data-name');

            $.ajax({
                url: window.APP_BASE + 'clients/delete/' + delId,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showToast('Client "' + delName + '" deleted');
                        loadClients();
                        if (activeClientId == delId) {
                            var def = clientsCache.find(function(x) { return x.is_default; }) || clientsCache[0];
                            if (def) {
                                activeClientId = def.id;
                                activeClientName = def.name;
                                $('#activeClientDisplay').text(activeClientName);
                            } else {
                                activeClientId = 0;
                                activeClientName = '';
                                $('#activeClientDisplay').text('Select Client');
                            }
                        }
                    } else {
                        showToast(res.message || 'Could not delete client');
                    }
                },
                error: function() {
                    showToast('Network error while deleting');
                }
            });
        });
    }

    var maxAllowedIso = '<?= h($todayIso) ?>';

    function setDate(isoDate) {
        currentDateIso = isoDate;
        currentDateFormatted = isoToFormatted(isoDate);

        $('#datePicker').val(isoDate);
        $('#activeDateTitle').text(currentDateFormatted);

        var parts = isoDate.split('-');
        var y = parseInt(parts[0]);
        var m = parseInt(parts[1]);
        if (y !== currentFilterYear || m !== currentFilterMonth) {
            currentFilterYear = y;
            currentFilterMonth = m;
            loadSidebarLogs();
        } else {
            renderSidebarLogs();
        }

        loadNoteFromDatabase(isoDate, activeProjectId);
        updateNextDayButtonState();
    }

    function updateNextDayButtonState() {
        var isTodayOrFuture = (currentDateIso >= maxAllowedIso);
        $('#btnNextDay').prop('disabled', isTodayOrFuture).css('opacity', isTodayOrFuture ? '0.4' : '1');
    }

    // Prev / Next Navigation
    $('#btnPrevDay').click(function() {
        var d = new Date(currentDateIso);
        d.setDate(d.getDate() - 1);
        var iso = d.toISOString().split('T')[0];
        setDate(iso);
    });

    $('#btnNextDay').click(function() {
        var d = new Date(currentDateIso);
        d.setDate(d.getDate() + 1);
        var iso = d.toISOString().split('T')[0];
        if (iso <= maxAllowedIso) {
            setDate(iso);
        }
    });

    $('#btnToday').click(function() {
        setDate(maxAllowedIso);
    });

    $('#datePicker').on('change', function() {
        var val = $(this).val();
        if (val) {
            if (val > maxAllowedIso) {
                val = maxAllowedIso;
                $(this).val(val);
            }
            setDate(val);
        }
    });

    // Month & Year Filter Controls
    $('#btnPrevMonth').click(function() {
        currentFilterMonth--;
        if (currentFilterMonth < 1) {
            currentFilterMonth = 12;
            currentFilterYear--;
        }
        loadSidebarLogs();
    });

    $('#btnNextMonth').click(function() {
        currentFilterMonth++;
        if (currentFilterMonth > 12) {
            currentFilterMonth = 1;
            currentFilterYear++;
        }
        loadSidebarLogs();
    });

    $('#searchInput').on('input', renderSidebarLogs);

    // Insert Template
    $('#btnInsertTemplate').click(function() {
        var tpl = currentDateFormatted + '\n-------------------\nBackend:\n- \n\nFrontend:\n- \n';
        var editor = $('#workNotesEditor');
        if (editor.val().trim().length > 0) {
            if (confirm('Insert template at top of current note?')) {
                editor.val(tpl + '\n' + editor.val());
            }
        } else {
            editor.val(tpl);
        }
        updateCharCount();
        saveNoteToDatabase();
        showToast('Template inserted!');
    });

    // Clear Note
    $('#btnClearCurrent').click(clearNoteFromDatabase);

    // Continue to Email Button
    $('#btnContinueToEmail').click(function(e) {
        e.preventDefault();
        saveNoteToDatabase();
        var url = window.APP_BASE + 'daily-updates?project_id=' + activeProjectId + '&date=' + currentDateIso;
        if (activeProjectName) {
            url += '&project_name=' + encodeURIComponent(activeProjectName);
        }
        if (activeClientName) {
            url += '&client_name=' + encodeURIComponent(activeClientName);
        }
        window.location.href = url;
    });

    // Project Dropdown Toggle
    $('#btnProjectDropdownToggle').click(function(e) {
        e.stopPropagation();
        $('#clientDropdownMenu').removeClass('active');
        $('#projectDropdownMenu').toggleClass('active');
    });

    // Client Dropdown Toggle
    $('#btnClientDropdownToggle').click(function(e) {
        e.stopPropagation();
        $('#projectDropdownMenu').removeClass('active');
        $('#clientDropdownMenu').toggleClass('active');
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('.custom-project-wrapper').length) {
            $('#projectDropdownMenu').removeClass('active');
            $('#clientDropdownMenu').removeClass('active');
        }
    });

    // Add Project Modal
    $('#btnAddProject, #btnBannerCreateProj').click(function() {
        $('#manageProjectModal').addClass('active');
        $('#newProjectInput').val('').focus();
    });

    $('#btnCloseProjectModal, #manageProjectModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('#manageProjectModal').removeClass('active');
        }
    });

    $('#btnSaveNewProject').click(function() {
        var val = $('#newProjectInput').val().trim();
        if (!val) return;

        $.ajax({
            url: window.APP_BASE + 'projects/add',
            type: 'POST',
            data: { name: val },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message);
                    loadProjects();
                    activeProjectId = res.project.id;
                    activeProjectName = res.project.name;
                    $('#activeProjectDisplay').text(activeProjectName);
                    $('#newProjectInput').val('');
                    $('#manageProjectModal').removeClass('active');
                    saveNoteToDatabase();
                } else {
                    alert(res.message || 'Failed to add project');
                }
            }
        });
    });

    // Add Client Modal
    $('#btnAddClient').click(function() {
        $('#manageClientModal').addClass('active');
        $('#newClientInput').val('').focus();
    });

    $('#btnCloseClientModal, #manageClientModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('#manageClientModal').removeClass('active');
        }
    });

    $('#btnSaveNewClient').click(function() {
        var val = $('#newClientInput').val().trim();
        if (!val) return;

        $.ajax({
            url: window.APP_BASE + 'clients/add',
            type: 'POST',
            data: { name: val },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message);
                    loadClients();
                    activeClientId = res.client.id;
                    activeClientName = res.client.name;
                    $('#activeClientDisplay').text(activeClientName);
                    $('#newClientInput').val('');
                    $('#manageClientModal').removeClass('active');
                    saveNoteToDatabase();
                } else {
                    alert(res.message || 'Failed to add client');
                }
            }
        });
    });

    // Global Search Modal
    $('#btnOpenGlobalSearch').click(function() {
        $('#globalSearchModal').addClass('active');
        $('#globalSearchInput').val('').focus();
        $('#globalSearchResults').html('<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Type any keyword above to search MySQL database.</div>');
    });

    $('#btnCloseSearchModal, #globalSearchModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('#globalSearchModal').removeClass('active');
        }
    });

    var searchTimer = null;
    $('#globalSearchInput').on('input', function() {
        var q = $(this).val().trim();
        clearTimeout(searchTimer);
        if (!q) {
            $('#globalSearchResults').html('<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Type any keyword above to search MySQL database.</div>');
            return;
        }

        searchTimer = setTimeout(function() {
            $.ajax({
                url: window.APP_BASE + 'tasks/global-search',
                type: 'GET',
                data: { q: q },
                dataType: 'json',
                success: function(res) {
                    if (!res.results || res.results.length === 0) {
                        $('#globalSearchResults').html('<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">No matching notes found for "' + escapeHtml(q) + '".</div>');
                        return;
                    }

                    var html = '';
                    res.results.forEach(function(item) {
                        html += '<div class="result-card" data-iso="' + item.iso_date + '" data-project-id="' + item.project_id + '" data-project-name="' + escapeHtml(item.project_name) + '">';
                        html += '  <div class="result-header">';
                        html += '    <span class="result-date"><i class="fa-regular fa-calendar"></i> ' + item.date_formatted + '</span>';
                        html += '    <span class="result-project"><i class="fa-solid fa-folder"></i> ' + escapeHtml(item.project_name) + '</span>';
                        html += '  </div>';
                        var preview = item.content.length > 200 ? item.content.substring(0, 200) + '...' : item.content;
                        html += '  <div class="result-snippet">' + escapeHtml(preview) + '</div>';
                        html += '</div>';
                    });

                    $('#globalSearchResults').html(html);

                    $('#globalSearchResults .result-card').click(function() {
                        var iso = $(this).attr('data-iso');
                        var pid = $(this).attr('data-project-id');
                        var pname = $(this).attr('data-project-name');

                        activeProjectId = pid;
                        activeProjectName = pname;
                        $('#activeProjectDisplay').text(activeProjectName);
                        renderProjectsDropdown();
                        $('#globalSearchModal').removeClass('active');
                        setDate(iso);
                    });
                }
            });
        }, 300);
    });

    // --- Gemini AI Grammar & Sentence Polish ---
    // --- Gemini AI Key Database Storage (MySQL users table) ---
    var userDatabaseApiKey = <?= json_encode($userApiKey ?? '') ?>;
    try {
        localStorage.removeItem('gemini_api_key');
    } catch(e) {}

    function getGeminiKey() {
        return userDatabaseApiKey || '';
    }

    // Toggle API Key visibility
    $('#btnToggleApiKeyVis').click(function() {
        var input = $('#geminiApiKeyInput');
        var icon = $('#apiKeyEyeIcon');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Open API Key modal
    $('#btnChangeAiKey').click(function() {
        $('#aiPolishModal').removeClass('active');
        $('#geminiKeyErrorMsg').hide().text('');
        $('#geminiApiKeyInput').val(userDatabaseApiKey);
        $('#geminiKeyModal').addClass('active');
    });

    $('#btnCloseGeminiKeyModal, #geminiKeyModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            $('#geminiKeyModal').removeClass('active');
        }
    });

    $('#btnSaveGeminiKey').click(function() {
        var key = $('#geminiApiKeyInput').val().trim();
        if (!key) {
            $('#geminiKeyErrorMsg').text('Please enter a valid Gemini API Key.').show();
            return;
        }

        $('#geminiKeyErrorMsg').hide().text('');
        var btn = $(this);
        var origHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Validating & Saving...');

        $.ajax({
            url: window.APP_BASE + 'tasks/save-gemini-key',
            type: 'POST',
            data: { api_key: key },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    userDatabaseApiKey = key;
                    $('#geminiKeyModal').removeClass('active');
                    showToast('Gemini API Key validated & saved to database!');
                    runAiPolish();
                } else {
                    $('#geminiKeyErrorMsg').text(res.message || 'Invalid API Key. Please check and try again.').show();
                }
            },
            error: function() {
                btn.prop('disabled', false).html(origHtml);
                $('#geminiKeyErrorMsg').text('Server error while validating API Key.').show();
            }
        });
    });

    // Close AI Polish Modal
    $('#btnCloseAiPolishModal, #btnCancelAiPolish, #aiPolishModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal') || e.target.id === 'btnCancelAiPolish') {
            $('#aiPolishModal').removeClass('active');
        }
    });

    // Copy Polished Text
    $('#btnCopyAiPolish').click(function() {
        var text = $('#aiPolishedTextarea').val();
        navigator.clipboard.writeText(text).then(function() {
            showToast('Polished text copied to clipboard!');
        });
    });

    // Apply Polished Text to Editor
    $('#btnApplyAiPolish').click(function() {
        var text = $('#aiPolishedTextarea').val();
        $('#workNotesEditor').val(text);
        updateCharCount();
        saveNoteToDatabase();
        $('#aiPolishModal').removeClass('active');
        showToast('Applied AI Polished notes to editor & saved!');
    });

    // Trigger AI Polish
    $('#btnAiPolish').click(function() {
        var content = $('#workNotesEditor').val().trim();
        if (!content) {
            showToast('Please type your task points before running AI Polish.');
            return;
        }

        var key = getGeminiKey();
        if (!key) {
            $('#geminiKeyErrorMsg').hide().text('');
            $('#geminiApiKeyInput').val('');
            $('#geminiKeyModal').addClass('active');
            return;
        }

        runAiPolish();
    });

    function runAiPolish() {
        var content = $('#workNotesEditor').val().trim();
        if (!content) return;

        var key = getGeminiKey();
        if (!key) {
            $('#geminiKeyErrorMsg').hide().text('');
            $('#geminiApiKeyInput').val('');
            $('#geminiKeyModal').addClass('active');
            return;
        }

        var btn = $('#btnAiPolish');
        var origHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Polishing with AI...');

        var promptText = "You are an expert technical editor for daily engineering updates and standups.\n" +
            "Please review and improve the following daily task notes.\n" +
            "Goals:\n" +
            "1. Fix all grammar, spelling, verb tense, and awkward phrasing errors.\n" +
            "2. Ensure each bullet point sounds professional, concise, clear, and action-oriented (e.g. 'Implemented...', 'Fixed...', 'Resolved...', 'Configured...').\n" +
            "3. PRESERVE the exact structure, headers (e.g. 'DD-MM-YYYY ProjectName', 'Backend:', 'Frontend:'), URLs, ticket IDs, and technical terminology exactly.\n" +
            "4. Do NOT add any conversational intro, outro, or markdown code fence blocks. Output ONLY the polished plain text.\n\n" +
            "Input Tasks Note:\n" + content;

        $.ajax({
            url: window.APP_BASE + 'tasks/ai-polish',
            type: 'POST',
            data: {
                content: content,
                api_key: key
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html(origHtml);
                if (res.success && res.polished) {
                    $('#aiPolishedTextarea').val(res.polished);
                    $('#aiPolishModal').addClass('active');
                    setTimeout(function() {
                        var ta = document.getElementById('aiPolishedTextarea');
                        if (ta) {
                            ta.style.height = 'auto';
                            var desiredHeight = Math.max(ta.scrollHeight + 15, 520);
                            var maxAllowed = window.innerHeight * 0.72;
                            ta.style.height = Math.min(desiredHeight, maxAllowed) + 'px';
                        }
                    }, 40);
                    showToast('AI Polish complete! Review & apply.');
                } else {
                    showToast(res.message || 'Failed to polish text with AI');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(origHtml);
                showToast('Server error communicating with Gemini API.');
            }
        });
    }

    // Boot App
    loadProjects();
    loadClients();
    loadSidebarLogs();
    updateCharCount();
    updateNextDayButtonState();

    var initialFlash = $('#initialFlashHolder').text().trim();
    if (initialFlash) {
        showToast(initialFlash);
    }
});
</script>
