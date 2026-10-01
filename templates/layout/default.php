<?php
/**
 * @var \App\View\AppView $this
 */
$pageTitle = $this->fetch('title', "Today's Work Log & Tasks Notepad - Helpdesk");
$metaDesc = $this->fetch('meta_description', 'Keep track of daily development progress, manage clients, organize project tasks, and generate formatted notes with Helpdesk.');
$metaKeywords = $this->fetch('meta_keywords', 'work log, daily task notepad, software developer journal, task manager, helpdesk');
$canonicalUrl = $this->Url->build($this->request->getRequestTarget(), ['fullBase' => true]);
if (!str_contains($canonicalUrl, 'localhost') && !str_contains($canonicalUrl, '127.0.0.1')) {
    $canonicalUrl = preg_replace('/^http:/i', 'https:', $canonicalUrl);
}
?><!DOCTYPE html>
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

    <!-- Preconnect & DNS-Prefetch for Protocol Speed & Resource Optimization -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <!-- Favicon (Physical icon + shortcut for standard SEO and crawler verification) -->
    <link rel="icon" type="image/x-icon" href="<?= $this->Url->build('/favicon.ico') ?>">
    <link rel="shortcut icon" href="<?= $this->Url->build('/favicon.ico') ?>">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><rect width='512' height='512' rx='128' fill='%234f46e5'/><path d='M368 112H144C126.3 112 112 126.3 112 144V368C112 385.7 126.3 400 144 400H272L368 304V144C368 126.3 353.7 112 336 112H368ZM256 384V304H336L256 384Z' fill='%23faf9f5'/></svg>">

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= h($metaDesc) ?>">
    <meta name="keywords" content="<?= h($metaKeywords) ?>">
    <meta name="author" content="Mohit Mokariya">
    <meta name="robots" content="index, follow">

    <!-- Canonical Link -->
    <link rel="canonical" href="<?= h($canonicalUrl) ?>">

    <!-- OpenGraph Social Sharing -->
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= h($canonicalUrl) ?>">
    <meta property="og:site_name" content="Helpdesk Daily Work Journal">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= h($pageTitle) ?>">
    <meta name="twitter:description" content="<?= h($metaDesc) ?>">

    <!-- JSON-LD Structured Data Schema for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebApplication",
          "@id": "<?= h($canonicalUrl) ?>#webapp",
          "name": "Helpdesk",
          "alternateName": "Helpdesk Daily Work Journal",
          "applicationCategory": "BusinessApplication",
          "operatingSystem": "All",
          "description": "<?= h($metaDesc) ?>",
          "url": "<?= h($canonicalUrl) ?>",
          "author": {
            "@type": "Person",
            "name": "Mohit Mokariya"
          }
        },
        {
          "@type": "WebSite",
          "@id": "<?= h($canonicalUrl) ?>#website",
          "url": "<?= h($canonicalUrl) ?>",
          "name": "Helpdesk",
          "description": "<?= h($metaDesc) ?>",
          "publisher": {
            "@type": "Person",
            "name": "Mohit Mokariya"
          }
        }
      ]
    }
    </script>

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

    <script>
        window.APP_BASE = '<?= $this->Url->build('/') ?>';
        window.CSRF_TOKEN = '<?= $this->request->getAttribute('csrfToken') ?>';

        function getCsrfToken() {
            var match = document.cookie.match(new RegExp('(^| )csrfToken=([^;]+)'));
            if (match) return decodeURIComponent(match[2]);
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta && meta.content) return meta.content;
            return window.CSRF_TOKEN || '';
        }

        // Setup jQuery global AJAX with CSRF token and zero-caching for fresh data
        $.ajaxSetup({
            cache: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
            }
        });

        $(document).ajaxSend(function(e, xhr, options) {
            xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
        });

        $(document).ajaxComplete(function(e, xhr) {
            var respCsrf = xhr.getResponseHeader('X-CSRF-Token');
            if (respCsrf) {
                window.CSRF_TOKEN = respCsrf;
            }
        });
    </script>

    <!-- Global Application JavaScript Library with Auto Cache-Busting -->
    <?= $this->Html->script('helpdesk.js?v=' . $jsVer) ?>

    <!-- Native View Transitions API for modern Chromium browsers -->
    <style>
        @view-transition {
            navigation: auto;
        }
    </style>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <!-- Top Glowing Iridescent Progress Bar (Vercel/Linear Style) -->
    <div id="hdGlobalProgressBar" class="hd-top-loader" aria-hidden="true"></div>

    <?= $this->fetch('content') ?>

    <?php if (!empty($currentUser)): ?>
        <?= $this->element('shared_modals') ?>
    <?php endif; ?>
</body>
</html>
