<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Sign In to Helpdesk - Daily Work Notepad');
$this->assign('meta_description', 'Sign in to access your daily development tasks, work logs, project notes, and client communication reports.');
$this->assign('meta_keywords', 'helpdesk login, daily task login, developer sign in');
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
        max-width: 420px;
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
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to your Daily Work Journal</p>
    </div>

    <!-- 100% Theme-Aware Dark Mode Google Sign-In Button -->
    <div class="google-auth-container">
        <div id="g_id_onload"
             data-client_id="<?= h(\Cake\Core\Configure::read('Google.clientId') ?: '') ?>"
             data-context="signin"
             data-callback="onGoogleAuthCallback"
             data-auto_prompt="false">
        </div>

        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'googleAuth']) ?>" class="btn-google-action" id="btnGoogleSigninAction">
            <svg viewBox="0 0 24 24" width="18" height="18" style="flex-shrink:0;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Sign in with Google</span>
        </a>
    </div>

    <div class="oauth-divider">
        <span>OR SIGN IN WITH EMAIL</span>
    </div>

    <?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'login'], 'id' => 'loginForm', 'novalidate' => true]) ?>
        <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-input" placeholder="example@gmail.com" required autofocus value="<?= h($this->request->getData('email')) ?>">
                </div>
                <div class="field-error-msg" id="emailError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label class="form-label" for="password">Password</label>
                    <a href="javascript:void(0);" id="btnOpenForgotPassword" style="font-size:11.5px; color:var(--primary); text-decoration:none; font-weight:600; cursor:pointer;" title="Reset your password">Forgot Password?</a>
                </div>
                <div class="input-wrapper">
                    <i class="fa-solid fa-key input-icon"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="field-error-msg" id="passwordError">
                    <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                <label style="display:inline-flex; align-items:center; gap:8px; font-size:12px; color:var(--text-muted); cursor:pointer; user-select:none;">
                    <input type="checkbox" name="remember_me" value="1" id="rememberMe" style="accent-color:var(--primary); width:15px; height:15px; cursor:pointer;">
                    Remember Me for 30 days
                </label>
            </div>

            <button type="submit" class="btn-submit" id="btnLoginSubmit">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Account
            </button>
        </div>
    <?= $this->Form->end() ?>

    <div class="auth-footer">
        Don't have an account? <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'signup']) ?>" class="auth-link">Sign Up</a>
    </div>
</div>

<!-- ========================================== -->
<!-- Forgot Password Multi-Step Modal -->
<!-- ========================================== -->
<div id="forgotPasswordModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:16px; box-sizing:border-box;">
    <div class="auth-card" style="position:relative; max-width:400px; box-shadow:0 20px 40px rgba(0,0,0,0.4); animation:fadeInScale 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <button type="button" id="btnCloseForgotModal" style="position:absolute; top:18px; right:18px; background:transparent; border:none; color:var(--text-muted); font-size:18px; cursor:pointer; padding:4px; line-height:1;" title="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Step 1: Request Email -->
        <div id="forgotStep1" class="forgot-step-block">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h2 class="auth-title" style="font-size:20px;">Forgot Password</h2>
                <p class="auth-subtitle">Enter your registered email to receive a 6-digit OTP verification code.</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px; margin-top:14px;">
                <div class="form-group">
                    <label class="form-label" for="forgotEmail">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="email" id="forgotEmail" class="form-input" placeholder="example@gmail.com" autocomplete="email">
                    </div>
                    <div class="field-error-msg" id="forgotEmailError">
                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                    </div>
                </div>

                <button type="button" class="btn-submit" id="btnSendForgotOtp">
                    <i class="fa-solid fa-paper-plane"></i> Send Verification Code
                </button>
            </div>
        </div>

        <!-- Step 2: Verify OTP -->
        <div id="forgotStep2" class="forgot-step-block" style="display:none;">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h2 class="auth-title" style="font-size:20px;">Enter OTP Code</h2>
                <p class="auth-subtitle">We sent a 6-digit code to <strong id="forgotTargetEmailDisplay" style="color:var(--text-main);"></strong></p>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px; margin-top:14px;">
                <div class="form-group">
                    <label class="form-label" for="forgotOtpCode">6-Digit Verification Code</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="text" id="forgotOtpCode" class="form-input" placeholder="123456" maxlength="6" style="letter-spacing: 6px; font-weight:700; font-size:16px; text-align:center;">
                    </div>
                    <div class="field-error-msg" id="forgotOtpError">
                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                    </div>
                </div>

                <button type="button" class="btn-submit" id="btnVerifyForgotOtp">
                    <i class="fa-solid fa-check"></i> Verify & Continue
                </button>

                <div style="text-align:center; font-size:12px; color:var(--text-muted);">
                    Didn't receive code? <a href="javascript:void(0);" id="btnResendForgotOtp" class="auth-link">Resend OTP</a>
                </div>
            </div>
        </div>

        <!-- Step 3: Create New Password -->
        <div id="forgotStep3" class="forgot-step-block" style="display:none;">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 class="auth-title" style="font-size:20px;">New Password</h2>
                <p class="auth-subtitle">Set your new password to regain access to your account.</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px; margin-top:14px;">
                <div class="form-group">
                    <label class="form-label" for="forgotNewPass">New Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-key input-icon"></i>
                        <input type="password" id="forgotNewPass" class="form-input" placeholder="Min. 6 characters" style="padding-right:36px;">
                        <button type="button" class="btn-toggle-eye" data-target="#forgotNewPass" style="position:absolute; right:10px; background:none; border:none; color:var(--text-muted); cursor:pointer;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="field-error-msg" id="forgotNewPassError">
                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="forgotConfirmPass">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <input type="password" id="forgotConfirmPass" class="form-input" placeholder="Repeat new password" style="padding-right:36px;">
                        <button type="button" class="btn-toggle-eye" data-target="#forgotConfirmPass" style="position:absolute; right:10px; background:none; border:none; color:var(--text-muted); cursor:pointer;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="field-error-msg" id="forgotConfirmPassError">
                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                    </div>
                </div>

                <button type="button" class="btn-submit" id="btnResetPasswordSubmit">
                    <i class="fa-solid fa-floppy-disk"></i> Reset & Save Password
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

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
            showToast('Authenticating with Google...', false);
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

    $(document).ready(function() {

        // Clear error on input
        $('#email, #password').on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.field-error-msg').removeClass('show').find('span').text('');
        });

        // Client-side form validation before submission
        $('#loginForm').on('submit', function(e) {
            var hasError = false;
            var firstInvalid = null;

            var emailVal = $('#email').val().trim();
            var passVal = $('#password').val();

            var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;

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
                $('#passwordError').addClass('show').find('span').text('Please enter your password.');
                hasError = true;
                if (!firstInvalid) firstInvalid = $('#password');
            }

            if (hasError) {
                e.preventDefault();
                firstInvalid.focus();
                showToast('Please enter your credentials to login.', true);
                return false;
            }
        });

        // ==========================================
        // Forgot Password Multi-Step Modal Handling
        // ==========================================
        var forgotTargetEmail = '';

        function openForgotModal() {
            $('#forgotEmailError, #forgotOtpError, #forgotNewPassError, #forgotConfirmPassError').removeClass('show').find('span').text('');
            $('#forgotEmail, #forgotOtpCode, #forgotNewPass, #forgotConfirmPass').removeClass('is-invalid').val('');
            
            var currentLoginEmail = $('#email').val().trim();
            if (currentLoginEmail) {
                $('#forgotEmail').val(currentLoginEmail);
            }

            $('#forgotStep1').show();
            $('#forgotStep2, #forgotStep3').hide();
            $('#forgotPasswordModal').css('display', 'flex').hide().fadeIn(200);
            $('#forgotEmail').focus();
        }

        function closeForgotModal() {
            $('#forgotPasswordModal').fadeOut(200);
        }

        $('#btnOpenForgotPassword').on('click', function(e) {
            e.preventDefault();
            openForgotModal();
        });

        $('#btnCloseForgotModal').on('click', function() {
            closeForgotModal();
        });

        $('#forgotPasswordModal').on('click', function(e) {
            if ($(e.target).is('#forgotPasswordModal')) {
                closeForgotModal();
            }
        });

        // Toggle password eye in modal
        $('.btn-toggle-eye').on('click', function() {
            var targetInput = $($(this).data('target'));
            var icon = $(this).find('i');
            if (targetInput.attr('type') === 'password') {
                targetInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                targetInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Clear modal field errors on typing
        $('#forgotEmail, #forgotOtpCode, #forgotNewPass, #forgotConfirmPass').on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.field-error-msg').removeClass('show').find('span').text('');
        });

        // Step 1: Send OTP
        $('#btnSendForgotOtp').on('click', function() {
            var emailVal = $('#forgotEmail').val().trim();
            var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;

            if (emailVal === '') {
                $('#forgotEmail').addClass('is-invalid');
                $('#forgotEmailError').addClass('show').find('span').text('Please enter your registered email address.');
                showToast('Please enter your email address to continue.', true);
                $('#forgotEmail').focus();
                return;
            } else if (!emailRegex.test(emailVal)) {
                $('#forgotEmail').addClass('is-invalid');
                $('#forgotEmailError').addClass('show').find('span').text('Please enter a valid email format (e.g. name@domain.com).');
                showToast('Please enter a valid email format.', true);
                $('#forgotEmail').focus();
                return;
            }

            var $btn = $(this);
            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Checking Email...');

            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Users', 'action' => 'forgotPassword']) ?>',
                type: 'POST',
                data: { email: emailVal },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        forgotTargetEmail = emailVal;
                        $('#forgotTargetEmailDisplay').text(emailVal);
                        showToast(res.message, false);
                        $('#forgotStep1').hide();
                        $('#forgotStep2').fadeIn(200);
                        $('#forgotOtpCode').val('').focus();
                    } else {
                        var errMsg = res.message || 'This email address is not registered in our system.';
                        $('#forgotEmail').addClass('is-invalid');
                        $('#forgotEmailError').addClass('show').find('span').text(errMsg);
                        showToast(errMsg, true);
                        $('#forgotEmail').focus();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    showToast('Server error while sending OTP. Please try again.', true);
                }
            });
        });

        // Step 2: Verify OTP
        $('#btnVerifyForgotOtp').on('click', function() {
            var otpVal = $('#forgotOtpCode').val().trim();

            if (otpVal.length < 6) {
                $('#forgotOtpCode').addClass('is-invalid');
                $('#forgotOtpError').addClass('show').find('span').text('Please enter the 6-digit OTP code.');
                showToast('Please enter the 6-digit OTP code received on email.', true);
                return;
            }

            var $btn = $(this);
            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Verifying...');

            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Users', 'action' => 'verifyResetOtp']) ?>',
                type: 'POST',
                data: { email: forgotTargetEmail, otp: otpVal },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        showToast(res.message, false);
                        $('#forgotStep2').hide();
                        $('#forgotStep3').fadeIn(200);
                        $('#forgotNewPass').focus();
                    } else {
                        $('#forgotOtpCode').addClass('is-invalid');
                        $('#forgotOtpError').addClass('show').find('span').text(res.message || 'Invalid OTP.');
                        showToast(res.message || 'Invalid or expired OTP code.', true);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    showToast('Server error while verifying OTP. Please try again.', true);
                }
            });
        });

        // Resend OTP Link
        $('#btnResendForgotOtp').on('click', function(e) {
            e.preventDefault();
            if (!forgotTargetEmail) return;

            showToast('Resending OTP code...', false);
            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Users', 'action' => 'forgotPassword']) ?>',
                type: 'POST',
                data: { email: forgotTargetEmail },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showToast('New OTP sent to ' + forgotTargetEmail, false);
                    } else {
                        showToast(res.message || 'Could not resend OTP.', true);
                    }
                }
            });
        });

        // Step 3: Reset & Save Password
        $('#btnResetPasswordSubmit').on('click', function() {
            var newPass = $('#forgotNewPass').val();
            var confirmPass = $('#forgotConfirmPass').val();

            var hasErr = false;
            if (newPass.length < 6) {
                $('#forgotNewPass').addClass('is-invalid');
                $('#forgotNewPassError').addClass('show').find('span').text('Password must be at least 6 characters.');
                hasErr = true;
            }

            if (newPass !== confirmPass) {
                $('#forgotConfirmPass').addClass('is-invalid');
                $('#forgotConfirmPassError').addClass('show').find('span').text('Passwords do not match.');
                hasErr = true;
            }

            if (hasErr) {
                showToast('Please correct password fields to continue.', true);
                return;
            }

            var $btn = $(this);
            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving Password...');

            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Users', 'action' => 'resetPassword']) ?>',
                type: 'POST',
                data: {
                    email: forgotTargetEmail,
                    password: newPass,
                    confirm_password: confirmPass
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        showToast(res.message, false);
                        closeForgotModal();
                        $('#email').val(forgotTargetEmail);
                        $('#password').val('').focus();
                    } else {
                        showToast(res.message || 'Failed to reset password.', true);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    showToast('Server error while resetting password.', true);
                }
            });
        });
    });
</script>
