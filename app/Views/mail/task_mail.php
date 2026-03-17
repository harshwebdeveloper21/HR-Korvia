<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assignment</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            padding: 30px;
        }

        .bordered-container {
            border: 3px solid #333;
            /* Border around the entire content */
            border-radius: 10px;
            padding: 20px;
            max-width: 700px;
            margin: auto;
            background-color: #ffffff;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 150px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .employee-name {
            font-size: 20px;
            color: #333;
            margin-top: 10px;
        }

        .task-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #fafafa;
            border-radius: 8px;
            border-left: 5px solid #333;
        }

        .task-details h4 {
            color: #333;
            font-size: 18px;
        }

        .task-details p {
            font-size: 16px;
            line-height: 1.5;
        }

        .task-details ul {
            list-style-type: none;
            padding-left: 0;
        }

        .task-details ul li {
            padding: 10px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .task-details ul li:last-child {
            border-bottom: none;
        }

        .footer {
            /* text-align: center; */
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }

        .footer a {
            color: #333;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="bordered-container">
        <div class="container">
            <div class="header">
                <img src="<?= esc($company_logo); ?>" alt="Company Logo">
                <h1><?= esc($company_name); ?></h1>
            </div>

            <div class="employee-name">
                <p><strong>Hello <?= esc($employee_name); ?>,</strong></p>
                <p>You have been assigned a new task! Below are the details:</p>
            </div>

            <div class="task-details">
                <h4>Task: <?= esc($task_title); ?></h4>
                <ul>
                    <li><strong>Assigned By:</strong> <?= esc($assigned_by); ?></li>
                    <li><strong>Department:</strong> <?= esc($department); ?></li>
                    <li><strong>Due Date:</strong> <?= esc($due_date); ?></li>
                    <li><strong>Description:</strong> <?= esc($description); ?></li>
                    <li><strong>Status:</strong> <?= esc($task_status); ?></li>
                </ul>
            </div>

            <div class="footer">
                <p>If you have any questions, feel free to reach out to your HR or manager.</p></br>
                Regards, <br> <strong><?= esc($company_name); ?></strong> - HR Department

            </div>
        </div>
    </div>
</body>

</html>