<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Hiring Process</title>
    <style>
        .onboarding-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .onboarding-container h2 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .onboarding-container p,
        .onboarding-container ul,
        .onboarding-container ol {
            font-size: 16px;
            color: #34495e;
            line-height: 1.6;
        }

        .onboarding-container ul,
        .onboarding-container ol {
            padding-left: 20px;
        }

        .onboarding-container a {
            color: #3498db;
            text-decoration: none;
        }

        .onboarding-container a:hover {
            text-decoration: underline;
        }

        .onboarding-container .contact-info {
            margin-top: 20px;
            padding: 15px;
            background: #ecf0f1;
            border-radius: 5px;
        }

        .onboarding-container .signature {
            margin-top: 30px;
            color: black !important;
        }
    </style>
</head>

<body>
    <div class="onboarding-container">
        <h2>Your Journey with {{company_name}} Begins!</h2>
        <p>Hi {{candidate_name}},</p>

        <p>Thank you for completing the necessary paperwork. Signing your job contract has made us happy that you will soon be working with us.</p>

        <p>We also thought we'd let you know the exact steps your onboarding will take. Below are your details:</p>

        <ul>
            <li><strong>Department:</strong> {{department}} </li>
            <li><strong>Job Role:</strong> {{job_role}}</li>
            <li><strong>Start Date:</strong> {{start_date}}</li>
            <li><strong>Bank Name:</strong> {{bank_name}}</li>
            <li><strong>Account Number:</strong> {{acc_number}}</li>
        </ul>

        <p>We have also attached a copy of our employee handbook for you to read. You can also access company information on our <a href="#">online knowledge base</a>.</p>

        <p>As you've now signed your job contract, you will receive:</p>
        <ol>
            <li>A welcome email with more information about working with us.</li>
            <li>An email confirming your first day at work.</li>
        </ol>

        <p>Enjoy your week!</p>

        <p class="signature">Best regards,<br>
        {{company_name}}<br>
    </div>

</body>

</html>