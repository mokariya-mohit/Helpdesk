<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Project> $projects
 * @var \App\Model\Entity\Project|null $defaultProject
 * @var string $todayFormatted
 * @var string $todayIso
 * @var \App\Model\Entity\WorkLog|null $todayLog
 */
$this->assign('title', "Today's Work Log & Tasks Notepad - Helpdesk");
$this->assign('meta_description', 'Keep track of daily development progress, manage clients, organize project tasks, and generate formatted notes with Helpdesk.');
$this->assign('meta_keywords', 'work log, daily task notepad, software developer journal, task manager, helpdesk');
?>

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
        line-height: 1.5;
        padding: 16px 20px;
        min-height: 100vh;
        overflow-x: hidden;
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

    .app-container {
        max-width: 1440px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
        height: calc(100vh - 36px);
        position: relative;
        z-index: 1;
    }

    /* Top Header: Frosted Floating Glass Bar */
    header {
        position: relative;
        z-index: 1000;
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

    /* Header Date Selector Capsule */
    .date-controls {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--glass-bg-subtle);
        padding: 4px 8px;
        border-radius: var(--radius-pill);
        border: 1.5px solid var(--glass-border);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    [data-theme="dark"] .date-controls {
        background: rgba(15, 23, 42, 0.75) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    .btn-nav {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 6px 12px;
        border-radius: var(--radius-pill);
        cursor: pointer;
        font-size: 12.5px;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-nav:hover {
        color: var(--text-headline);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }

    [data-theme="dark"] .btn-nav:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .btn-nav#btnToday {
        background: #ffffff;
        border: 1px solid rgba(203, 213, 225, 0.8);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        color: var(--text-headline);
        padding: 5px 14px;
    }

    [data-theme="dark"] .btn-nav#btnToday {
        background: #4f46e5 !important;
        border: 1px solid #6366f1 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35) !important;
    }

    [data-theme="dark"] .btn-nav#btnToday:hover {
        background: #4338ca !important;
        border-color: #818cf8 !important;
        color: #ffffff !important;
    }

    .date-picker {
        background: transparent;
        border: none;
        color: var(--text-headline);
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 8px;
        outline: none;
        cursor: pointer;
    }

    [data-theme="dark"] .date-controls .date-picker,
    [data-theme="dark"] #datePicker {
        background: #1e293b !important;
        background-color: #1e293b !important;
        border: 1.5px solid #334155 !important;
        border-radius: var(--radius-pill) !important;
        color: #f8fafc !important;
        color-scheme: dark !important;
        font-family: 'Fira Code', monospace !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        padding: 5px 12px !important;
        outline: none !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
        transition: all 0.2s ease;
    }

    [data-theme="dark"] .date-controls .date-picker:focus,
    [data-theme="dark"] #datePicker:focus {
        background-color: #334155 !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
    }

    [data-theme="dark"] .date-picker::-webkit-calendar-picker-indicator,
    [data-theme="dark"] #datePicker::-webkit-calendar-picker-indicator {
        filter: invert(0.9) brightness(1.2) !important;
        cursor: pointer !important;
        opacity: 0.85;
    }

    .action-btns {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn {
        background: var(--glass-bg);
        border: 1.5px solid var(--glass-border);
        color: var(--text-headline);
        padding: 8px 16px;
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .btn:hover {
        background: var(--glass-bg-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        color: var(--text-headline);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4) !important;
        font-weight: 800 !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%) !important;
        transform: translateY(-1.5px) scale(1.02) !important;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.6) !important;
        color: #ffffff !important;
    }

    #btnOpenGlobalSearch {
        background: var(--glass-bg);
        border: 1.5px solid var(--glass-border);
        color: var(--text-headline);
        padding: 8px 18px;
        border-radius: var(--radius-pill);
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    #btnOpenGlobalSearch:hover {
        background: #ffffff;
        transform: translateY(-1.5px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }

    [data-theme="dark"] #btnOpenGlobalSearch {
        background: #1e293b !important;
        border: 1.5px solid #334155 !important;
        color: #f8fafc !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] #btnOpenGlobalSearch:hover {
        background: #334155 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }

    /* Rulse Signature Sunshine Yellow CTA Pill (Matches "+ Add Task") */
    /* Rulse Signature Sunshine Yellow CTA Pill (Matches "+ Add Task") */
    #btnInsertTemplate {
        background: var(--sun-yellow) !important;
        color: #0f172a !important;
        border: 1.5px solid #d9e638 !important;
        border-radius: var(--radius-pill) !important;
        padding: 8px 20px !important;
        font-size: 12.5px !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 12px rgba(161, 98, 7, 0.15) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #btnInsertTemplate:hover {
        background: var(--sun-yellow-hover) !important;
        transform: translateY(-1.5px) scale(1.02) !important;
        box-shadow: 0 6px 18px rgba(161, 98, 7, 0.25) !important;
    }

    [data-theme="dark"] #btnInsertTemplate {
        background: #facc15 !important;
        color: #090d16 !important;
        border: 1.5px solid #fde047 !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] #btnInsertTemplate:hover {
        background: #fde047 !important;
        border-color: #fef08a !important;
        color: #000000 !important;
        transform: translateY(-1.5px) scale(1.02) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6) !important;
    }

    /* User Profile Pill */
    .user-menu-wrapper {
        position: relative;
    }

    .user-pill-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 9px !important;
        background: var(--glass-bg) !important;
        border: 1.5px solid var(--glass-border) !important;
        padding: 5px 14px !important;
        border-radius: var(--radius-pill) !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        color: var(--text-headline) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
        font-family: inherit;
    }

    .user-pill-btn:hover {
        background: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08) !important;
    }

    [data-theme="dark"] .user-pill-btn:hover {
        background: rgba(255, 255, 255, 0.15) !important;
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

    .user-popover-menu.active {
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
        font-weight: 800;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.35);
    }

    .user-popover-meta {
        display: flex;
        flex-direction: column;
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

    /* Main Workspace Layout */
    .workspace {
        display: grid;
        grid-template-columns: 310px 1fr;
        gap: 16px;
        flex: 1;
        min-height: 0;
    }

    /* Left Sidebar: Past Journal Logs */
    .sidebar {
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 18px 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: var(--glass-shadow);
        min-height: 0;
    }

    .sidebar-header {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: var(--text-headline);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1.5px solid var(--glass-border-subtle);
    }

    #logCountBadge {
        font-size: 11.5px !important;
        font-weight: 800 !important;
        color: #4f46e5 !important;
        background: rgba(99, 102, 241, 0.12) !important;
        border: 1px solid rgba(99, 102, 241, 0.25) !important;
        padding: 2px 10px !important;
        border-radius: var(--radius-pill) !important;
    }

    [data-theme="dark"] #logCountBadge {
        color: #818cf8 !important;
        background: rgba(99, 102, 241, 0.2) !important;
    }

    .month-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--glass-bg-subtle);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-pill);
        padding: 5px 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .btn-month-nav {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .btn-month-nav:hover {
        background: #ffffff;
        color: var(--text-headline);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    [data-theme="dark"] .btn-month-nav:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .month-label {
        font-family: 'Outfit', sans-serif;
        font-size: 12.5px;
        font-weight: 800;
        color: var(--text-headline);
    }

    .search-box {
        position: relative;
    }

    .search-input {
        width: 100%;
        background: var(--glass-bg-subtle);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-pill);
        padding: 8px 14px 8px 36px;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-headline);
        outline: none;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    [data-theme="dark"] .search-input:focus {
        background: rgba(15, 23, 42, 0.85);
    }

    .search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 12px;
    }

    .log-list {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 6px;
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
        padding-right: 4px;
    }

    .log-list::-webkit-scrollbar {
        width: 4px;
    }

    .log-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .log-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.4);
        border-radius: 4px;
    }

    .log-item {
        position: relative;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.45);
        border: 1.5px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    [data-theme="dark"] .log-item {
        background: rgba(15, 23, 42, 0.45);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
    }

    .log-item:hover {
        background: rgba(255, 255, 255, 0.88);
        border-color: rgba(255, 255, 255, 0.95);
        transform: translateY(-1.5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    [data-theme="dark"] .log-item:hover {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.18);
    }

    /* Rulse Active Item Style */
    .log-item.active {
        background: linear-gradient(135deg, rgba(235, 248, 74, 0.35) 0%, rgba(254, 240, 138, 0.55) 100%) !important;
        border: 1.5px solid rgba(235, 248, 74, 0.95) !important;
        box-shadow: 0 6px 20px rgba(235, 248, 74, 0.4) !important;
        font-weight: 800;
    }

    [data-theme="dark"] .log-item.active {
        background: linear-gradient(135deg, rgba(235, 248, 74, 0.15) 0%, rgba(245, 158, 11, 0.22) 100%) !important;
        border: 1.5px solid rgba(235, 248, 74, 0.6) !important;
        box-shadow: 0 6px 20px rgba(235, 248, 74, 0.2) !important;
    }

    .log-date {
        font-size: 12.5px;
        color: var(--text-headline);
        font-family: 'Fira Code', monospace;
        font-weight: 700;
        white-space: nowrap;
    }

    .log-badge {
        font-size: 11px;
        color: var(--text-muted);
        background: rgba(255, 255, 255, 0.85);
        padding: 2px 9px;
        border-radius: var(--radius-pill);
        border: 1px solid rgba(203, 213, 225, 0.8);
        white-space: nowrap;
        font-weight: 700;
    }

    .log-item.active .log-badge {
        background: #0f172a !important;
        color: #fde047 !important;
        border-color: #0f172a !important;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 26px 12px;
        background: var(--glass-bg-subtle);
        border: 1.5px dashed var(--glass-border);
        border-radius: var(--radius-lg);
        margin: 6px 0;
        gap: 6px;
    }

    .empty-state .empty-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: var(--text-muted);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 4px;
    }

    .empty-state .empty-text {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-headline);
    }

    .empty-state .empty-subtext {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* Right Main Panel: Clean Text Editor */
    .editor-container {
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: var(--radius-xl);
        display: flex;
        flex-direction: column;
        box-shadow: var(--glass-shadow);
        min-height: 0;
        overflow: hidden;
    }

    .editor-toolbar {
        padding: 10px 18px;
        background: var(--glass-bg-subtle);
        border-bottom: 1.5px solid var(--glass-border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: nowrap;
        overflow-x: auto;
    }

    .editor-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        flex-shrink: 0;
    }

    .editor-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
        flex-shrink: 0;
        margin-left: auto;
    }

    .editor-title {
        font-family: 'Fira Code', monospace;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-headline);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Custom Project & Client Selection Pill Dropdown */
    .custom-project-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--glass-bg);
        padding: 4px 6px 4px 14px;
        border-radius: var(--radius-pill);
        border: 1.5px solid var(--glass-border);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .custom-project-wrapper:hover {
        background: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }

    [data-theme="dark"] .custom-project-wrapper:hover {
        background: rgba(30, 41, 59, 0.85);
    }

    .custom-project-trigger {
        background: transparent;
        border: none;
        outline: none;
        font-family: 'Fira Code', monospace;
        font-size: 12.5px;
        font-weight: 800;
        color: var(--text-headline);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 2px 4px;
    }

    .custom-project-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        min-width: 240px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(32px) saturate(190%);
        -webkit-backdrop-filter: blur(32px) saturate(190%);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 18px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
        padding: 8px;
        z-index: 2500;
        display: none;
        flex-direction: column;
        gap: 4px;
        animation: fadeIn 0.15s ease-out;
    }

    .custom-project-menu.active {
        display: flex;
    }

    .custom-menu-header {
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 6px 10px 6px 10px;
        border-bottom: 1px solid var(--glass-border-subtle);
        letter-spacing: 0.05em;
    }

    .custom-menu-list {
        display: flex;
        flex-direction: column;
        gap: 3px;
        max-height: 220px;
        overflow-y: auto;
    }

    .custom-menu-item {
        padding: 8px 12px;
        border-radius: 10px;
        font-family: 'Fira Code', monospace;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-headline);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
    }

    .custom-menu-item:hover {
        background: rgba(255, 255, 255, 0.7);
        color: var(--primary);
    }

    [data-theme="dark"] .custom-menu-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .custom-menu-item.active {
        background: rgba(235, 248, 74, 0.3);
        color: #0f172a;
        font-weight: 800;
    }

    .btn-add-project {
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.25);
        padding: 4px 11px;
        border-radius: var(--radius-pill);
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-add-project:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    .btn-add-client {
        background: rgba(2, 132, 199, 0.12);
        color: #0284c7;
        border: 1px solid rgba(2, 132, 199, 0.25);
        padding: 4px 11px;
        border-radius: var(--radius-pill);
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-add-client:hover {
        background: #0284c7;
        color: #ffffff;
    }

    /* Toolbar Action Buttons */
    #btnTogglePreview {
        background: var(--glass-bg) !important;
        border: 1.5px solid var(--glass-border) !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: var(--text-headline) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #btnTogglePreview:hover {
        background: #ffffff !important;
        transform: translateY(-1px);
    }

    [data-theme="dark"] #btnTogglePreview {
        background: #1e293b !important;
        border: 1.5px solid #334155 !important;
        color: #f8fafc !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] #btnTogglePreview:hover {
        background: #334155 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }

    /* AI Polish Gradient Pill */
    #btnAiPolish {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%) !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 800 !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        box-shadow: 0 4px 16px rgba(139, 92, 246, 0.4) !important;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
        min-width: 96px;
    }

    #btnAiPolish:hover {
        transform: translateY(-1.5px) scale(1.02);
        box-shadow: 0 6px 22px rgba(139, 92, 246, 0.6) !important;
    }

    [data-theme="dark"] #btnAiPolish {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 18px rgba(124, 58, 237, 0.5) !important;
    }

    [data-theme="dark"] #btnAiPolish:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 50%, #db2777 100%) !important;
        box-shadow: 0 6px 24px rgba(124, 58, 237, 0.75) !important;
        transform: translateY(-1.5px) scale(1.03) !important;
        color: #ffffff !important;
    }

    #btnCopyOnlyTasks {
        background: var(--glass-bg) !important;
        border: 1.5px solid var(--glass-border) !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: var(--text-headline) !important;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    #btnCopyOnlyTasks:hover {
        background: #ffffff !important;
        transform: translateY(-1px);
    }

    [data-theme="dark"] #btnCopyOnlyTasks {
        background: #1e293b !important;
        border: 1.5px solid #334155 !important;
        color: #f8fafc !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] #btnCopyOnlyTasks:hover {
        background: #334155 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }

    #btnClearCurrent {
        background: rgba(254, 242, 242, 0.85) !important;
        border: 1.5px solid rgba(254, 202, 202, 0.85) !important;
        border-radius: var(--radius-pill) !important;
        padding: 6px 12px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #e11d48 !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #btnClearCurrent:hover {
        background: #fee2e2 !important;
        transform: translateY(-1px);
    }

    /* Editor Textarea */
    .editor-textarea {
        flex: 1;
        width: 100%;
        padding: 24px 28px;
        background: rgba(255, 255, 255, 0.58);
        border: none;
        outline: none;
        font-family: 'Fira Code', monospace;
        font-size: 13.5px;
        line-height: 1.75;
        color: var(--text-headline);
        resize: none;
        tab-size: 2;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
        overflow-y: auto;
    }

    [data-theme="dark"] .editor-textarea {
        background: rgba(15, 23, 42, 0.6);
        color: #f1f5f9;
    }

    .editor-preview-box {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.75 !important;
        color: var(--text-headline) !important;
        padding: 24px 28px !important;
    }

    .editor-preview-box a {
        color: #2563eb !important;
        text-decoration: underline !important;
        cursor: pointer !important;
    }

    .link-chip {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid #7dd3fc;
        padding: 3px 10px;
        border-radius: var(--radius-pill);
        font-size: 11.5px;
        font-weight: 700;
        color: #0369a1 !important;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
    }

    .link-chip:hover {
        background: #f0f9ff !important;
        border-color: #0284c7 !important;
    }

    /* Editor Footer Bar */
    .editor-footer {
        padding: 12px 22px;
        background: var(--glass-bg-subtle);
        border-top: 1.5px solid var(--glass-border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        margin-right: 6px;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.7);
    }

    .status-dot.saving {
        background: #f59e0b;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.7);
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }

    /* Continue to Email Gradient Pill CTA */
    #btnContinueToEmail {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        color: #ffffff !important;
        border-radius: var(--radius-pill) !important;
        padding: 9px 24px !important;
        font-weight: 800 !important;
        font-size: 13px !important;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.42) !important;
        border: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #btnContinueToEmail:hover {
        transform: translateY(-1.5px) scale(1.02);
        box-shadow: 0 8px 26px rgba(79, 70, 229, 0.6) !important;
    }

    /* Modals: Frosted Floating Dialogs */
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
        width: 100%;
        max-width: 680px;
        max-height: 85vh;
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
        gap: 16px;
        overflow-y: auto;
        flex: 1;
    }

    .global-search-input-box {
        position: relative;
    }

    .global-search-input {
        width: 100%;
        padding: 12px 18px 12px 42px;
        background: rgba(255, 255, 255, 0.7);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        border-radius: var(--radius-lg);
        font-family: 'Fira Code', monospace;
        font-size: 13px;
        color: var(--text-headline);
        outline: none;
        transition: all 0.2s ease;
    }

    [data-theme="dark"] .global-search-input {
        background: rgba(15, 23, 42, 0.6);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    .global-search-input:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .search-results-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 440px;
        overflow-y: auto;
    }

    .result-card {
        background: rgba(255, 255, 255, 0.6);
        border: 1.5px solid var(--glass-border-subtle);
        border-radius: var(--radius-lg);
        padding: 14px 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .result-card:hover {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    [data-theme="dark"] .result-card {
        background: rgba(15, 23, 42, 0.5);
    }

    [data-theme="dark"] .result-card:hover {
        background: rgba(30, 41, 59, 0.8);
    }

    .result-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .result-date {
        font-family: 'Fira Code', monospace;
        font-size: 12.5px;
        font-weight: 800;
        color: var(--primary);
    }

    .result-project {
        font-size: 11px;
        font-weight: 700;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        padding: 3px 10px;
        border-radius: var(--radius-pill);
    }

    .result-snippet {
        font-family: 'Fira Code', monospace;
        font-size: 12px;
        color: var(--text-headline);
        line-height: 1.55;
        white-space: pre-wrap;
        background: rgba(255, 255, 255, 0.7);
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid var(--glass-border-subtle);
    }

    [data-theme="dark"] .result-snippet {
        background: rgba(15, 23, 42, 0.6);
    }

    mark.highlight {
        background: #fef08a;
        color: #1e1b4b;
        font-weight: 800;
        padding: 1px 4px;
        border-radius: 4px;
    }

    /* Modal Project Item List */
    .modal-project-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 280px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .modal-project-item {
        background: rgba(255, 255, 255, 0.78);
        border: 1.5px solid rgba(226, 232, 240, 0.95);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-project-item:hover {
        background: #ffffff;
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.12);
        transform: translateY(-1.5px);
    }

    [data-theme="dark"] .modal-project-item {
        background: rgba(15, 23, 42, 0.65);
        border-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    [data-theme="dark"] .modal-project-item:hover {
        background: rgba(30, 41, 59, 0.85);
        border-color: rgba(99, 102, 241, 0.5);
    }

    .modal-project-name {
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-headline);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-action-icon {
        background: rgba(255, 255, 255, 0.9);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        color: var(--text-muted);
        cursor: pointer;
        padding: 7px 11px;
        border-radius: 10px;
        font-size: 12px;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-action-icon:hover {
        background: #ffffff;
        color: var(--primary);
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.22);
    }

    .btn-action-icon.delete:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.22);
    }

    /* Mobile Sidebar Toggle Button */
    .btn-toggle-sidebar-mobile {
        display: none;
        align-items: center;
        gap: 6px;
        background: var(--glass-bg);
        border: 1.5px solid var(--glass-border);
        padding: 6px 12px;
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        color: var(--text-headline);
        transition: all 0.15s ease;
    }

    .btn-toggle-sidebar-mobile:hover {
        background: #ffffff;
        border-color: var(--primary);
    }

    .brand-row {
        display: contents;
    }

    .header-mobile-tools {
        display: none;
    }

    /* Multi-Device Responsive Breakpoints */
    @media (min-width: 1201px) {
        .btn-toggle-sidebar-mobile {
            display: none !important;
        }
    }

    /* Laptop screens (1024px - 1200px) */
    @media (max-width: 1200px) {
        body {
            padding: 12px 16px;
        }
        .app-container {
            max-width: 100%;
            height: calc(100vh - 24px);
            gap: 12px;
        }
        .workspace {
            grid-template-columns: 260px 1fr;
            gap: 12px;
        }
        header {
            padding: 10px 16px;
            gap: 12px;
        }
        .sidebar {
            padding: 14px;
        }
    }

    /* Tablet screens (768px - 991px) */
    @media (max-width: 991px) {
        body {
            padding: 10px;
            height: auto;
            min-height: 100vh;
            overflow-y: auto;
        }
        .app-container {
            height: auto;
            min-height: calc(100vh - 20px);
            gap: 12px;
        }
        header {
            padding: 14px 16px;
            gap: 12px;
            flex-direction: column;
            align-items: stretch;
        }
        .brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .header-mobile-tools {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-toggle-sidebar-mobile {
            display: inline-flex;
        }
        .date-controls {
            width: 100%;
            justify-content: center;
            padding: 6px 12px;
        }
        .date-picker {
            flex: 1;
            max-width: 220px;
            text-align: center;
        }
        .action-btns {
            width: 100%;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }
        .action-btns .btn, #btnOpenGlobalSearch, #btnInsertTemplate {
            flex: 1 1 auto;
            justify-content: center;
            padding: 9px 14px;
        }
        .workspace {
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: auto;
        }
        .sidebar {
            order: 2;
            max-height: 380px;
            display: none;
        }
        .sidebar.open-mobile {
            display: flex !important;
            animation: fadeIn 0.2s ease-in-out;
        }
        .editor-container {
            order: 1;
            min-height: 520px;
        }
        .editor-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            padding: 12px 14px;
        }
        .editor-title-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .custom-project-wrapper {
            flex: 1 1 calc(50% - 6px);
            min-width: 130px;
        }
        .custom-project-trigger {
            width: 100%;
        }
        .editor-toolbar-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .editor-textarea {
            min-height: 400px;
        }
        .editor-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            padding: 12px 16px;
        }
        .editor-footer-right {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
            gap: 8px;
        }
        #btnContinueToEmail {
            width: 100%;
            justify-content: center;
            padding: 10px 18px;
        }
    }

    /* Mobile screens (< 768px) */
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

<div class="app-container">
    <!-- Header -->
    <header>
        <div class="brand-row">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-note-sticky"></i>
                </div>
                <div>
                    <h1 class="brand-title">Today's Work Log</h1>
                    <p class="brand-subtitle">Track your daily tasks, progress, and development notes</p>
                </div>
            </div>

            <!-- Mobile Quick Sidebar Toggle (Visible only on <= 991px) -->
            <div class="header-mobile-tools">
                <button type="button" class="btn btn-toggle-sidebar-mobile" id="btnToggleSidebarMobile" title="View / Hide Past Work Logs">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i>
                    <span>Logs</span>
                </button>
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
                <i class="fa-solid fa-magnifying-glass text-indigo-600"></i> <span class="action-btn-text">Global Search</span>
            </button>
            <button type="button" class="btn btn-primary" id="btnInsertTemplate" title="Insert Standard Daily Template">
                <i class="fa-solid fa-wand-magic-sparkles"></i> <span class="action-btn-text">Insert Template</span>
            </button>

            <?php if (!empty($currentUser)): ?>
                <!-- User Menu Dropdown -->
                <div class="user-menu-wrapper">
                    <button type="button" class="user-pill-btn" id="btnUserMenuToggle" title="Click for Profile & Logout">
                        <span style="width:24px; height:24px; border-radius:50%; background:linear-gradient(135deg, #6366f1, #4f46e5); color:#ffffff; font-size:11px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(99,102,241,0.4);"><?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?></span>
                        <span class="user-pill-name" style="font-size:12.5px; font-weight:700;"><?= h($currentUser['name']) ?></span>
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
                <input type="text" id="searchInput" class="search-input" placeholder="Search date logs (DD, MM, YYYY)..." inputmode="numeric" autocomplete="off" title="Filter by date, month or year (numbers only)">
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
                                <span>Select Project</span>
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
                        <button type="button" id="btnAddClient" class="btn-add-client" title="Manage Clients">
                            <i class="fa-solid fa-plus"></i> Client
                        </button>

                        <!-- Custom Client Popover Menu -->
                        <div id="clientDropdownMenu" class="custom-project-menu">
                            <div class="custom-menu-header">
                                <span>Select Client</span>
                            </div>
                            <div id="clientMenuList" class="custom-menu-list">
                                <!-- Dynamically populated from Database -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="editor-toolbar-actions" style="display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; flex-shrink: 0;">
                    <button type="button" class="btn" id="btnTogglePreview" title="Preview notes with clickable links">
                        <i class="fa-regular fa-eye" id="iconTogglePreview"></i> <span id="textTogglePreview">Preview</span>
                    </button>
                    <button type="button" class="btn" id="btnAiPolish" title="Fix grammar and make sentences professional with Gemini AI">
                        <i class="fa-solid fa-pen-to-square"></i> AI Polish
                    </button>
                    <button type="button" class="btn" id="btnCopyOnlyTasks" title="Copy only tasks (without headers, dates, or bullets)">
                        <i class="fa-regular fa-copy" style="color: #4f46e5;"></i> Copy Content
                    </button>
                    <button type="button" class="btn" id="btnClearCurrent" title="Clear current editor note">
                        <i class="fa-solid fa-trash text-rose-500"></i> Clear Note
                    </button>
                </div>
            </div>

            <!-- No Projects Alert Banner -->
            <div id="noProjectWarningBanner" style="display: <?= empty($projects) || count($projects) === 0 ? 'flex' : 'none' ?>; align-items:center; justify-content:space-between; background:rgba(254, 243, 199, 0.85); border-bottom:1.5px solid rgba(253, 230, 138, 0.9); padding:10px 18px; color:#92400e; font-size:12px; font-weight:600;">
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
            <!-- Formatted Clickable Links Preview -->
            <div id="workNotesPreview" class="editor-textarea editor-preview-box" style="display:none;"></div>

            <div class="editor-footer">
                <div>
                    <span class="status-dot" id="saveStatusDot"></span>
                    <span id="saveStatusText"><?= $todayLog && $todayLog->modified ? 'Last saved at ' . $todayLog->modified->format('h:i:s A') : 'All changes saved' ?></span>
                </div>
                <div class="editor-footer-right" style="display: flex; align-items: center; gap: 14px;">
                    <span id="charCount">0 characters | 0 tasks</span>
                    <a href="#" id="btnContinueToEmail" class="btn btn-primary btn-continue-to-email" title="Continue to Daily Update Generator">
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
                <div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Enter a keyword above to search your saved tasks and updates.</div>
            </div>
        </div>
    </div>
</div>

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
            <div style="background: rgba(255, 255, 255, 0.55); border: 1.5px solid rgba(226, 232, 240, 0.85); border-radius: 18px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px;">
                <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block;">Add New Project to Database</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="newProjectInput" class="global-search-input" placeholder="project_name (e.g. 8.bloqs, internal-crm)" style="padding: 11px 16px; flex: 1; border-radius: 12px; font-family: 'Inter', system-ui, sans-serif; font-size: 13.5px; border: 1.5px solid rgba(203, 213, 225, 0.85); background: #ffffff;">
                    <button type="button" id="btnSaveNewProject" class="btn btn-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important; color: #ffffff !important; border: none !important; border-radius: 999px !important; font-weight: 800 !important; font-size: 13px !important; padding: 10px 22px !important; box-shadow: 0 6px 18px rgba(79, 70, 229, 0.42) !important; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); white-space: nowrap;">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
                <div id="newProjectNameError" style="display:none; color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <!-- Existing Projects List -->
            <div>
                <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 10px;">All Projects in Database</label>
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
            <div style="background: rgba(255, 255, 255, 0.55); border: 1.5px solid rgba(226, 232, 240, 0.85); border-radius: 18px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px;">
                <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block;">Add New Client to Database</label>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div>
                        <input type="text" id="newClientInput" class="global-search-input" placeholder="Client Name (e.g. John Doe, Example Company)" style="padding: 10px 16px; width: 100%; box-sizing: border-box; border-radius: 12px; font-family: 'Inter', system-ui, sans-serif; font-size: 13.5px; border: 1.5px solid rgba(203, 213, 225, 0.85); background: #ffffff;">
                        <div id="newClientNameError" style="display:none; color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                    <div>
                        <div class="add-client-inputs-row" style="display: flex; gap: 10px; align-items: center;">
                            <input type="email" id="newClientEmailInput" class="global-search-input" placeholder="Client Email (e.g. client@company.com)" style="padding: 10px 16px; flex: 1; min-width: 0; box-sizing: border-box; border-radius: 12px; font-family: 'Inter', system-ui, sans-serif; font-size: 13.5px; border: 1.5px solid rgba(203, 213, 225, 0.85); background: #ffffff;">
                            <button type="button" id="btnSaveNewClient" class="btn btn-primary btn-add-client-submit" style="background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%) !important; color: #ffffff !important; border: none !important; border-radius: 999px !important; font-weight: 800 !important; font-size: 13px !important; white-space: nowrap; padding: 10px 20px !important; box-shadow: 0 6px 18px rgba(2, 132, 199, 0.42) !important; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                                <i class="fa-solid fa-plus"></i> Add Client
                            </button>
                        </div>
                        <div id="newClientEmailError" style="display:none; color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px;">
                            <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Clients List -->
            <div>
                <label style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 10px;">All Clients in Database</label>
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
            <div id="profileAlert" style="display: none; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;"></div>

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

<!-- Toast -->
<div id="toastNotification" class="toast">
    <i class="fa-solid fa-circle-check" id="toastIcon"></i>
    <span id="toastMessage">Saved to MySQL database!</span>
</div>

<?php
$renderedFlash = $this->Flash->render();
?>
<?php if (!empty($renderedFlash)): ?>
    <div style="display:none;" id="initialFlashHolder"><?= $renderedFlash ?></div>
<?php endif; ?>

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
    var allLogsCache = [];
    var autoSaveTimer = null;

    function isoToFormatted(iso) {
        if (!iso) return '';
        var parts = iso.split('-');
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    function countTasks(text) {
        if (!text || !text.trim()) return 0;
        var lines = text.split(/\r?\n/);
        var count = 0;

        lines.forEach(function(rawLine) {
            var trimmed = rawLine.trim();
            if (!trimmed) return;

            // 1. Skip date header (e.g. 03-09-2026)
            if (/^\d{2}-\d{2}-\d{4}/.test(trimmed)) return;

            // 2. Skip separator lines (e.g. -------------------)
            if (/^[-=]{3,}$/.test(trimmed)) return;

            // 3. Skip category headers (e.g. "Backend:", "**Backend:**", "Frontend & UI:")
            var cleanHeaderCandidate = trimmed.replace(/^\*\*|\*\*$/g, '').trim();
            if (/^[A-Za-z0-9\s_\-\/&]{2,60}:$/.test(cleanHeaderCandidate) && !/^\d+[\.\)]/.test(trimmed) && !/^[•▪▫◦*\-–—]/.test(trimmed)) {
                return;
            }

            // 4. Skip indented sub-points (lines starting with 2+ spaces or \t)
            if (/^(\s{2,}|\t)/.test(rawLine)) return;

            // 5. Check if valid task body exists
            var clean = trimmed.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[•▪▫◦*\-–—]+)\s*/, '').trim();
            if (clean) {
                count++;
            }
        });
        return count;
    }

    function updateCharCount() {
        var val = $('#workNotesEditor').val();
        var len = val.length;
        var tasks = countTasks(val);
        $('#charCount').text(len + ' characters | ' + tasks + (tasks === 1 ? ' task' : ' tasks'));
    }

    // Dedicated Preview Content Renderer (Faithful to user's text with clickable links)
    function renderPreviewContent() {
        var $editor = $('#workNotesEditor');
        var $preview = $('#workNotesPreview');
        var raw = $editor.val() || '';
        if (!raw.trim()) {
            $preview.html('<i style="color: #94a3b8;">No notes to preview. Type your tasks in Edit mode.</i>');
            return;
        }

        var lines = raw.split(/\r?\n/);
        var headerHtml = '';
        var taskBodyLines = [];
        var pastHeader = false;

        for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            var trimmed = line.trim();
            if (!pastHeader && /^\d{2}-\d{2}-\d{4}/.test(trimmed)) {
                headerHtml += '<div style="font-weight: 700; color: #4338ca; margin: 6px 0 3px 0;">' + escapeHtml(trimmed) + '</div>';
                continue;
            }
            if (!pastHeader && /^[-=]{3,}$/.test(trimmed)) {
                headerHtml += '<hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 6px 0;">';
                continue;
            }
            pastHeader = true;
            taskBodyLines.push(line);
        }

        var parsedTasks = window.parseTaskMarkdownToHtml(taskBodyLines.join('\n'), false);
        $preview.html(headerHtml + (headerHtml ? '<div style="height: 4px;"></div>' : '') + parsedTasks);
    }

    function setPreviewMode(enable) {
        isPreviewMode = !!enable;
        var $editor = $('#workNotesEditor');
        var $preview = $('#workNotesPreview');
        var $icon = $('#iconTogglePreview');
        var $text = $('#textTogglePreview');
        var $btn = $('#btnTogglePreview');

        if (isPreviewMode) {
            renderPreviewContent();
            $editor.hide();
            $preview.show();
            $icon.attr('class', 'fa-solid fa-pen');
            $text.text('Edit');
            $btn.css({'background': '#e0e7ff', 'color': '#4338ca', 'border-color': '#c7d2fe'});
        } else {
            $preview.hide();
            $editor.show().focus();
            $icon.attr('class', 'fa-regular fa-eye');
            $text.text('Preview');
            $btn.css({'background': '', 'color': '', 'border-color': ''});
        }
    }

    // Toggle Preview Mode
    var isPreviewMode = false;
    $('#btnTogglePreview').click(function() {
        setPreviewMode(!isPreviewMode);
    });

    function updateEditorProjectHeader() {
        var val = $('#workNotesEditor').val();
        if (!val || val.trim() === '') return;
        var lines = val.split('\n');
        if (lines.length > 0 && /^\d{2}-\d{2}-\d{4}/.test(lines[0].trim())) {
            var dateMatch = lines[0].trim().match(/^(\d{2}-\d{2}-\d{4})/);
            var datePart = dateMatch ? dateMatch[1] : currentDateFormatted;
            lines[0] = datePart + (activeProjectName ? '  ' + activeProjectName : '');
            $('#workNotesEditor').val(lines.join('\n'));
            updateCharCount();
            triggerAutoSave();
        }
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
                if (isPreviewMode) {
                    renderPreviewContent();
                }
            },
            error: function() {
                $('#saveStatusDot').removeClass('saving');
                $('#saveStatusText').text('Connection error');
            }
        });
    }

    // 2. Save note to MySQL (Debounced Auto-Save)
    function saveNoteToDatabase(callback) {
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
                    broadcastTaskUpdate();
                } else {
                    $('#saveStatusText').text('Save failed');
                }
                if (typeof callback === 'function') callback(res);
            },
            error: function() {
                $('#saveStatusDot').removeClass('saving');
                $('#saveStatusText').text('Connection error');
                if (typeof callback === 'function') callback({ success: false });
            }
        });
    }

    // Real-Time Cross-Tab Synchronization (0ms instant sync without constant server AJAX)
    var taskSyncChannel = (typeof window.BroadcastChannel !== 'undefined') ? new BroadcastChannel('helpdesk_tasks_sync_channel') : null;

    function broadcastTaskUpdate() {
        var content = $('#workNotesEditor').val() || '';
        var payload = {
            type: 'TASKS_UPDATED',
            date: currentDateIso,
            tasks: content,
            project_name: activeProjectName,
            client_name: activeClientName,
            timestamp: Date.now()
        };

        if (taskSyncChannel) {
            try {
                taskSyncChannel.postMessage(payload);
            } catch (err) {}
        }

        try {
            localStorage.setItem('helpdesk_task_sync_storage_event', JSON.stringify(payload));
        } catch (err) {}
    }

    function triggerAutoSave() {
        $('#saveStatusDot').addClass('saving');
        $('#saveStatusText').text('Unsaved changes...');
        broadcastTaskUpdate();
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(saveNoteToDatabase, 600);
    }

    // 3. Clear note from MySQL
    function clearNoteFromDatabase() {
        if (!confirm('Are you sure you want to clear this note from Database?')) return;
        $('#workNotesEditor').val('');
        updateCharCount();
        broadcastTaskUpdate();
        saveNoteToDatabase();
        showToast('Note cleared from database');
    }

    // Two-Way Real-Time Sync: Receive Done Tasks edits made in Daily Update Generator
    function updateWorkNotesFromDoneTasks(doneTasksRaw, newProjName) {
        var currentText = $('#workNotesEditor').val() || '';
        var lines = currentText.split(/\r?\n/);
        var headerLines = [];

        if (lines.length > 0 && /^\d{2}-\d{2}-\d{4}/.test(lines[0].trim())) {
            headerLines.push(lines[0].trim());
            if (lines.length > 1 && /^[-=]{3,}/.test(lines[1].trim())) {
                headerLines.push(lines[1].trim());
            } else {
                headerLines.push('-------------------');
            }
        } else {
            var pName = newProjName || activeProjectName || 'General';
            headerLines.push(formatIsoToDisplay(currentDateIso) + '  ' + pName);
            headerLines.push('-------------------');
        }

        var result = headerLines.join('\n');
        if (doneTasksRaw && doneTasksRaw.trim() !== '') {
            result += '\n' + doneTasksRaw.trim();
        }

        if (result.trim() !== currentText.trim()) {
            $('#workNotesEditor').val(result);
            updateCharCount();
            if (typeof renderPreviewContent === 'function') renderPreviewContent();
            triggerAutoSave();
        }
    }

    if (taskSyncChannel) {
        taskSyncChannel.onmessage = function(e) {
            if (e.data && e.data.type === 'DONE_TASKS_SYNC_TO_LOG' && e.data.date === currentDateIso) {
                updateWorkNotesFromDoneTasks(e.data.done_tasks, e.data.project_name);
            }
        };
    }

    window.addEventListener('storage', function(e) {
        if (e.key === 'helpdesk_done_tasks_sync_event' && e.newValue) {
            try {
                var parsed = JSON.parse(e.newValue);
                if (parsed.date === currentDateIso) {
                    updateWorkNotesFromDoneTasks(parsed.done_tasks, parsed.project_name);
                }
            } catch (err) {}
        }
    });

    // Smart Tab Focus Sync: If user switches back to this tab, check date rollover and refresh if not typing
    $(window).on('focus', function() {
        checkAndApplyDateRollover();
        if (!$('#workNotesEditor').is(':focus')) {
            loadNoteFromDatabase(currentDateIso, activeProjectId);
        }
    });
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            checkAndApplyDateRollover();
            if (!$('#workNotesEditor').is(':focus')) {
                loadNoteFromDatabase(currentDateIso, activeProjectId);
            }
        }
    });


    // 4. Load Sidebar Journal Logs for current month from MySQL
    // 4. Load Sidebar Journal Logs for current month + all logs from MySQL
    function loadSidebarLogs(callback) {
        var reqMonth = $.ajax({
            url: window.APP_BASE + 'tasks/get-month-logs',
            type: 'GET',
            data: {
                year: currentFilterYear,
                month: currentFilterMonth
            },
            dataType: 'json'
        });

        var reqAll = $.ajax({
            url: window.APP_BASE + 'tasks/get-all-logs',
            type: 'GET',
            dataType: 'json'
        });

        $.when(reqMonth, reqAll).done(function(resMonth, resAll) {
            if (resMonth && resMonth[0] && resMonth[0].success) {
                monthLogsCache = resMonth[0].logs || [];
            }
            if (resAll && resAll[0] && resAll[0].success) {
                allLogsCache = resAll[0].logs || [];
            }
            renderSidebarLogs();
            if (typeof callback === 'function') callback();
        }).fail(function() {
            renderSidebarLogs();
            if (typeof callback === 'function') callback();
        });
    }

    function checkLogMatch(log, rawSearch) {
        if (!rawSearch) return true;

        // Normalize slashes or dots to hyphens: e.g. "2/9" -> "2-9", "02/09" -> "02-09"
        var query = rawSearch.replace(/[\/\.]/g, '-').trim();
        if (!query) return true;

        var logDateStr = log.date_formatted || ''; // e.g. "02-09-2026"
        var logParts = logDateStr.split('-'); // ["02", "09", "2026"]
        if (logParts.length !== 3) return false;

        var logDay = parseInt(logParts[0], 10);
        var logMonth = parseInt(logParts[1], 10);
        var logYearStr = logParts[2]; // "2026"

        var inputParts = query.split('-');

        if (inputParts.length === 1) {
            // User typed Day only (e.g. "2", "02", "21")
            var dayStr = inputParts[0].trim();
            if (!dayStr) return true;

            var dayNum = parseInt(dayStr, 10);
            if (isNaN(dayNum)) return false;

            // Match exact Day number (e.g. "2" or "02" -> logDay === 2)
            return logDay === dayNum;
        } else if (inputParts.length === 2) {
            // User typed Day - Month (e.g. "2-", "02-", "2-9", "02-09", "2-8")
            var dayStr = inputParts[0].trim();
            var monthStr = inputParts[1].trim();

            var dayNum = parseInt(dayStr, 10);
            if (isNaN(dayNum)) return false;
            if (logDay !== dayNum) return false;

            // If month is not typed yet (e.g. "2-" or "02-")
            if (monthStr === '') return true;

            var monthNum = parseInt(monthStr, 10);
            if (isNaN(monthNum)) return false;

            // Match exact Month number (e.g. "9" or "09" -> logMonth === 9)
            return logMonth === monthNum;
        } else if (inputParts.length >= 3) {
            // User typed Day - Month - Year (e.g. "2-9-", "02-09-2026", "2-9-26")
            var dayStr = inputParts[0].trim();
            var monthStr = inputParts[1].trim();
            var yearStr = inputParts[2].trim();

            var dayNum = parseInt(dayStr, 10);
            var monthNum = parseInt(monthStr, 10);
            if (isNaN(dayNum) || isNaN(monthNum)) return false;
            if (logDay !== dayNum || logMonth !== monthNum) return false;

            if (yearStr === '') return true;

            return logYearStr.startsWith(yearStr) || logYearStr.endsWith(yearStr);
        }

        return false;
    }

    function renderSidebarLogs() {
        var container = $('#logListContainer');
        var countBadge = $('#logCountBadge');
        var rawSearch = ($('#searchInput').val() || '').trim();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $('#currentMonthDisplay').text(monthNames[currentFilterMonth - 1] + ' ' + currentFilterYear);

        var html = '';

        if (!rawSearch) {
            // Default view: current month only
            countBadge.text(monthLogsCache.length);
            $('#mobileLogCountBadge').text(monthLogsCache.length);

            if (monthLogsCache.length === 0) {
                var currentMonthName = monthNames[currentFilterMonth - 1] + ' ' + currentFilterYear;
                var emptyHtml = '<div class="empty-state">' +
                    '<div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div>' +
                    '<div class="empty-text">No Logs Found</div>' +
                    '<div class="empty-subtext">No logs found for ' + currentMonthName + ' in database.</div>' +
                    '</div>';
                container.html(emptyHtml);
                return;
            }

            monthLogsCache.forEach(function(item) {
                var isCurrent = (item.iso_date === currentDateIso);
                html += '<div class="log-item ' + (isCurrent ? 'active' : '') + '" data-iso="' + item.iso_date + '" data-project-id="' + item.project_id + '" data-project-name="' + escapeHtml(item.project_name) + '">';
                html += '  <span class="log-date">' + item.date_formatted + '</span>';
                html += '  <span class="log-badge">' + item.task_count + (item.task_count === 1 ? ' task' : ' tasks') + '</span>';
                html += '</div>';
            });

            container.html(html);
        } else {
            // Global search with 100% PRIORITY to Current Month!
            var sourceList = (allLogsCache && allLogsCache.length > 0) ? allLogsCache : monthLogsCache;

            var currentMonthMatches = [];
            var otherMonthMatches = [];

            sourceList.forEach(function(log) {
                if (checkLogMatch(log, rawSearch)) {
                    if (log.year === currentFilterYear && log.month === currentFilterMonth) {
                        currentMonthMatches.push(log);
                    } else {
                        otherMonthMatches.push(log);
                    }
                }
            });

            // Ensure currentMonthMatches & otherMonthMatches are strictly sorted in descending date order (09, 08, 07...)
            currentMonthMatches.sort(function(a, b) {
                return (b.iso_date || '').localeCompare(a.iso_date || '');
            });

            otherMonthMatches.sort(function(a, b) {
                return (b.iso_date || '').localeCompare(a.iso_date || '');
            });

            // Combined with Priority 1 to Current Month!
            var combinedList = currentMonthMatches.concat(otherMonthMatches);

            countBadge.text(combinedList.length);
            $('#mobileLogCountBadge').text(combinedList.length);

            if (combinedList.length === 0) {
                var emptyHtml = '<div class="empty-state">' +
                    '<div class="empty-icon"><i class="fa-solid fa-calendar-xmark"></i></div>' +
                    '<div class="empty-text">No Matching Logs</div>' +
                    '<div class="empty-subtext">No entries found for date "' + escapeHtml(rawSearch) + '" across all records.</div>' +
                    '</div>';
                container.html(emptyHtml);
                return;
            }

            combinedList.forEach(function(item) {
                var isCurrent = (item.iso_date === currentDateIso);
                html += '<div class="log-item ' + (isCurrent ? 'active' : '') + '" data-iso="' + item.iso_date + '" data-project-id="' + item.project_id + '" data-project-name="' + escapeHtml(item.project_name) + '">';
                html += '  <span class="log-date">' + item.date_formatted + '</span>';
                html += '  <span class="log-badge">' + item.task_count + (item.task_count === 1 ? ' task' : ' tasks') + '</span>';
                html += '</div>';
            });

            container.html(html);
        }

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
            if ($(window).width() <= 991) {
                $('html, body').animate({ scrollTop: $('.editor-container').offset().top - 10 }, 250);
            }
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
            updateEditorProjectHeader();
            saveNoteToDatabase();
            showToast('Active project set to: ' + activeProjectName);
        });
    }

    function renderModalProjectsList() {
        var html = '';
        if (projectsCache.length === 0) {
            html = '<div style="font-size:12.5px; color:var(--text-muted); text-align:center; padding:24px; background:rgba(255,255,255,0.5); border-radius:14px; border:1px dashed rgba(203,213,225,0.8);">No projects in database. Create one above!</div>';
        } else {
            projectsCache.forEach(function(p) {
                html += '<div class="modal-project-item" id="projRow-' + p.id + '" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '">';
                
                // View Mode
                html += '  <div class="proj-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:12px;">';
                html += '    <span class="modal-project-name">';
                html += '      <div style="width:32px; height:32px; border-radius:9px; background:rgba(99,102,241,0.12); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;"><i class="fa-solid fa-folder"></i></div>';
                html += '      <span class="proj-name-text">' + escapeHtml(p.name) + '</span>';
                if (p.is_default) {
                    html += '  <span style="font-size:10.5px; color:#15803d; background:#dcfce7; padding:2px 9px; border-radius:999px; font-weight:700; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-check" style="font-size:9px;"></i> Default</span>';
                }
                html += '    </span>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn-action-icon btn-start-edit-proj" data-id="' + p.id + '" title="Rename Project"><i class="fa-solid fa-pen"></i></button>';
                if (!p.is_default) {
                    html += '    <button type="button" class="btn-action-icon delete btn-delete-project" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" title="Delete Project"><i class="fa-solid fa-trash"></i></button>';
                }
                html += '    </div>';
                html += '  </div>';

                // Inline Edit Mode
                html += '  <div class="proj-edit-mode" style="display:none; align-items:center; justify-content:space-between; width:100%; gap:8px;">';
                html += '    <div style="display:flex; align-items:center; gap:8px; flex:1;">';
                html += '      <div style="width:30px; height:30px; border-radius:8px; background:rgba(99,102,241,0.12); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0;"><i class="fa-solid fa-folder"></i></div>';
                html += '      <input type="text" class="proj-inline-input" value="' + escapeHtml(p.name) + '" placeholder="Project name" style="padding:7px 12px; font-size:13px; flex:1; border:1.5px solid #4f46e5; border-radius:10px; background:#ffffff; color:var(--text-headline); outline:none; box-shadow:0 0 0 3px rgba(79,70,229,0.15); font-family:\'Inter\', system-ui, sans-serif;">';
                html += '    </div>';
                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                html += '      <button type="button" class="btn btn-primary btn-save-inline-proj" data-id="' + p.id + '" title="Save Changes" style="padding:6px 14px; font-size:11.5px; border-radius:999px; background:linear-gradient(135deg, #4f46e5, #3b82f6); color:#fff; border:none; font-weight:700;"><i class="fa-solid fa-check"></i> Save</button>';
                html += '      <button type="button" class="btn btn-cancel-inline-proj" data-id="' + p.id + '" title="Cancel" style="padding:6px 10px; font-size:11.5px; border-radius:999px; background:rgba(255,255,255,0.85); color:var(--text-muted); border:1.5px solid rgba(203,213,225,0.8);"><i class="fa-solid fa-xmark"></i></button>';
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
                showToast('Project name cannot be empty', 'warning');
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
                        showToast('Project renamed to: ' + newName, 'success');
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
                            updateEditorProjectHeader();
                        }
                        renderProjectsDropdown();
                    } else {
                        showToast(res.message || 'Failed to rename', 'error');
                    }
                },
                error: function() {
                    showToast('Network error while saving', 'error');
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
                        showToast('Project "' + delName + '" deleted', 'success');
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
                        showToast(res.message || 'Could not delete project', 'error');
                    }
                },
                error: function() {
                    showToast('Network error while deleting', 'error');
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
            html = '<div style="font-size:12.5px; color:var(--text-muted); text-align:center; padding:24px; background:rgba(255,255,255,0.5); border-radius:14px; border:1px dashed rgba(203,213,225,0.8);">No clients in database. Add one above!</div>';
        } else {
            clientsCache.forEach(function(c) {
                html += '<div class="modal-project-item client-card-item" id="clientRow-' + c.id + '" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" data-email="' + escapeHtml(c.email || '') + '">';
                
                // View Mode
                html += '  <div class="client-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:12px;">';
                html += '    <div style="display:flex; flex-direction:column; gap:4px; flex:1; min-width:0;">';
                html += '      <div style="display:flex; align-items:center; gap:8px; font-size:13.5px; font-weight:700; color:var(--text-headline); min-width:0;">';
                html += '        <div style="width:32px; height:32px; border-radius:9px; background:rgba(2,132,199,0.12); color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;"><i class="fa-solid fa-user-tie"></i></div>';
                html += '        <span class="client-name-text" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-family:\'Inter\', system-ui, sans-serif;">' + escapeHtml(c.name) + '</span>';
                if (c.is_default) {
                    html += '    <span style="font-size:10.5px; color:#15803d; background:#dcfce7; padding:2px 9px; border-radius:999px; font-weight:700; border:1px solid #bbf7d0; flex-shrink:0;"><i class="fa-solid fa-check" style="font-size:9px;"></i> Default</span>';
                }
                html += '      </div>';
                if (c.email) {
                    html += '    <div class="client-email-badge" style="font-size:11px; color:#0369a1; background:#e0f2fe; padding:3px 10px; border-radius:999px; font-weight:600; display:inline-flex; align-items:center; gap:5px; max-width:fit-content; border:1px solid #bae6fd;" title="' + escapeHtml(c.email) + '">';
                    html += '      <i class="fa-regular fa-envelope" style="font-size:10px;"></i> <span>' + escapeHtml(c.email) + '</span>';
                    html += '    </div>';
                }
                html += '    </div>';
                html += '    <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">';
                html += '      <button type="button" class="btn-action-icon btn-start-edit-client" data-id="' + c.id + '" title="Edit Client"><i class="fa-solid fa-pen"></i></button>';
                if (!c.is_default) {
                    html += '    <button type="button" class="btn-action-icon delete btn-delete-client" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" title="Delete Client"><i class="fa-solid fa-trash"></i></button>';
                }
                html += '    </div>';
                html += '  </div>';

                // Inline Edit Mode
                html += '  <div class="client-edit-mode" style="display:none; flex-direction:column; gap:6px; width:100%; box-sizing:border-box;">';
                html += '    <div class="client-inline-inputs-row" style="display:flex; flex-direction:column; gap:6px; width:100%;">';
                html += '      <input type="text" class="client-inline-input" value="' + escapeHtml(c.name) + '" placeholder="Client Name" style="padding:6px 10px; font-size:12.5px; width:100%; border:1px solid #0284c7; border-radius:6px; background:var(--bg-card); color:var(--text-main); outline:none; box-sizing:border-box;">';
                html += '      <input type="email" class="client-inline-email" value="' + escapeHtml(c.email || '') + '" placeholder="Client Email (e.g. client@company.com)" style="padding:6px 10px; font-size:12.5px; width:100%; border:1px solid var(--border-color); border-radius:6px; background:var(--bg-card); color:var(--text-main); outline:none; box-sizing:border-box;">';
                html += '    </div>';
                html += '    <div class="inline-client-error" style="display:none; color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px; margin-left:4px;"><i class="fa-solid fa-circle-exclamation"></i> <span></span></div>';
                html += '    <div style="display:flex; justify-content:flex-end; gap:6px; margin-top:2px;">';
                html += '      <button type="button" class="btn btn-save-inline-client" data-id="' + c.id + '" title="Save Changes" style="padding:5px 12px; font-size:11px; height:28px; background:#0284c7; color:#fff; border:none; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-check"></i> Save</button>';
                html += '      <button type="button" class="btn btn-cancel-inline-client" data-id="' + c.id + '" title="Cancel" style="padding:5px 8px; font-size:11px; height:28px; background:var(--bg-card); color:var(--text-muted); border:1px solid var(--border-color);"><i class="fa-solid fa-xmark"></i></button>';
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
            var emailInput = editMode.find('.client-inline-email');
            input.css({'border-color': '#0284c7', 'background': 'var(--bg-card)'});
            emailInput.css({'border-color': 'var(--border-color)', 'background': 'var(--bg-card)'});
            editMode.find('.inline-client-error').hide().find('span').text('');
            input.focus().select();
        });

        // Click Cancel
        $('.btn-cancel-inline-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            var origName = row.attr('data-name');
            var origEmail = row.attr('data-email') || '';
            row.find('.client-inline-input').val(origName).css({'border-color': '#0284c7', 'background': 'var(--bg-card)'});
            row.find('.client-inline-email').val(origEmail).css({'border-color': 'var(--border-color)', 'background': 'var(--bg-card)'});
            row.find('.inline-client-error').hide().find('span').text('');
            row.find('.client-edit-mode').hide();
            row.find('.client-view-mode').css('display', 'flex');
        });

        function saveInlineClient(row) {
            var editId = row.attr('data-id');
            var origName = row.attr('data-name');
            var origEmail = row.attr('data-email') || '';
            var input = row.find('.client-inline-input');
            var emailInput = row.find('.client-inline-email');
            var errorBox = row.find('.inline-client-error');
            var newName = input.val().trim();
            var newEmail = emailInput.val().trim();

            // Clear previous errors
            input.css({'border-color': '#0284c7', 'background': 'var(--bg-card)'});
            emailInput.css({'border-color': 'var(--border-color)', 'background': 'var(--bg-card)'});
            errorBox.hide().find('span').text('');

            if (!newName) {
                input.css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                errorBox.show().find('span').text('Client name cannot be empty');
                showToast('Client name cannot be empty');
                return;
            }

            var emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
            if (newEmail && !emailPattern.test(newEmail)) {
                emailInput.css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                errorBox.show().find('span').text('Please enter a valid email address (e.g. client@company.com)');
                showToast('Please enter a valid email address');
                return;
            }

            if (newName === origName && newEmail === origEmail) {
                row.find('.client-edit-mode').hide();
                row.find('.client-view-mode').css('display', 'flex');
                return;
            }

            var saveBtn = row.find('.btn-save-inline-client');
            var origBtnHtml = saveBtn.html();
            saveBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            $.ajax({
                url: window.APP_BASE + 'clients/edit/' + editId,
                type: 'POST',
                data: { name: newName, email: newEmail },
                dataType: 'json',
                success: function(res) {
                    saveBtn.prop('disabled', false).html(origBtnHtml);
                    if (res.success) {
                        showToast('Client updated: ' + newName);
                        row.attr('data-name', newName);
                        row.attr('data-email', newEmail);
                        row.find('.client-name-text').text(newName);
                        if (newEmail) {
                            if (row.find('.client-email-badge').length) {
                                row.find('.client-email-badge').html('<i class="fa-regular fa-envelope"></i> ' + escapeHtml(newEmail));
                            } else {
                                row.find('.client-name-text').after(' <span class="client-email-badge" style="font-size:11px; color:#0369a1; background:#e0f2fe; padding:2px 8px; border-radius:6px; font-weight:500;"><i class="fa-regular fa-envelope"></i> ' + escapeHtml(newEmail) + '</span>');
                            }
                        } else {
                            row.find('.client-email-badge').remove();
                        }
                        row.find('.client-edit-mode').hide();
                        row.find('.client-view-mode').css('display', 'flex');

                        var cItem = clientsCache.find(function(x) { return x.id == editId; });
                        if (cItem) {
                            cItem.name = newName;
                            cItem.email = newEmail;
                        }
                        if (activeClientId == editId) {
                            activeClientName = newName;
                            $('#activeClientDisplay').text(activeClientName);
                        }
                        renderClientsDropdown();
                    } else {
                        var msg = res.message || 'Failed to update client';
                        if (msg.toLowerCase().indexOf('email') !== -1) {
                            emailInput.css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        } else {
                            input.css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        }
                        errorBox.show().find('span').text(msg);
                        showToast(msg);
                    }
                },
                error: function() {
                    saveBtn.prop('disabled', false).html(origBtnHtml);
                    showToast('Network error while saving');
                }
            });
        }

        $('.btn-save-inline-client').click(function() {
            var row = $(this).closest('.modal-project-item');
            saveInlineClient(row);
        });

        $('.client-inline-input, .client-inline-email').on('input', function() {
            var row = $(this).closest('.modal-project-item');
            $(this).css({'border-color': '', 'background': '#ffffff'});
            row.find('.inline-client-error').hide().find('span').text('');
        });

        $('.client-inline-input, .client-inline-email').on('keydown', function(e) {
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

    function getRealTodayIso() {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    var maxAllowedIso = '<?= h($todayIso) ?>' || getRealTodayIso();

    function checkAndApplyDateRollover() {
        var realToday = getRealTodayIso();
        if (maxAllowedIso !== realToday) {
            var wasViewingLatest = (currentDateIso === maxAllowedIso);
            maxAllowedIso = realToday;
            $('#datePicker').attr('max', maxAllowedIso);
            updateNextDayButtonState();
            if (wasViewingLatest && currentDateIso < realToday) {
                setDate(realToday);
                showToast('Rolled over to today: ' + isoToFormatted(realToday), 'info');
            }
        }
    }

    function setDate(isoDate) {
        clearTimeout(autoSaveTimer);

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

    // Restrict Month Filter Input to Numbers & Date Separators Only
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 0 || e.which === 8 || e.which === 13) return true;
        var char = String.fromCharCode(e.which);
        if (!/[0-9\-\/]/.test(char)) {
            e.preventDefault();
            return false;
        }
    });

    $('#searchInput').on('input', function() {
        var raw = $(this).val();
        var clean = raw.replace(/[^0-9\-\/]/g, '');
        if (raw !== clean) {
            $(this).val(clean);
        }
        renderSidebarLogs();
    });

    // Insert Template
    $('#btnInsertTemplate').click(function() {
        var header = currentDateFormatted + (activeProjectName ? '  ' + activeProjectName : '');
        var tpl = header + '\n-------------------\nBackend:\n- \n\nFrontend:\n- \n';
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
        showToast('Template inserted with ' + (activeProjectName || 'project') + '!');
    });

    // Editor Input & Auto-Save
    $('#workNotesEditor').on('input', function() {
        updateCharCount();
        triggerAutoSave();
    });

    // Clean Tab Key indentation (2 spaces) without altering user text
    $('#workNotesEditor').on('keydown', function(e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            var textarea = this;
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var val = textarea.value;

            if (e.shiftKey) {
                // Shift+Tab: Remove up to 2 leading spaces on current line
                var lastNewline = val.lastIndexOf('\n', start - 1);
                var lineStart = lastNewline === -1 ? 0 : lastNewline + 1;
                var currentLine = val.substring(lineStart, end);
                if (currentLine.startsWith('  ')) {
                    textarea.value = val.substring(0, lineStart) + currentLine.substring(2) + val.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = Math.max(lineStart, start - 2);
                } else if (currentLine.startsWith(' ') || currentLine.startsWith('\t')) {
                    textarea.value = val.substring(0, lineStart) + currentLine.substring(1) + val.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = Math.max(lineStart, start - 1);
                }
            } else {
                // Tab: Insert 2 spaces at cursor
                textarea.value = val.substring(0, start) + '  ' + val.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 2;
            }
            updateCharCount();
            triggerAutoSave();
        }
    });

    // Helper to format raw tasks into clean plain text for Queueloop / Bubbles timesheets (normal font weight, clean sub-bullets)
    function formatCleanTaskPoints(rawText) {
        if (!rawText || !rawText.trim()) return '';

        var lines = rawText.split(/\r?\n/);
        var resultLines = [];

        for (var i = 0; i < lines.length; i++) {
            var rawLine = lines[i];
            var trimmed = rawLine.trim();
            if (!trimmed) {
                if (resultLines.length > 0 && resultLines[resultLines.length - 1] !== '') {
                    resultLines.push('');
                }
                continue;
            }

            // 1. Skip date header (e.g. "09-09-2026 8.bloqs" or "09-09-2026")
            if (/^\d{2}-\d{2}-\d{4}/.test(trimmed)) continue;

            // 2. Skip horizontal rule / separator line (e.g. "-------------------", "====")
            if (/^[-=]{3,}$/.test(trimmed)) continue;

            // 3. Category headers (e.g. "Backend:", "**Backend:**", "Frontend & UI:")
            var cleanHeaderTest = trimmed.replace(/^\*\*|\*\*$/g, '').trim();
            if (/^[A-Za-z0-9\s_\-\/&]{2,60}:$/.test(cleanHeaderTest) && !/^\d+[\.\)]/.test(trimmed) && !/^[•▪▫◦*\-–—]/.test(trimmed)) {
                if (resultLines.length > 0 && resultLines[resultLines.length - 1] !== '') {
                    resultLines.push('');
                }
                resultLines.push(cleanHeaderTest);
                continue;
            }

            // 4. Check indentation & bullet
            var isIndented = /^(\s{2,}|\t)/.test(rawLine);
            var isBullet = /^[-*•▪▫◦–—]/.test(trimmed);

            // Sub-bullet (indented under parent task) - uses unicode bullet '   • ' without leading dash so it remains normal weight and doesn't create '--'
            if (isIndented && isBullet) {
                var cleanSub = trimmed.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[-*•▪▫◦–—]+)\s*/, '').trim();
                cleanSub = cleanSub.replace(/\s*\[Done\]\s*$/i, '').trim();
                cleanSub = cleanSub.replace(/\*\*([^*]+)\*\*/g, '$1');
                cleanSub = cleanSub.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/g, '$1 ($2)');
                if (cleanSub) {
                    resultLines.push('   • ' + cleanSub);
                }
                continue;
            }

            // Sub-note / continuation (indented without bullet)
            if (isIndented) {
                var cleanNote = trimmed.replace(/\s*\[Done\]\s*$/i, '').trim();
                cleanNote = cleanNote.replace(/\*\*([^*]+)\*\*/g, '$1');
                cleanNote = cleanNote.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/g, '$1 ($2)');
                if (cleanNote) {
                    resultLines.push('     ' + cleanNote);
                }
                continue;
            }

            // 5. Main task item: Clean unformatted line (so external portals like Queueloop Bubbles render it in normal regular font weight)
            var cleanTask = trimmed.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[-*•▪▫◦–—]+)\s*/, '').trim();
            cleanTask = cleanTask.replace(/\s*\[Done\]\s*$/i, '').trim();
            cleanTask = cleanTask.replace(/\*\*([^*]+)\*\*/g, '$1');
            cleanTask = cleanTask.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)]+)\)/g, '$1 ($2)');

            if (cleanTask) {
                resultLines.push(cleanTask);
            }
        }

        return resultLines.join('\n').trim();
    }

    // Copy Content (Directly copies clean task points & indented sub-points)
    $('#btnCopyOnlyTasks').click(function() {
        var raw = $('#workNotesEditor').val() || '';
        var copyText = formatCleanTaskPoints(raw);
        if (!copyText) {
            showToast('No task points found to copy!', 'warning');
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(copyText).then(function() {
                showToast('Task points copied to clipboard!', 'success');
            }).catch(function() {
                fallbackCopy(copyText, 'Task points copied to clipboard!');
            });
        } else {
            fallbackCopy(copyText, 'Task points copied to clipboard!');
        }
    });

    // Clear Note
    $('#btnClearCurrent').click(clearNoteFromDatabase);

    // Continue to Email Button
    $('#btnContinueToEmail').click(function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
        
        saveNoteToDatabase(function() {
            var url = window.APP_BASE + 'daily-updates?project_id=' + activeProjectId + '&date=' + currentDateIso;
            if (activeProjectName) {
                url += '&project_name=' + encodeURIComponent(activeProjectName);
            }
            if (activeClientName) {
                url += '&client_name=' + encodeURIComponent(activeClientName);
            }
            window.location.href = url;
        });
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

    $('#newProjectInput').on('input', function() {
        $(this).css({'border-color': '', 'background': ''});
        $('#newProjectNameError').hide().find('span').text('');
    });

    $('#btnSaveNewProject').click(function() {
        var $input = $('#newProjectInput');
        var val = $input.val().trim();
        var $err = $('#newProjectNameError');

        if (!val) {
            $input.css({'border-color': '#ef4444', 'background': '#fef2f2'});
            $err.show().find('span').text('Please enter a project name.');
            $input.focus();
            showToast('Project name is required', true);
            return;
        }

        if (val.length > 100) {
            $input.css({'border-color': '#ef4444', 'background': '#fef2f2'});
            $err.show().find('span').text('Project name cannot exceed 100 characters.');
            $input.focus();
            showToast('Project name is too long', true);
            return;
        }

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
                    $('#newProjectInput').val('').css({'border-color': '', 'background': ''});
                    $err.hide().find('span').text('');
                    $('#manageProjectModal').removeClass('active');
                    updateEditorProjectHeader();
                    saveNoteToDatabase();
                } else {
                    $input.css({'border-color': '#ef4444', 'background': '#fef2f2'});
                    $err.show().find('span').text(res.message || 'Failed to add project');
                    showToast(res.message || 'Failed to add project', true);
                }
            },
            error: function() {
                showToast('Network error while saving project', true);
            }
        });
    });

    function clearNewClientErrors() {
        $('#newClientInput').css({'border-color': '', 'background': ''});
        $('#newClientNameError').hide().find('span').text('');
        $('#newClientEmailInput').css({'border-color': '', 'background': ''});
        $('#newClientEmailError').hide().find('span').text('');
    }

    // Add Client Modal
    $('#btnAddClient').click(function() {
        $('#manageClientModal').addClass('active');
        $('#newClientInput').val('').focus();
        $('#newClientEmailInput').val('');
        clearNewClientErrors();
    });

    $('#newClientInput').on('input', function() {
        $(this).css({'border-color': '', 'background': ''});
        $('#newClientNameError').hide().find('span').text('');
    });

    $('#newClientEmailInput').on('input', function() {
        $(this).css({'border-color': '', 'background': ''});
        $('#newClientEmailError').hide().find('span').text('');
    });

    $('#newClientEmailInput').on('blur', function() {
        var emailVal = $(this).val().trim();
        var emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        if (emailVal && !emailPattern.test(emailVal)) {
            $(this).css({'border-color': '#ef4444', 'background': '#fef2f2'});
            $('#newClientEmailError').show().find('span').text('Please enter a valid email address (e.g. client@company.com)');
        }
    });

    $('#newClientInput, #newClientEmailInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#btnSaveNewClient').click();
        }
    });

    $('#btnCloseClientModal, #manageClientModal').click(function(e) {
        if (e.target === this || $(this).hasClass('btn-close-modal')) {
            clearNewClientErrors();
            $('#manageClientModal').removeClass('active');
        }
    });

    $('#btnSaveNewClient').click(function() {
        clearNewClientErrors();
        var val = $('#newClientInput').val().trim();
        var emailVal = $('#newClientEmailInput').val().trim();
        var hasError = false;

        if (!val) {
            $('#newClientInput').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
            $('#newClientNameError').show().find('span').text('Client name cannot be empty');
            hasError = true;
        }

        var emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        if (emailVal && !emailPattern.test(emailVal)) {
            $('#newClientEmailInput').css({'border-color': '#ef4444', 'background': '#fef2f2'});
            $('#newClientEmailError').show().find('span').text('Please enter a valid email address (e.g. client@company.com)');
            if (!hasError) {
                $('#newClientEmailInput').focus();
            }
            hasError = true;
        }

        if (hasError) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Adding...');

        $.ajax({
            url: window.APP_BASE + 'clients/add',
            type: 'POST',
            data: { name: val, email: emailVal },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    showToast(res.message);
                    loadClients();
                    activeClientId = res.client.id;
                    activeClientName = res.client.name;
                    $('#activeClientDisplay').text(activeClientName);
                    $('#newClientInput').val('');
                    $('#newClientEmailInput').val('');
                    clearNewClientErrors();
                    $('#manageClientModal').removeClass('active');
                    saveNoteToDatabase();
                } else {
                    var msg = res.message || 'Failed to add client';
                    if (msg.toLowerCase().indexOf('email') !== -1) {
                        $('#newClientEmailInput').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#newClientEmailError').show().find('span').text(msg);
                    } else {
                        $('#newClientInput').css({'border-color': '#ef4444', 'background': '#fef2f2'}).focus();
                        $('#newClientNameError').show().find('span').text(msg);
                    }
                    showToast(msg);
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(origHtml);
                showToast('Network error while adding client');
            }
        });
    });

    // Global Search Modal
    $('#btnOpenGlobalSearch').click(function() {
        $('#globalSearchModal').addClass('active');
        $('#globalSearchInput').val('').focus();
        $('#globalSearchResults').html('<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Enter a keyword above to search your saved tasks and updates.</div>');
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
            $('#globalSearchResults').html('<div style="font-size:12px; color:var(--text-muted); text-align:center; padding:20px;">Enter a keyword above to search your saved tasks and updates.</div>');
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
                        // Open directly in Preview Mode
                        setPreviewMode(true);
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
            error: function(xhr) {
                btn.prop('disabled', false).html(origHtml);
                var errText = 'Server error while validating API Key.';
                if (xhr.status === 401) {
                    errText = 'Session expired. Redirecting to login...';
                    setTimeout(function() {
                        window.location.href = window.APP_BASE + 'login';
                    }, 1200);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errText = xhr.responseJSON.message;
                }
                $('#geminiKeyErrorMsg').text(errText).show();
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
        }).catch(function() {
            showToast('Could not copy text.');
        });
    });

    // Apply Polished Text to Editor
    $('#btnApplyAiPolish').click(function() {
        var text = $('#aiPolishedTextarea').val();
        if (text && text.trim().length > 0) {
            $('#workNotesEditor').val(text);
            updateCharCount();
            saveNoteToDatabase();
            $('#aiPolishModal').removeClass('active');
            showToast('Polished tasks applied & saved!');
        }
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
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Polishing...');

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

    // ==========================================
    // User Menu Popover & Profile Modal Logic
    // ==========================================
    function openProfileModal() {
        $('#userPopoverMenu').removeClass('show');
        $('#btnUserMenuToggle').removeClass('active');
        $('#profileAlert').hide().text('').css({'background': '', 'color': '', 'border': ''});
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

    // Toggle password view/hide for all eye buttons
    $(document).on('click', '.btn-toggle-password', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var targetSelector = $(this).attr('data-target') || $(this).data('target');
        var target = $(targetSelector);
        if (target.length) {
            var icon = $(this).find('i');
            if (target.attr('type') === 'password') {
                target.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                target.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }
    });

    // Clear profile errors on input
    $('#profInputName, #profInputEmail, #profCurrentPass, #profNewPass, #profConfirmPass').on('input', function() {
        $(this).css({'border-color': '', 'background': ''});
        $(this).closest('div').find('div[id$="Error"]').hide().find('span').text('');
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
        alertBox.hide().text('').css({'background': '', 'color': '', 'border': ''});
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
                    alertBox.css({'background': '#f0fdf4', 'color': '#15803d', 'border': '1px solid #bbf7d0'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message).show();
                    showToast(res.message, 'success');

                    // Update UI across page
                    var initial = (res.user.name || 'U').charAt(0).toUpperCase();
                    $('.user-pill-name').text(res.user.name);
                    $('.user-menu-wrapper span').first().text(initial);
                    $('.user-popover-avatar').text(initial);
                    $('.user-popover-name').text(res.user.name);
                    $('.user-popover-email').text(res.user.email);
                    $('#profileBannerName').text(res.user.name);
                    $('#profileBannerEmail').text(res.user.email);
                    $('#profileAvatarBig').text(initial);

                    if (res.user.api_key) {
                        userDatabaseApiKey = res.user.api_key;
                    }

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
                        alertBox.css({'background': '#fef2f2', 'color': '#b91c1c', 'border': '1px solid #fecaca'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + msg).show();
                    }
                    showToast(msg);
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Network error while updating profile.';
                alertBox.css({'background': '#fef2f2', 'color': '#b91c1c', 'border': '1px solid #fecaca'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + errMsg).show();
            }
        });
    });

    // Mobile Sidebar Toggle
    $('#btnToggleSidebarMobile').click(function(e) {
        e.preventDefault();
        var $sidebar = $('.sidebar');
        $sidebar.toggleClass('open-mobile');
        if ($sidebar.hasClass('open-mobile')) {
            $('html, body').animate({
                scrollTop: $sidebar.offset().top - 15
            }, 250);
        }
    });

    // Boot App
    loadProjects();
    loadClients();
    loadSidebarLogs();
    updateCharCount();
    updateNextDayButtonState();
});
</script>
