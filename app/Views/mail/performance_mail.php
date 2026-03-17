<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Performance Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            margin: auto;
        }

        .header {
            text-align: center;
            background: #555;
            color: white;
            padding: 15px;
            font-size: 22px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 15px;
        }

        .section {
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .rating {
            font-weight: bold;
            color: #28a745;
        }

        .footer {
            /* text-align: center; */
            padding: 10px;
            font-size: 14px;
            color: #555;
            border-top: 1px solid #ddd;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .btn:hover {
            background: #0056b3;
        }

        ul {
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">Performance Review</div>
        <div class="content">
            <p>Dear <strong><?= $employee_name ?></strong>,</p>
            <p>Your performance review for <strong><?= $review_date ?></strong> has been completed. Here are the details:</p>

            <div class="section">
                <strong>Reviewer:</strong> <?= $reviewer_name ?><br>
                <strong>Designation:</strong> <?= $designation ?>
            </div>

            <div class="section">
                <strong>Performance Metrics:</strong>
                <ul>
                    <li>Goals Achieved: <span class="rating"><?= $goals_achieved ?></span></li>
                    <li>Team Work: <span class="rating"><?= $team_work ?></span></li>
                    <li>Management: <span class="rating"><?= $management ?></span></li>
                    <li>Presentation Skills: <span class="rating"><?= $presentation_skill ?></span></li>
                    <li>Behaviour: <span class="rating"><?= $behaviour ?></span></li>
                </ul>
            </div>

            <div class="section">
                <strong>Overall Rating:</strong> <span class="rating"><?= $rating ?>/10</span>
            </div>

            <div class="section">
                <strong>Review Notes:</strong>
                <p><?= $notes ?></p>
            </div>

            <p>If you have any questions, please contact HR.</p>

        </div>

        <div class="footer">
            Regards, <br> <strong>HR Department</strong>
        </div>
    </div>
</body>

</html>
