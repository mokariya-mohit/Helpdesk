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
    /* =========================================================
       Glassmorphism & Sunrise Theme - Inspired by Rulse Design
       ========================================================= */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.74);
        --glass-bg-hover: rgba(255, 255, 255, 0.88);
        --glass-bg-subtle: rgba(255, 255, 255, 0.52);
        --glass-border: rgba(255, 255, 255, 0.90);
        --glass-border-subtle: rgba(226, 232, 240, 0.80);
        --glass-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.10), 0 4px 16px rgba(0, 0, 0, 0.04);
        --text-headline: #0f172a;
        --text-body: #1e293b;
        --text-muted: #64748b;
        --text-light: #94a3b8;
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --sun-yellow: #ebf84a;
        --sun-yellow-hover: #e2f038;
        --radius-xl: 24px;
        --radius-lg: 18px;
        --radius-md: 14px;
        --radius-sm: 10px;
        --radius-pill: 999px;
    }

    [data-theme="dark"] {
        --glass-bg: rgba(15, 23, 42, 0.76);
        --glass-bg-hover: rgba(30, 41, 59, 0.85);
        --glass-bg-subtle: rgba(15, 23, 42, 0.55);
        --glass-border: rgba(255, 255, 255, 0.12);
        --glass-border-subtle: rgba(255, 255, 255, 0.08);
        --glass-shadow: 0 24px 50px -12px rgba(0, 0, 0, 0.6);
        --text-headline: #f8fafc;
        --text-body: #e2e8f0;
        --text-muted: #94a3b8;
        --text-light: #64748b;
        --primary: #6366f1;
        --primary-hover: #4f46e5;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html, body {
        height: 100%;
        overflow-x: hidden;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: #80bdfc;
        background: radial-gradient(circle at 15% 12%, #93c5fd 0%, transparent 40%),
                    radial-gradient(circle at 85% 18%, #c4b5fd 0%, transparent 45%),
                    radial-gradient(circle at 80% 85%, #fef08a 0%, #fed7aa 25%, transparent 55%),
                    radial-gradient(circle at 10% 85%, #a7f3d0 0%, transparent 40%),
                    linear-gradient(135deg, #60a5fa 0%, #93c5fd 35%, #e0e7ff 70%, #fef3c7 100%);
        background-attachment: fixed;
        color: var(--text-body);
        padding: 16px 20px;
        font-size: 13.5px;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    [data-theme="dark"] body {
        background: #090d16;
        background: radial-gradient(circle at 15% 15%, rgba(59, 130, 246, 0.22) 0%, transparent 45%),
                    radial-gradient(circle at 85% 85%, rgba(99, 102, 241, 0.15) 0%, transparent 45%),
                    radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.12) 0%, transparent 55%),
                    linear-gradient(135deg, #090d16 0%, #111827 50%, #0f172a 100%);
        background-attachment: fixed;
        color: var(--text-body);
    }

    /* Ambient Glowing Floating Orbs */
    .ambient-glow-orb {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        z-index: 0;
        opacity: 0.65;
        animation: floatOrb 18s ease-in-out infinite alternate;
    }

    [data-theme="dark"] .ambient-glow-orb {
        opacity: 0.28;
    }

    .orb-1 {
        width: 480px;
        height: 480px;
        top: -80px;
        left: -80px;
        background: radial-gradient(circle, #60a5fa, #818cf8);
    }

    [data-theme="dark"] .orb-1 {
        background: radial-gradient(circle, #3b82f6, #6366f1);
    }

    .orb-2 {
        width: 520px;
        height: 520px;
        bottom: -100px;
        right: -80px;
        background: radial-gradient(circle, #fde047, #fb923c);
        animation-delay: -6s;
    }

    [data-theme="dark"] .orb-2 {
        background: radial-gradient(circle, #6366f1, #8b5cf6);
    }

    .orb-3 {
        width: 380px;
        height: 380px;
        top: 30%;
        right: 15%;
        background: radial-gradient(circle, #c084fc, #e879f9);
        animation-delay: -12s;
    }

    [data-theme="dark"] .orb-3 {
        background: radial-gradient(circle, #8b5cf6, #ec4899);
    }

    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -25px) scale(1.06); }
        100% { transform: translate(-20px, 20px) scale(0.96); }
    }

    .container-fluid {
        max-width: 1440px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        width: 100%;
        gap: 14px;
        position: relative;
        z-index: 1;
    }

    /* Top Header: Floating Frosted Glass Bar */
    header.app-header {
        position: relative;
        z-index: 1100;
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 12px 22px;
        box-shadow: var(--glass-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fde047;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
    }

    [data-theme="dark"] .brand-icon {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fde047;
    }

    .brand-title {
        font-family: 'Outfit', sans-serif;
        font-size: 19px;
        font-weight: 800;
        color: var(--text-headline);
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .brand-subtitle {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Back to Tasks: Clean Dark Glass Pill */
    .btn-dark, #btnBackToTasks {
        background: #0f172a !important;
        color: #ffffff !important;
        border: 1.5px solid #0f172a !important;
        border-radius: var(--radius-pill) !important;
        padding: 8px 18px !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-dark:hover, #btnBackToTasks:hover {
        background: #1e293b !important;
        border-color: #1e293b !important;
        transform: none !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.3) !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .btn-dark,
    [data-theme="dark"] #btnBackToTasks {
        background: #1e293b !important;
        color: #f8fafc !important;
        border: 1.5px solid #334155 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .btn-dark:hover,
    [data-theme="dark"] #btnBackToTasks:hover {
        background: #334155 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
        transform: none !important;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3) !important;
    }

    /* Sync Today's Tasks: Signature Rulse Sunshine Yellow CTA Button */
    #btnSyncFromDb {
        background: var(--sun-yellow) !important;
        color: #0f172a !important;
        border: 1.5px solid #d9e638 !important;
        border-radius: var(--radius-pill) !important;
        padding: 8px 18px !important;
        font-size: 12.5px !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 12px rgba(161, 98, 7, 0.15) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #btnSyncFromDb:hover {
        background: var(--sun-yellow-hover) !important;
        transform: none !important;
        filter: brightness(1.04);
        box-shadow: 0 4px 16px rgba(161, 98, 7, 0.25) !important;
    }

    [data-theme="dark"] #btnSyncFromDb {
        background: #facc15 !important;
        color: #090d16 !important;
        border: 1.5px solid #fde047 !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] #btnSyncFromDb:hover {
        background: #fde047 !important;
        border-color: #fef08a !important;
        color: #000000 !important;
        transform: none !important;
        filter: brightness(1.04);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.6) !important;
    }

    /* Reset Tasks Button */
    #btnResetTasks {
        background: var(--glass-bg);
        border: 1.5px solid var(--glass-border);
        color: var(--text-headline);
        padding: 8px 16px;
        border-radius: var(--radius-pill);
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #btnResetTasks:hover {
        background: #ffffff;
        transform: none !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }

    [data-theme="dark"] #btnResetTasks {
        background: #1e293b !important;
        border: 1.5px solid #334155 !important;
        color: #f1f5f9 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    [data-theme="dark"] #btnResetTasks:hover {
        background: #334155 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
        transform: none !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25) !important;
    }

    /* User Pill Button */
    .user-menu-wrapper {
        position: relative;
        display: inline-block;
    }

    .user-pill-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 9px !important;
        background: var(--glass-bg) !important;
        border: 1.5px solid var(--glass-border) !important;
        color: var(--text-headline) !important;
        padding: 5px 14px !important;
        border-radius: var(--radius-pill) !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
        font-family: inherit;
    }

    .user-pill-btn:hover, .user-pill-btn.active {
        background: #ffffff !important;
        transform: none !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08) !important;
    }

    [data-theme="dark"] .user-pill-btn:hover,
    [data-theme="dark"] .user-pill-btn.active {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    .user-avatar-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.4);
    }

    .user-popover-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 10px);
        min-width: 250px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(32px) saturate(190%);
        -webkit-backdrop-filter: blur(32px) saturate(190%);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2), 0 6px 16px rgba(0, 0, 0, 0.06);
        padding: 12px;
        z-index: 2500;
        display: none;
        flex-direction: column;
        gap: 4px;
        animation: fadeIn 0.18s ease-out;
    }

    .user-popover-menu.show {
        display: flex;
    }

    .user-popover-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
    }

    .user-popover-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.35);
        flex-shrink: 0;
    }

    .user-popover-name {
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-headline);
    }

    .user-popover-email {
        font-size: 11px;
        color: var(--text-muted);
    }

    .user-popover-divider {
        height: 1px;
        background: var(--glass-border-subtle);
        margin: 4px 6px;
    }

    .user-popover-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        color: var(--text-body);
        font-size: 12.5px;
        font-weight: 600;
        background: transparent;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: all 0.15s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .user-popover-item:hover {
        background: rgba(255, 255, 255, 0.7);
        color: var(--primary);
    }

    [data-theme="dark"] .user-popover-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .user-popover-item.item-logout {
        color: #ef4444;
    }

    .user-popover-item.item-logout:hover {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    /* Main Content Row: 2 Frosted Cards Grid */
    .main-content-row {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        min-height: 0;
    }

    /* Left Panel: Update Details */
    .card-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--glass-shadow);
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 0;
        overflow-y: auto;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1.5px solid var(--glass-border-subtle);
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-headline);
    }

    #updateSaveStatus {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #4f46e5 !important;
        background: rgba(99, 102, 241, 0.12) !important;
        border: 1px solid rgba(99, 102, 241, 0.25) !important;
        padding: 3px 11px !important;
        border-radius: var(--radius-pill) !important;
    }

    [data-theme="dark"] #updateSaveStatus {
        color: #818cf8 !important;
        background: rgba(99, 102, 241, 0.2) !important;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label, .task_label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .form-control, .custom-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.65);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        border-radius: 12px;
        padding: 10px 14px;
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        color: var(--text-headline);
        outline: none;
        transition: all 0.2s ease;
        resize: vertical;
        box-sizing: border-box;
    }

    .form-control:focus, .custom-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    [data-theme="dark"] .form-control,
    [data-theme="dark"] .custom-input {
        background: rgba(15, 23, 42, 0.6);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    /* Right Panel: Live Email Preview */
    .preview-card {
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--glass-shadow);
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .preview-header {
        padding-bottom: 12px;
        border-bottom: 1.5px solid var(--glass-border-subtle);
        margin-bottom: 14px;
    }

    .preview-header h3 {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-headline);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .email-body {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 6px;
        padding-inline: 4px;
    }

    .mail_body_wrapper {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .subject-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 6px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--glass-border-subtle);
    }

    .subject-row .subject {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-headline);
        flex: 1;
        min-width: 0;
    }

    .btn-copy-subject {
        background: rgba(255, 255, 255, 0.85);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        border-radius: var(--radius-pill);
        padding: 6px 14px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-headline);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-copy-subject:hover {
        background: #ffffff;
        transform: none !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Copy Content: Vibrant Indigo/Purple Gradient Pill */
    .btn-copy-content, #btnCopyContent {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.38) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .btn-copy-content:hover, #btnCopyContent:hover {
        transform: none !important;
        filter: brightness(1.08);
        box-shadow: 0 4px 18px rgba(79, 70, 229, 0.5) !important;
    }

    [data-theme="dark"] .btn-copy-content,
    [data-theme="dark"] #btnCopyContent {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.45) !important;
    }

    [data-theme="dark"] .btn-copy-content:hover,
    [data-theme="dark"] #btnCopyContent:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%) !important;
        box-shadow: 0 4px 20px rgba(124, 58, 237, 0.6) !important;
        transform: none !important;
        color: #ffffff !important;
        filter: brightness(1.08);
    }

    .mail_body {
        position: relative;
        background: rgba(255, 255, 255, 0.6);
        border: 1.5px solid var(--glass-border-subtle);
        border-radius: 16px;
        padding: 18px 20px;
        font-size: 13.5px;
        line-height: 1.75;
        color: var(--text-headline);
    }

    [data-theme="dark"] .mail_body {
        background: rgba(15, 23, 42, 0.6);
        color: #f1f5f9;
    }

    /* Preview Footer / Google App Password Notice */
    .preview-footer {
        padding: 12px 18px !important;
        border-top: 1.5px solid var(--glass-border-subtle) !important;
        background: var(--glass-bg-subtle) !important;
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    }

    #smtpSetupNotice {
        background: rgba(239, 246, 255, 0.85);
        border: 1.5px solid rgba(191, 219, 254, 0.9);
        border-radius: 14px;
        padding: 10px 14px;
        color: #1e40af;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.45;
    }

    [data-theme="dark"] #smtpSetupNotice {
        background: rgba(30, 58, 138, 0.3);
        border-color: rgba(59, 130, 246, 0.3);
        color: #93c5fd;
    }

    .btn-send-email-bottom {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: var(--radius-pill) !important;
        padding: 8px 22px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.4) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-send-email-bottom:hover {
        transform: none !important;
        filter: brightness(1.08);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5) !important;
    }

    /* Modals & Dialogs: Frosted Glass Floating Style */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 99999 !important;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-card {
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(32px) saturate(190%);
        -webkit-backdrop-filter: blur(32px) saturate(190%);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 28px;
        width: 95%;
        max-width: 680px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.25);
        overflow: hidden;
    }

    [data-theme="dark"] .modal-card {
        background: rgba(22, 28, 45, 0.88);
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.7);
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1.5px solid var(--glass-border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--glass-bg-subtle);
        flex-shrink: 0;
    }

    .modal-title {
        font-family: 'Outfit', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: var(--text-headline);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-close-modal {
        background: transparent;
        border: none;
        font-size: 22px;
        color: var(--text-muted);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.9);
        color: var(--text-headline);
    }

    [data-theme="dark"] .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .modal-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    .modal-footer {
        padding: 14px 24px;
        border-top: 1.5px solid var(--glass-border-subtle);
        background: var(--glass-bg-subtle);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
    }

    /* User Profile Modal Specific Styling (Matching Screenshot 2) */
    .profile-modal-card {
        max-width: 640px;
    }

    .profile-banner-card {
        background: rgba(255, 255, 255, 0.7);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    }

    [data-theme="dark"] .profile-banner-card {
        background: rgba(15, 23, 42, 0.6);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .profile-avatar-circle {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        flex-shrink: 0;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    }

    .profile-banner-name {
        font-family: 'Outfit', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: var(--text-headline);
        line-height: 1.2;
    }

    .profile-banner-sub {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 4px;
        flex-wrap: wrap;
        font-size: 12px;
        color: var(--text-muted);
    }

    .profile-section-title {
        font-family: 'Outfit', sans-serif;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .profile-field-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 5px;
        display: block;
    }

    .profile-input-field {
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        padding: 10px 14px;
        font-size: 13px;
        color: var(--text-headline);
        background: rgba(255, 255, 255, 0.7);
        transition: all 0.2s ease;
        outline: none;
        font-family: 'Inter', system-ui, sans-serif;
        box-sizing: border-box;
    }

    .profile-input-field:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    [data-theme="dark"] .profile-input-field {
        background: rgba(15, 23, 42, 0.6);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    .profile-box-panel {
        background: rgba(255, 255, 255, 0.55);
        border: 1.5px solid var(--glass-border-subtle);
        border-radius: 18px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    [data-theme="dark"] .profile-box-panel {
        background: rgba(15, 23, 42, 0.5);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .btn-toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px 8px;
        font-size: 13px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .btn-toggle-password:hover {
        color: var(--text-headline);
    }

    .btn-prof-cancel {
        background: rgba(255, 255, 255, 0.85);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        color: var(--text-headline);
        padding: 8px 20px;
        border-radius: var(--radius-pill);
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-prof-cancel:hover {
        background: #ffffff;
        transform: translateY(-1px);
    }

    /* Save Changes: High-Impact Indigo Gradient Pill Button */
    .btn-prof-save {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        border: none !important;
        color: #ffffff !important;
        padding: 9px 24px !important;
        border-radius: var(--radius-pill) !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.42) !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-prof-save:hover {
        transform: translateY(-1.5px) scale(1.02);
        box-shadow: 0 8px 26px rgba(79, 70, 229, 0.6) !important;
    }

    .hidden-row {
        display: none !important;
    }

    .hidden-row,
    #rowBcc.hidden-row,
    #rowCc.hidden-row {
        display: none !important;
    }

    /* Send Email Modal Compose Elements - Theme-Matched Glassmorphic Design */
    .send-email-modal-card {
        max-width: 760px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(36px) saturate(200%);
        -webkit-backdrop-filter: blur(36px) saturate(200%);
        border: 1.5px solid rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        box-shadow: 0 35px 80px -15px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(99, 102, 241, 0.08);
    }

    [data-theme="dark"] .send-email-modal-card {
        background: #0d121c !important;
        border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 35px 80px -15px rgba(0, 0, 0, 0.9), 0 0 0 1px rgba(99, 102, 241, 0.15) !important;
        border-radius: 24px !important;
    }

    [data-theme="dark"] .send-email-modal-card .modal-header {
        background: #151b26 !important;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.08) !important;
        padding: 16px 24px;
    }

    [data-theme="dark"] .send-email-modal-card .modal-title span:first-child {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .send-email-modal-card .modal-title span:last-child {
        color: #94a3b8 !important;
    }

    [data-theme="dark"] .send-email-modal-card .modal-body {
        background: #0d121c !important;
        padding: 20px 24px;
    }

    [data-theme="dark"] .send-email-modal-card .modal-footer {
        background: #151b26 !important;
        border-top: 1.5px solid rgba(255, 255, 255, 0.08) !important;
        padding: 14px 24px;
    }

    .compose-card-fields {
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1.5px solid rgba(226, 232, 240, 0.9);
        border-radius: 18px;
        padding: 6px 16px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    }

    [data-theme="dark"] .compose-card-fields {
        background: #151b26 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35) !important;
        border-radius: 18px !important;
    }

    .compose-field-row {
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(226, 232, 240, 0.75);
        padding: 9px 4px;
        gap: 12px;
        min-width: 0;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    [data-theme="dark"] .compose-field-row {
        border-bottom-color: rgba(255, 255, 255, 0.07) !important;
    }

    .compose-from-row {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.04) 100%);
        border: 1.5px solid rgba(99, 102, 241, 0.2);
        border-radius: 16px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    [data-theme="dark"] .compose-from-row {
        background: #151b26 !important;
        border: 1.5px solid rgba(99, 102, 241, 0.28) !important;
        border-radius: 16px !important;
    }

    .compose-from-text {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-headline);
    }

    [data-theme="dark"] .compose-from-text {
        color: #f8fafc !important;
    }

    .compose-account-badge {
        font-size: 11px;
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.28);
        padding: 3px 12px;
        border-radius: var(--radius-pill);
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    [data-theme="dark"] .compose-account-badge {
        background: rgba(16, 185, 129, 0.18) !important;
        color: #34d399 !important;
        border-color: rgba(16, 185, 129, 0.35) !important;
    }

    .compose-field-label {
        font-family: 'Outfit', sans-serif !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.25);
        padding: 4px 10px;
        border-radius: 8px;
        min-width: 58px;
        text-align: center;
        user-select: none;
    }

    [data-theme="dark"] .compose-field-label {
        background: rgba(99, 102, 241, 0.22) !important;
        color: #c7d2fe !important;
        border-color: rgba(99, 102, 241, 0.35) !important;
    }

    .compose-field-input {
        flex: 1;
        min-width: 0;
        border: none !important;
        background: transparent !important;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-headline);
        outline: none !important;
        box-shadow: none !important;
        padding: 4px 6px;
        font-family: inherit;
    }

    [data-theme="dark"] .compose-field-input {
        color: #f8fafc !important;
    }

    [data-theme="dark"] .compose-field-input::placeholder {
        color: #64748b !important;
    }

    .compose-toggle-btn {
        background: rgba(99, 102, 241, 0.12);
        border: 1.5px solid rgba(99, 102, 241, 0.3);
        color: #4f46e5;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        padding: 4px 14px;
        border-radius: var(--radius-pill);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 0.03em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .compose-toggle-btn:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 3px 12px rgba(79, 70, 229, 0.35);
        transform: translateY(-1px);
    }

    [data-theme="dark"] .compose-toggle-btn {
        background: rgba(99, 102, 241, 0.18) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.35) !important;
        color: #c7d2fe !important;
    }

    [data-theme="dark"] .compose-toggle-btn:hover {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5) !important;
    }

    .btn-copy-preview-modal {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 16px !important;
        font-size: 11.5px !important;
        font-weight: 800 !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-copy-preview-modal:hover {
        transform: translateY(-1.5px) scale(1.02);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6) !important;
    }

    /* Email Preview Box: Theme-Matched & Readable */
    .email-preview-box {
        background: #ffffff;
        border: 1.5px solid rgba(226, 232, 240, 0.95);
        border-radius: 16px;
        padding: 18px 22px;
        flex: 1;
        min-height: 140px;
        max-height: 38vh;
        overflow-y: auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        font-size: 13.5px;
        line-height: 1.7;
        color: #0f172a;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03), inset 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .email-preview-box b u,
    #emailHtmlPreviewContainer b u {
        color: #2563eb;
        font-weight: 800;
        text-decoration: underline;
    }

    .email-preview-box .task-category-header,
    #emailHtmlPreviewContainer .task-category-header {
        color: #4f46e5;
        font-weight: 700;
    }

    .email-preview-box .task-done-badge,
    #emailHtmlPreviewContainer .task-done-badge {
        color: #059669;
        font-weight: 800;
    }

    .email-preview-box a,
    #emailHtmlPreviewContainer a {
        color: #2563eb;
        text-decoration: underline;
    }

    /* Dark Mode: Matching our dark UI theme with crisp, beautiful, glowing text */
    [data-theme="dark"] .email-preview-box {
        background: #111622 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.45), inset 0 1px 2px rgba(255, 255, 255, 0.03) !important;
    }

    [data-theme="dark"] .email-preview-box *,
    [data-theme="dark"] #emailHtmlPreviewContainer,
    [data-theme="dark"] #emailHtmlPreviewContainer * {
        color: #f8fafc !important;
        background-color: transparent !important;
    }

    [data-theme="dark"] .email-preview-box b,
    [data-theme="dark"] .email-preview-box strong,
    [data-theme="dark"] #emailHtmlPreviewContainer b,
    [data-theme="dark"] #emailHtmlPreviewContainer strong {
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    [data-theme="dark"] .email-preview-box b u,
    [data-theme="dark"] #emailHtmlPreviewContainer b u {
        color: #38bdf8 !important; /* Sky blue headers matching Tasks UI */
        font-weight: 800 !important;
        text-decoration: underline !important;
    }

    [data-theme="dark"] .email-preview-box .task-category-header,
    [data-theme="dark"] #emailHtmlPreviewContainer .task-category-header {
        color: #a5b4fc !important; /* Indigo category headers */
        font-weight: 700 !important;
    }

    [data-theme="dark"] .email-preview-box .task-done-badge,
    [data-theme="dark"] #emailHtmlPreviewContainer .task-done-badge {
        color: #34d399 !important; /* Emerald green [Done] */
        font-weight: 800 !important;
    }

    [data-theme="dark"] .email-preview-box a,
    [data-theme="dark"] #emailHtmlPreviewContainer a {
        color: #60a5fa !important;
        text-decoration: underline !important;
    }

    [data-theme="dark"] .email-preview-box ol,
    [data-theme="dark"] #emailHtmlPreviewContainer ol {
        color: #cbd5e1 !important;
    }

    [data-theme="dark"] .email-preview-box li,
    [data-theme="dark"] #emailHtmlPreviewContainer li {
        color: #f1f5f9 !important;
        margin-bottom: 4px;
        line-height: 1.6;
    }

    /* Modal Footer Buttons matching Our UI */
    .btn-cancel-modal {
        background: rgba(241, 245, 249, 0.95);
        border: 1.5px solid rgba(203, 213, 225, 0.95);
        color: #334155;
        border-radius: var(--radius-pill);
        padding: 10px 26px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel-modal:hover {
        background: #ffffff;
        color: #0f172a;
        border-color: #94a3b8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    [data-theme="dark"] .btn-cancel-modal {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.16) !important;
        color: #f1f5f9 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
    }

    [data-theme="dark"] .btn-cancel-modal:hover {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45) !important;
    }

    .btn-send-email-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: var(--radius-pill) !important;
        font-weight: 800 !important;
        font-size: 13.5px !important;
        padding: 11px 32px !important;
        box-shadow: 0 8px 25px -4px rgba(79, 70, 229, 0.5) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 0.02em;
    }

    .btn-send-email-submit:hover {
        transform: translateY(-1.5px) scale(1.02);
        box-shadow: 0 12px 32px -4px rgba(79, 70, 229, 0.7) !important;
    }


    /* Multi-Device Responsive Breakpoints */
    @media (max-width: 1200px) {
        .main-content-row {
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
    }

    @media (max-width: 991px) {
        .app-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 14px 16px;
        }
        .header-actions {
            width: 100%;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .header-actions .btn, #btnBackToTasks, #btnSyncFromDb, #btnResetTasks {
            flex: 1 1 auto;
            justify-content: center;
            padding: 9px 14px;
        }
        .main-content-row {
            grid-template-columns: 1fr;
            height: auto;
            gap: 14px;
        }
    }

    @media (max-width: 767px) {
        body {
            padding: 8px;
        }
        .brand-title {
            font-size: 17px;
        }
        .brand-subtitle {
            display: none;
        }
        .brand-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        .modal-card {
            width: 95% !important;
            border-radius: var(--radius-xl) !important;
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
            left: 0 !important;
            right: 0 !important;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Radiant Ambient Glowing Mesh Orbs -->
<div class="ambient-glow-orb orb-1"></div>
<div class="ambient-glow-orb orb-2"></div>
<div class="ambient-glow-orb orb-3"></div>

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
                        <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                            <button type="button" class="btn-copy-subject" id="btnCopySubject"><i class="fa-solid fa-heading"></i> Copy Subject</button>
                            <button type="button" class="btn-copy-content" id="btnCopyContent" title="Copy email content (formatted)">
                                <i class="fa-solid fa-copy"></i> Copy Content
                            </button>
                        </div>
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

    <!-- Footer Copyright matching Tasks theme -->
    <footer style="text-align:center; font-size:11px; color:var(--text-muted); padding:6px 0 2px 0; font-weight:500; opacity:0.85; flex-shrink:0;">
        &copy; <?= date('Y') ?> Mohit Mokariya. All Rights Reserved. Powered by CakePHP 5 & MySQL Database.
    </footer>
</div>

    <!-- Send Email Modal (Fully Responsive for All Screen Sizes) -->
    <div id="sendEmailModal" class="modal-overlay">
        <div class="modal-card send-email-modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <div style="width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35); flex-shrink: 0;">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <span style="display: block; font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800; color: var(--text-headline); line-height: 1.2;">Send Daily Update Email</span>
                        <span style="display: block; font-size: 11px; font-weight: 500; color: var(--text-muted); margin-top: 2px;">Review recipient details and dispatch your daily report</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseSendEmailModal" title="Close Modal">&times;</button>
            </div>
            
            <div class="modal-body" style="padding: 20px 24px; gap: 14px;">
                <div id="sendEmailAlert" class="alert alert-danger" style="display: none; font-size: 12px; padding: 8px 12px; border-radius: 6px; margin-bottom: 4px;"></div>

                <!-- From Field (Readonly pill display) -->
                <div class="compose-from-row">
                    <span class="compose-field-label">From</span>
                    <div class="compose-from-meta">
                        <span class="compose-from-text"><span id="emailSenderName"><?= h($currentUser['name'] ?? 'Your Name') ?></span> &lt;<span id="emailSenderAddress"><?= h($currentUser['email'] ?? 'your.email@queueloopsolutions.com') ?></span>&gt;</span>
                        <span class="compose-account-badge">
                            <i class="fa-solid fa-user-check"></i> Your Account
                        </span>
                    </div>
                </div>

                <!-- Recipient & Subject Fields Box -->
                <div class="compose-card-fields">
                    <!-- Row 1: To -->
                    <div class="compose-field-row" id="rowTo">
                        <span class="compose-field-label">To</span>
                        <input type="text" id="emailTo" class="compose-field-input" placeholder="Recipient email addresses (comma-separated)" autocomplete="off" required>
                        <div class="compose-toggles" id="toRowToggles">
                            <button type="button" class="compose-toggle-btn" id="btnToggleCc">Cc</button>
                            <button type="button" class="compose-toggle-btn" id="btnToggleBcc">Bcc</button>
                        </div>
                    </div>

                    <!-- Row 2: Cc (Collapsible) -->
                    <div class="compose-field-row hidden-row" id="rowCc" style="display: none;">
                        <span class="compose-field-label">Cc</span>
                        <input type="text" id="emailCc" class="compose-field-input" autocomplete="off" placeholder="Comma-separated emails">
                        <div class="compose-toggles" id="ccRowToggles" style="display: none;">
                            <button type="button" class="compose-toggle-btn" id="btnToggleBccFromCc">Bcc</button>
                        </div>
                    </div>

                    <!-- Row 3: Bcc (Collapsible) -->
                    <div class="compose-field-row hidden-row" id="rowBcc" style="display: none;">
                        <span class="compose-field-label">Bcc</span>
                        <input type="text" id="emailBcc" class="compose-field-input" autocomplete="off" placeholder="Comma-separated emails">
                        <div class="compose-toggles" id="bccRowToggles" style="display: none;">
                            <button type="button" class="compose-toggle-btn" id="btnToggleCcFromBcc">Cc</button>
                        </div>
                    </div>

                    <!-- Row 4: Subject -->
                    <div class="compose-field-row" style="border-bottom: none; padding-bottom: 4px;">
                        <span class="compose-field-label">Subject</span>
                        <input type="text" id="emailSubject" class="compose-field-input" placeholder="Daily update subject" autocomplete="off" required>
                    </div>
                </div>

                <!-- Email Content Preview -->
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 2px; margin-bottom: 0; flex: 1; min-height: 0;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-family: 'Outfit', sans-serif; font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-headline); display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-file-lines" style="color: #4f46e5;"></i> Email Content Preview :
                        </span>
                        <span style="background: rgba(99, 102, 241, 0.1); color: #4f46e5; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-code"></i> HTML formatted
                        </span>
                    </div>
                    <div id="emailHtmlPreviewContainer" class="email-preview-box">
                        <!-- Live rendered HTML content -->
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 16px 24px; display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn-cancel-modal" id="btnCancelSendEmail">Cancel</button>
                <button type="button" class="btn-send-email-submit" id="btnSendEmailSubmit">
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
                    <div class="profile-avatar-wrapper">
                        <div id="profileAvatarBig" class="profile-avatar-circle">
                            <?php
                                $duUserId = (int)($currentUser['id'] ?? 1);
                                $duGender = !empty($currentUser['gender']) ? $currentUser['gender'] : 'male';
                                $duMaleIdx = (($duUserId - 1) % 7) + 1;
                                $duFemaleIdx = (($duUserId - 1) % 6) + 1;
                                $duInitMalePic = ($duGender === 'male' && !empty($currentUser['picture'])) ? $currentUser['picture'] : "img/avatars/male/male_{$duMaleIdx}.png";
                                $duInitFemalePic = ($duGender === 'female' && !empty($currentUser['picture'])) ? $currentUser['picture'] : "img/avatars/female/female_{$duFemaleIdx}.png";
                                $duInitMaleUrl = $this->Url->build('/' . ltrim($duInitMalePic, '/'));
                                $duInitFemaleUrl = $this->Url->build('/' . ltrim($duInitFemalePic, '/'));
                                $duAvatar = !empty($currentUser['picture']) ? $currentUser['picture'] : ($duGender === 'female' ? $duInitFemalePic : $duInitMalePic);
                                $duAvatarUrl = str_starts_with($duAvatar, 'http') ? $duAvatar : $this->Url->build('/' . ltrim($duAvatar, '/'));
                            ?>
                            <img src="<?= h($duAvatarUrl) ?>" alt="Avatar" id="profileBigAvatarImg" class="profile-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                            <span class="profile-avatar-fallback" style="display:none;"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></span>
                        </div>
                        <input type="hidden" id="profSelectedPicture" value="<?= h($duAvatar) ?>" data-male-pic="<?= h($duInitMalePic) ?>" data-male-url="<?= h($duInitMaleUrl) ?>" data-female-pic="<?= h($duInitFemalePic) ?>" data-female-url="<?= h($duInitFemaleUrl) ?>">
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

<!-- Toast Notification - Strictly Bottom-Right Corner -->
<div id="toastNotification" class="toast">
    <i class="fa-solid fa-circle-check" id="toastIcon"></i>
    <span id="toastMessage"></span>
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

    // Copy Content (Always copies clean black text #000000 regardless of active theme)
    $('#btnCopyContent').click(function() {
        var container = document.querySelector('.mail_body');
        if (!container || !container.innerText.trim()) {
            showToast('No Mail Content to copy', 'warning');
            return;
        }
        window.copyElementAsBlackText(container, 'Mail Content copied to clipboard (Black Text)!');
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
        window.showConfirmModal({
            title: 'Reset All Tasks?',
            message: 'Are you sure you want to clear all task fields from the form and database? This action cannot be undone.',
            type: 'danger',
            icon: 'fa-solid fa-rotate-left',
            confirmText: 'Yes, Reset Tasks',
            cancelText: 'Cancel',
            onConfirm: function() {
                $('textarea.task_detail').val('');
                renderAllTaskSections();
                updateSendEmailButtonVisibility();
                broadcastDoneTasksToWorkLog();
                saveUpdateToDatabase();
                showToast('All task fields cleared!', 'success');
            }
        });
    });

    // Sync Today's Tasks Button
    $('#btnSyncFromDb').click(function() {
        var $btn = $(this);
        var $icon = $btn.find('i');
        $icon.addClass('fa-spin');
        $btn.prop('disabled', true);

        checkForTaskUpdates(true, function() {
            setTimeout(function() {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
            }, 400);
        });
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

    function checkForTaskUpdates(isManual, callback) {
        if (isCheckingTasks) {
            if (typeof callback === 'function') callback();
            return;
        }
        isCheckingTasks = true;

        $.ajax({
            url: window.APP_BASE + 'daily-updates/get-today-tasks',
            type: 'GET',
            data: { date: currentDateIso },
            dataType: 'json',
            success: function(res) {
                isCheckingTasks = false;
                if (res && res.success && res.found) {
                    var newTasks = res.tasks || '';
                    var currentTasks = $('#txt_done_task').val() || '';

                    if (isManual) {
                        // In manual mode, always synchronize the latest DB tasks
                        if (newTasks.trim() !== '') {
                            lastSyncedTasks = newTasks;
                            $('#txt_done_task').val(newTasks);
                            if (res.project_name) {
                                $('#project_name').val(res.project_name);
                                updateProjectHeader();
                            }
                            if (res.client_name && res.client_name !== '') {
                                $('#client_name').val(res.client_name);
                                updateClientGreeting();
                            }
                            formatTaskSection('list_done');
                            renderAllTaskSections();
                            updateSendEmailButtonVisibility();
                            saveUpdateToDatabase();
                            showToast('Synced latest tasks from database!', 'success');
                        } else {
                            showToast('No tasks recorded in database for today.', 'info');
                        }
                    } else {
                        // In background / auto-sync mode, only update if tasks changed
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
                            renderAllTaskSections();
                            updateSendEmailButtonVisibility();
                            saveUpdateToDatabase();
                        }
                    }
                } else if (isManual) {
                    showToast('No tasks found in DB for ' + currentDateFormatted(currentDateIso), 'info');
                }
                updateSendEmailButtonVisibility();
                if (typeof callback === 'function') callback(res);
            },
            error: function(xhr, status, err) {
                isCheckingTasks = false;
                if (isManual) {
                    showToast('Failed to sync tasks from DB: ' + (err || 'Server error'), 'error');
                }
                if (typeof callback === 'function') callback(null);
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
            $('#btnToggleBcc').show();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').hide();
            $('#rowCc').addClass('hidden-row').hide();
            $('#rowBcc').addClass('hidden-row').hide();
        } else if (isCcOpen && !isBccOpen) {
            // Cc open, Bcc closed: hide To toggles, show "Bcc" on Cc row
            $('#toRowToggles').hide();
            $('#ccRowToggles').show();
            $('#bccRowToggles').hide();
            $('#rowCc').removeClass('hidden-row').css('display', 'flex');
            $('#rowBcc').addClass('hidden-row').hide();
        } else if (!isCcOpen && isBccOpen) {
            // Bcc open, Cc closed: hide To toggles, show "Cc" on Bcc row
            $('#toRowToggles').hide();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').show();
            $('#rowCc').addClass('hidden-row').hide();
            $('#rowBcc').removeClass('hidden-row').css('display', 'flex');
        } else {
            // Both open: hide all toggle buttons
            $('#toRowToggles').hide();
            $('#ccRowToggles').hide();
            $('#bccRowToggles').hide();
            $('#rowCc').removeClass('hidden-row').css('display', 'flex');
            $('#rowBcc').removeClass('hidden-row').css('display', 'flex');
        }
    }

    // Toggle button handlers (Cc and Bcc)
    $('#btnToggleCc, #btnToggleCcFromBcc').click(function(e) {
        e.preventDefault();
        $('#rowCc').removeClass('hidden-row').css('display', 'flex');
        syncCcBccToggles();
        $('#emailCc').focus();
    });

    $('#btnToggleBcc, #btnToggleBccFromCc').click(function(e) {
        e.preventDefault();
        $('#rowBcc').removeClass('hidden-row').css('display', 'flex');
        syncCcBccToggles();
        $('#emailBcc').focus();
    });

    function getCleanMailBodyHtml() {
        var $clone = $('.mail_body').clone();
        $clone.find('button, .btn, .btn-copy-content, .btn-copy-subject, .btn-copy-preview-modal, script, style').remove();
        return $clone.html().trim();
    }

    function getCleanMailBodyText() {
        var $clone = $('.mail_body').clone();
        $clone.find('button, .btn, .btn-copy-content, .btn-copy-subject, .btn-copy-preview-modal, script, style').remove();
        return $clone.text().trim();
    }

    function openSendEmailModal() {
        var currentSubject = $('.subject').text().trim();
        var currentMailHtml = getCleanMailBodyHtml();

        $('#emailSubject').val(currentSubject);
        $('#emailHtmlPreviewContainer').html(currentMailHtml || '<i>No tasks or content to display. Please add tasks first.</i>');
        $('#sendEmailAlert').hide().removeClass('alert-danger alert-success').text('');

        // Dynamic From display matching logged-in user account
        var currName = $('.user-pill-name').text().trim() || '<?= h($currentUser['name'] ?? 'User') ?>';
        var currEmail = $('.user-popover-email').text().trim() || '<?= h($currentUser['email'] ?? 'user@helpdesk.local') ?>';
        if (currName) {
            $('#emailSenderName').text(currName);
        }
        if (currEmail) {
            $('#emailSenderAddress').text(currEmail);
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
            $('#rowCc').removeClass('hidden-row').css('display', 'flex');
        } else {
            $('#emailCc').val('');
            $('#rowCc').addClass('hidden-row').hide();
        }

        // Keep Bcc hidden by default until explicitly requested by clicking Bcc toggle
        $('#emailBcc').val('');
        $('#rowBcc').addClass('hidden-row').hide();

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
        var bodyHtmlVal = getCleanMailBodyHtml();
        var bodyTextVal = getCleanMailBodyText();

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
    // User Menu Popover & Profile Modal Logic
    // ==========================================
    // User assigned avatars cache (deterministic by gender, no random cycling)
    window.profileUserAvatars = {
        male: {
            pic: $('#profSelectedPicture').data('male-pic') || '',
            url: $('#profSelectedPicture').data('male-url') || ''
        },
        female: {
            pic: $('#profSelectedPicture').data('female-pic') || '',
            url: $('#profSelectedPicture').data('female-url') || ''
        }
    };

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

                    // Cache assigned avatars for both genders
                    if (u.male_avatar && u.male_avatar_url) {
                        window.profileUserAvatars.male = { pic: u.male_avatar, url: u.male_avatar_url };
                    }
                    if (u.female_avatar && u.female_avatar_url) {
                        window.profileUserAvatars.female = { pic: u.female_avatar, url: u.female_avatar_url };
                    }

                    // Populate gender radio selection & hidden picture
                    var gender = (u.gender || 'male').toLowerCase();
                    if (gender === 'female') {
                        $('#profGenderFemale').prop('checked', true);
                        $('#labelGenderFemale').addClass('active');
                        $('#labelGenderMale').removeClass('active');
                    } else {
                        $('#profGenderMale').prop('checked', true);
                        $('#labelGenderMale').addClass('active');
                        $('#labelGenderFemale').removeClass('active');
                    }
                    $('#profSelectedPicture').val(u.picture || '');

                    // Banner gender badge
                    var genderIcon = gender === 'female' ? '<i class="fa-solid fa-venus" style="color:#ec4899; margin-right:4px;"></i>' : '<i class="fa-solid fa-mars" style="color:#3b82f6; margin-right:4px;"></i>';
                    $('#profileGenderBadge').html(genderIcon + '<span id="profileGenderText">' + (gender.charAt(0).toUpperCase() + gender.slice(1)) + '</span>');

                    if (u.avatar_url) {
                        var $bigImg = $('#profileBigAvatarImg');
                        if ($bigImg.length && $bigImg[0]) $bigImg[0].style.display = '';
                        $bigImg.attr('src', u.avatar_url).show();
                        $('#profileAvatarBig .profile-avatar-fallback').hide();
                    } else {
                        var initial = (u.name || 'U').charAt(0).toUpperCase();
                        $('#profileAvatarBig .profile-avatar-fallback').text(initial).show();
                        $('#profileBigAvatarImg').hide();
                    }

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

    // Gender toggle change handler (switches deterministically to user's assigned avatar for chosen gender)
    $('input[name="profGender"]').change(function() {
        var selGender = $(this).val();
        $('.profile-gender-toggle-opt').removeClass('active');
        $(this).closest('.profile-gender-toggle-opt').addClass('active');

        // Update banner badge
        var gIcon = selGender === 'female' ? '<i class="fa-solid fa-venus" style="color:#ec4899; margin-right:4px;"></i>' : '<i class="fa-solid fa-mars" style="color:#3b82f6; margin-right:4px;"></i>';
        $('#profileGenderBadge').html(gIcon + '<span id="profileGenderText">' + (selGender.charAt(0).toUpperCase() + selGender.slice(1)) + '</span>');

        // Use deterministic assigned avatar for this gender (never randomly cycles on toggle)
        var assigned = (window.profileUserAvatars && window.profileUserAvatars[selGender])
            ? window.profileUserAvatars[selGender]
            : {
                pic: $('#profSelectedPicture').data(selGender + '-pic') || '',
                url: $('#profSelectedPicture').data(selGender + '-url') || ''
            };

        if (assigned && assigned.pic) {
            $('#profSelectedPicture').val(assigned.pic);
        }
        if (assigned && assigned.url) {
            var $bigImg = $('#profileBigAvatarImg');
            if ($bigImg.length && $bigImg[0]) $bigImg[0].style.display = '';
            $bigImg.attr('src', assigned.url).show();
            $('#profileAvatarBig .profile-avatar-fallback').hide();
        }
    });

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

        var gender = $('input[name="profGender"]:checked').val() || 'male';
        var picture = $('#profSelectedPicture').val() || '';

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: window.APP_BASE + 'users/update-profile',
            type: 'POST',
            data: {
                name: name,
                email: email,
                gender: gender,
                picture: picture,
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

                    if (res.user.gender) {
                        var g = res.user.gender.toLowerCase();
                        var gIcon = g === 'female' ? '<i class="fa-solid fa-venus" style="color:#ec4899; margin-right:4px;"></i>' : '<i class="fa-solid fa-mars" style="color:#3b82f6; margin-right:4px;"></i>';
                        $('#profileGenderBadge').html(gIcon + '<span id="profileGenderText">' + (g.charAt(0).toUpperCase() + g.slice(1)) + '</span>');
                    }

                    if (res.user.male_avatar && res.user.male_avatar_url) {
                        window.profileUserAvatars.male = { pic: res.user.male_avatar, url: res.user.male_avatar_url };
                    }
                    if (res.user.female_avatar && res.user.female_avatar_url) {
                        window.profileUserAvatars.female = { pic: res.user.female_avatar, url: res.user.female_avatar_url };
                    }
                    if (res.user.picture) {
                        $('#profSelectedPicture').val(res.user.picture);
                    }

                    if (res.user.avatar_url) {
                        var bigImg = $('#profileBigAvatarImg')[0];
                        if (bigImg) bigImg.style.display = '';
                        $('#profileBigAvatarImg').attr('src', res.user.avatar_url).show();
                        $('#profileAvatarBig .profile-avatar-fallback').hide();
                    }

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
