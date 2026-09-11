<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Client> $clients
 * @var iterable<\App\Model\Entity\Project> $projects
 * @var \App\Model\Entity\Project|null $defaultProject
 * @var string $todayIso
 * @var string $todayFormatted
 * @var \App\Model\Entity\DailyUpdate|null $savedUpdate
 * @var string $autoDoneTasks
 * @var string $detectedProjectName
 */
$this->assign('title', 'Daily Update & Email Generator - Helpdesk');
$this->assign('meta_description', 'Convert daily task logs into structured client updates, preview emails in real-time, and dispatch updates securely via SMTP.');
$this->assign('meta_keywords', 'daily work update, email generator, client communication, task report, helpdesk');
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html, body {
        height: 100%;
        overflow: hidden;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: var(--bg-main);
        color: var(--text-main);
        padding: 16px 20px;
        font-size: 14px;
        display: flex;
        flex-direction: column;
    }

    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        width: 100%;
        gap: 14px;
    }

    /* Top Header matching Tasks */
    header.app-header {
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
        background: var(--primary);
        color: var(--bg-card);
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

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-dark {
        background: #1c201e;
        color: #f7f6f0;
        border: 1px solid #1c201e;
    }

    .btn-dark:hover {
        background: #343a37;
        color: #ffffff;
        border-color: #343a37;
    }

    .btn-primary {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #ffffff;
    }

    .user-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 6px 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
    }

    .main-content-row {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        min-height: 0;
    }

    .card-panel, .preview-card {
        background: var(--bg-card);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .card-panel {
        padding: 16px 20px;
        overflow-y: auto;
    }

    .panel-header {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-group {
        margin-bottom: 6px;
    }

    label {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 5px;
        display: block;
    }

    .form-control {
        width: 100%;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
        padding: 8px 12px;
        font-size: 13px;
        color: var(--text-main);
        background-color: var(--bg-editor);
        transition: all 0.15s ease;
        outline: none;
        font-family: inherit;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    textarea.task_detail {
        resize: vertical;
        min-height: 64px;
        line-height: 1.5;
        font-family: inherit;
    }

    /* Preview Card */
    .preview-header {
        background: var(--bg-secondary);
        padding: 12px 18px;
        border-bottom: 1px solid var(--border-color);
        border-top-left-radius: var(--radius-md);
        border-top-right-radius: var(--radius-md);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .preview-header h3 {
        margin: 0;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .btn-copy-subject {
        background: var(--bg-card);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        font-family: inherit;
    }

    .btn-copy-subject:hover {
        background: var(--bg-secondary);
        border-color: var(--border-hover);
    }

    .btn-copy-content {
        background: var(--primary);
        color: #ffffff;
        border: 1px solid var(--primary);
        border-radius: var(--radius-sm);
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        font-family: inherit;
    }

    .btn-copy-content:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .email-body {
        padding: 20px;
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-main);
        background: var(--bg-card);
        flex: 1;
        min-height: 0;
        overflow-y: auto;
    }

    .subject-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 12px;
        gap: 12px;
    }

    .subject-row .subject {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        color: var(--text-main);
        font-size: 15px;
        margin: 0;
        flex-grow: 1;
    }

    .content-header-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 12px;
    }

    ol {
        padding-left: 22px;
        margin-top: 5px;
        margin-bottom: 14px;
    }

    li {
        margin-bottom: 5px;
    }

    /* Toast */
    #toast {
        visibility: hidden;
        min-width: 220px;
        background-color: #1c201e;
        color: #f7f6f0;
        text-align: center;
        border-radius: var(--radius-sm);
        padding: 10px 18px;
        position: fixed;
        z-index: 9999;
        right: 24px;
        bottom: 24px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: var(--shadow-md);
        opacity: 0;
        transition: opacity 0.25s, bottom 0.25s;
    }

    #toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 28px;
    }

    #toast.toast-success, #toast.success {
        background-color: #059669 !important;
        color: #ffffff !important;
    }

    #toast.toast-error, #toast.error {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }

    #toast.toast-warning, #toast.warning {
        background-color: #d97706 !important;
        color: #ffffff !important;
    }

    #toast.toast-info, #toast.info {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }

    /* Modal Overlay & Card */
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
        width: 95%;
        max-width: 760px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-editor);
        flex-shrink: 0;
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
        font-size: 22px;
        color: var(--text-muted);
        cursor: pointer;
        padding: 2px 8px;
        border-radius: 6px;
        line-height: 1;
        transition: all 0.15s ease;
    }

    .btn-close-modal:hover {
        background: var(--bg-secondary);
        color: var(--text-main);
    }

    .modal-body {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        overflow-y: auto;
        flex: 1;
        min-height: 0; /* CRITICAL: Enables proper nested flex scrolling on all screens */
        background: #ffffff;
    }

    .modal-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-editor);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }

    .compose-field-row {
        display: flex;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        padding: 6px 2px;
        gap: 10px;
        transition: all 0.15s ease;
        min-width: 0;
    }

    .compose-from-row {
        background: var(--bg-editor);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .compose-from-meta {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        min-width: 0;
        flex-wrap: wrap;
    }

    .compose-from-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        word-break: break-all;
    }

    .compose-account-badge {
        font-size: 10.5px;
        background: #e0e7ff;
        color: #4338ca;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 700;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .compose-field-row.hidden-row {
        display: none !important;
    }

    .compose-field-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted);
        min-width: 50px;
        user-select: none;
        flex-shrink: 0;
    }

    .compose-field-input {
        flex: 1;
        min-width: 0;
        border: none;
        background: transparent;
        font-size: 13.5px;
        color: var(--text-main);
        outline: none;
        padding: 4px 0;
        font-family: inherit;
    }

    .compose-field-input:focus {
        outline: none;
    }

    .compose-toggles {
        display: flex;
        align-items: center;
        gap: 4px;
        user-select: none;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .compose-toggle-btn {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        padding: 2px 7px;
        border-radius: 4px;
        transition: all 0.15s ease;
    }

    .compose-toggle-btn:hover {
        color: var(--primary);
        border-color: var(--primary);
        background: #ffffff;
    }

    .email-preview-box {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        flex: 1;
        min-height: 120px;
        max-height: 36vh;
        overflow-y: auto;
        font-size: 13px;
        line-height: 1.6;
        color: var(--text-main);
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .email-preview-box a, .mail_body a, #emailHtmlPreviewContainer a {
        color: rgb(59, 130, 246) !important;
        text-decoration: underline !important;
        cursor: pointer !important;
        word-break: break-all;
        transition: color 0.15s ease;
    }

    .email-preview-box a:hover, .mail_body a:hover, #emailHtmlPreviewContainer a:hover {
        color: rgb(30, 58, 138) !important;
        text-decoration: none !important;
    }

    .alert-box {
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 500;
    }

    .alert-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .btn-send-email-bottom {
        background: var(--primary);
        color: #ffffff;
        border: 1px solid var(--primary);
        border-radius: var(--radius-sm);
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.15s ease;
        font-family: inherit;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
    }

    .btn-send-email-bottom:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* User Menu Dropdown Popover */
    .user-menu-wrapper {
        position: relative;
        display: inline-block;
    }

    .user-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 5px 12px;
        border-radius: var(--radius-sm);
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        font-family: inherit;
    }

    .user-pill-btn:hover, .user-pill-btn.active {
        background: #f8fafc;
        border-color: #6366f1;
        color: #4f46e5;
    }

    .user-avatar-badge {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .user-popover-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        min-width: 230px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        padding: 6px;
        z-index: 1060;
        display: none;
    }

    .user-popover-menu.show {
        display: block;
        animation: popoverFadeIn 0.15s ease-out;
    }

    @keyframes popoverFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .user-popover-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px 6px 10px;
    }

    .user-popover-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-popover-meta {
        overflow: hidden;
    }

    .user-popover-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-popover-email {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-popover-divider {
        height: 1px;
        background: var(--border-color);
        margin: 4px 0;
    }

    .user-popover-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-family: inherit;
    }

    .user-popover-item:hover {
        background: #f1f5f9;
        color: #4f46e5;
    }

    .user-popover-item.item-logout {
        color: #ef4444;
    }

    .user-popover-item.item-logout:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Multi-Device Responsive Breakpoints */
    @media (max-width: 1200px) {
        .container-fluid {
            padding: 12px 16px;
        }
        .main-content-row {
            grid-template-columns: 1.1fr 1fr;
            gap: 14px;
        }
    }

    @media (max-width: 991px) {
        html, body {
            height: auto;
            overflow-y: auto;
        }
        .container-fluid {
            padding: 10px;
            height: auto;
            min-height: calc(100vh - 20px);
        }
        .app-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 12px 14px;
        }
        .header-actions {
            width: 100%;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .header-actions .btn {
            flex: 1 1 auto;
            justify-content: center;
            padding: 8px 12px;
            font-size: 12px;
        }
        .main-content-row {
            grid-template-columns: 1fr;
            height: auto;
            gap: 14px;
        }
        .card-panel, .preview-card {
            height: auto;
            max-height: none;
            min-height: auto;
        }
        .preview-footer {
            display: flex !important;
            padding: 10px 14px !important;
        }
        #btnSendEmailBottom {
            width: 100%;
            justify-content: center;
            padding: 10px 16px;
            font-size: 13.5px;
        }
    }

    @media (max-width: 767px) {
        .container-fluid {
            padding: 6px;
        }
        .brand-title {
            font-size: 16px;
        }
        .brand-subtitle {
            display: none;
        }
        .brand-icon {
            width: 34px;
            height: 34px;
            font-size: 15px;
        }
        .header-actions .btn {
            font-size: 11px;
            padding: 7px 8px;
        }
        .card-panel, .preview-card {
            padding: 12px 14px;
        }
        .subject-row {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .btn-copy-subject, .btn-copy-content {
            width: 100%;
            justify-content: center;
            padding: 7px 12px;
            font-size: 12px;
        }
        .modal-card {
            width: 95% !important;
            max-width: 95% !important;
            margin: 10px auto !important;
            max-height: 92vh !important;
            border-radius: var(--radius-md) !important;
        }
        .profile-modal-card {
            width: 95% !important;
            max-width: 95% !important;
            max-height: 92vh !important;
        }
        .profile-modal-body {
            padding: 12px 14px !important;
            gap: 12px !important;
        }
        .profile-modal-body div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .profile-banner-card {
            padding: 10px 12px !important;
            gap: 10px !important;
        }
        .profile-banner-sub {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 3px !important;
        }
        .profile-avatar-circle {
            width: 40px !important;
            height: 40px !important;
            font-size: 17px !important;
        }
        #sendEmailModal {
            padding: 8px !important;
        }
        .send-email-modal-card {
            width: 100% !important;
            max-width: 100% !important;
            max-height: 94vh !important;
            border-radius: var(--radius-md) !important;
        }
        .send-email-modal-card .modal-header {
            padding: 12px 14px !important;
        }
        .send-email-modal-card .modal-title {
            font-size: 15px !important;
        }
        .send-email-modal-card .modal-body {
            padding: 12px 14px !important;
            gap: 8px !important;
        }
        .send-email-modal-card .compose-from-row {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 4px !important;
            padding: 8px 10px !important;
        }
        .send-email-modal-card .compose-from-meta {
            width: 100% !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 4px !important;
        }
        .send-email-modal-card .compose-field-row {
            padding: 6px 0 !important;
        }
        .send-email-modal-card .compose-field-label {
            min-width: 44px !important;
            font-size: 12px !important;
        }
        .send-email-modal-card .compose-field-input {
            font-size: 13px !important;
        }
        .send-email-modal-card .email-preview-box {
            min-height: 100px !important;
            max-height: 25vh !important;
            font-size: 12px !important;
            padding: 8px 10px !important;
        }
        .send-email-modal-card .modal-footer {
            padding: 10px 14px !important;
            display: flex !important;
            gap: 8px !important;
        }
        .send-email-modal-card .modal-footer .btn {
            flex: 1 !important;
            justify-content: center !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
        }
        .user-menu-wrapper {
            width: 100% !important;
        }
        .user-pill-btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .user-popover-menu {
            width: 100% !important;
            min-width: 240px !important;
            max-width: 100% !important;
            left: 0 !important;
            right: 0 !important;
        }
    }

    @media (max-width: 480px) {
        .app-header {
            padding: 10px 12px;
        }
        .header-actions {
            gap: 6px;
        }
        .header-actions .btn {
            font-size: 10.5px;
            padding: 6px 6px;
            gap: 4px;
        }
        .card-panel, .preview-card {
            padding: 10px 12px;
        }
        .form-control {
            font-size: 13px !important;
            padding: 8px 10px !important;
        }
        #sendEmailModal {
            padding: 4px !important;
        }
        .send-email-modal-card {
            width: 100% !important;
            max-width: 100% !important;
            max-height: 96vh !important;
        }
        .send-email-modal-card .modal-header {
            padding: 10px 12px !important;
        }
        .send-email-modal-card .modal-body {
            padding: 10px 12px !important;
        }
        .send-email-modal-card .modal-footer {
            padding: 8px 12px !important;
        }
        .compose-toggle-btn {
            padding: 2px 6px !important;
            font-size: 11px !important;
        }
        #toast {
            left: 14px !important;
            right: 14px !important;
            bottom: 14px !important;
            min-width: 0 !important;
            width: auto !important;
            max-width: calc(100% - 28px) !important;
            margin: 0 auto !important;
            justify-content: center !important;
            text-align: center !important;
        }
    }

    /* Small Screen Height (e.g. Laptops with small height, landscape phones) */
    @media (max-height: 768px) {
        .send-email-modal-card {
            max-height: 96vh !important;
        }
        .send-email-modal-card .modal-header {
            padding: 8px 16px !important;
        }
        .send-email-modal-card .modal-body {
            padding: 8px 16px !important;
            gap: 6px !important;
        }
        .send-email-modal-card .compose-field-row {
            padding: 4px 0 !important;
        }
        .send-email-modal-card .compose-from-row {
            padding: 5px 8px !important;
        }
        .send-email-modal-card .email-preview-box {
            min-height: 80px !important;
            max-height: 140px !important;
            padding: 6px 10px !important;
        }
        .send-email-modal-card .modal-footer {
            padding: 8px 16px !important;
        }
    }
</style>

<div class="container-fluid">
    <!-- Top Header matching Tasks theme -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <h1 class="brand-title">Daily Update Generator</h1>
                <p class="brand-subtitle">Track your tasks and generate daily work updates</p>
            </div>
        </div>

        <div class="header-actions">
            <a href="<?= $this->Url->build(['controller' => 'Tasks', 'action' => 'index']) ?>" class="btn btn-dark" id="btnBackToTasks" title="Back to Task Notepad">
                <i class="fa-solid fa-arrow-left"></i> Back to Tasks
            </a>
            <button type="button" class="btn" id="btnSyncFromDb">
                <i class="fa-solid fa-arrows-rotate"></i> Sync Today's Tasks
            </button>
            <button type="button" class="btn" id="btnResetTasks">
                <i class="fa-solid fa-rotate-left"></i> Reset Tasks
            </button>

            <?php if (!empty($currentUser)): ?>
                <!-- User Menu Dropdown -->
                <div class="user-menu-wrapper">
                    <button type="button" class="user-pill-btn" id="btnUserMenuToggle" title="Click for Profile & Logout">
                        <span class="user-avatar-badge"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></span>
                        <span class="user-pill-name"><?= h($currentUser['name']) ?></span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 10px; opacity: 0.7;"></i>
                    </button>
                    <div class="user-popover-menu" id="userPopoverMenu">
                        <div class="user-popover-header">
                            <div class="user-popover-avatar"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></div>
                            <div class="user-popover-meta">
                                <div class="user-popover-name"><?= h($currentUser['name']) ?></div>
                                <div class="user-popover-email"><?= h($currentUser['email'] ?? '') ?></div>
                            </div>
                        </div>
                        <div class="user-popover-divider"></div>
                        <button type="button" class="user-popover-item" id="btnOpenProfileModal">
                            <i class="fa-solid fa-user-gear" style="color: #4f46e5;"></i>
                            <span>Profile</span>
                        </button>
                        <button type="button" class="user-popover-item" id="btnThemeTogglePopover">
                            <i class="fa-solid fa-moon" style="color: #64748b; width: 16px;"></i>
                            <span>Switch to Dark Mode</span>
                        </button>
                        <div class="user-popover-divider"></div>
                        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>" class="user-popover-item item-logout" id="btnLogoutLink">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Grid -->
    <div class="main-content-row">
        <!-- Form Input Panel -->
        <div class="card-panel">
            <div class="panel-header">
                <span><i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Update Details</span>
                <span id="updateSaveStatus" style="font-size:11px; color:var(--text-muted);">Synced with Database</span>
            </div>

            <div class="form-group">
                <label for="client_name">Client Name :</label>
                <input type="text" class="form-control" id="client_name" placeholder="Enter Client Name" autocomplete="off" value="<?= h($detectedClientName ?: ($savedUpdate && $savedUpdate->client_name ? $savedUpdate->client_name : 'Hitesh sir')) ?>">
            </div>

            <div class="form-group">
                <label for="project_name">Project Name :</label>
                <input type="text" class="form-control" id="project_name" placeholder="Enter Project Name" autocomplete="off" value="<?= h($detectedProjectName ?: ($savedUpdate && $savedUpdate->project_name ? $savedUpdate->project_name : ($defaultProject ? $defaultProject->name : ''))) ?>">
            </div>

            <div class="form-group">
                <label class="task_label" for="txt_done_task">List of Completed Tasks :</label>
                <textarea placeholder="Paste all Done Tasks here (one per line)..." name="list_done" id="txt_done_task" class="form-control custom-input task_detail done_task" rows="3"><?= h($initialDoneTasks ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="task_label" for="txt_progress_task">List of In-Progress Tasks :</label>
                <textarea placeholder="Paste In-Progress Tasks here..." name="list_progress" id="txt_progress_task" class="form-control custom-input task_detail progress_task" rows="3"><?= h($savedUpdate ? $savedUpdate->progress_tasks : '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="task_label" for="txt_remaining_task">List of Remaining Tasks :</label>
                <textarea placeholder="Paste Remaining Tasks here..." name="list_remaining" id="txt_remaining_task" class="form-control custom-input task_detail remaining_task" rows="3"><?= h($savedUpdate ? $savedUpdate->remaining_tasks : '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="task_label" for="txt_query">Queries :</label>
                <textarea placeholder="Paste Queries here..." name="list_query" id="txt_query" class="form-control custom-input task_detail query_task" rows="3"><?= h($savedUpdate ? $savedUpdate->queries : '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="task_label" for="txt_note">Notes :</label>
                <textarea placeholder="Paste Notes here..." name="list_note" id="txt_note" class="form-control custom-input task_detail note_task" rows="3"><?= h($savedUpdate ? $savedUpdate->notes : '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="tl_name">TL Name :</label>
                <input type="text" class="form-control" id="tl_name" placeholder="TL Name" value="<?= h($detectedClientName ? (rtrim($detectedClientName, '.') . '.') : ($savedUpdate && $savedUpdate->tl_name ? $savedUpdate->tl_name : 'Hitesh sir.')) ?>">
            </div>
        </div>

        <!-- Live Email Preview Panel -->
        <div class="preview-card">
            <div class="preview-header">
                <h3><i class="fa-solid fa-eye" style="color: var(--primary);"></i> Live Email Preview</h3>
            </div>

            <div class="email-body">
                <div class="mail_body_wrapper">
                    <div class="subject-row">
                        <span class="subject"></span>
                        <button type="button" class="btn-copy-subject" id="btnCopySubject"><i class="fa-solid fa-heading"></i> Copy Subject</button>
                    </div>

                    <div class="content-header-row">
                        <button type="button" class="btn-copy-content" id="btnCopyContent">
                            <i class="fa-solid fa-copy"></i> Copy Content
                        </button>
                    </div>

                    <div class="mail_body">
                        <span class="client_name"></span>
                        <span class="update_msg"></span>
                        <span class="list_done"></span>
                        <span class="list_progress"></span>
                        <span class="list_remaining"></span>
                        <span class="list_query"></span>
                        <span class="list_note"></span>
                        <span class="review_note"></span>
                        <span class="thanks"></span>
                        <span class="total_worked"></span>
                    </div>
                </div>
            </div>

            <!-- Bottom Right Send Email / Setup Note Bar -->
            <div class="preview-footer" id="previewFooterSend" style="padding: 10px 18px; border-top: 1px solid var(--border-color); background: var(--bg-secondary); display: none; align-items: center; justify-content: space-between; flex-shrink: 0; border-bottom-left-radius: var(--radius-md); border-bottom-right-radius: var(--radius-md);">
                <!-- Helpful Note for Users who haven't set up SMTP password yet -->
                <div id="smtpSetupNotice" style="display: none; align-items: center; gap: 8px; font-size: 12px; color: #475569; line-height: 1.4; flex: 1;">
                    <i class="fa-solid fa-circle-info" style="color: #3b82f6; font-size: 14px; flex-shrink: 0;"></i>
                    <span>To send updates directly from Helpdesk, click your <strong>Name (Profile)</strong> in the top-right and add your <strong>16-digit Google App Password</strong>.</span>
                </div>

                <!-- Send Email Button (for configured users) -->
                <button type="button" class="btn-send-email-bottom" id="btnSendEmailBottom" style="display: none; margin-left: auto;">
                    <i class="fa-solid fa-paper-plane"></i> Send Email
                </button>
            </div>
        </div>
    </div>

    <!-- Send Email Modal (Fully Responsive for All Screen Sizes) -->
    <div id="sendEmailModal" class="modal-overlay">
        <div class="modal-card send-email-modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-envelope-open-text" style="color: var(--primary);"></i>
                    <span>Send Daily Update Email</span>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseSendEmailModal" title="Close Modal">&times;</button>
            </div>
            
            <div class="modal-body" style="padding: 16px 20px 20px 20px;">
                <div id="sendEmailAlert" class="alert alert-danger" style="display: none; font-size: 12px; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px;"></div>

                <!-- From Field (Readonly pill display) -->
                <div class="compose-from-row" style="margin-bottom: 10px;">
                    <span class="compose-field-label">From</span>
                    <div class="compose-from-meta">
                        <span class="compose-from-text"><span id="emailSenderName"><?= h($currentUser['name'] ?? 'Your Name') ?></span> &lt;<span id="emailSenderAddress"><?= h($currentUser['email'] ?? 'your.email@queueloopsolutions.com') ?></span>&gt;</span>
                        <span class="compose-account-badge">
                            <i class="fa-solid fa-user-check"></i> Your Account
                        </span>
                    </div>
                </div>

                <!-- Row 1: To -->
                <div class="compose-field-row" id="rowTo">
                    <span class="compose-field-label">To</span>
                    <input type="text" id="emailTo" class="compose-field-input" placeholder="Recipient email addresses (comma-separated)" autocomplete="off" required>
                    <div class="compose-toggles">
                        <button type="button" class="compose-toggle-btn" id="btnToggleCc">Cc</button>
                        <button type="button" class="compose-toggle-btn" id="btnToggleBcc">Bcc</button>
                    </div>
                </div>

                <!-- Row 2: Cc (Collapsible) -->
                <div class="compose-field-row hidden-row" id="rowCc">
                    <span class="compose-field-label">Cc</span>
                    <input type="text" id="emailCc" class="compose-field-input" autocomplete="off" placeholder="Comma-separated emails">
                    <div class="compose-toggles" id="ccRowToggles" style="display: none;">
                        <button type="button" class="compose-toggle-btn" id="btnToggleBccFromCc">Bcc</button>
                    </div>
                </div>

                <!-- Row 3: Bcc (Collapsible) -->
                <div class="compose-field-row hidden-row" id="rowBcc">
                    <span class="compose-field-label">Bcc</span>
                    <input type="text" id="emailBcc" class="compose-field-input" autocomplete="off" placeholder="Comma-separated emails">
                    <div class="compose-toggles" id="bccRowToggles" style="display: none;">
                        <button type="button" class="compose-toggle-btn" id="btnToggleCcFromBcc">Cc</button>
                    </div>
                </div>

                <!-- Row 4: Subject -->
                <div class="compose-field-row" style="margin-bottom: 4px;">
                    <span class="compose-field-label">Subject</span>
                    <input type="text" id="emailSubject" class="compose-field-input" placeholder="Daily update subject" autocomplete="off" required>
                </div>

                <!-- Email Content Preview -->
                <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 4px; margin-bottom: 0; flex: 1; min-height: 0;">
                    <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: flex; align-items: center; justify-content: space-between;">
                        <span>Email Content Preview :</span>
                        <span style="font-size: 10.5px; font-weight: 500; color: var(--text-muted); text-transform: none;"><i class="fa-solid fa-code"></i> HTML formatted</span>
                    </label>
                    <div id="emailHtmlPreviewContainer" class="email-preview-box">
                        <!-- Live rendered HTML content -->
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary-custom" id="btnCancelSendEmail">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSendEmailSubmit" style="background: var(--primary); color: #ffffff; border-color: var(--primary);">
                    <i class="fa-solid fa-paper-plane"></i> Send Email
                </button>
            </div>
        </div>
    </div>

    <!-- User Profile Modal (Centered, Theme-Matched) -->
    <div id="userProfileModal" class="modal-overlay">
        <div class="modal-card profile-modal-card">
            <div class="profile-modal-header">
                <div class="profile-header-title">
                    <i class="fa-solid fa-id-badge" style="color: var(--primary);"></i>
                    <span>User Profile & Account Settings</span>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseProfileModal" title="Close">&times;</button>
            </div>

            <div class="profile-modal-body">
                <div id="profileAlert" class="alert-box" style="display: none; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;"></div>

                <!-- User Info Card (Theme-Matched) -->
                <div class="profile-banner-card">
                    <div id="profileAvatarBig" class="profile-avatar-circle">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="profile-banner-meta">
                        <div id="profileBannerName" class="profile-banner-name"><?= h($currentUser['name'] ?? 'User') ?></div>
                        <div class="profile-banner-sub">
                            <span><i class="fa-regular fa-envelope" style="margin-right: 3px;"></i> <span id="profileBannerEmail"><?= h($currentUser['email'] ?? '') ?></span></span>
                            <span><i class="fa-regular fa-calendar-check" style="margin-right: 3px;"></i> Member Since: <strong id="profileMemberSince">Loading...</strong></span>
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
                </div>

                <!-- Section 2: Security & Password -->
                <div class="profile-box-panel">
                    <div class="profile-section-title" style="margin-bottom: 0;">
                        <span><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 4px;"></i> Change Password (Optional)</span>
                        <span style="font-size: 11px; text-transform: none; font-weight: 500; color: var(--text-muted); font-style: italic;">Leave empty to keep current password</span>
                    </div>

                    <div id="profGoogleAccountNotice" style="display:none; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2); border-radius:6px; padding:6px 10px; font-size:11.5px; color:var(--text-muted); margin-bottom:10px;">
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

                <!-- Section 3: Gemini AI API Key -->
                <div class="profile-box-panel">
                    <div class="profile-section-title" style="margin-bottom: 0;">
                        <span><i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary); margin-right: 4px;"></i> Gemini AI API Key</span>
                        <span style="font-size: 11px; text-transform: none; font-weight: 600; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 2px 7px; border-radius: 4px;">
                            <i class="fa-solid fa-shield-halved"></i> Encrypted in MySQL
                        </span>
                    </div>
                    <div style="position: relative; margin-top: 6px;">
                        <input type="password" id="profApiKey" class="profile-input-field" placeholder="AIzaSy... (leave blank to keep current key)" style="padding-right: 36px;">
                        <button type="button" class="btn-toggle-password" data-target="#profApiKey" title="Show/Hide Key">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div style="font-size: 11px; margin-top: 4px; display: flex; justify-content: flex-end;">
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" style="color: #2563eb; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-key"></i> Get Gemini API Key <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                        </a>
                    </div>
                </div>

                <!-- Section 4: Email App / SMTP Password -->
                <div class="profile-box-panel">
                    <div class="profile-section-title" style="margin-bottom: 0;">
                        <span><i class="fa-solid fa-envelope" style="color: var(--primary); margin-right: 4px;"></i> Email App / SMTP Password</span>
                        <span style="font-size: 11px; text-transform: none; font-weight: 600; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 2px 7px; border-radius: 4px;">
                            <i class="fa-solid fa-shield-halved"></i> Encrypted in MySQL
                        </span>
                    </div>
                    <div style="position: relative; margin-top: 6px;">
                        <input type="password" id="profSmtpPass" class="profile-input-field" placeholder="Enter your 16-digit Google App Password" style="padding-right: 36px;">
                        <button type="button" class="btn-toggle-password" data-target="#profSmtpPass" title="Show/Hide Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div style="font-size: 11px; margin-top: 4px; display: flex; justify-content: flex-end;">
                        <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer" style="color: #2563eb; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-brands fa-google"></i> Get Google App Password <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                        </a>
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

    <!-- Footer Copyright matching Tasks theme -->
    <footer style="text-align:center; font-size:11px; color:var(--text-muted); padding:6px 0 2px 0; font-weight:500; opacity:0.85; flex-shrink:0;">
        &copy; <?= date('Y') ?> Mohit Mokariya. All Rights Reserved. Powered by CakePHP 5 & MySQL Database.
    </footer>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <i class="fa-solid fa-circle-check" id="toastIcon"></i>
    <span id="toastMessage">✨ Content copied to clipboard!</span>
</div>

<?php
$renderedFlash = $this->Flash->render();
?>
<?php if (!empty($renderedFlash)): ?>
    <div style="display:none;" id="initialFlashHolder"><?= $renderedFlash ?></div>
<?php endif; ?>

<script>
$(document).ready(function() {
    function getRealTodayIso() {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    var maxAllowedIso = '<?= h($todayIso) ?>' || getRealTodayIso();
    var currentDateIso = '<?= h($todayIso) ?>' || getRealTodayIso();
    var saveUpdateTimer = null;
    var allClients = <?= json_encode($clients ?? []) ?>;

    function loadLatestClients() {
        $.ajax({
            url: window.APP_BASE + 'clients/get-clients',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.clients) {
                    allClients = res.clients;
                }
            }
        });
    }
    loadLatestClients();

    function getOrdinal(n) {
        if ((parseFloat(n) == parseInt(n)) && !isNaN(n)) {
            var s = ["th", "st", "nd", "rd"],
                v = n % 100;
            return n + (s[(v - 20) % 10] || s[v] || s[0]);
        }
        return n;
    }

    function currentDateFormatted(isoStr) {
        var targetIso = isoStr || currentDateIso || getRealTodayIso();
        var parts = targetIso.split('-');
        var y = parseInt(parts[0]);
        var m = parseInt(parts[1]) - 1;
        var d = parseInt(parts[2]);
        var month = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return getOrdinal(d) + " " + month[m] + ", " + y;
    }

    // Auto Date Rollover & Fresh State Detector across Midnight / Next Morning
    function checkAndApplyDateRollover() {
        var realToday = getRealTodayIso();
        if (maxAllowedIso !== realToday) {
            var wasViewingLatest = (currentDateIso === maxAllowedIso);
            maxAllowedIso = realToday;
            if (wasViewingLatest && currentDateIso < realToday) {
                currentDateIso = realToday;
                if (window.history && window.history.replaceState) {
                    var newUrl = window.location.pathname + '?date=' + currentDateIso;
                    var pId = $('#project_name').val();
                    if (pId) newUrl += '&project_name=' + encodeURIComponent(pId);
                    var cName = $('#client_name').val();
                    if (cName) newUrl += '&client_name=' + encodeURIComponent(cName);
                    window.history.replaceState(null, '', newUrl);
                }
                updateProjectHeader();
                updateClientGreeting();
                checkForTaskUpdates(false);
                loadLatestClients();
                showToast('Date updated to today: ' + currentDateFormatted(realToday), 'info');
            }
        }
    }

    function formatTaskSection(txtName) {
        var rawText = $("textarea[name='" + txtName + "']").val() || '';
        var rawLines = rawText.split(/\r?\n/);
        var hasContent = rawLines.some(function(l) { return l.trim().length > 0; });

        if (!hasContent) {
            $('.' + txtName).empty();
            if (txtName === 'list_done') {
                $('.review_note').empty();
            }
            return;
        }

        var isDoneList = (txtName === 'list_done');
        if (isDoneList) {
            $('.review_note').html('<br>Please check with the latest updates and let us know your thoughts for the same.<br>');
        }

        var sectionLabel = $("textarea[name='" + txtName + "']").closest('.form-group').find('label').text().replace(/\s*:\s*$/, '').trim();
        var parsedBody = window.parseTaskMarkdownToHtml(rawText, isDoneList);
        var taskDetail = "<b><u>" + escapeHtml(sectionLabel) + " :</u></b><br>" + parsedBody;

        $('.' + txtName).empty().html(taskDetail);
    }

    var userHasSmtpConfigured = <?= !empty($hasSmtpConfigured) ? 'true' : 'false' ?>;

    function updateSendEmailButtonVisibility() {
        var doneVal = ($('#txt_done_task').val() || '').trim();
        var progVal = ($('#txt_progress_task').val() || '').trim();
        var remVal = ($('#txt_remaining_task').val() || '').trim();
        var queryVal = ($('#txt_query').val() || '').trim();
        var noteVal = ($('#txt_note').val() || '').trim();

        var hasContent = (doneVal !== '' || progVal !== '' || remVal !== '' || queryVal !== '' || noteVal !== '');

        if (hasContent) {
            $('#previewFooterSend').show().css('display', 'flex');
            if (userHasSmtpConfigured) {
                $('#btnSendEmailBottom').show().css('display', 'inline-flex');
                $('#smtpSetupNotice').hide();
            } else {
                $('#btnSendEmailBottom').hide();
                $('#smtpSetupNotice').show().css('display', 'flex');
            }
        } else {
            $('#previewFooterSend').hide();
            $('#btnSendEmailBottom').hide();
            $('#smtpSetupNotice').hide();
        }
    }

    function renderAllTaskSections() {
        formatTaskSection('list_done');
        formatTaskSection('list_progress');
        formatTaskSection('list_remaining');
        formatTaskSection('list_query');
        formatTaskSection('list_note');
        updateSendEmailButtonVisibility();
    }

    function updateClientGreeting() {
        var clientVal = $('#client_name').val();
        if (clientVal) {
            $('.client_name').html('Hi ' + clientVal + ',<br><br>');
            var tlVal = clientVal.trim();
            if (tlVal !== '') {
                tlVal = tlVal.endsWith('.') ? tlVal : (tlVal + '.');
            }
            $('#tl_name').val(tlVal);
            updateTlSignoff();
        } else {
            $('.client_name').html('');
            $('#tl_name').val('');
            updateTlSignoff();
        }
    }

    function updateProjectHeader() {
        var projVal = $('#project_name').val();
        if (projVal) {
            var updateMsg = 'Following are the updates for ' + projVal + ' as on ' + currentDateFormatted() + ':<br><br>';
            var subject = 'Updates for ' + projVal + ' as on ' + currentDateFormatted();
            $('.update_msg').html(updateMsg);
            $('.subject').html(subject);
        } else {
            $('.update_msg').html('');
            $('.subject').html('');
        }
    }

    function updateTlSignoff() {
        var tlVal = $('#tl_name').val();
        if (tlVal) {
            $('.thanks').html('<br>Thanks,<br>' + tlVal);
        } else {
            $('.thanks').html('');
        }
    }

    // Auto-save form to MySQL database
    function saveUpdateToDatabase(callback) {
        $('#updateSaveStatus').text('Saving to DB...');
        $.ajax({
            url: window.APP_BASE + 'daily-updates/save-update',
            type: 'POST',
            data: {
                update_date: currentDateIso,
                client_name: $('#client_name').val(),
                project_name: $('#project_name').val(),
                tl_name: $('#tl_name').val(),
                done_tasks: $('#txt_done_task').val(),
                progress_tasks: $('#txt_progress_task').val(),
                remaining_tasks: $('#txt_remaining_task').val(),
                queries: $('#txt_query').val(),
                notes: $('#txt_note').val()
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#updateSaveStatus').text('Saved to DB at ' + res.saved_at);
                    broadcastDoneTasksToWorkLog();
                } else {
                    $('#updateSaveStatus').text('Save error');
                }
                if (typeof callback === 'function') callback(res);
            },
            error: function() {
                $('#updateSaveStatus').text('DB Connection error');
                if (typeof callback === 'function') callback({ success: false });
            }
        });
    }

    // Two-Way Sync: Broadcast Done Tasks to Today's Work Log
    function broadcastDoneTasksToWorkLog() {
        var payload = {
            type: 'DONE_TASKS_SYNC_TO_LOG',
            date: currentDateIso,
            done_tasks: $('#txt_done_task').val() || '',
            project_name: $('#project_name').val() || '',
            client_name: $('#client_name').val() || '',
            timestamp: Date.now()
        };

        if (typeof taskSyncChannel !== 'undefined' && taskSyncChannel) {
            try {
                taskSyncChannel.postMessage(payload);
            } catch (err) {}
        }

        try {
            localStorage.setItem('helpdesk_done_tasks_sync_event', JSON.stringify(payload));
        } catch (err) {}
    }

    function triggerAutoSave() {
        clearTimeout(saveUpdateTimer);
        saveUpdateTimer = setTimeout(saveUpdateToDatabase, 600);
    }

    // Event Bindings
    $('#client_name').on('input change keyup', function() {
        updateClientGreeting();
        triggerAutoSave();
    });

    $('#project_name').on('input change', function() {
        updateProjectHeader();
        triggerAutoSave();
    });

    $('#tl_name').on('input change', function() {
        updateTlSignoff();
        triggerAutoSave();
    });

    // Tab key support for sub-point indentation in textareas
    $('.task_detail').on('keydown', function(e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            var textarea = this;
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var val = textarea.value;

            if (e.shiftKey) {
                // Shift+Tab: Remove 2 leading spaces if present
                var before = val.substring(0, start);
                var lastNewLine = before.lastIndexOf('\n');
                var lineStart = lastNewLine === -1 ? 0 : lastNewLine + 1;
                if (val.substring(lineStart, lineStart + 2) === '  ') {
                    textarea.value = val.substring(0, lineStart) + val.substring(lineStart + 2);
                    textarea.selectionStart = Math.max(lineStart, start - 2);
                    textarea.selectionEnd = Math.max(lineStart, end - 2);
                }
            } else {
                // Tab: Insert 2 spaces at cursor
                textarea.value = val.substring(0, start) + '  ' + val.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 2;
            }

            var txtName = $(this).attr('name');
            formatTaskSection(txtName);
            updateSendEmailButtonVisibility();
            if (txtName === 'list_done') {
                broadcastDoneTasksToWorkLog();
            }
            triggerAutoSave();
        }
    });

    $('.task_detail').on('input change keyup', function() {
        var txtName = $(this).attr('name');
        formatTaskSection(txtName);
        updateSendEmailButtonVisibility();
        if (txtName === 'list_done') {
            broadcastDoneTasksToWorkLog();
        }
        triggerAutoSave();
    });

    // Copy Subject
    $('#btnCopySubject').click(function() {
        var rawSubject = $('.subject').text().trim();
        if (!rawSubject) {
            showToast('No Subject to copy', 'warning');
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(rawSubject).then(function() {
                showToast('Subject copied to clipboard!', 'success');
            }).catch(function() {
                fallbackCopy(rawSubject, 'Subject copied!');
            });
        } else {
            fallbackCopy(rawSubject, 'Subject copied!');
        }
    });

    // Copy Content
    $('#btnCopyContent').click(function() {
        var container = document.querySelector('.mail_body');
        if (!container || !container.innerText.trim()) {
            showToast('No Mail Content to copy', 'warning');
            return;
        }

        var range = document.createRange();
        var selection = window.getSelection();
        selection.removeAllRanges();
        range.selectNodeContents(container);
        selection.addRange(range);

        try {
            document.execCommand('copy');
            selection.removeAllRanges();
            showToast('Mail Content copied to clipboard!', 'success');
        } catch (err) {
            showToast('Failed to copy content', 'error');
        }
    });

    // Back to Tasks Button with safe synchronous wait
    $('#btnBackToTasks').click(function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
        var clientVal = $('#client_name').val() || '';

        saveUpdateToDatabase(function() {
            var url = window.APP_BASE + '?date=' + currentDateIso;
            if (clientVal) {
                url += '&client_name=' + encodeURIComponent(clientVal);
            }
            window.location.href = url;
        });
    });

    // Reset Tasks
    $('#btnResetTasks').click(function() {
        if (!confirm('Clear all task fields in form and DB?')) return;
        $('textarea.task_detail').val('');
        renderAllTaskSections();
        updateSendEmailButtonVisibility();
        broadcastDoneTasksToWorkLog();
        saveUpdateToDatabase();
        showToast('All task fields cleared!', 'success');
    });

    var lastSyncedTasks = $('#txt_done_task').val() || '';
    var isCheckingTasks = false;

    function extractTaskLinesJs(text) {
        if (!text || !text.trim()) return '';
        var lines = text.split(/\r?\n/);
        var startIndex = 0;
        if (lines.length > 0 && /^\d{2}-\d{2}-\d{4}/.test(lines[0].trim())) {
            startIndex = 1;
            if (lines.length > 1 && /^[-=]{3,}/.test(lines[1].trim())) {
                startIndex = 2;
            }
        }
        var extracted = lines.slice(startIndex);
        while (extracted.length > 0 && extracted[0].trim() === '') {
            extracted.shift();
        }
        while (extracted.length > 0 && extracted[extracted.length - 1].trim() === '') {
            extracted.pop();
        }
        return extracted.join('\n');
    }

    function checkForTaskUpdates(isManual) {
        if (isCheckingTasks) return;
        isCheckingTasks = true;

        $.ajax({
            url: window.APP_BASE + 'daily-updates/get-today-tasks',
            type: 'GET',
            data: { date: currentDateIso },
            dataType: 'json',
            success: function(res) {
                isCheckingTasks = false;
                if (res.success && res.found) {
                    var newTasks = res.tasks || '';
                    var currentTasks = $('#txt_done_task').val() || '';

                    if (newTasks.trim() !== '' && (newTasks !== currentTasks || newTasks !== lastSyncedTasks)) {
                        lastSyncedTasks = newTasks;
                        $('#txt_done_task').val(newTasks);
                        if (res.project_name && (!$('#project_name').val() || $('#project_name').val() === 'Create Project')) {
                            $('#project_name').val(res.project_name);
                            updateProjectHeader();
                        }
                        if (res.client_name && res.client_name !== '') {
                            $('#client_name').val(res.client_name);
                            updateClientGreeting();
                        }
                        formatTaskSection('list_done');
                        updateSendEmailButtonVisibility();
                        saveUpdateToDatabase();
                        if (isManual) {
                            showToast('Synced latest tasks from database!', 'success');
                        }
                    } else if (isManual) {
                        showToast('Already up to date with DB.');
                    }
                } else if (isManual) {
                    showToast('No tasks found in DB for this date.');
                }
                updateSendEmailButtonVisibility();
            },
            error: function() {
                isCheckingTasks = false;
            }
        });
    }

    // Real-Time Cross-Tab Synchronization via BroadcastChannel (0ms instant sync with ZERO continuous server polling)
    function applyRealtimeSyncData(data) {
        if (!data || data.date !== currentDateIso) return;
        var newTasks = extractTaskLinesJs(data.tasks || '');
        var currentTasks = $('#txt_done_task').val() || '';

        if (newTasks !== currentTasks && newTasks.trim() !== '') {
            lastSyncedTasks = newTasks;
            $('#txt_done_task').val(newTasks);
            formatTaskSection('list_done');
            updateSendEmailButtonVisibility();
            triggerAutoSave();
        }

        if (data.project_name && (!$('#project_name').val() || $('#project_name').val() === 'Create Project')) {
            $('#project_name').val(data.project_name);
            updateProjectHeader();
        }

        if (data.client_name && !$('#client_name').val()) {
            $('#client_name').val(data.client_name);
            updateClientGreeting();
        }
    }

    if (typeof window.BroadcastChannel !== 'undefined') {
        var taskSyncChannel = new BroadcastChannel('helpdesk_tasks_sync_channel');
        taskSyncChannel.onmessage = function(e) {
            if (e.data && e.data.type === 'TASKS_UPDATED') {
                applyRealtimeSyncData(e.data);
            }
        };
    }

    // Storage event fallback for cross-tab sync
    window.addEventListener('storage', function(e) {
        if (e.key === 'helpdesk_task_sync_storage_event' && e.newValue) {
            try {
                var parsed = JSON.parse(e.newValue);
                applyRealtimeSyncData(parsed);
            } catch (err) {}
        }
    });

    // On-demand date check & task sync when user switches back to this tab / window
    $(window).on('focus', function() {
        checkAndApplyDateRollover();
        checkForTaskUpdates(false);
    });
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            checkAndApplyDateRollover();
            checkForTaskUpdates(false);
        }
    });


    // Send Email Modal Logic & Dynamic Toggles
    function syncCcBccToggles() {
        var isCcOpen = !$('#rowCc').hasClass('hidden-row');
        var isBccOpen = !$('#rowBcc').hasClass('hidden-row');

        if (!isCcOpen && !isBccOpen) {
            // Neither open: show "Cc / Bcc" on To row
            $('#toRowToggles').show();
            $('#btnToggleCc').show();
            $('#ccBccDivider').show();
            $('#btnToggleBcc').show();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').hide();
        } else if (isCcOpen && !isBccOpen) {
            // Cc open, Bcc closed: hide To toggles, show "Bcc" on Cc row
            $('#toRowToggles').hide();
            $('#ccRowToggles').show();
            $('#bccRowToggles').hide();
        } else if (!isCcOpen && isBccOpen) {
            // Bcc open, Cc closed: hide To toggles, show "Cc" on Bcc row
            $('#toRowToggles').hide();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').show();
        } else {
            // Both open: hide all toggle buttons
            $('#toRowToggles').hide();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').hide();
        }
    }

    // Toggle button handlers (Cc and Bcc)
    $('#btnToggleCc, #btnToggleCcFromBcc').click(function(e) {
        e.preventDefault();
        $('#rowCc').removeClass('hidden-row');
        syncCcBccToggles();
        $('#emailCc').focus();
    });

    $('#btnToggleBcc, #btnToggleBccFromCc').click(function(e) {
        e.preventDefault();
        $('#rowBcc').removeClass('hidden-row');
        syncCcBccToggles();
        $('#emailBcc').focus();
    });

    function openSendEmailModal() {
        var currentSubject = $('.subject').text().trim();
        var currentMailHtml = $('.mail_body').html().trim();

        $('#emailSubject').val(currentSubject);
        $('#emailHtmlPreviewContainer').html(currentMailHtml || '<i>No tasks or content to display. Please add tasks first.</i>');
        $('#sendEmailAlert').hide().removeClass('alert-danger alert-success').text('');

        // Dynamic From display matching logged-in user account
        var currName = $('.user-pill-name').text().trim() || '<?= h($currentUser['name'] ?? 'User') ?>';
        var currEmail = $('.user-popover-email').text().trim() || '<?= h($currentUser['email'] ?? 'user@helpdesk.local') ?>';
        if (currName && currEmail) {
            $('#composeFromText').text(currName + ' <' + currEmail + '>');
        }

        // Autofill To: Selected client's email address
        var selectedClientName = ($('#client_name').val() || '').trim();

        function normalizeClientName(str) {
            return (str || '').toLowerCase().replace(/[\s\.\-_]+/g, '');
        }

        var matchedClient = null;
        if (selectedClientName && Array.isArray(allClients)) {
            matchedClient = allClients.find(function(c) {
                return normalizeClientName(c.name) === normalizeClientName(selectedClientName);
            });
        }

        if (matchedClient && matchedClient.email && matchedClient.email.trim() !== '') {
            $('#emailTo').val(matchedClient.email.trim());
        } else {
            $('#emailTo').val('');
        }

        // Autofill Cc: All other clients that have an email configured
        var otherEmails = [];
        if (Array.isArray(allClients)) {
            allClients.forEach(function(c) {
                var isCurrentSelected = matchedClient ? (c.id === matchedClient.id) : (normalizeClientName(c.name) === normalizeClientName(selectedClientName));
                if (!isCurrentSelected && c.email && c.email.trim() !== '') {
                    otherEmails.push(c.email.trim());
                }
            });
        }

        if (otherEmails.length > 0) {
            $('#emailCc').val(otherEmails.join(', '));
            $('#rowCc').removeClass('hidden-row');
        } else {
            $('#emailCc').val('');
            $('#rowCc').addClass('hidden-row');
        }

        // Keep Bcc hidden by default
        $('#emailBcc').val('');
        $('#rowBcc').addClass('hidden-row');

        syncCcBccToggles();

        $('#sendEmailModal').addClass('active');
        if (!$('#emailTo').val()) {
            $('#emailTo').focus();
        } else {
            $('#emailSubject').focus();
        }
    }

    function closeSendEmailModal() {
        $('#sendEmailModal').removeClass('active');
    }

    // Only one Send Email button in bottom right corner of Preview card
    $('#btnSendEmailBottom').click(function() {
        openSendEmailModal();
    });

    $('#btnCloseSendEmailModal, #btnCancelSendEmail').click(function() {
        closeSendEmailModal();
    });

    $('#sendEmailModal').click(function(e) {
        if (e.target === this) {
            closeSendEmailModal();
        }
    });

    $('#emailTo, #emailCc, #emailBcc, #emailSubject').on('input', function() {
        $(this).closest('.compose-field-row').css({'border-bottom-color': '', 'background': ''});
        $('#sendEmailAlert').hide().text('');
    });

    $('#btnSendEmailSubmit').click(function() {
        var toVal = $('#emailTo').val().trim();
        var ccVal = $('#rowCc').hasClass('hidden-row') ? '' : $('#emailCc').val().trim();
        var bccVal = $('#rowBcc').hasClass('hidden-row') ? '' : $('#emailBcc').val().trim();
        var subjectVal = $('#emailSubject').val().trim();
        var bodyHtmlVal = $('.mail_body').html().trim();
        var bodyTextVal = $('.mail_body').text().trim();

        var alertBox = $('#sendEmailAlert');
        // Reset visual row styles
        $('.compose-field-row').css({'border-bottom-color': '', 'background': ''});

        if (!toVal) {
            $('#rowTo').css({'border-bottom-color': '#ef4444', 'background': '#fef2f2'});
            alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> Please enter at least one recipient email address in the "To" field.').show();
            $('#emailTo').focus();
            return;
        }

        var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        function getInvalidEmail(csv) {
            if (!csv) return null;
            var list = csv.split(/[,;]/);
            for (var i = 0; i < list.length; i++) {
                var em = list[i].trim();
                if (em && !emailRegex.test(em)) {
                    return em;
                }
            }
            return null;
        }

        var badTo = getInvalidEmail(toVal);
        if (badTo) {
            $('#rowTo').css({'border-bottom-color': '#ef4444', 'background': '#fef2f2'});
            alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> Invalid email address in "To": <strong>' + escapeHtml(badTo) + '</strong>').show();
            $('#emailTo').focus();
            return;
        }

        var badCc = getInvalidEmail(ccVal);
        if (badCc) {
            $('#rowCc').css({'border-bottom-color': '#ef4444', 'background': '#fef2f2'});
            alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> Invalid email address in "Cc": <strong>' + escapeHtml(badCc) + '</strong>').show();
            $('#emailCc').focus();
            return;
        }

        var badBcc = getInvalidEmail(bccVal);
        if (badBcc) {
            $('#rowBcc').css({'border-bottom-color': '#ef4444', 'background': '#fef2f2'});
            alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> Invalid email address in "Bcc": <strong>' + escapeHtml(badBcc) + '</strong>').show();
            $('#emailBcc').focus();
            return;
        }

        if (!subjectVal) {
            $('#emailSubject').closest('.compose-field-row').css({'border-bottom-color': '#ef4444', 'background': '#fef2f2'});
            alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> Please enter the email subject.').show();
            $('#emailSubject').focus();
            return;
        }

        if (!bodyTextVal) {
            alertBox.addClass('alert-danger').text('Email content cannot be empty. Please ensure your tasks are entered.').show();
            return;
        }

        var btn = $(this);
        var origHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sending Email...');

        $.ajax({
            url: window.APP_BASE + 'daily-updates/send-email',
            type: 'POST',
            data: {
                to: toVal,
                cc: ccVal,
                bcc: bccVal,
                subject: subjectVal,
                body_html: bodyHtmlVal,
                body_text: bodyTextVal
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    alertBox.addClass('alert-success').text(res.message || 'Email sent successfully!').show();
                    showToast(res.message || 'Email sent successfully!', 'success');
                    setTimeout(function() {
                        closeSendEmailModal();
                    }, 1400);
                } else {
                    alertBox.addClass('alert-danger').text(res.message || 'Failed to send email. Please verify settings.').show();
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(origHtml);
                if (xhr.status === 401) {
                    alertBox.addClass('alert-danger').text('Session expired. Redirecting to login...').show();
                    setTimeout(function() {
                        window.location.href = window.APP_BASE + 'login';
                    }, 1200);
                } else {
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error while sending email.';
                    alertBox.addClass('alert-danger').text(errMsg).show();
                }
            }
        });
    });

    // Global AJAX Session Expiry Handler
    $(document).ajaxError(function(event, jqxhr) {
        if (jqxhr.status === 401) {
            var res = jqxhr.responseJSON;
            var msg = (res && res.message) ? res.message : 'Session expired. Redirecting to login...';
            showToast(msg);
            setTimeout(function() {
                window.location.href = window.APP_BASE + 'login';
            }, 1200);
        }
    });

    // ==========================================
    // User Menu Popover & Profile Modal Logic
    // ==========================================
    function openProfileModal() {
        $('#userPopoverMenu').removeClass('show');
        $('#btnUserMenuToggle').removeClass('active');
        $('#profileAlert').hide().removeClass('alert-danger alert-success').text('');
        $('#profNameError, #profEmailError, #profCurrentPassError, #profNewPassError, #profConfirmPassError').hide().find('span').text('');
        $('#profInputName, #profInputEmail, #profCurrentPass, #profNewPass, #profConfirmPass, #profApiKey').css({'border-color': '', 'background': ''});
        $('#profCurrentPass, #profNewPass, #profConfirmPass').val('');

        // Fetch fresh profile data via AJAX
        $.ajax({
            url: window.APP_BASE + 'users/get-profile',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.user) {
                    var u = res.user;
                    window.currentProfileNeedsPasswordSetup = !!u.needs_password_setup;
                    if (window.currentProfileNeedsPasswordSetup) {
                        $('#profGoogleAccountNotice').show();
                        $('#profCurrentPassWrapper').hide();
                        $('#profCurrentPass').val('');
                    } else {
                        $('#profGoogleAccountNotice').hide();
                        $('#profCurrentPassWrapper').show();
                    }

                    $('#profInputName').val(u.name);
                    $('#profInputEmail').val(u.email);
                    $('#profileBannerName').text(u.name);
                    $('#profileBannerEmail').text(u.email);
                    $('#profileMemberSince').text(u.created_formatted);
                    $('#profileAvatarBig').text((u.name || 'U').charAt(0).toUpperCase());
                    if (u.api_key) {
                        $('#profApiKey').val(u.api_key);
                    }
                    if (u.smtp_password) {
                        $('#profSmtpPass').val(u.smtp_password);
                    } else {
                        $('#profSmtpPass').val('');
                    }
                }
            }
        });

        $('#userProfileModal').addClass('active');
    }

    function closeProfileModal() {
        $('#userProfileModal').removeClass('active');
    }

    $('#btnOpenProfileModal').click(function(e) {
        e.preventDefault();
        openProfileModal();
    });

    $('#btnCloseProfileModal, #btnCancelProfileModal').click(function() {
        closeProfileModal();
    });

    $('#userProfileModal').click(function(e) {
        if (e.target === this) {
            closeProfileModal();
        }
    });

    // Eye toggle for passwords and API key
    $(document).on('click', '.btn-toggle-password', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var targetSelector = $(this).attr('data-target') || $(this).data('target');
        var targetInput = $(targetSelector);
        if (targetInput.length) {
            var icon = $(this).find('i');
            if (targetInput.attr('type') === 'password') {
                targetInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                targetInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }
    });

    // Clear profile errors on input
    $('#profInputName, #profInputEmail, #profCurrentPass, #profNewPass, #profConfirmPass').on('input', function() {
        $(this).css({'border-color': '', 'background': ''});
        $(this).closest('.form-group').find('div[id$="Error"]').hide().find('span').text('');
        $('#profileAlert').hide().text('');
    });

    // Save Profile Submit
    $('#btnSaveProfileModal').click(function() {
        var name = $('#profInputName').val().trim();
        var email = $('#profInputEmail').val().trim();
        var currentPass = $('#profCurrentPass').val();
        var newPass = $('#profNewPass').val();
        var confirmPass = $('#profConfirmPass').val();
        var apiKey = $('#profApiKey').val().trim();
        var smtpPassword = $('#profSmtpPass').val().trim();

        var alertBox = $('#profileAlert');
        alertBox.hide().removeClass('alert-danger alert-success').text('');
        $('#profNameError, #profEmailError, #profCurrentPassError, #profNewPassError, #profConfirmPassError').hide().find('span').text('');
        $('#profInputName, #profInputEmail, #profCurrentPass, #profNewPass, #profConfirmPass').css({'border-color': '', 'background': ''});

        var hasError = false;
        if (!name) {
            $('#profInputName').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
            $('#profNameError').show().find('span').text('Full Name cannot be empty');
            hasError = true;
        }

        var emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        if (!email || !emailPattern.test(email)) {
            $('#profInputEmail').css({'border-color': '#ef4444', 'background': '#fef2f2'});
            $('#profEmailError').show().find('span').text('Please enter a valid email address');
            if (!hasError) $('#profInputEmail').focus();
            hasError = true;
        }

        if (newPass) {
            if (!window.currentProfileNeedsPasswordSetup && !currentPass) {
                $('#profCurrentPass').css({'border-color': '#ef4444', 'background': '#fef2f2'});
                $('#profCurrentPassError').show().find('span').text('Current password is required to change password');
                if (!hasError) $('#profCurrentPass').focus();
                hasError = true;
            }
            if (newPass.length < 6) {
                $('#profNewPass').css({'border-color': '#ef4444', 'background': '#fef2f2'});
                $('#profNewPassError').show().find('span').text('New password must be at least 6 characters');
                if (!hasError) $('#profNewPass').focus();
                hasError = true;
            }
            if (newPass !== confirmPass) {
                $('#profConfirmPass').css({'border-color': '#ef4444', 'background': '#fef2f2'});
                $('#profConfirmPassError').show().find('span').text('Passwords do not match');
                if (!hasError) $('#profConfirmPass').focus();
                hasError = true;
            }
        }

        if (hasError) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: window.APP_BASE + 'users/update-profile',
            type: 'POST',
            data: {
                name: name,
                email: email,
                current_password: currentPass,
                new_password: newPass,
                confirm_password: confirmPass,
                api_key: apiKey,
                smtp_password: smtpPassword
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    alertBox.addClass('alert-success').html('<i class="fa-solid fa-circle-check"></i> ' + res.message).show();
                    showToast(res.message, 'success');

                    // Update UI across page
                    var initial = (res.user.name || 'U').charAt(0).toUpperCase();
                    $('.user-pill-name').text(res.user.name);
                    $('.user-avatar-badge').text(initial);
                    $('.user-popover-avatar').text(initial);
                    $('.user-popover-name').text(res.user.name);
                    $('.user-popover-email').text(res.user.email);
                    $('#profileBannerName').text(res.user.name);
                    $('#profileBannerEmail').text(res.user.email);
                    $('#profileAvatarBig').text(initial);

                    // Dynamically update Send Email button visibility based on whether user has configured SMTP password
                    userHasSmtpConfigured = !!(res.user && res.user.email && res.user.has_smtp_password);
                    updateSendEmailButtonVisibility();

                    // Clear passwords
                    $('#profCurrentPass, #profNewPass, #profConfirmPass').val('');

                    setTimeout(function() {
                        closeProfileModal();
                    }, 1200);
                } else {
                    var msg = res.message || 'Failed to update profile.';
                    if (res.field === 'name') {
                        $('#profInputName').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#profNameError').show().find('span').text(msg);
                    } else if (res.field === 'email') {
                        $('#profInputEmail').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#profEmailError').show().find('span').text(msg);
                    } else if (res.field === 'current_password') {
                        $('#profCurrentPass').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#profCurrentPassError').show().find('span').text(msg);
                    } else if (res.field === 'new_password') {
                        $('#profNewPass').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#profNewPassError').show().find('span').text(msg);
                    } else if (res.field === 'confirm_password') {
                        $('#profConfirmPass').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#profConfirmPassError').show().find('span').text(msg);
                    } else {
                        alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> ' + msg).show();
                    }
                    showToast(msg);
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Network error while updating profile.';
                alertBox.addClass('alert-danger').html('<i class="fa-solid fa-circle-exclamation"></i> ' + errMsg).show();
            }
        });
    });

    // Initial render & Immediate check
    updateClientGreeting();
    updateProjectHeader();
    updateTlSignoff();
    renderAllTaskSections();
    updateSendEmailButtonVisibility();
    checkForTaskUpdates(false);
});
</script>
