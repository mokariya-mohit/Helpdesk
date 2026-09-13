<?php
/**
 * @var \App\View\AppView $this
 */
$pageTitle = $this->fetch('title', 'Helpdesk - Daily Work Notepad & Update Generator');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $this->request->getAttribute('csrfToken') ?>">
    <title><?= h($pageTitle) ?></title>

    <!-- Instant Zero-Flash Theme Hydration -->
    <script>
        (function() {
            var theme = localStorage.getItem('helpdesk_theme');
            if (!theme) {
                theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
        })();
    </script>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= h($this->fetch('meta_description', 'Helpdesk is an intelligent, distraction-free daily work journal, task notepad, and daily update generator powered by CakePHP 5 & Gemini AI.')) ?>">
    <meta name="keywords" content="<?= h($this->fetch('meta_keywords', 'work log, daily update, task manager, developer journal, helpdesk, cakephp 5, gemini ai')) ?>">
    <meta name="author" content="Mohit Mokariya">
    <meta name="robots" content="index, follow">

    <!-- OpenGraph Social Sharing -->
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($this->fetch('meta_description', 'Track daily tasks, format notes into professional reports, and dispatch updates effortlessly.')) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= h($this->Url->build($this->request->getRequestTarget(), ['fullBase' => true])) ?>">
    <meta property="og:site_name" content="Helpdesk Daily Work Journal">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= h($pageTitle) ?>">
    <meta name="twitter:description" content="<?= h($this->fetch('meta_description', 'Track daily tasks, format notes into professional reports, and dispatch updates effortlessly.')) ?>">

    <!-- Canonical Link -->
    <link rel="canonical" href="<?= h($this->Url->build($this->request->getRequestTarget(), ['fullBase' => true])) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><rect width='512' height='512' rx='128' fill='%234f46e5'/><path d='M368 112H144C126.3 112 112 126.3 112 144V368C112 385.7 126.3 400 144 400H272L368 304V144C368 126.3 353.7 112 336 112H368ZM256 384V304H336L256 384Z' fill='%23faf9f5'/></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Global Application Stylesheet with Auto Cache-Busting -->
    <?php
        $cssVer = file_exists(WWW_ROOT . 'css' . DS . 'helpdesk.css') ? filemtime(WWW_ROOT . 'css' . DS . 'helpdesk.css') : time();
        $jsVer = file_exists(WWW_ROOT . 'js' . DS . 'helpdesk.js') ? filemtime(WWW_ROOT . 'js' . DS . 'helpdesk.js') : time();
    ?>
    <?= $this->Html->css('helpdesk.css?v=' . $cssVer) ?>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Global Application JavaScript Library with Auto Cache-Busting -->
    <?= $this->Html->script('helpdesk.js?v=' . $jsVer) ?>

    <script>
        window.APP_BASE = '<?= $this->Url->build('/') ?>';
        window.CSRF_TOKEN = '<?= $this->request->getAttribute('csrfToken') ?>';

        function getCsrfToken() {
            var match = document.cookie.match(new RegExp('(^| )csrfToken=([^;]+)'));
            if (match) return decodeURIComponent(match[2]);
            return window.CSRF_TOKEN;
        }

        // Setup jQuery global AJAX with CSRF token and zero-caching for fresh data
        $.ajaxSetup({
            cache: false,
            headers: {
                'X-CSRF-Token': getCsrfToken()
            }
        });

        $(document).ajaxSend(function(e, xhr, options) {
            xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
        });
    </script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <?= $this->fetch('content') ?>
</body>
</html>
