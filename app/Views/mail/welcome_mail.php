<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        .header {
            text-align: center;
            padding: 20px 0;
        }

        .header img {
            max-width: 150px;
        }

        .content {
            padding: 20px;
            /* font-style: italic; */
            color: black !important;
        }

        .credentials {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            font-style: normal;
        }

        .button {
            display: inline-block;
            background: #E66136;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #666;
            background: #f4f4f4;
            border-radius: 0 0 8px 8px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="<?= esc($company_logo); ?>" alt="Company Logo" class="welcome-mail-logo">
        </div>
        <div class="content">
            <p>Dear <strong><?= esc($employee_name); ?></strong>,</p>
            <p>I’m <strong><?= esc($sender_name); ?></strong>, the <strong><?= esc($job_position); ?></strong> of <strong><?= esc($company_name); ?></strong>, and I’d like to personally welcome you to our team.</p>
            <p class="pfont">We established <strong><?= esc($company_name); ?></strong> to <strong><?= esc($company_mission); ?></strong>. Your skills and experience will be a great addition to our company.</p>
            <p>To get started, here are your login credentials:</p>
            <div class="credentials">
                Email: <strong><?= esc($employee_email); ?></strong><br>
                Password: <strong><?= esc($generated_password); ?></strong>
            </div>
            <!-- <p>Please change your password upon login for security reasons.</p>
            <a href="<?= esc($portal_url); ?>" class="button">Login to Portal</a> -->
            <p>If you have any questions, feel free to reply to this email. We're here to help.</p>
        </div>
        <div class="footer">
            <p>&copy; <?= esc($year); ?> <?= esc($company_name); ?>. All rights reserved.</p>
            <p>Company Address | Contact: support@<?= strtolower(str_replace(' ', '', esc($company_name))); ?>.com</p>
        </div>
    </div>
</body>

</html>