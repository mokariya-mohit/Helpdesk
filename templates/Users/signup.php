<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Create Helpdesk Account - Register Today');
$this->assign('meta_description', 'Join Helpdesk to streamline daily task management, work journal logging, and automated client email updates.');
$this->assign('meta_keywords', 'helpdesk register, create account, daily task management, sign up');
?>

<style>
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
        box-shadow: var(--shadow-md);
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
        background: var(--bg-secondary);
        color: var(--primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
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
        pointer-events: none;
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
        background: var(--bg-card);
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        color: var(--text-main);
    }

    .form-input:-webkit-autofill,
    .form-input:-webkit-autofill:hover, 
    .form-input:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--text-main) !important;
        -webkit-box-shadow: 0 0 0px 1000px var(--bg-secondary) inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    .btn-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff;
        border: 1px solid #4f46e5;
        border-radius: var(--radius-sm);
        padding: 11px 16px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 6px;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
    }

    .auth-footer {
        text-align: center;
        font-size: 13px;
        color: var(--text-muted);
        border-top: 1px solid var(--border-color);
        padding-top: 18px;
    }

    .oauth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 4px 0;
    }

    .oauth-divider::before,
    .oauth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid var(--border-color);
    }

    .oauth-divider span {
        padding: 0 12px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

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
        gap: 10px;
        background: var(--bg-secondary);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 10px 16px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-google-action:hover {
        background: var(--bg-card);
        border-color: var(--primary);
        color: var(--text-main);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
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
    @media (max-width: 480px) {
        body {
            padding: 12px;
        }
        .auth-card {
            padding: 24px 18px;
            gap: 16px;
            border-radius: var(--radius-md);
        }
        .auth-title {
            font-size: 20px;
        }
        .btn-submit {
            padding: 12px 16px;
            font-size: 13.5px;
        }
    }
</style>

<!-- Google Identity Services SDK -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<div class="auth-card">
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

    var toastTimeout = null;
    function showToast(msg, typeOrIsError) {
        var $toast = $('#toastNotification');
        var $icon = $('#toastIcon');
        var $msg = $('#toastMessage');

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        // 1. Strip any emoji characters from the beginning of the message
        var cleanMsg = (msg || '').replace(/^[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE00}-\u{FE0F}\u{1F900}-\u{1F9FF}\u{1F600}-\u{1F64F}\u{1F680}-\u{1F6FF}✅✨🎉⚠️❌ℹ️💡🗑️📌🧹\s]+/u, '').trim();

        $toast.removeClass('success error warning info toast-success toast-error toast-warning toast-info');

        // 2. Identify message intent to set classic icon and message-matching background color
        var isErr = (typeOrIsError === 'error' || typeOrIsError === true || /^(error|failed|invalid|could not|network error|cannot)/i.test(cleanMsg) || /^(❌)/.test(msg));
        var isWarn = (typeOrIsError === 'warning' || /^(warning|caution|please enter|required|too long|empty)/i.test(cleanMsg) || /^(⚠️)/.test(msg));
        var isSuccess = (typeOrIsError === 'success' || typeOrIsError === false || /^(saved|success|synced|copied|renamed|deleted|cleared|applied|auto-structured|validated|complete|created|signed up)/i.test(cleanMsg) || /^(✅|🎉|✨)/.test(msg));

        if (isErr) {
            $toast.addClass('toast-error');
            $icon.attr('class', 'fa-solid fa-circle-xmark');
        } else if (isWarn) {
            $toast.addClass('toast-warning');
            $icon.attr('class', 'fa-solid fa-triangle-exclamation');
        } else if (isSuccess) {
            $toast.addClass('toast-success');
            $icon.attr('class', 'fa-solid fa-circle-check');
        } else {
            $toast.addClass('toast-info');
            $icon.attr('class', 'fa-solid fa-circle-info');
        }

        $msg.text(cleanMsg);
        $toast.addClass('show');
        toastTimeout = setTimeout(function() {
            $toast.removeClass('show');
        }, 3000);
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
