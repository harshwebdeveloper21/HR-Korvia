<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }
        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
        }
        p {
            margin-top: 1.5rem;
        }
        .footer {
            margin-top: 2rem;
            border-top: 1px solid #efefef;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }
        a:active,
        a:link,
        a:visited {
            color: #dd4814;
        }
    </style>
    <script>
        // Immediate redirect to dashboard if this is a 404 page opened from notification
        (function() {
            // Check if we came from a notification (check referrer or sessionStorage)
            const fromNotification = sessionStorage.getItem('fromNotification') === 'true' || 
                                    document.referrer === '' || 
                                    !document.referrer;
            
            // Always redirect 404 pages to dashboard (especially from notifications)
            const dashboardUrl = '<?= base_url("/dashboard") ?>';
            
            // Small delay to ensure page is rendered, then redirect
            setTimeout(function() {
                console.log('🔄 404 page detected, redirecting to dashboard:', dashboardUrl);
                window.location.href = dashboardUrl;
            }, 300);
            
            // Also redirect immediately if page is already loaded
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                window.location.href = dashboardUrl;
            }
        })();
    </script>
</head>
<body>
    <div class="wrap">
        <h1>404</h1>

        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>
        <p style="margin-top: 1rem;">
            <small>Redirecting to dashboard...</small>
        </p>
    </div>
</body>
</html>
