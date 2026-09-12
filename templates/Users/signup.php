<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Create Account - Helpdesk');
$this->assign('meta_description', 'Join Helpdesk to streamline daily task management, work journal logging, and automated client email updates.');
$this->assign('meta_keywords', 'helpdesk register, create account, daily task management, sign up');
?>

<style>
    /* =========================================================
       Glassmorphism & Sunrise Theme - Inspired by Rulse Design
       ========================================================= */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.76);
        --glass-border: rgba(255, 255, 255, 0.88);
        --glass-border-subtle: rgba(255, 255, 255, 0.6);
        --glass-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.14), 0 8px 24px -4px rgba(0, 0, 0, 0.04);
        --glass-inset: 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
        --input-bg: rgba(255, 255, 255, 0.58);
        --input-border: rgba(203, 213, 225, 0.82);
        --input-text: #0f172a;
        --text-headline: #0f172a;
        --text-sub: #64748b;
        --primary-gradient: linear-gradient(135deg, #5b52e8 0%, #4338ca 100%);
        --primary-hover: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --sun-accent: #fde047;
    }

    [data-theme="dark"] {
        --glass-bg: rgba(22, 28, 45, 0.78);
        --glass-border: rgba(255, 255, 255, 0.12);
        --glass-border-subtle: rgba(255, 255, 255, 0.06);
        --glass-shadow: 0 24px 50px -12px rgba(0, 0, 0, 0.6);
        --glass-inset: 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        --input-bg: rgba(15, 23, 42, 0.6);
        --input-border: rgba(255, 255, 255, 0.12);
        --input-text: #f8fafc;
        --text-headline: #f8fafc;
        --text-sub: #94a3b8;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        position: relative;
        overflow-x: hidden;
        background: #80bdfc;
        background: radial-gradient(circle at 15% 15%, #93c5fd 0%, transparent 40%),
                    radial-gradient(circle at 85% 20%, #c4b5fd 0%, transparent 45%),
                    radial-gradient(circle at 80% 85%, #fef08a 0%, #fed7aa 25%, transparent 55%),
                    radial-gradient(circle at 10% 85%, #a7f3d0 0%, transparent 40%),
                    linear-gradient(135deg, #60a5fa 0%, #93c5fd 35%, #e0e7ff 70%, #fef3c7 100%);
        background-attachment: fixed;
    }

    [data-theme="dark"] body {
        background: #090d16;
        background: radial-gradient(circle at 15% 15%, rgba(59, 130, 246, 0.25) 0%, transparent 45%),
                    radial-gradient(circle at 85% 85%, rgba(245, 158, 11, 0.18) 0%, transparent 45%),
                    radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 55%),
                    linear-gradient(135deg, #090d16 0%, #111827 50%, #0f172a 100%);
        background-attachment: fixed;
    }

    /* Ambient Glowing Mesh Blobs */
    .ambient-glow-orb {
        position: fixed;
        border-radius: 50%;
        filter: blur(75px);
        pointer-events: none;
        z-index: 0;
        opacity: 0.65;
        animation: floatOrb 18s ease-in-out infinite alternate;
    }

    .orb-1 {
        width: 440px;
        height: 440px;
        top: -80px;
        left: -80px;
        background: radial-gradient(circle, #60a5fa, #818cf8);
    }

    .orb-2 {
        width: 480px;
        height: 480px;
        bottom: -100px;
        right: -80px;
        background: radial-gradient(circle, #fde047, #fb923c);
        animation-delay: -6s;
    }

    .orb-3 {
        width: 360px;
        height: 360px;
        top: 35%;
        right: 12%;
        background: radial-gradient(circle, #c084fc, #e879f9);
        animation-delay: -12s;
    }

    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -25px) scale(1.06); }
        100% { transform: translate(-25px, 20px) scale(0.95); }
    }

    /* Glass Card Container */
    .auth-card {
        position: relative;
        z-index: 1;
        background: var(--glass-bg);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border: 1.5px solid var(--glass-border);
        border-radius: 28px;
        box-shadow: var(--glass-shadow), var(--glass-inset);
        width: 100%;
        max-width: 450px;
        padding: 38px 32px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Top Pill Header (Sunrise Mode Badge) */
    .top-pill-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 2px;
    }

    .pill-sunrise {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: 0.2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
    }

    [data-theme="dark"] .pill-sunrise {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    .pill-sunrise i {
        color: #f59e0b;
    }

    .pill-sparkle {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: linear-gradient(135deg, rgba(235, 248, 74, 0.3) 0%, rgba(254, 240, 138, 0.4) 100%);
        border: 1px solid rgba(235, 248, 74, 0.6);
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        color: #854d0e;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    [data-theme="dark"] .pill-sparkle {
        background: rgba(235, 248, 74, 0.15);
        border-color: rgba(235, 248, 74, 0.3);
        color: #fef08a;
    }

    .auth-header {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .auth-logo {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(238, 242, 255, 0.8) 100%);
        color: #4f46e5;
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 10px 22px -4px rgba(79, 70, 229, 0.24), 0 0 0 1px rgba(255, 255, 255, 0.9) inset;
        margin-bottom: 2px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    [data-theme="dark"] .auth-logo {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%);
        color: #818cf8;
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 22px -4px rgba(0, 0, 0, 0.4);
    }

    .auth-logo:hover {
        transform: translateY(-2px) scale(1.05);
    }

    .auth-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 800;
        color: var(--text-headline);
        letter-spacing: -0.6px;
        margin: 0;
    }

    .auth-subtitle {
        font-size: 13.5px;
        color: var(--text-sub);
        font-weight: 500;
        margin: 0;
    }

    /* Google Button Glassmorphism */
    .google-auth-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 44px;
    }

    .btn-google-action {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.68);
        color: var(--text-headline);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 11px 18px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-sizing: border-box;
        cursor: pointer;
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.05);
        backdrop-filter: blur(12px);
    }

    [data-theme="dark"] .btn-google-action {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    .btn-google-action:hover {
        background: #ffffff;
        border-color: rgba(99, 102, 241, 0.4);
        color: var(--text-headline);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px -4px rgba(79, 70, 229, 0.16);
    }

    [data-theme="dark"] .btn-google-action:hover {
        background: rgba(30, 41, 59, 0.95);
        border-color: rgba(99, 102, 241, 0.5);
    }

    /* Divider */
    .oauth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 2px 0;
    }

    .oauth-divider::before,
    .oauth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid rgba(203, 213, 225, 0.7);
    }

    [data-theme="dark"] .oauth-divider::before,
    [data-theme="dark"] .oauth-divider::after {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .oauth-divider span {
        padding: 4px 14px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-sub);
        letter-spacing: 0.8px;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 999px;
        margin: 0 8px;
        backdrop-filter: blur(8px);
    }

    [data-theme="dark"] .oauth-divider span {
        background: rgba(30, 41, 59, 0.65);
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* Form Fields */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #475569;
    }

    [data-theme="dark"] .form-label {
        color: #94a3b8;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--input-bg);
        border: 1.5px solid var(--input-border);
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02) inset;
    }

    .input-wrapper:focus-within {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.16), 0 4px 14px rgba(0, 0, 0, 0.03);
    }

    [data-theme="dark"] .input-wrapper:focus-within {
        background: rgba(15, 23, 42, 0.95);
        border-color: #818cf8;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
    }

    .input-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 13.5px;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-wrapper:focus-within .input-icon {
        color: #4f46e5;
    }

    [data-theme="dark"] .input-wrapper:focus-within .input-icon {
        color: #818cf8;
    }

    .form-input {
        width: 100%;
        background: transparent;
        border: none;
        padding: 11px 14px 11px 42px;
        font-size: 13.5px;
        color: var(--input-text);
        font-family: inherit;
        font-weight: 500;
        outline: none;
    }

    .form-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .form-input:-webkit-autofill,
    .form-input:-webkit-autofill:hover, 
    .form-input:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--input-text) !important;
        -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.8) inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    [data-theme="dark"] .form-input:-webkit-autofill,
    [data-theme="dark"] .form-input:-webkit-autofill:hover, 
    [data-theme="dark"] .form-input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px rgba(15, 23, 42, 0.9) inset !important;
    }

    .form-input.is-invalid {
        color: #ef4444;
    }

    .field-error-msg {
        display: none;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        color: #ef4444;
        font-weight: 600;
        margin-top: 1px;
    }

    .field-error-msg.show {
        display: flex;
    }

    /* Submit Button */
    .btn-submit {
        background: var(--primary-gradient);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        padding: 13px 20px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        margin-top: 6px;
        box-shadow: 0 8px 24px -4px rgba(79, 70, 229, 0.42), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
        letter-spacing: 0.2px;
    }

    .btn-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -4px rgba(79, 70, 229, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.3) inset;
    }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    }

    .auth-footer {
        text-align: center;
        font-size: 13px;
        color: var(--text-sub);
        border-top: 1px solid rgba(203, 213, 225, 0.7);
        padding-top: 16px;
    }

    [data-theme="dark"] .auth-footer {
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    .auth-link {
        color: #4f46e5;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.15s;
    }

    [data-theme="dark"] .auth-link {
        color: #818cf8;
    }

    .auth-link:hover {
        text-decoration: underline;
        color: #3730a3;
    }

    /* Toast Notification Floating Pill */
    .toast {
        display: none;
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 10000;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        color: #0f172a;
        padding: 12px 20px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.2);
        align-items: center;
        gap: 10px;
        animation: toastIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [data-theme="dark"] .toast {
        background: rgba(22, 28, 45, 0.92);
        border-color: rgba(255, 255, 255, 0.12);
        color: #f8fafc;
    }

    .toast.toast-error,
    .toast.error {
        border-color: rgba(239, 68, 68, 0.4);
    }

    .toast.toast-error i,
    .toast.error i {
        color: #ef4444;
    }

    .toast.toast-warning,
    .toast.warning {
        border-color: rgba(245, 158, 11, 0.4);
    }

    .toast.toast-warning i,
    .toast.warning i {
        color: #f59e0b;
    }

    .toast.toast-success,
    .toast.success {
        border-color: rgba(16, 185, 129, 0.4);
    }

    .toast:not(.error) i,
    .toast.toast-success i,
    .toast.success i {
        color: #10b981;
    }

    .toast.toast-info,
    .toast.info {
        border-color: rgba(59, 130, 246, 0.4);
    }

    .toast.toast-info i,
    .toast.info i {
        color: #3b82f6;
    }

    .toast.show {
        display: inline-flex !important;
    }

    @keyframes toastIn {
        from { opacity: 0; transform: translateY(12px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @media (max-width: 480px) {
        body {
            padding: 14px;
        }
        .auth-card {
            padding: 28px 20px;
            border-radius: 24px;
            gap: 16px;
        }
        .auth-title {
            font-size: 22px;
        }
    }
</style>

<!-- Google Identity Services SDK -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<!-- Ambient Glowing Orbs -->
<div class="ambient-glow-orb orb-1"></div>
<div class="ambient-glow-orb orb-2"></div>
<div class="ambient-glow-orb orb-3"></div>

<div class="auth-card">
    <div class="top-pill-row">
        <span class="pill-sunrise">
            <i class="fa-solid fa-cloud-sun"></i> Mode: Sunrise
        </span>
        <span class="pill-sparkle">
            <i class="fa-solid fa-user-plus"></i> Join System
        </span>
    </div>

    <div class="auth-header">
        <div class="auth-logo">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join Daily Work Journal & Tasks System</p>
    </div>

    <!-- 100% Theme-Aware Dark Mode Google Sign-Up Button -->
    <div class="google-auth-container">
        <div id="g_id_onload"
             data-client_id="<?= h(\Cake\Core\Configure::read('Google.clientId') ?: '') ?>"
             data-context="signup"
             data-callback="onGoogleAuthCallback"
             data-auto_prompt="false">
        </div>

        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'googleAuth']) ?>" class="btn-google-action" id="btnGoogleSignupAction">
            <svg viewBox="0 0 24 24" width="18" height="18" style="flex-shrink:0;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Sign up with Google</span>
        </a>
    </div>

    <div class="oauth-divider">
        <span>OR CONTINUE WITH EMAIL</span>
    </div>

    <?= $this->Form->create($user, ['url' => ['controller' => 'Users', 'action' => 'signup'], 'id' => 'signupForm', 'novalidate' => true]) ?>
        <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Your Full Name" required autofocus value="<?= h($this->request->getData('name')) ?>">
                </div>
                <div class="field-error-msg" id="nameError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-input" placeholder="example@gmail.com" required value="<?= h($this->request->getData('email')) ?>">
                </div>
                <div class="field-error-msg" id="emailError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password (min. 6 characters)</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-key input-icon"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required minlength="6">
                </div>
                <div class="field-error-msg" id="passwordError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-shield-halved input-icon"></i>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="field-error-msg" id="confirmPasswordError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btnSignupSubmit">
                <i class="fa-solid fa-user-check"></i> Register & Create Account
            </button>
        </div>
    <?= $this->Form->end() ?>

    <div class="auth-footer">
        Already have an account? <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'login']) ?>" class="auth-link">Sign In</a>
    </div>
</div>

<!-- Floating Toast Element -->
<div id="toastNotification" class="toast">
    <i id="toastIcon" class="fa-solid fa-circle-check"></i>
    <span id="toastMessage">Message</span>
</div>

<?php
$renderedFlash = $this->Flash->render();
?>
<?php if (!empty($renderedFlash)): ?>
    <div style="display:none;" id="initialFlashHolder"><?= $renderedFlash ?></div>
<?php endif; ?>

<script>
    // Handle Google GIS Authentication Callback
    function onGoogleAuthCallback(response) {
        if (response && response.credential) {
            showToast('Creating account with Google...', false);
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= $this->Url->build(['controller' => 'Users', 'action' => 'googleLogin']) ?>';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'credential';
            input.value = response.credential;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Toast Notification Utility - Delegates to global window.showToast
    function showToast(msg, typeOrIsError) {
        if (typeof window.showToast === 'function') {
            window.showToast(msg, typeOrIsError);
            return;
        }
        var $toast = $('#toastNotification');
        var $icon = $('#toastIcon');
        var $msg = $('#toastMessage');

        var cleanMsg = (msg || '').replace(/^[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE00}-\u{FE0F}\u{1F900}-\u{1F9FF}\u{1F600}-\u{1F64F}\u{1F680}-\u{1F6FF}✅✨🎉⚠️❌ℹ️💡🗑️📌🧹\s]+/u, '').trim();
        $toast.removeClass('success error warning info toast-success toast-error toast-warning toast-info');

        var isErr = (typeOrIsError === 'error' || typeOrIsError === true || /^(error|failed|invalid|could not|network error|cannot)/i.test(cleanMsg) || /^(❌)/.test(msg));
        var isWarn = (typeOrIsError === 'warning' || /^(warning|caution|please enter|required|too long|empty)/i.test(cleanMsg) || /^(⚠️)/.test(msg));
        var isSuccess = (typeOrIsError === 'success' || typeOrIsError === false || /^(saved|success|synced|copied|renamed|deleted|cleared|applied|auto-structured|validated|complete|created|signed up)/i.test(cleanMsg) || /^(✅|🎉|✨)/.test(msg));

        if (isErr) {
            $toast.addClass('toast-error error');
            $icon.attr('class', 'fa-solid fa-circle-xmark');
        } else if (isWarn) {
            $toast.addClass('toast-warning warning');
            $icon.attr('class', 'fa-solid fa-triangle-exclamation');
        } else if (isSuccess) {
            $toast.addClass('toast-success success');
            $icon.attr('class', 'fa-solid fa-circle-check');
        } else {
            $toast.addClass('toast-info info');
            $icon.attr('class', 'fa-solid fa-circle-info');
        }

        $msg.text(cleanMsg);
        $toast.addClass('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(function() {
            $toast.removeClass('show');
        }, 3200);
    }

    $(document).ready(function() {
        var flashHolder = $('#initialFlashHolder');
        if (flashHolder.length) {
            var msgText = flashHolder.text().trim();
            if (msgText) {
                var isErr = flashHolder.find('.error').length > 0 || !flashHolder.find('.success').length;
                showToast(msgText, isErr);
            }
        }

        // Clear error on input
        $('#name, #email, #password, #confirm_password').on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.field-error-msg').removeClass('show').find('span').text('');
        });

        // Client-side form validation before submission
        $('#signupForm').on('submit', function(e) {
            var hasError = false;
            var firstInvalid = null;

            var nameVal = $('#name').val().trim();
            var emailVal = $('#email').val().trim();
            var passVal = $('#password').val();
            var confVal = $('#confirm_password').val();

            var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;

            if (nameVal === '') {
                $('#name').addClass('is-invalid');
                $('#nameError').addClass('show').find('span').text('Please enter your full name.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#name');
            } else if (nameVal.length < 2) {
                $('#name').addClass('is-invalid');
                $('#nameError').addClass('show').find('span').text('Full name must be at least 2 characters.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#name');
            }

            if (emailVal === '') {
                $('#email').addClass('is-invalid');
                $('#emailError').addClass('show').find('span').text('Please enter your email address.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#email');
            } else if (!emailRegex.test(emailVal)) {
                $('#email').addClass('is-invalid');
                $('#emailError').addClass('show').find('span').text('Please enter a valid email address.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#email');
            }

            if (passVal === '') {
                $('#password').addClass('is-invalid');
                $('#passwordError').addClass('show').find('span').text('Please enter a password.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#password');
            } else if (passVal.length < 6) {
                $('#password').addClass('is-invalid');
                $('#passwordError').addClass('show').find('span').text('Password must be at least 6 characters.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#password');
            }

            if (confVal === '') {
                $('#confirm_password').addClass('is-invalid');
                $('#confirmPasswordError').addClass('show').find('span').text('Please confirm your password.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#confirm_password');
            } else if (passVal !== confVal) {
                $('#confirm_password').addClass('is-invalid');
                $('#confirmPasswordError').addClass('show').find('span').text('Passwords do not match. Please try again.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#confirm_password');
            }

            if (hasError) {
                e.preventDefault();
                firstInvalid.focus();
                showToast('Please correct the errors in the form.', true);
                return false;
            }
        });
    });
</script>
