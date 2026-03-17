<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Training Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* text-align: center; */
            border: 2px solid #666; /* Border around the container */
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;    
            /* text-align: center; */
        }   

        .footer strong {
            color: #666; /* Company name in the footer */
        }
    </style>
</head>

<body>
    <div class="email-container">
        <img src="<?= esc($company_logo); ?>" alt="Company Logo" class="logo">
        <h2><?= esc($company_name); ?> - Training Invitation</h2>
        <p>Hello <?= esc($employee_name); ?>,</p>
        <p>You have been invited to attend a training session.</p>
        <p><strong>Training Title:</strong> <?= esc($training_title); ?></p>
        <p><strong>Department:</strong> <?= esc($department_name); ?></p>
        <p><strong>Date:</strong> <?= esc($start_date); ?> to <?= esc($end_date); ?></p>
        <p><strong>Location:</strong> <?= esc($location); ?></p>
        <div class="footer">
            Regards, <br> <strong><?= esc($company_name); ?></strong> - HR Department
        </div>
    </div>
</body>

</html>
