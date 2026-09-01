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
$this->assign('title', 'Daily Update Generator - Helpdesk');
?>

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
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #f7f7f4;
        color: #27272a;
        padding: 12px 16px;
        font-size: 14px;
        display: flex;
        flex-direction: column;
    }

    .container-fluid {
        max-width: 1560px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        width: 100%;
    }

    .app-header {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .app-header h1 {
        font-weight: 700;
        font-size: 20px;
        color: #18181b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .app-header p {
        color: #71717a;
        font-size: 13px;
        margin: 2px 0 0 0;
    }

    .main-content-row {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        min-height: 0;
    }

    .card-panel {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        border: 1px solid #e4e4e0;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow-y: auto;
    }

    .panel-header {
        font-size: 15px;
        font-weight: 600;
        color: #18181b;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f4f4f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-group {
        margin-bottom: 10px;
    }

    label {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #52525b;
        margin-bottom: 4px;
        display: block;
    }

    .form-control {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d4d4d0;
        padding: 7px 12px;
        font-size: 13px;
        color: #18181b;
        height: auto;
        transition: all 0.15s ease;
        background-color: #fafaf8;
        outline: none;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
    }

    textarea.task_detail {
        resize: vertical;
        min-height: 62px;
        line-height: 1.45;
        font-family: inherit;
    }

    /* Preview Card */
    .preview-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e4e4e0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .preview-header {
        background: #fafaf8;
        padding: 12px 18px;
        border-bottom: 1px solid #e4e4e0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .preview-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #27272a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-copy-subject {
        background: #f4f4f0;
        color: #27272a;
        border: 1px solid #d4d4d0;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .btn-copy-subject:hover {
        background: #e4e4e0;
        color: #000000;
    }

    .btn-copy-content {
        background: #6366f1;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .btn-copy-content:hover {
        background: #4f46e5;
    }

    .btn-back-theme {
        background: #1c201e;
        color: #ffffff;
        border: 1px solid #1c201e;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .btn-back-theme:hover {
        background: #343a37;
        color: #ffffff;
    }

    .btn-secondary-custom {
        background: #ffffff;
        color: #52525b;
        border: 1px solid #d4d4d0;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-secondary-custom:hover {
        background: #f4f4f0;
        color: #18181b;
    }

    .email-body {
        padding: 18px;
        font-size: 14px;
        line-height: 1.55;
        color: #27272a;
        background: #ffffff;
        flex: 1;
        min-height: 0;
        overflow-y: auto;
    }

    .subject-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        border-bottom: 1px solid #e4e4e0;
        margin-bottom: 8px;
        gap: 12px;
    }

    .subject-row .subject {
        font-weight: 700;
        color: #18181b;
        font-size: 15px;
        margin: 0;
        flex-grow: 1;
    }

    .content-header-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
    }

    ol {
        padding-left: 20px;
        margin-top: 4px;
        margin-bottom: 12px;
    }

    li {
        margin-bottom: 4px;
    }

    /* Toast */
    #toast {
        visibility: hidden;
        min-width: 220px;
        background-color: #18181b;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 10px 16px;
        position: fixed;
        z-index: 9999;
        right: 20px;
        bottom: 20px;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        opacity: 0;
        transition: opacity 0.25s, bottom 0.25s;
    }

    #toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 24px;
    }

    @media (max-width: 991px) {
        html, body {
            height: auto;
            overflow: auto;
        }
        .main-content-row {
            grid-template-columns: 1fr;
            height: auto;
        }
        .card-panel, .preview-card {
            min-height: 480px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Top Header -->
    <div class="app-header">
        <div>
            <h1><i class="fa-solid fa-paper-plane" style="color: #6366f1;"></i> Daily Update Generator</h1>
            <p>MySQL Database-backed Daily Email Generator</p>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <a href="<?= $this->Url->build(['controller' => 'Tasks', 'action' => 'index']) ?>" class="btn-back-theme" id="btnBackToTasks" title="Back to Task Notepad">
                <i class="fa-solid fa-arrow-left"></i> Back to Tasks
            </a>
            <button type="button" class="btn-secondary-custom" id="btnSyncFromDb">
                <i class="fa-solid fa-arrows-rotate"></i> Sync Today's Tasks
            </button>
            <button type="button" class="btn-secondary-custom" id="btnResetTasks">
                <i class="fa-solid fa-rotate-left"></i> Reset Tasks
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-content-row">
        <!-- Form Input Panel -->
        <div class="card-panel">
            <div class="panel-header">
                <span><i class="fa-solid fa-pen-to-square" style="color: #6366f1;"></i> Update Details (MySQL DB)</span>
                <span id="updateSaveStatus" style="font-size:11px; color:#71717a;">Synced with Database</span>
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
                <textarea placeholder="Paste all Done Tasks here (one per line)..." name="list_done" id="txt_done_task" class="form-control custom-input task_detail done_task" rows="3"><?= h($savedUpdate ? $savedUpdate->done_tasks : $autoDoneTasks) ?></textarea>
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
                <h3><i class="fa-solid fa-eye" style="color: #6366f1;"></i> Live Email Preview</h3>
            </div>

            <div class="email-body">
                <div class="mail_body_wrapper">
                    <div class="subject-row">
                        <span class="subject"></span>
                        <button type="button" class="btn-copy-subject" id="btnCopySubject"><i class="fa-solid fa-heading"></i> Copy Subject</button>
                    </div>

                    <div class="content-header-row">
                        <button type="button" class="btn-copy-content" id="btnCopyContent"><i class="fa-solid fa-copy"></i> Copy Content</button>
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
        </div>
    </div>

    <!-- Footer Copyright -->
    <footer style="text-align:center; font-size:11px; color:#71717a; padding:6px 0 2px 0; font-weight:500; opacity:0.85; flex-shrink:0;">
        &copy; <?= date('Y') ?> Mohit Mokariya. All Rights Reserved. Powered by CakePHP 5 & MySQL Database.
    </footer>
</div>

<!-- Toast Notification -->
<div id="toast">✨ Content copied to clipboard!</div>

<script>
$(document).ready(function() {
    var currentDateIso = '<?= h($todayIso) ?>';
    var saveUpdateTimer = null;

    function getOrdinal(n) {
        if ((parseFloat(n) == parseInt(n)) && !isNaN(n)) {
            var s = ["th", "st", "nd", "rd"],
                v = n % 100;
            return n + (s[(v - 20) % 10] || s[v] || s[0]);
        }
        return n;
    }

    function currentDateFormatted() {
        var date = new Date();
        var month = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return getOrdinal(date.getDate()) + " " + month[date.getMonth()] + ", " + date.getFullYear();
    }

    function showToast(msg) {
        var toast = $('#toast');
        toast.text(msg).addClass('show');
        setTimeout(function() {
            toast.removeClass('show');
        }, 2200);
    }

    function formatTaskSection(txtName) {
        var rawText = $("textarea[name='" + txtName + "']").val() || '';
        var lines = rawText.split('\n').map(function(l) { return l.trim(); }).filter(function(l) { return l.length > 0; });

        if (lines.length === 0) {
            $('.' + txtName).empty();
            if (txtName === 'list_done') {
                $('.review_note').empty();
            }
            return;
        }

        var isDoneList = (txtName === 'list_done');
        if (isDoneList) {
            $('.review_note').html('Please check with the latest updates and let us know your thoughts for the same.<br>');
        }

        var sectionLabel = $("textarea[name='" + txtName + "']").closest('.form-group').find('label').text().replace(/\s*:\s*$/, '').trim();
        var taskDetail = "<b><u>" + sectionLabel + "</u></b><br>";
        var inList = false;

        lines.forEach(function(rawLine) {
            var isHeader = rawLine.endsWith(':');
            if (isHeader) {
                if (inList) {
                    taskDetail += "</ol>";
                    inList = false;
                }
                var headerText = rawLine.replace(/^[-*\u2022\d+\.]+\s*/, '');
                taskDetail += "<b>" + headerText + "</b>";
            } else {
                var taskText = rawLine.replace(/^[-*\u2022\d+\.]+\s*/, '');
                if (!inList) {
                    taskDetail += "<ol type='1'>";
                    inList = true;
                }
                if (isDoneList) {
                    taskDetail += "<li>" + taskText + " <b>[Done]</b></li>";
                } else {
                    taskDetail += "<li>" + taskText + "</li>";
                }
            }
        });

        if (inList) {
            taskDetail += "</ol>";
        }

        $('.' + txtName).empty().html(taskDetail);
    }

    function renderAllTaskSections() {
        formatTaskSection('list_done');
        formatTaskSection('list_progress');
        formatTaskSection('list_remaining');
        formatTaskSection('list_query');
        formatTaskSection('list_note');
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
    function saveUpdateToDatabase() {
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
                } else {
                    $('#updateSaveStatus').text('Save error');
                }
            },
            error: function() {
                $('#updateSaveStatus').text('DB Connection error');
            }
        });
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

    $('.task_detail').on('input change keyup', function() {
        var txtName = $(this).attr('name');
        formatTaskSection(txtName);
        triggerAutoSave();
    });

    // Copy Subject
    $('#btnCopySubject').click(function() {
        var rawSubject = $('.subject').text().trim();
        if (!rawSubject) {
            showToast('⚠️ No Subject to copy');
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(rawSubject).then(function() {
                showToast('📌 Subject copied to clipboard!');
            }).catch(function() {
                fallbackCopy(rawSubject, '📌 Subject copied!');
            });
        } else {
            fallbackCopy(rawSubject, '📌 Subject copied!');
        }
    });

    // Copy Content
    $('#btnCopyContent').click(function() {
        var container = document.querySelector('.mail_body');
        if (!container || !container.innerText.trim()) {
            showToast('⚠️ No Mail Content to copy');
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
            showToast('✨ Mail Content copied to clipboard!');
        } catch (err) {
            showToast('Failed to copy content');
        }
    });

    function fallbackCopy(text, msg) {
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
        showToast(msg);
    }

    // Back to Tasks Button
    $('#btnBackToTasks').click(function(e) {
        e.preventDefault();
        var clientVal = $('#client_name').val() || '';
        saveUpdateToDatabase();
        var url = window.APP_BASE + '?date=' + currentDateIso;
        if (clientVal) {
            url += '&client_name=' + encodeURIComponent(clientVal);
        }
        window.location.href = url;
    });

    // Reset Tasks
    $('#btnResetTasks').click(function() {
        if (!confirm('Clear all task fields in form and DB?')) return;
        $('textarea.task_detail').val('');
        renderAllTaskSections();
        saveUpdateToDatabase();
        showToast('🧹 All task fields cleared!');
    });

    var lastSyncedTasks = $('#txt_done_task').val() || '';
    var isCheckingTasks = false;

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
                if (res.success && res.tasks) {
                    var newTasks = res.tasks.trim();
                    var currentTasks = ($('#txt_done_task').val() || '').trim();

                    // If tasks changed or are fresh
                    if (newTasks !== '' && (newTasks !== currentTasks || newTasks !== lastSyncedTasks)) {
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
                        saveUpdateToDatabase();
                        if (isManual) {
                            showToast('✅ Synced latest tasks from database!');
                        }
                    } else if (isManual) {
                        showToast('Already up to date with DB.');
                    }
                } else if (isManual) {
                    showToast('No tasks found in DB for today.');
                }
            },
            error: function() {
                isCheckingTasks = false;
            }
        });
    }

    // Auto check on 1-second interval
    setInterval(function() {
        checkForTaskUpdates(false);
    }, 1000);

    // Auto check when user switches back to this tab / window
    $(window).on('focus', function() {
        checkForTaskUpdates(false);
    });
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            checkForTaskUpdates(false);
        }
    });

    // Sync From Database Work Logs (Manual Button)
    $('#btnSyncFromDb').click(function() {
        checkForTaskUpdates(true);
    });

    // Initial render & Immediate check
    updateClientGreeting();
    updateProjectHeader();
    updateTlSignoff();
    renderAllTaskSections();
    checkForTaskUpdates(false);
});
</script>
