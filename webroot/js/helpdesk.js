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
        // Complete Top Shimmer Loader & Process Query/Hash Triggers
        if (window.HDMotion) {
            window.HDMotion.finishTopLoader();
            window.HDMotion.checkUrlParamsToOpenModals();
        }

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

        // Synchronize across multiple open tabs in real-time
        window.syncThemeUi();
        window.initMotionDock();
    });

    // ==========================================================================
    // 9. Classic Sun / Moon Dark & Light Theme Controller & Mode Switcher Pill
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

        // Mode Switcher Pill (Matching Image 1: Mode: Sunrise ▾ / Mode: Dark ▾)
        var $modePill = $('#btnHeaderModeToggle');
        var $modeText = $('#modeSwitcherText');
        var $modeIcon = $('#modeSwitcherIcon');
        if ($modePill.length) {
            if (isDark) {
                if ($modeText.length) $modeText.text('Mode: Dark');
                if ($modeIcon.length) $modeIcon.html('<i class="fa-solid fa-moon" style="color: #94a3b8;"></i>');
                $modePill.attr('title', 'Current: Dark Mode (Click to switch Sunrise)');
            } else {
                if ($modeText.length) $modeText.text('Mode: Sunrise');
                if ($modeIcon.length) $modeIcon.html('<i class="fa-solid fa-sun" style="color: #f59e0b;"></i>');
                $modePill.attr('title', 'Current: Sunrise Mode (Click to switch Dark)');
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
            var msg = (theme === 'dark') ? 'Dark Mode activated' : 'Sunrise Light Mode activated';
            window.showToast(msg, 'info');
        }
    };

    window.toggleTheme = function () {
        var current = window.getTheme();
        var next = (current === 'dark') ? 'light' : 'dark';
        window.setTheme(next, true);
    };

    // Event Delegations for Theme Toggle
    $(document).on('click', '.btn-theme-toggle-header, #btnThemeToggleHeader, #btnThemeTogglePopover, #btnHeaderModeToggle', function (e) {
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

    // ==========================================================================
    // 10. Motion Primitives Vertical Dock Magnification (macOS Wave Physics & Dynamic Spacing)
    // ==========================================================================
    window.initMotionDock = function () {
        var $dock = $('#motionDockSidebar');
        var $items = $dock.find('.dock-item');
        if (!$dock.length || !$items.length) return;

        var maxDistance = 110; // Radius of magnification wave influence (px)
        var maxScale = 1.40;   // Reduced by ~2.6px (rendered diameter ~61.6px vs ~64.2px)
        var maxPushX = 11;     // Clean, balanced protrusion outside dock border
        var maxSpreadY = 5;    // Subtle vertical breathing room to keep icons closely spaced

        var itemCenters = [];

        function updateRestingCenters() {
            itemCenters = [];
            $items.each(function () {
                var rect = this.getBoundingClientRect();
                itemCenters.push(rect.top + (rect.height / 2));
            });
        }

        $dock.off('mouseenter.motionDock mousemove.motionDock mouseleave.motionDock');
        $(window).off('resize.motionDock scroll.motionDock');

        $dock.on('mouseenter.motionDock', function () {
            // Measure static resting positions before transforms to avoid jitter
            $items.each(function () {
                this.style.transform = 'translate(0px, 0px) scale(1)';
            });
            updateRestingCenters();
        });

        $(window).on('resize.motionDock scroll.motionDock', function () {
            updateRestingCenters();
        });

        // Initialize centers on setup
        updateRestingCenters();

        $dock.on('mousemove.motionDock', function (e) {
            var mouseY = e.clientY;
            if (!itemCenters.length) updateRestingCenters();

            $items.each(function (i) {
                var item = this;
                var itemCenterY = itemCenters[i] || (item.getBoundingClientRect().top + item.offsetHeight / 2);
                var diffY = mouseY - itemCenterY;
                var absDiffY = Math.abs(diffY);

                if (absDiffY < maxDistance) {
                    var normDist = absDiffY / maxDistance; // 0 to 1
                    var t = 1 - normDist;

                    // Power curve: high peak on hovered icon (~1.62x), decent falloff on adjacent neighbor (~1.20x)
                    var scale = 1 + (maxScale - 1) * Math.pow(t, 2.4);

                    // Horizontal pop-out: hovered item pushes ~26px rightwards (almost 50% outside sidebar)
                    var pushX = maxPushX * Math.pow(t, 2.0);

                    // Dynamic vertical separation:
                    // Items above cursor (diffY > 0) are pushed UPWARDS (negative Y)
                    // Items below cursor (diffY < 0) are pushed DOWNWARDS (positive Y)
                    // At the peak item itself (diffY near 0), pushY is 0 so it stays centered
                    var spreadFactor = Math.sin(normDist * Math.PI);
                    var pushY = 0;
                    if (diffY > 0) {
                        pushY = -1 * maxSpreadY * spreadFactor;
                    } else if (diffY < 0) {
                        pushY = maxSpreadY * spreadFactor;
                    }

                    item.style.transform = 'translate(' + pushX.toFixed(1) + 'px, ' + pushY.toFixed(1) + 'px) scale(' + scale.toFixed(3) + ')';
                    item.style.zIndex = Math.round(scale * 100).toString();
                } else {
                    item.style.transform = 'translate(0px, 0px) scale(1)';
                    item.style.zIndex = '1';
                }
            });
        });

        $dock.on('mouseleave.motionDock', function () {
            $items.each(function () {
                this.style.transform = 'translate(0px, 0px) scale(1)';
                this.style.zIndex = '1';
            });
        });
    };

    // ==========================================================================
    // 11. HDMotion: Linear/Apple Top Progress Bar & Smooth Motion Architecture
    // ==========================================================================
    var HDMotion = {
        loaderTimer: null,
        loaderWidth: 0,

        startTopLoader: function () {
            var $loader = $('#hdGlobalProgressBar');
            if (!$loader.length) {
                $loader = $('<div id="hdGlobalProgressBar" class="hd-top-loader" aria-hidden="true"></div>');
                $('body').prepend($loader);
            }
            if (this.loaderTimer) {
                clearInterval(this.loaderTimer);
            }
            $loader.removeClass('hd-page-exiting').addClass('active').css({ width: '0%', opacity: 1 });
            this.loaderWidth = 12;
            $loader.css('width', this.loaderWidth + '%');

            var self = this;
            this.loaderTimer = setInterval(function () {
                if (self.loaderWidth < 80) {
                    self.loaderWidth += (80 - self.loaderWidth) * 0.15 + 1;
                    $loader.css('width', self.loaderWidth + '%');
                } else if (self.loaderWidth < 92) {
                    self.loaderWidth += 0.5;
                    $loader.css('width', self.loaderWidth + '%');
                }
            }, 60);
        },

        finishTopLoader: function () {
            var $loader = $('#hdGlobalProgressBar');
            if (this.loaderTimer) {
                clearInterval(this.loaderTimer);
                this.loaderTimer = null;
            }
            if ($loader.length && $loader.hasClass('active')) {
                $loader.css({ width: '100%', opacity: 1 });
                setTimeout(function () {
                    $loader.css('opacity', 0);
                    setTimeout(function () {
                        $loader.removeClass('active').css({ width: '0%' });
                    }, 250);
                }, 180);
            }
        },

        smoothNavigate: function (targetUrl) {
            if (!targetUrl || targetUrl === '#' || targetUrl.indexOf('javascript:') === 0) {
                return;
            }

            var currentPath = window.location.pathname + window.location.search;
            var targetPath = targetUrl.replace(window.location.origin, '');
            if (targetPath.indexOf('#') === 0) {
                window.location.hash = targetPath;
                return;
            }

            this.startTopLoader();
            $('body').addClass('hd-page-exiting');

            setTimeout(function () {
                window.location.href = targetUrl;
            }, 140);
        },

        checkUrlParamsToOpenModals: function () {
            var search = window.location.search || '';
            var hash = window.location.hash || '';

            if (search.indexOf('open=projects') !== -1 || hash === '#projects') {
                if (window.HDModals && typeof window.HDModals.openProjects === 'function') {
                    window.HDModals.openProjects();
                } else {
                    $('#manageProjectModal').addClass('active');
                }
            } else if (search.indexOf('open=clients') !== -1 || hash === '#clients') {
                if (window.HDModals && typeof window.HDModals.openClients === 'function') {
                    window.HDModals.openClients();
                } else {
                    $('#manageClientModal').addClass('active');
                }
            } else if (search.indexOf('open=settings') !== -1 || hash === '#settings') {
                if (window.HDModals && typeof window.HDModals.openSettings === 'function') {
                    window.HDModals.openSettings();
                } else {
                    $('#settingsModal').addClass('active');
                }
            } else if (search.indexOf('open=profile') !== -1 || hash === '#profile') {
                if (window.HDModals && typeof window.HDModals.openProfile === 'function') {
                    window.HDModals.openProfile();
                } else {
                    $('#userProfileModal').addClass('active');
                }
            } else if (search.indexOf('open=worklogs') !== -1 || hash === '#worklogs') {
                var $w = $('#workLogsDrawer');
                if ($w.length) {
                    setTimeout(function () {
                        $w.addClass('open');
                    }, 120);
                }
            }
        }
    };
    window.HDMotion = HDMotion;

    // Work Logs Drawer Slide In/Out with Motion Primitives
    $(document).on('click', '#btnDockWorkLogs', function (e) {
        if ($('#workLogsDrawer').length) {
            e.preventDefault();
            $('#workLogsDrawer').toggleClass('open');
        } else if ($(this).is('a')) {
            e.preventDefault();
            HDMotion.smoothNavigate($(this).attr('href'));
        } else {
            HDMotion.smoothNavigate(window.APP_BASE + '?open=worklogs');
        }
    });

    $(document).on('click', '#btnCloseWorkLogsDrawer', function (e) {
        e.preventDefault();
        $('#workLogsDrawer').removeClass('open');
    });

    // Top Header Global Search Transfer
    $(document).on('click', '#topHeaderSearchBox, #topHeaderSearchInput', function (e) {
        e.preventDefault();
        var $searchModal = $('#globalSearchModal');
        if ($searchModal.length) {
            $searchModal.addClass('active');
            var val = $('#topHeaderSearchInput').val();
            if (val) {
                $('#globalSearchInput').val(val).trigger('input');
            }
            setTimeout(function () {
                $('#globalSearchInput').focus();
            }, 100);
        }
    });

    // Dock Projects & Clients Triggers
    $(document).on('click', '#btnDockProjects', function (e) {
        e.preventDefault();
        if (window.HDModals && typeof window.HDModals.openProjects === 'function') {
            window.HDModals.openProjects();
        } else {
            $('#manageProjectModal').addClass('active');
            $('#newProjectInput').val('').focus();
        }
    });
    $(document).on('click', '#btnDockClients', function (e) {
        e.preventDefault();
        if (window.HDModals && typeof window.HDModals.openClients === 'function') {
            window.HDModals.openClients();
        } else {
            $('#manageClientModal').addClass('active');
            $('#newClientInput').val('').focus();
        }
    });

    // Dock Team Chat Navigation & Global Badge Synchronization
    $(document).on('click', '#btnDockChat', function (e) {
        var href = $(this).attr('href') || (window.APP_BASE + 'messages');
        e.preventDefault();
        HDMotion.smoothNavigate(href);
    });

    // Global Internal Navigation Links Auto-Intercept
    $(document).on('click', 'a[href]', function (e) {
        var href = $(this).attr('href');
        var target = $(this).attr('target');
        if (!href || href === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
            return;
        }
        if (target && target === '_blank') {
            return;
        }
        if ($(this).attr('download') !== undefined) {
            return;
        }
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.which === 2) {
            return; // Allow opening in new tab
        }

        var isInternal = false;
        try {
            var urlObj = new URL(href, window.location.href);
            if (urlObj.origin === window.location.origin) {
                if (urlObj.pathname === window.location.pathname && urlObj.search === window.location.search && urlObj.hash) {
                    return; // same page hash
                }
                isInternal = true;
            }
        } catch (err) {
            if (href.indexOf('/') === 0 || href.indexOf('./') === 0 || href.indexOf('?') === 0) {
                isInternal = true;
            }
        }

        if (isInternal) {
            e.preventDefault();
            HDMotion.smoothNavigate(href);
        }
    });

    // Browser bfcache restore handler
    window.addEventListener('pageshow', function () {
        $('body').removeClass('hd-page-exiting');
        if (window.HDMotion) {
            window.HDMotion.finishTopLoader();
        }
    });

    // =========================================================================
    // Native Desktop / Web Notifications & Microsoft Teams Sound System
    // =========================================================================
    var hdNotificationSoundEnabled = true;

    function playNotificationSound() {
        if (!hdNotificationSoundEnabled) return;
        try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            var ctx = new AudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }
            var now = ctx.currentTime;

            // Dual-tone harmonic chime (Microsoft Teams inspired)
            // Tone 1: D5 (587.33 Hz)
            var osc1 = ctx.createOscillator();
            var gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0.18, now);
            gain1.gain.exponentialRampToValueAtTime(0.0001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Tone 2: A5 (880 Hz) staggered chime
            var osc2 = ctx.createOscillator();
            var gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, now + 0.11);
            gain2.gain.setValueAtTime(0.22, now + 0.11);
            gain2.gain.exponentialRampToValueAtTime(0.0001, now + 0.55);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.11);
            osc2.stop(now + 0.55);
        } catch (e) {
            // Audio context not allowed or not supported
        }
    }
    window.playNotificationSound = playNotificationSound;

    function requestDesktopNotificationPermission(callback) {
        if (!('Notification' in window)) {
            if (callback) callback(false);
            return;
        }
        if (Notification.permission === 'granted') {
            if (callback) callback(true);
            return;
        }
        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function (permission) {
                var granted = (permission === 'granted');
                if (granted) {
                    playNotificationSound();
                    $('#desktopNotifPrompt').fadeOut(250, function () { $(this).remove(); });
                    if (typeof window.showToast === 'function') {
                        window.showToast('Desktop notifications enabled!', 'success');
                    }
                }
                if (callback) callback(granted);
            }).catch(function () {
                if (callback) callback(false);
            });
        } else {
            if (callback) callback(false);
        }
    }
    window.requestDesktopNotificationPermission = requestDesktopNotificationPermission;

    function showDesktopNotification(title, options, clickCallback) {
        // Disabled per user preference: only the custom Helpdesk in-app popup notification is displayed (no Chrome OS native notification)
        return null;
    }
    window.showDesktopNotification = showDesktopNotification;

    // Microsoft Teams-Style In-App Helpdesk Toast with Inline Quick Reply
    function showInAppTeamsToast(msgData) {
        if (!msgData) return;

        // Dismiss any existing toast
        $('.hd-teams-toast').remove();

        var avatar = msgData.sender_avatar || (window.APP_BASE + 'webroot/img/avatar-male.svg');
        var name = $('<div>').text(msgData.sender_name || 'Teammate').html();
        var preview = $('<div>').text(msgData.message || (msgData.attachment_name ? '📎 ' + msgData.attachment_name : 'Sent a message')).html();
        var isRequest = !msgData.conversation_id || msgData.is_request;

        var toastHtml = '' +
            '<div class="hd-teams-toast" data-conv-id="' + (msgData.conversation_id || 0) + '" data-is-req="' + (isRequest ? '1' : '0') + '">' +
            '  <div class="hd-teams-toast-header">' +
            '    <div class="hd-teams-toast-brand">' +
            '      <span class="hd-teams-brand-badge"><i class="fa-solid fa-headset"></i> HELPDESK</span>' +
            '      <span class="hd-teams-channel">&bull; ' + (isRequest ? 'Chat Request' : 'Team Chat') + '</span>' +
            '    </div>' +
            '    <button type="button" class="btn-hd-teams-close" title="Dismiss"><i class="fa-solid fa-xmark"></i></button>' +
            '  </div>' +
            '  <div class="hd-teams-toast-body">' +
            '    <img src="' + avatar + '" class="hd-teams-toast-avatar" alt="' + name + '">' +
            '    <div class="hd-teams-toast-details">' +
            '      <div class="hd-teams-toast-sender">' + name + '</div>' +
            '      <div class="hd-teams-toast-snippet">' + preview + '</div>' +
            '    </div>' +
            '  </div>';

        if (!isRequest) {
            toastHtml += '' +
                '  <div class="hd-teams-toast-reply-box">' +
                '    <input type="text" class="hd-teams-reply-input" placeholder="Type a reply..." maxlength="4000" />' +
                '    <button type="button" class="btn-hd-teams-send" title="Send Reply"><i class="fa-solid fa-paper-plane"></i></button>' +
                '  </div>';
        } else {
            toastHtml += '' +
                '  <div style="padding: 6px 14px 12px; display: flex; justify-content: flex-end;">' +
                '    <button type="button" class="btn-hd-teams-view-req" style="padding: 6px 14px; background: var(--primary, #6366f1); color: #fff; border: none; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">View Request</button>' +
                '  </div>';
        }

        toastHtml += '</div>';

        var $toast = $(toastHtml);
        $('body').append($toast);

        var autoDismissTimer = setTimeout(function () {
            $toast.fadeOut(300, function () { $(this).remove(); });
        }, 12000);

        $toast.on('mouseenter', function () {
            clearTimeout(autoDismissTimer);
        });

        $toast.on('mouseleave', function () {
            if (!$toast.find('.hd-teams-reply-input').is(':focus')) {
                autoDismissTimer = setTimeout(function () {
                    $toast.fadeOut(300, function () { $(this).remove(); });
                }, 6000);
            }
        });
    }
    window.showInAppTeamsToast = showInAppTeamsToast;

    // Send Quick Reply directly from Toast
    function executeTeamsQuickReply($toast) {
        var convId = $toast.data('conv-id');
        var $input = $toast.find('.hd-teams-reply-input');
        var $btn = $toast.find('.btn-hd-teams-send');
        var text = ($input.val() || '').trim();
        if (!text || !convId) return;

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        $input.prop('disabled', true);

        $.ajax({
            url: window.APP_BASE + 'messages/post-msg',
            type: 'POST',
            data: {
                conversation_id: convId,
                message: text
            },
            dataType: 'json',
            success: function (res) {
                if (res && res.success) {
                    $toast.find('.hd-teams-toast-reply-box').html('<div class="hd-teams-reply-success"><i class="fa-solid fa-circle-check"></i> Reply sent!</div>');
                    if (typeof window.showToast === 'function') {
                        window.showToast('Reply sent successfully', 'success');
                    }
                    setTimeout(function () {
                        $toast.fadeOut(300, function () { $(this).remove(); });
                    }, 1200);

                    if (typeof window.appendIncomingMessage === 'function') {
                        window.appendIncomingMessage(res.message);
                    }
                } else {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i>');
                    $input.prop('disabled', false);
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message || 'Could not send reply', 'error');
                    }
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i>');
                $input.prop('disabled', false);
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to send reply';
                if (typeof window.showToast === 'function') {
                    window.showToast(err, 'error');
                }
            }
        });
    }

    $(document).on('click', '.btn-hd-teams-send', function (e) {
        e.stopPropagation();
        var $toast = $(this).closest('.hd-teams-toast');
        executeTeamsQuickReply($toast);
    });

    $(document).on('keydown', '.hd-teams-reply-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            var $toast = $(this).closest('.hd-teams-toast');
            executeTeamsQuickReply($toast);
        }
    });

    $(document).on('click', '.btn-hd-teams-close', function (e) {
        e.stopPropagation();
        $(this).closest('.hd-teams-toast').fadeOut(200, function () { $(this).remove(); });
    });

    $(document).on('click', '.hd-teams-toast', function (e) {
        if ($(e.target).closest('.hd-teams-toast-reply-box, .btn-hd-teams-close, .btn-hd-teams-view-req').length) return;
        var isReq = $(this).data('is-req') == 1;
        var convId = $(this).data('conv-id');
        if (isReq || !convId) {
            window.location.href = window.APP_BASE + 'messages?tab=requests';
        } else {
            window.location.href = window.APP_BASE + 'messages?c=' + convId;
        }
    });

    $(document).on('click', '.btn-hd-teams-view-req', function (e) {
        e.stopPropagation();
        window.location.href = window.APP_BASE + 'messages?tab=requests';
    });

    function checkAndShowNotifPrompt() {
        // Disabled per user preference: user only wants the Helpdesk in-app popup (no Chrome native permission prompt)
    }

    var hasInitializedNotifBaseline = false;

    function updateGlobalNotifications() {
        if (!window.APP_BASE) return;
        $.ajax({
            url: window.APP_BASE + 'messages/get-badge',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res && res.success) {
                    var total = parseInt(res.total, 10) || 0;
                    var $headerBadge = $('#headerNotificationBadge');
                    if (total > 0) {
                        $headerBadge.text(total > 99 ? '99+' : total)
                            .attr('data-count', total)
                            .removeClass('d-none')
                            .addClass('show')
                            .css('display', 'inline-flex');
                    } else {
                        $headerBadge.empty()
                            .attr('data-count', '0')
                            .removeClass('show')
                            .addClass('d-none')
                            .css('display', 'none');
                    }
                    // Count should ONLY show on top header, NOT on left dock
                    $('#dockChatBadge').empty().attr('data-count', '0').removeClass('show').addClass('d-none').css('display', 'none');

                    // Scoped storage per user to prevent cross-account baseline overwrites during multi-window testing
                    var userId = res.user_id || 0;
                    var storageKeyMsg = 'hd_last_msg_' + userId;
                    var storageKeyReq = 'hd_last_req_' + userId;
                    var lastNotifiedMsgId = parseInt(sessionStorage.getItem(storageKeyMsg) || '0', 10);
                    var lastNotifiedReqId = parseInt(sessionStorage.getItem(storageKeyReq) || '0', 10);

                    // Helpdesk Toast & Audio Chime Handling
                    if (!hasInitializedNotifBaseline) {
                        // First load: baseline current IDs so we don't alert for existing messages
                        hasInitializedNotifBaseline = true;
                        if (res.latest_unread_msg && res.latest_unread_msg.id) {
                            if (lastNotifiedMsgId === 0) {
                                lastNotifiedMsgId = res.latest_unread_msg.id;
                                sessionStorage.setItem(storageKeyMsg, lastNotifiedMsgId);
                            }
                        }
                        if (res.latest_pending_request && res.latest_pending_request.id) {
                            if (lastNotifiedReqId === 0) {
                                lastNotifiedReqId = res.latest_pending_request.id;
                                sessionStorage.setItem(storageKeyReq, lastNotifiedReqId);
                            }
                        }
                    } else {
                        // New Incoming Message Notification -> Show ONLY Helpdesk in-app toast
                        if (res.latest_unread_msg && res.latest_unread_msg.id > lastNotifiedMsgId) {
                            lastNotifiedMsgId = res.latest_unread_msg.id;
                            sessionStorage.setItem(storageKeyMsg, lastNotifiedMsgId);

                            playNotificationSound();

                            // Show In-App Helpdesk Toast with Quick Reply (NO Chrome OS notification)
                            showInAppTeamsToast(res.latest_unread_msg);
                        }

                        // New Chat Request Notification -> Show ONLY Helpdesk in-app toast
                        if (res.latest_pending_request && res.latest_pending_request.id > lastNotifiedReqId) {
                            lastNotifiedReqId = res.latest_pending_request.id;
                            sessionStorage.setItem(storageKeyReq, lastNotifiedReqId);

                            playNotificationSound();

                            // Show In-App Helpdesk Toast for Chat Request (NO Chrome OS notification)
                            showInAppTeamsToast({
                                conversation_id: 0,
                                is_request: true,
                                sender_name: res.latest_pending_request.sender_name,
                                sender_avatar: res.latest_pending_request.sender_avatar,
                                message: res.latest_pending_request.sender_name + ' sent you a chat request.',
                            });
                        }
                    }
                }
            }
        });
    }

    window.updateGlobalNotifications = updateGlobalNotifications;

    $(function () {
        updateGlobalNotifications();
        checkAndShowNotifPrompt();

        // Background polling across all non-chat pages (every 3 seconds, regardless of tab visibility)
        if (!window.location.pathname.includes('/messages')) {
            setInterval(function () {
                updateGlobalNotifications();
            }, 3000);
        }
    });

    // Header Notification: Click opens chat or alerts
    $(document).on('click', '#btnHeaderNotification', function (e) {
        e.preventDefault();
        var count = parseInt($('#headerNotificationBadge').text(), 10) || 0;
        if (!window.location.pathname.includes('/messages')) {
            if (count > 0) {
                window.location.href = window.APP_BASE + 'messages';
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast('No unread notifications at this time.', 'info');
                }
            }
        } else {
            var reqCount = parseInt($('#requestsTabBadge').text(), 10) || 0;
            if (reqCount > 0) {
                $('#tabBtnRequests').click();
            } else if (count > 0) {
                $('#tabBtnChats').click();
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast('No unread notifications at this time.', 'info');
                }
            }
        }
    });

    // Dock Logout with Custom Confirmation Modal
    $(document).on('click', '#btnDockLogout', function (e) {
        e.preventDefault();
        var logoutUrl = $(this).data('url') || (window.APP_BASE + 'logout');
        window.showConfirmModal({
            title: 'Sign Out Session?',
            message: 'Are you sure you want to log out of your Helpdesk account?',
            type: 'warning',
            icon: 'fa-solid fa-arrow-right-from-bracket',
            confirmText: 'Sign Out',
            cancelText: 'Stay Logged In',
            onConfirm: function () {
                window.location.href = logoutUrl;
            }
        });
    });

    // ==========================================
    // Global Modal Controller (HDModals)
    // Works uniformly across all pages (Tasks, Chats, Daily Updates)
    // ==========================================
    var HDModals = {
        openSettings: function () {
            var $m = $('#settingsModal');
            if (!$m.length) return;
            $('#settingsAlert').hide().text('').removeAttr('style');
            $('.settings-tab-btn').removeClass('active');
            $('.settings-tab-btn[data-tab="geminiTab"]').addClass('active');
            $('.settings-tab-pane').removeClass('active').hide();
            $('#geminiTab').addClass('active').show();

            $.ajax({
                url: window.APP_BASE + 'users/get-profile',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res && res.success && res.user) {
                        var u = res.user;
                        $('#settingsGeminiKey').val(u.api_key || '');
                        $('#settingsSmtpPass').val(u.smtp_password || '');
                        if (u.api_key) {
                            $('#settingsGeminiStatus').html('<span style="color:#15803d;"><i class="fa-solid fa-circle-check"></i> Configured (Google Gemini)</span>');
                        } else {
                            $('#settingsGeminiStatus').html('<span style="color:#d97706;"><i class="fa-solid fa-triangle-exclamation"></i> Not Configured</span>');
                        }
                        if (u.smtp_password) {
                            $('#settingsSmtpStatus').html('<span style="color:#15803d;"><i class="fa-solid fa-circle-check"></i> Configured</span>');
                        } else {
                            $('#settingsSmtpStatus').html('<span style="color:#d97706;"><i class="fa-solid fa-triangle-exclamation"></i> Not Configured</span>');
                        }
                    }
                }
            });
            $m.addClass('active');
        },

        closeSettings: function () {
            $('#settingsModal').removeClass('active');
        },

        openProfile: function () {
            var $m = $('#userProfileModal');
            if (!$m.length) return;
            $('#profileAlert').hide().text('').removeAttr('style');
            $('#profCurrentPass, #profNewPass, #profConfirmPass').val('');

            $.ajax({
                url: window.APP_BASE + 'users/get-profile',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res && res.success && res.user) {
                        var u = res.user;
                        $('#profInputName').val(u.name || '');
                        $('#profInputEmail').val(u.email || '');
                        $('#profileBannerName').text(u.name || 'User');
                        $('#profileBannerEmail').text(u.email || '');
                        $('#profileMemberSince').text(u.created_formatted || '-');

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

                        var gIcon = gender === 'female' ? '<i class="fa-solid fa-venus" style="color:#ec4899; margin-right:4px;"></i>' : '<i class="fa-solid fa-mars" style="color:#3b82f6; margin-right:4px;"></i>';
                        $('#profileGenderBadge').html(gIcon + '<span id="profileGenderText">' + (gender.charAt(0).toUpperCase() + gender.slice(1)) + '</span>');

                        if (u.avatar_url) {
                            $('#profileBigAvatarImg').attr('src', u.avatar_url).show();
                            $('#profileAvatarBig .profile-avatar-fallback').hide();
                        }
                    }
                }
            });
            $m.addClass('active');
        },

        closeProfile: function () {
            $('#userProfileModal').removeClass('active');
        },

        openProjects: function () {
            var $m = $('#manageProjectModal');
            if (!$m.length) return;
            $m.addClass('active');
            $('#newProjectInput').val('').removeClass('is-invalid').focus();
            $('#newProjectNameError').hide();
            HDModals.loadProjects();
        },

        closeProjects: function () {
            $('#manageProjectModal').removeClass('active');
        },

        loadProjects: function () {
            var $list = $('#modalProjectList');
            if (!$list.length) return;
            $list.html('<div style="text-align:center; padding:16px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Loading projects...</div>');

            $.ajax({
                url: window.APP_BASE + 'projects/get-projects',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res && res.success && res.projects) {
                        var html = '';
                        if (res.projects.length === 0) {
                            html = '<div style="text-align:center; padding:16px; color:var(--text-muted); font-size:12px;">No projects created yet.</div>';
                        } else {
                            res.projects.forEach(function (p) {
                                var safeName = $('<div>').text(p.name).html();
                                html += '<div class="modal-project-item" data-id="' + p.id + '" data-name="' + safeName + '">';
                                html += '  <div class="proj-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%;">';
                                html += '    <div style="display:flex; align-items:center; gap:8px;">';
                                html += '      <i class="fa-solid fa-folder" style="color:#6366f1;"></i>';
                                html += '      <span class="proj-item-name" style="font-weight:600; font-size:13px;">' + safeName + '</span>';
                                if (p.is_default) {
                                    html += '      <span style="font-size:10px; background:rgba(99,102,241,0.15); color:#6366f1; padding:2px 6px; border-radius:4px; font-weight:700;">DEFAULT</span>';
                                }
                                html += '    </div>';
                                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                                html += '      <button type="button" class="btn-start-edit-proj" data-id="' + p.id + '" title="Rename Project" style="background:none; border:none; color:var(--text-muted); cursor:pointer; padding:4px 6px;"><i class="fa-solid fa-pen-to-square"></i></button>';
                                html += '      <button type="button" class="btn-delete-proj" data-id="' + p.id + '" title="Delete Project" style="background:none; border:none; color:#ef4444; cursor:pointer; padding:4px 6px;"><i class="fa-solid fa-trash-can"></i></button>';
                                html += '    </div>';
                                html += '  </div>';
                                html += '  <div class="proj-edit-mode" style="display:none; align-items:center; gap:8px; width:100%;">';
                                html += '    <input type="text" class="proj-inline-input modal-form-input" value="' + safeName + '" style="flex:1; padding:6px 10px; font-size:12px;">';
                                html += '    <button type="button" class="btn-save-inline-proj btn btn-primary" data-id="' + p.id + '" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-check"></i></button>';
                                html += '    <button type="button" class="btn-cancel-inline-proj" style="padding:6px 10px; font-size:12px; background:none; border:none; color:var(--text-muted); cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>';
                                html += '  </div>';
                                html += '</div>';
                            });
                        }
                        $list.html(html);
                    }
                }
            });
        },

        openClients: function () {
            var $m = $('#manageClientModal');
            if (!$m.length) return;
            $m.addClass('active');
            $('#newClientInput').val('').removeClass('is-invalid').focus();
            $('#newClientEmailInput').val('').removeClass('is-invalid');
            $('#newClientNameError, #newClientEmailError').hide();
            HDModals.loadClients();
        },

        closeClients: function () {
            $('#manageClientModal').removeClass('active');
        },

        loadClients: function () {
            var $list = $('#modalClientList');
            if (!$list.length) return;
            $list.html('<div style="text-align:center; padding:16px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Loading clients...</div>');

            $.ajax({
                url: window.APP_BASE + 'clients/get-clients',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res && res.success && res.clients) {
                        var html = '';
                        if (res.clients.length === 0) {
                            html = '<div style="text-align:center; padding:16px; color:var(--text-muted); font-size:12px;">No clients created yet.</div>';
                        } else {
                            res.clients.forEach(function (c) {
                                var safeName = $('<div>').text(c.name).html();
                                var safeEmail = c.email ? $('<div>').text(c.email).html() : '';
                                html += '<div class="modal-project-item" data-id="' + c.id + '" data-name="' + safeName + '" data-email="' + safeEmail + '">';
                                html += '  <div class="client-view-mode" style="display:flex; align-items:center; justify-content:space-between; width:100%;">';
                                html += '    <div style="display:flex; flex-direction:column; gap:2px;">';
                                html += '      <div style="display:flex; align-items:center; gap:8px;">';
                                html += '        <i class="fa-solid fa-user-tie" style="color:#0284c7;"></i>';
                                html += '        <span class="client-item-name" style="font-weight:600; font-size:13px;">' + safeName + '</span>';
                                if (c.is_default) {
                                    html += '        <span style="font-size:10px; background:rgba(2,132,199,0.15); color:#0284c7; padding:2px 6px; border-radius:4px; font-weight:700;">DEFAULT</span>';
                                }
                                html += '      </div>';
                                if (safeEmail) {
                                    html += '      <span style="font-size:11px; color:var(--text-muted); margin-left:22px;"><i class="fa-regular fa-envelope"></i> ' + safeEmail + '</span>';
                                }
                                html += '    </div>';
                                html += '    <div style="display:flex; align-items:center; gap:6px;">';
                                html += '      <button type="button" class="btn-start-edit-client" data-id="' + c.id + '" title="Edit Client" style="background:none; border:none; color:var(--text-muted); cursor:pointer; padding:4px 6px;"><i class="fa-solid fa-pen-to-square"></i></button>';
                                html += '      <button type="button" class="btn-delete-client" data-id="' + c.id + '" title="Delete Client" style="background:none; border:none; color:#ef4444; cursor:pointer; padding:4px 6px;"><i class="fa-solid fa-trash-can"></i></button>';
                                html += '    </div>';
                                html += '  </div>';
                                html += '  <div class="client-edit-mode" style="display:none; align-items:center; gap:8px; width:100%;">';
                                html += '    <input type="text" class="client-inline-name-input modal-form-input" value="' + safeName + '" placeholder="Client Name" style="flex:1; padding:6px 10px; font-size:12px;">';
                                html += '    <input type="email" class="client-inline-email-input modal-form-input" value="' + safeEmail + '" placeholder="Email (optional)" style="flex:1; padding:6px 10px; font-size:12px;">';
                                html += '    <button type="button" class="btn-save-inline-client btn btn-primary" data-id="' + c.id + '" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-check"></i></button>';
                                html += '    <button type="button" class="btn-cancel-inline-client" style="padding:6px 10px; font-size:12px; background:none; border:none; color:var(--text-muted); cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>';
                                html += '  </div>';
                                html += '</div>';
                            });
                        }
                        $list.html(html);
                    }
                }
            });
        }
    };
    window.HDModals = HDModals;

    // Global Modal Open & Close Event Bindings
    $(document).on('click', '#btnHeaderSettings', function (e) {
        e.preventDefault();
        HDModals.openSettings();
    });

    $(document).on('click', '#btnCloseSettingsModal, #btnCancelSettingsModal', function (e) {
        e.preventDefault();
        HDModals.closeSettings();
    });

    $(document).on('click', '#btnSaveSettingsModal', function (e) {
        e.preventDefault();
        var apiKey = $('#settingsGeminiKey').val().trim();
        var smtpPass = $('#settingsSmtpPass').val().trim();
        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: window.APP_BASE + 'users/save-settings',
            type: 'POST',
            data: { api_key: apiKey, smtp_password: smtpPass },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    setTimeout(function () { HDModals.closeSettings(); }, 600);
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Failed to save settings', 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                if (typeof window.showToast === 'function') window.showToast('Network error while saving settings', 'error');
            }
        });
    });

    $(document).on('click', '#btnHeaderProfile', function (e) {
        e.preventDefault();
        HDModals.openProfile();
    });

    $(document).on('click', '#btnCloseProfileModal, #btnCancelProfileModal', function (e) {
        e.preventDefault();
        HDModals.closeProfile();
    });

    $(document).on('click', '#btnSaveProfileModal', function (e) {
        e.preventDefault();
        var name = $('#profInputName').val().trim();
        var email = $('#profInputEmail').val().trim();
        var gender = $('input[name="profGender"]:checked').val() || 'male';
        var currentPass = $('#profCurrentPass').val();
        var newPass = $('#profNewPass').val();
        var confirmPass = $('#profConfirmPass').val();

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
                current_password: currentPass,
                new_password: newPass,
                confirm_password: confirmPass
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    if (res.user && res.user.avatar_url) {
                        $('#headerProfileAvatarImg').attr('src', res.user.avatar_url).show();
                        $('#btnHeaderProfile .header-user-avatar-fallback').hide();
                    }
                    setTimeout(function () { HDModals.closeProfile(); }, 600);
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Failed to update profile', 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                if (typeof window.showToast === 'function') window.showToast('Network error while updating profile', 'error');
            }
        });
    });

    $(document).on('click', '#btnCloseProjectModal', function (e) {
        e.preventDefault();
        HDModals.closeProjects();
    });

    $(document).on('click', '#btnSaveNewProject', function (e) {
        e.preventDefault();
        var name = $('#newProjectInput').val().trim();
        if (!name) {
            $('#newProjectInput').addClass('is-invalid').focus();
            $('#newProjectNameError').show().find('span').text('Please enter a project name.');
            if (typeof window.showToast === 'function') window.showToast('Project name is required', 'warning');
            return;
        }

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.APP_BASE + 'projects/add',
            type: 'POST',
            data: { name: name },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    $('#newProjectInput').val('');
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    HDModals.loadProjects();
                    if (typeof window.loadProjects === 'function') window.loadProjects();
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Failed to add project', 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                if (typeof window.showToast === 'function') window.showToast('Network error while adding project', 'error');
            }
        });
    });

    $(document).on('click', '#btnCloseClientModal', function (e) {
        e.preventDefault();
        HDModals.closeClients();
    });

    $(document).on('click', '#btnSaveNewClient', function (e) {
        e.preventDefault();
        var name = $('#newClientInput').val().trim();
        var email = $('#newClientEmailInput').val().trim();
        if (!name) {
            $('#newClientInput').addClass('is-invalid').focus();
            $('#newClientNameError').show().find('span').text('Client name cannot be empty.');
            if (typeof window.showToast === 'function') window.showToast('Client name is required', 'warning');
            return;
        }

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.APP_BASE + 'clients/add',
            type: 'POST',
            data: { name: name, email: email },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    $('#newClientInput').val('');
                    $('#newClientEmailInput').val('');
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    HDModals.loadClients();
                    if (typeof window.loadClients === 'function') window.loadClients();
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Failed to add client', 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                if (typeof window.showToast === 'function') window.showToast('Network error while adding client', 'error');
            }
        });
    });

    // Inline edit / delete handlers for projects
    $(document).on('click', '.btn-start-edit-proj', function () {
        var row = $(this).closest('.modal-project-item');
        row.find('.proj-view-mode').hide();
        row.find('.proj-edit-mode').css('display', 'flex').find('.proj-inline-input').focus().select();
    });

    $(document).on('click', '.btn-cancel-inline-proj', function () {
        var row = $(this).closest('.modal-project-item');
        row.find('.proj-edit-mode').hide();
        row.find('.proj-view-mode').css('display', 'flex');
    });

    $(document).on('click', '.btn-save-inline-proj', function () {
        var row = $(this).closest('.modal-project-item');
        var id = $(this).data('id');
        var newName = row.find('.proj-inline-input').val().trim();
        if (!newName) return;

        $.ajax({
            url: window.APP_BASE + 'projects/edit/' + id,
            type: 'POST',
            data: { name: newName },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    HDModals.loadProjects();
                    if (typeof window.loadProjects === 'function') window.loadProjects();
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Could not rename project', 'error');
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-proj', function () {
        var id = $(this).data('id');
        var row = $(this).closest('.modal-project-item');
        var name = row.data('name') || 'this project';

        window.showConfirmModal({
            title: 'Delete Project?',
            message: 'Are you sure you want to delete "' + name + '"? This will unassign tasks from this project.',
            type: 'danger',
            confirmText: 'Delete',
            onConfirm: function () {
                $.ajax({
                    url: window.APP_BASE + 'projects/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                            HDModals.loadProjects();
                            if (typeof window.loadProjects === 'function') window.loadProjects();
                        } else {
                            if (typeof window.showToast === 'function') window.showToast(res.message || 'Could not delete project', 'error');
                        }
                    }
                });
            }
        });
    });

    // Inline edit / delete handlers for clients
    $(document).on('click', '.btn-start-edit-client', function () {
        var row = $(this).closest('.modal-project-item');
        row.find('.client-view-mode').hide();
        row.find('.client-edit-mode').css('display', 'flex').find('.client-inline-name-input').focus().select();
    });

    $(document).on('click', '.btn-cancel-inline-client', function () {
        var row = $(this).closest('.modal-project-item');
        row.find('.client-edit-mode').hide();
        row.find('.client-view-mode').css('display', 'flex');
    });

    $(document).on('click', '.btn-save-inline-client', function () {
        var row = $(this).closest('.modal-project-item');
        var id = $(this).data('id');
        var newName = row.find('.client-inline-name-input').val().trim();
        var newEmail = row.find('.client-inline-email-input').val().trim();
        if (!newName) return;

        $.ajax({
            url: window.APP_BASE + 'clients/edit/' + id,
            type: 'POST',
            data: { name: newName, email: newEmail },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                    HDModals.loadClients();
                    if (typeof window.loadClients === 'function') window.loadClients();
                } else {
                    if (typeof window.showToast === 'function') window.showToast(res.message || 'Could not update client', 'error');
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-client', function () {
        var id = $(this).data('id');
        var row = $(this).closest('.modal-project-item');
        var name = row.data('name') || 'this client';

        window.showConfirmModal({
            title: 'Delete Client?',
            message: 'Are you sure you want to delete "' + name + '"?',
            type: 'danger',
            confirmText: 'Delete',
            onConfirm: function () {
                $.ajax({
                    url: window.APP_BASE + 'clients/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            if (typeof window.showToast === 'function') window.showToast(res.message, 'success');
                            HDModals.loadClients();
                            if (typeof window.loadClients === 'function') window.loadClients();
                        } else {
                            if (typeof window.showToast === 'function') window.showToast(res.message || 'Could not delete client', 'error');
                        }
                    }
                });
            }
        });
    });

    // Close on overlay backdrop click or Escape key
    $(document).on('click', '.modal-overlay', function (e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $('.modal-overlay.active').removeClass('active');
        }
    });

})(window, window.jQuery);

