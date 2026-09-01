<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Sign Up - Daily Work Journal');
?>

<style>
    :root {
        --bg-main: #f7f6f0;
        --bg-card: #ffffff;
        --bg-secondary: #f0eee6;
        --border-color: #e5e3d7;
        --text-main: #1c201e;
        --text-muted: #6e7570;
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--bg-main);
        color: var(--text-main);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .auth-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: 0 10px 30px rgba(90, 85, 60, 0.08);
        width: 100%;
        max-width: 440px;
        padding: 36px 30px;
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .auth-header {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .auth-logo {
        width: 48px;
        height: 48px;
        background: #1c201e;
        color: #f7f6f0;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 4px;
    }

    .auth-title {
        font-family: 'Outfit', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: var(--text-main);
    }

    .auth-subtitle {
        font-size: 13px;
        color: var(--text-muted);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 13px;
    }

    .form-input {
        width: 100%;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 10px 12px 10px 36px;
        font-size: 13px;
        color: var(--text-main);
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .btn-submit {
        background: #1c201e;
        color: #ffffff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 6px;
    }

    .btn-submit:hover {
        background: #343a37;
        transform: translateY(-1px);
    }

    .auth-footer {
        text-align: center;
        font-size: 13px;
        color: var(--text-muted);
        border-top: 1px solid var(--border-color);
        padding-top: 18px;
    }

    .auth-link {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
    }

    .auth-link:hover {
        text-decoration: underline;
    }

    /* Floating Toast Notification */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #1c201e;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.25s ease;
        z-index: 9999;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.success {
        background: #15803d;
        color: #ffffff;
    }

    .toast.error {
        background: #b91c1c;
        color: #ffffff;
    }
</style>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join Daily Work Journal & Tasks System</p>
    </div>

    <?= $this->Form->create($user, ['url' => ['controller' => 'Users', 'action' => 'signup']]) ?>
        <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Your Full Name" required autofocus value="<?= h($this->request->getData('name')) ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-input" placeholder="example@gmail.com" required value="<?= h($this->request->getData('email')) ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password (min. 6 characters)</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-key input-icon"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-shield-halved input-icon"></i>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">
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
    function showToast(msg, isError) {
        var $toast = $('#toastNotification');
        var $icon = $('#toastIcon');
        var $msg = $('#toastMessage');

        $msg.text(msg);
        $toast.removeClass('success error');

        if (isError) {
            $toast.addClass('error');
            $icon.attr('class', 'fa-solid fa-circle-exclamation');
        } else {
            $toast.addClass('success');
            $icon.attr('class', 'fa-solid fa-circle-check');
        }

        $toast.addClass('show');
        setTimeout(function() {
            $toast.removeClass('show');
        }, 3200);
    }

    $(document).ready(function() {
        var flashHolder = $('#initialFlashHolder');
        if (flashHolder.length) {
            var msgText = flashHolder.text().trim();
            if (msgText) {
                var isErr = flashHolder.find('.error').length > 0 || msgText.toLowerCase().indexOf('error') !== -1 || msgText.toLowerCase().indexOf('invalid') !== -1 || msgText.toLowerCase().indexOf('failed') !== -1 || msgText.toLowerCase().indexOf('not match') !== -1;
                showToast(msgText, isErr);
            }
        }
    });
</script>
