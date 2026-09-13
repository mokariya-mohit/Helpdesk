/**
 * Helpdesk Global JavaScript Library
 * Shared Utilities, Real-Time Helpers, Toast & Popover Components
 */

(function (window, $) {
    'use strict';

    // 1. Toast Notification System - Always Bottom-Right with Accurate Type Colors
    var toastTimeout = null;

    window.showToast = function (msg, typeOrIsError) {
        var $toast = $('#toastNotification').length ? $('#toastNotification') : $('#toast');

        // Dynamically create toast container in body if not already present
        if (!$toast.length) {
            $toast = $('<div id="toastNotification" class="toast"><i class="fa-solid fa-circle-check" id="toastIcon"></i><span id="toastMessage"></span></div>');
            $('body').append($toast);
        }

        var $icon = $toast.find('#toastIcon').length ? $toast.find('#toastIcon') : $('#toastIcon');
        var $msg = $toast.find('#toastMessage').length ? $toast.find('#toastMessage') : $('#toastMessage');

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        // Clean out any leading emojis for clean text
        var cleanMsg = (msg || '').replace(/^[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE00}-\u{FE0F}\u{1F900}-\u{1F9FF}\u{1F600}-\u{1F64F}\u{1F680}-\u{1F6FF}✅✨🎉⚠️❌ℹ️💡🗑️📌🧹\s]+/u, '').trim();

        $toast.removeClass('success error warning info toast-success toast-error toast-warning toast-info');

        var isErr = (typeOrIsError === 'error' || typeOrIsError === 'danger' || typeOrIsError === true || /^(error|failed|invalid|could not|network error|cannot|incorrect|server error)/i.test(cleanMsg) || /^(❌)/.test(msg));
        var isWarn = (typeOrIsError === 'warning' || typeOrIsError === 'warn' || /^(warning|caution|please enter|required|too long|empty|no tasks|no subject|nothing to copy)/i.test(cleanMsg) || /^(⚠️)/.test(msg));
        var isSuccess = (typeOrIsError === 'success' || typeOrIsError === false || /^(saved|success|synced|copied|renamed|deleted|cleared|applied|auto-structured|validated|complete|sent|created|signed up|logged in|active|template|updated|added|welcome)/i.test(cleanMsg) || /^(✅|🎉|✨)/.test(msg));

        if (isErr) {
            $toast.addClass('toast-error error');
            if ($icon.length) $icon.attr('class', 'fa-solid fa-circle-xmark');
        } else if (isWarn) {
            $toast.addClass('toast-warning warning');
            if ($icon.length) $icon.attr('class', 'fa-solid fa-triangle-exclamation');
        } else if (isSuccess) {
            $toast.addClass('toast-success success');
            if ($icon.length) $icon.attr('class', 'fa-solid fa-circle-check');
        } else {
            $toast.addClass('toast-info info');
            if ($icon.length) $icon.attr('class', 'fa-solid fa-circle-info');
        }

        if ($msg.length) {
            $msg.text(cleanMsg);
        } else {
            $toast.text(cleanMsg);
        }

        $toast.addClass('show');
        toastTimeout = setTimeout(function () {
            $toast.removeClass('show');
        }, 3200);
    };

    // 2. Universal Custom Confirmation Modal (UI Themed Glassmorphism Modal)
    window.showConfirmModal = function (options, onConfirmCallback, onCancelCallback) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        options = options || {};

        var title = options.title || 'Please Confirm';
        var subtitle = options.subtitle || (type === 'danger' ? 'This action cannot be undone' : 'Please confirm your action');
        var message = options.message || 'Are you sure you want to proceed?';
        var confirmText = options.confirmText || 'Confirm';
        var cancelText = options.cancelText || 'Cancel';
        var type = options.type || 'danger'; // 'danger' | 'warning' | 'primary' | 'info'
        var icon = options.icon;

        if (!icon) {
            if (type === 'danger') icon = 'fa-solid fa-trash-can';
            else if (type === 'warning') icon = 'fa-solid fa-triangle-exclamation';
            else if (type === 'primary') icon = 'fa-solid fa-wand-magic-sparkles';
            else icon = 'fa-solid fa-circle-info';
        }

        var onConfirm = options.onConfirm || onConfirmCallback || function () { };
        var onCancel = options.onCancel || onCancelCallback || function () { };

        return new Promise(function (resolve) {
            var $modal = $('#customConfirmModal');
            if (!$modal.length) {
                var modalHtml = [
                    '<div id="customConfirmModal" class="modal-overlay" style="z-index: 100005 !important;">',
                    '  <div class="modal-card custom-confirm-modal-card">',
                    '    <div class="modal-header confirm-modal-header">',
                    '      <div class="modal-title confirm-modal-title">',
                    '        <div id="confirmModalIconWrapper" class="confirm-icon-badge">',
                    '          <i id="confirmModalIcon" class="fa-solid fa-triangle-exclamation"></i>',
                    '        </div>',
                    '        <div class="confirm-title-group">',
                    '          <span id="confirmModalTitle" class="confirm-title-text">Please Confirm</span>',
                    '          <span id="confirmModalSubtitle" class="confirm-subtitle-text">Confirmation required</span>',
                    '        </div>',
                    '      </div>',
                    '      <button type="button" class="btn-close-modal" id="btnCancelConfirmX" title="Close">&times;</button>',
                    '    </div>',
                    '    <div class="modal-body confirm-modal-body">',
                    '      <p id="confirmModalMessage" class="confirm-message-text"></p>',
                    '    </div>',
                    '    <div class="modal-footer confirm-modal-footer">',
                    '      <button type="button" class="btn-confirm-cancel" id="btnCancelConfirm">Cancel</button>',
                    '      <button type="button" class="btn-confirm-action" id="btnAcceptConfirm">Confirm</button>',
                    '    </div>',
                    '  </div>',
                    '</div>'
                ].join('\n');
                $('body').append(modalHtml);
                $modal = $('#customConfirmModal');
            }

            $('#confirmModalTitle').text(title);
            if (subtitle) {
                $('#confirmModalSubtitle').text(subtitle).show();
            } else {
                $('#confirmModalSubtitle').hide();
            }
            $('#confirmModalMessage').html(typeof message === 'string' ? message.replace(/\n/g, '<br>') : message);
            $('#btnAcceptConfirm').text(confirmText);
            $('#btnCancelConfirm').text(cancelText);

            var $iconBadge = $('#confirmModalIconWrapper');
            var $icon = $('#confirmModalIcon');
            var $confirmBtn = $('#btnAcceptConfirm');

            $iconBadge.removeClass('badge-danger badge-warning badge-primary badge-info')
                .addClass('badge-' + type);
            $icon.attr('class', icon);

            $confirmBtn.removeClass('btn-type-danger btn-type-warning btn-type-primary btn-type-info')
                .addClass('btn-type-' + type);

            $modal.off('click.confirmModal');
            $('#btnAcceptConfirm').off('click.confirm');
            $('#btnCancelConfirm, #btnCancelConfirmX').off('click.confirm');
            $(document).off('keydown.confirmModal');

            var isHandled = false;
            function closeModal(confirmed) {
                if (isHandled) return;
                isHandled = true;
                $modal.removeClass('active');
                $(document).off('keydown.confirmModal');
                setTimeout(function () {
                    if (confirmed) {
                        onConfirm();
                        resolve(true);
                    } else {
                        onCancel();
                        resolve(false);
                    }
                }, 100);
            }

            $('#btnAcceptConfirm').on('click.confirm', function (e) {
                e.preventDefault();
                closeModal(true);
            });

            $('#btnCancelConfirm, #btnCancelConfirmX').on('click.confirm', function (e) {
                e.preventDefault();
                closeModal(false);
            });

            $modal.on('click.confirmModal', function (e) {
                if ($(e.target).is($modal)) {
                    closeModal(false);
                }
            });

            $(document).on('keydown.confirmModal', function (e) {
                if (e.key === 'Escape') {
                    closeModal(false);
                } else if (e.key === 'Enter' && !$(e.target).is('button, textarea, input')) {
                    closeModal(true);
                }
            });

            $modal.addClass('active');
            $('#btnAcceptConfirm').focus();
        });
    };

    window.showConfirmDialog = window.showConfirmModal;

    // Override browser native confirm to prevent system alert popups
    window.confirm = function (msg) {
        console.warn('Native confirm() intercepted with: "' + msg + '". Displaying custom themed modal instead.');
        window.showConfirmModal({
            title: 'Please Confirm',
            message: msg,
            type: 'warning'
        });
        return false;
    };

    // 3. Safe HTML Escaping
    window.escapeHtml = function (str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    // 3. Universal URL & Markdown Inline Formatter
    window.formatInlineMarkdown = function (text) {
        if (!text) return '';
        var escaped = window.escapeHtml(text);

        // 1. Matches markdown links [Anchor Text](https://url "optional title") or [Anchor Text](https://url)
        escaped = escaped.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)\"\&]+)(?:\s+(?:&quot;|["'])[\s\S]*?(?:&quot;|["']))?\)/gi, function (match, anchor, url) {
            return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" style="color: rgb(59, 130, 246); text-decoration: underline; word-break: break-all;">' + anchor + '</a>';
        });

        // 2. Matches inline backticks `code`
        escaped = escaped.replace(/`([^`]+)`/g, function (match, codeText) {
            return '<code style="background: rgba(148, 163, 184, 0.15); padding: 1px 5px; border-radius: 4px; font-family: \'Fira Code\', monospace; font-size: 12px; color: #334155;">' + codeText + '</code>';
        });

        // 3. Matches markdown bold **text** -> <b>text</b>
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, function (match, boldText) {
            return '<b>' + boldText + '</b>';
        });

        // 4. Matches http:// or https:// (not preceded by href=" or inside a tag or anchor)
        escaped = escaped.replace(/(^|[^"'>=])(https?:\/\/[^\s<]+[^<.,:;"')\]\s])/gi, function (match, prefix, url) {
            return prefix + '<a href="' + url + '" target="_blank" rel="noopener noreferrer" style="color: rgb(59, 130, 246); text-decoration: underline; word-break: break-all;">' + url + '</a>';
        });

        // 5. Matches standalone www. (not preceded by // or inside an href)
        escaped = escaped.replace(/(^|[\s(])(www\.[^\s<]+[^<.,:;"')\]\s])/gi, function (match, prefix, url) {
            return prefix + '<a href="https://' + url + '" target="_blank" rel="noopener noreferrer" style="color: rgb(59, 130, 246); text-decoration: underline; word-break: break-all;">' + url + '</a>';
        });

        return escaped;
    };

    window.autolinkUrls = function (text) {
        return window.formatInlineMarkdown(text);
    };

    // 4. Universal Task Markdown Parser Engine
    window.parseTaskMarkdownToHtml = function (rawText, isDoneList) {
        if (!rawText || !rawText.trim()) return '';

        var rawLines = rawText.split(/\r?\n/);
        var taskDetail = '';
        var inOl = false;
        var inUl = false;
        var inLi = false;

        for (var i = 0; i < rawLines.length; i++) {
            var rawLine = rawLines[i];
            var trimmed = rawLine.trim();

            if (!trimmed) {
                if (inUl) { taskDetail += "</ul>"; inUl = false; }
                continue;
            }

            // Strip outer markdown bold from potential header test: **Backend:** -> Backend:
            var headerCandidate = trimmed.replace(/^\*\*|\*\*$/g, '').trim();

            // 1. Check if Category Header (e.g. "Backend:", "Frontend:", "Design:", "API Integration:")
            if (/^[A-Za-z0-9\s_\-\/&]{2,60}:$/.test(headerCandidate) && !/^\d+[\.\)]/.test(trimmed) && !/^[•▪▫◦*\-–—]/.test(trimmed)) {
                // Lookahead: verify if this category actually has tasks under it
                var hasTasksUnderCategory = false;
                for (var j = i + 1; j < rawLines.length; j++) {
                    var nextTrimmed = rawLines[j].trim();
                    if (!nextTrimmed) continue;
                    var nextCandidate = nextTrimmed.replace(/^\*\*|\*\*$/g, '').trim();
                    if (/^[A-Za-z0-9\s_\-\/&]{2,60}:$/.test(nextCandidate) && !/^\d+[\.\)]/.test(nextTrimmed) && !/^[•▪▫◦*\-–—]/.test(nextTrimmed)) {
                        break; // Next category header reached without any task items
                    }
                    hasTasksUnderCategory = true;
                    break;
                }

                if (!hasTasksUnderCategory) {
                    continue; // Skip orphan category header with no tasks
                }

                if (inUl) { taskDetail += "</ul>"; inUl = false; }
                if (inLi) { taskDetail += "</li>"; inLi = false; }
                if (inOl) { taskDetail += "</ol>"; inOl = false; }
                taskDetail += "<b class='task-category-header' style='font-weight:bold; display:inline-block; margin-top:8px; margin-bottom:3px;'>" + window.escapeHtml(headerCandidate) + "</b><br>";
                continue;
            }

            // 2. Check indentation & bullet markers
            var isIndented = /^(\s{2,}|\t)/.test(rawLine);
            var isBulletChar = /^[-*•▪▫◦–—]/.test(trimmed);

            // Sub-bullet: ONLY when indented (starts with 2+ spaces or \t) under an active list item
            if (inLi && isIndented && isBulletChar) {
                if (!inUl) {
                    taskDetail += "<ul style='list-style-type:disc; padding-left:22px; margin:4px 0 6px 0;'>";
                    inUl = true;
                }
                var cleanSub = trimmed.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[-*•▪▫◦–—]+)\s*/, '');
                var formattedSub = window.formatInlineMarkdown(cleanSub);
                taskDetail += "<li style='margin-bottom:3px; line-height:1.5;'>" + formattedSub + "</li>";
                continue;
            }

            // Indented continuation/paragraph note under parent item (no bullet char)
            if (inLi && isIndented) {
                if (inUl) { taskDetail += "</ul>"; inUl = false; }
                var formattedNote = window.formatInlineMarkdown(trimmed);
                taskDetail += "<div style='margin-top:3px; margin-bottom:4px; line-height:1.5;'>" + formattedNote + "</div>";
                continue;
            }

            // 3. Main Task Item (Root level item - column 0)
            if (inUl) { taskDetail += "</ul>"; inUl = false; }
            if (inLi) { taskDetail += "</li>"; inLi = false; }

            if (!inOl) {
                taskDetail += "<ol type='1' style='padding-left:22px; margin:4px 0 10px 0;'>";
                inOl = true;
            }

            // Clean leading bullet markers, numbering tags (e.g. "1. ", "- ", "* ", "• ")
            var cleanTask = trimmed.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[-*•▪▫◦–—]+)\s*/, '');
            if (!cleanTask.trim()) {
                continue;
            }

            // Check if user already added [Done] at the end
            var hasDoneTag = /\s*\[done\]\s*$/i.test(cleanTask);
            var taskBody = cleanTask.replace(/\s*\[done\]\s*$/i, '').trim();
            var formattedTask = window.formatInlineMarkdown(taskBody);

            if (isDoneList && !trimmed.endsWith(':')) {
                taskDetail += "<li style='margin-bottom:4px; line-height:1.5;'>" + formattedTask + " <b class='task-done-badge'>[Done]</b>";
            } else {
                taskDetail += "<li style='margin-bottom:4px; line-height:1.5;'>" + formattedTask + (hasDoneTag ? " <b class='task-done-badge'>[Done]</b>" : "");
            }
            inLi = true;
        }

        if (inUl) { taskDetail += "</ul>"; }
        if (inLi) { taskDetail += "</li>"; }
        if (inOl) { taskDetail += "</ol>"; }

        return taskDetail;
    };

    // 5. URL Extractor
    window.extractUrls = function (text) {
        if (!text) return [];
        var matches = text.match(/(https?:\/\/[^\s<]+[^<.,:;"')\]\s]|(?:^|[\s(])www\.[^\s<]+[^<.,:;"')\]\s])/gi) || [];
        var cleanUrls = [];
        matches.forEach(function (m) {
            var url = m.trim();
            if (url.startsWith('www.')) url = 'https://' + url;
            if (cleanUrls.indexOf(url) === -1) {
                cleanUrls.push(url);
            }
        });
        return cleanUrls;
    };

    // 6. Smart Task Text Cleaner: Strips external bullets, numbers, and converts **bold** to "quotes"
    window.cleanTaskText = function (text) {
        if (!text) return '';
        // Strip bullet symbols, numbered list tags (e.g. "1. ", "• ", "* ", "- ", "▪ ", "[1]")
        var clean = text.replace(/^(\d+[\.\)]|[a-zA-Z][\.\)]|\[\d+\]|[\*\u2022\u25AA\u25AB\u2713\u2714\u25BA\u25CF\u25CB\u2013\u2014\-]+)\s*/, '').trim();

        // Convert markdown bold **word** to double quotes "word"
        clean = clean.replace(/\*\*([^*]+)\*\*/g, function (match, inner) {
            var trimmed = inner.trim();
            if (trimmed.startsWith('"') && trimmed.endsWith('"')) return trimmed;
            return '"' + trimmed + '"';
        });

        clean = clean.replace(/__([^_]+)__/g, function (match, inner) {
            var trimmed = inner.trim();
            if (trimmed.startsWith('"') && trimmed.endsWith('"')) return trimmed;
            return '"' + trimmed + '"';
        });

        return clean;
    };

    // 6. Date Formatting Utilities
    window.formatIsoToDisplay = function (iso) {
        if (!iso) return '';
        var parts = iso.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return iso;
    };

    window.getOrdinal = function (n) {
        var s = ["th", "st", "nd", "rd"];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    };

    // 7. Universal Black Text Clipboard Engine & Fallback Copy
    window.copyElementAsBlackText = function (sourceContainer, successMsg, customHtmlModifier) {
        if (!sourceContainer) {
            window.showToast('Nothing to copy!', 'warning');
            return;
        }

        // Clone the container to isolate DOM manipulation
        var clone = sourceContainer.cloneNode(true);

        if (typeof customHtmlModifier === 'function') {
            customHtmlModifier(clone);
        }

        // Strip any buttons or non-content controls
        var removeEls = clone.querySelectorAll('button, .btn, .btn-copy-subject, .btn-copy-content, .btn-copy-preview-modal, script, style');
        for (var b = 0; b < removeEls.length; b++) {
            removeEls[b].remove();
        }

        // Deep-walk every child node to enforce pure black #000000 text
        var allEls = clone.querySelectorAll('*');
        for (var i = 0; i < allEls.length; i++) {
            var el = allEls[i];
            el.style.setProperty('color', '#000000', 'important');
            el.style.setProperty('background-color', 'transparent', 'important');
            var tag = el.tagName.toLowerCase();
            if (tag === 'b' || tag === 'strong') {
                el.style.setProperty('font-weight', 'bold', 'important');
                el.style.setProperty('color', '#000000', 'important');
            } else if (tag === 'a') {
                el.style.setProperty('color', '#1a56db', 'important');
            } else if (tag === 'li') {
                el.style.setProperty('color', '#000000', 'important');
                el.style.setProperty('margin-bottom', '4px', 'important');
                el.style.setProperty('line-height', '1.6', 'important');
            }
        }

        clone.style.setProperty('color', '#000000', 'important');
        clone.style.setProperty('background-color', '#ffffff', 'important');
        clone.style.setProperty('font-family', 'Arial, Helvetica, sans-serif', 'important');

        var cleanOuterHtml = '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.65; color: #000000 !important; background-color: #ffffff;">' + clone.innerHTML + '</div>';
        var plainText = (clone.innerText || clone.textContent || '').trim();

        function execCommandFallback() {
            var tempDiv = document.createElement('div');
            tempDiv.className = 'force-black-copy-container';
            tempDiv.setAttribute('data-theme', 'light');
            tempDiv.style.position = 'fixed';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '0';
            tempDiv.style.opacity = '0';
            tempDiv.style.color = '#000000';
            tempDiv.style.backgroundColor = '#ffffff';
            tempDiv.style.fontFamily = 'Arial, Helvetica, sans-serif';
            tempDiv.innerHTML = cleanOuterHtml;
            document.body.appendChild(tempDiv);

            var range = document.createRange();
            var sel = window.getSelection();
            sel.removeAllRanges();
            range.selectNodeContents(tempDiv);
            sel.addRange(range);

            try {
                document.execCommand('copy');
                sel.removeAllRanges();
                window.showToast(successMsg || 'Content copied to clipboard (Black Text)!', 'success');
            } catch (err) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(plainText).then(function () {
                        window.showToast(successMsg || 'Content copied as text!', 'success');
                    });
                } else {
                    window.showToast('Could not copy content', 'error');
                }
            } finally {
                document.body.removeChild(tempDiv);
            }
        }

        if (navigator.clipboard && window.ClipboardItem) {
            try {
                var blobHtml = new Blob([cleanOuterHtml], { type: 'text/html' });
                var blobText = new Blob([plainText], { type: 'text/plain' });
                var item = new ClipboardItem({
                    'text/html': blobHtml,
                    'text/plain': blobText
                });
                navigator.clipboard.write([item]).then(function () {
                    window.showToast(successMsg || 'Content copied to clipboard (Black Text)!', 'success');
                }).catch(function () {
                    execCommandFallback();
                });
            } catch (e) {
                execCommandFallback();
            }
        } else {
            execCommandFallback();
        }
    };

    window.fallbackCopy = function (text, msg) {
        var $temp = $('<textarea>');
        $temp.css({ position: 'fixed', left: '-9999px', top: '0', opacity: '0' });
        $('body').append($temp);
        $temp.val(text).select();
        try {
            document.execCommand('copy');
            window.showToast(msg || 'Copied to clipboard!', 'success');
        } catch (e) {
            window.showToast('Could not copy to clipboard', 'error');
        }
        $temp.remove();
    };

    // Global copy event listener: Guarantees that ANY manual selection copy in Dark Mode pastes as black text
    document.addEventListener('copy', function (e) {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount || sel.isCollapsed) return;

        var range = sel.getRangeAt(0);
        var container = range.commonAncestorContainer;
        if (container.nodeType === Node.TEXT_NODE) {
            container = container.parentElement;
        }
        if (!container) return;

        var isDarkTheme = (document.documentElement.getAttribute('data-theme') === 'dark' || document.body.getAttribute('data-theme') === 'dark');
        var isPreviewOrTask = container.closest('.mail_body, #emailHtmlPreviewContainer, .email-preview-box, .preview-card, .editor-preview-box, #workNotesEditor, .task-list-panel');

        // Only intercept if dark theme is active or selection is inside preview/task sections
        if (isDarkTheme || isPreviewOrTask) {
            var cloned = range.cloneContents();
            var tempDiv = document.createElement('div');
            tempDiv.appendChild(cloned);

            var els = tempDiv.querySelectorAll('*');
            for (var i = 0; i < els.length; i++) {
                var el = els[i];
                el.style.setProperty('color', '#000000', 'important');
                if (el.style.backgroundColor && el.style.backgroundColor !== 'transparent') {
                    el.style.backgroundColor = 'transparent';
                }
                var t = el.tagName.toLowerCase();
                if (t === 'b' || t === 'strong') {
                    el.style.setProperty('font-weight', 'bold', 'important');
                    el.style.setProperty('color', '#000000', 'important');
                } else if (t === 'a') {
                    el.style.setProperty('color', '#1a56db', 'important');
                }
            }

            tempDiv.style.setProperty('color', '#000000', 'important');
            tempDiv.style.setProperty('background-color', '#ffffff', 'important');
            tempDiv.style.setProperty('font-family', 'Arial, Helvetica, sans-serif', 'important');

            var cleanHtml = '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.65; color: #000000 !important; background-color: #ffffff;">' + tempDiv.innerHTML + '</div>';
            var plainText = sel.toString();

            if (e.clipboardData) {
                e.clipboardData.setData('text/html', cleanHtml);
                e.clipboardData.setData('text/plain', plainText);
                e.preventDefault();
            }
        }
    });

    // 8. Global UI Controller on Document Ready
    $(document).ready(function () {
        // Universal User Profile Popover Menu
        var $userBtn = $('#btnUserMenuToggle, #btnUserPill');
        var $userPopover = $('#userPopoverMenu');

        if ($userBtn.length && $userPopover.length) {
            $userBtn.click(function (e) {
                e.stopPropagation();
                var isOpen = $userPopover.hasClass('show');
                $userPopover.toggleClass('show');

                if (!isOpen) {
                    var rect = $userPopover[0].getBoundingClientRect();
                    var pad = 8;
                    if (rect.right > window.innerWidth - pad) {
                        $userPopover.css({ right: '0', left: 'auto' });
                    }
                    if (rect.left < pad) {
                        $userPopover.css({ left: '0', right: 'auto' });
                    }
                }
            });

            $(document).click(function (e) {
                if (!$(e.target).closest('#userPopoverMenu, #btnUserMenuToggle, #btnUserPill').length) {
                    $userPopover.removeClass('show');
                }
            });

            $(window).on('resize orientationchange', function () {
                if ($userPopover.hasClass('show')) {
                    $userPopover.removeClass('show');
                }
            });
        }

        // Global Session Flash Message Bridge
        var $flash = $('#initialFlashHolder');
        if ($flash.length) {
            var msgText = $flash.text().trim();
            if (msgText) {
                var isErr = $flash.find('.error').length > 0 || !$flash.find('.success').length;
                window.showToast(msgText, isErr ? 'error' : 'success');
                $flash.remove();
            }
        }

        // Logout Confirmation Bridge
        $(document).on('click', '#btnLogoutLink, .item-logout', function (e) {
            e.preventDefault();
            var logoutUrl = $(this).attr('href');
            window.showConfirmModal({
                title: 'Log Out?',
                message: 'Are you sure you want to sign out of your Helpdesk session?',
                type: 'warning',
                icon: 'fa-solid fa-arrow-right-from-bracket',
                confirmText: 'Log Out',
                cancelText: 'Stay Signed In',
                onConfirm: function () {
                    window.location.href = logoutUrl;
                }
            });
        });

        // Initialize and sync Theme UI
        window.syncThemeUi();
    });

    // ==========================================================================
    // 9. Classic Sun / Moon Dark & Light Theme Controller
    // ==========================================================================
    window.getTheme = function () {
        var current = document.documentElement.getAttribute('data-theme');
        if (!current) {
            current = localStorage.getItem('helpdesk_theme') || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light');
        }
        return current;
    };

    window.syncThemeUi = function () {
        var theme = window.getTheme();
        var isDark = (theme === 'dark');

        // Update Header Icon & Tooltip
        var $toggleBtns = $('.btn-theme-toggle-header, #btnThemeToggleHeader');
        if ($toggleBtns.length) {
            if (isDark) {
                $toggleBtns.html('<i class="fa-solid fa-sun" style="color: #f59e0b;"></i>');
                $toggleBtns.attr('title', 'Switch to Light Mode');
                $toggleBtns.attr('aria-label', 'Switch to Light Mode');
            } else {
                $toggleBtns.html('<i class="fa-solid fa-moon" style="color: #64748b;"></i>');
                $toggleBtns.attr('title', 'Switch to Dark Mode');
                $toggleBtns.attr('aria-label', 'Switch to Dark Mode');
            }
        }

        // Update Popover item text if exists
        var $popoverThemeItem = $('#btnThemeTogglePopover');
        if ($popoverThemeItem.length) {
            if (isDark) {
                $popoverThemeItem.html('<i class="fa-solid fa-sun" style="color: #f59e0b; width: 16px;"></i> <span>Switch to Light Mode</span>');
            } else {
                $popoverThemeItem.html('<i class="fa-solid fa-moon" style="color: #64748b; width: 16px;"></i> <span>Switch to Dark Mode</span>');
            }
        }

        // Update Profile Modal theme pill options
        $('.theme-pill-option').each(function () {
            var optTheme = $(this).data('theme');
            $(this).toggleClass('active', optTheme === theme);
        });
    };

    window.setTheme = function (themeName, notify) {
        var theme = (themeName === 'dark') ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.style.colorScheme = theme;
        try {
            localStorage.setItem('helpdesk_theme', theme);
        } catch (e) { }

        window.syncThemeUi();

        if (notify && typeof window.showToast === 'function') {
            var msg = (theme === 'dark') ? 'Dark Mode activated' : 'Light Mode activated';
            window.showToast(msg, 'info');
        }
    };

    window.toggleTheme = function () {
        var current = window.getTheme();
        var next = (current === 'dark') ? 'light' : 'dark';
        window.setTheme(next, true);
    };

    // Event Delegations for Theme Toggle
    $(document).on('click', '.btn-theme-toggle-header, #btnThemeToggleHeader, #btnThemeTogglePopover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        window.toggleTheme();
    });

    $(document).on('click', '.theme-pill-option', function (e) {
        e.preventDefault();
        var selectedTheme = $(this).data('theme');
        if (selectedTheme) {
            window.setTheme(selectedTheme, true);
        }
    });

    // Synchronize across multiple open tabs in real-time
    window.addEventListener('storage', function (e) {
        if (e.key === 'helpdesk_theme' && e.newValue) {
            window.setTheme(e.newValue, false);
        }
    });

})(window, window.jQuery);
