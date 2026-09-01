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

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><rect width='512' height='512' rx='128' fill='%234f46e5'/><path d='M368 112H144C126.3 112 112 126.3 112 144V368C112 385.7 126.3 400 144 400H272L368 304V144C368 126.3 353.7 112 336 112H368ZM256 384V304H336L256 384Z' fill='%23faf9f5'/></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script>
        window.APP_BASE = '<?= $this->Url->build('/') ?>';
        window.CSRF_TOKEN = '<?= $this->request->getAttribute('csrfToken') ?>';

        function getCsrfToken() {
            var match = document.cookie.match(new RegExp('(^| )csrfToken=([^;]+)'));
            if (match) return decodeURIComponent(match[2]);
            return window.CSRF_TOKEN;
        }

        // Setup jQuery global AJAX with CSRF token
        $.ajaxSetup({
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
