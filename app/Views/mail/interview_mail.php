<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            border: 2px solid #555; /* Added border for better visibility */
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px 0;
        }
        h2 {
            color: #555; /* Changed to match the border */
        }
        .details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 2px solid #555; /* Added border to the details box */
            margin-top: 15px;
        }
        .details p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{company_logo}}" alt="Company Logo">
            <h2>Interview Invitation</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{{candidate_name}}</strong>,</p>
            <p>We are pleased to inform you that you have been selected for an interview for the position of <strong>{{job_title}}</strong> at <strong>{{company_name}}</strong>. Your qualifications and experience align with our needs, and we look forward to discussing your potential contributions.</p>
            
            <div class="details">
                <p><strong>Interview Details:</strong></p>
                <p><strong>Date:</strong> {{schedule_date}}</p>
                <p><strong>Time:</strong> {{schedule_time}}</p>
            </div>
            
            <p><strong>Please bring the following documents with you:</strong></p>
            <ul>
                <li>An updated copy of your resume</li>
                <li>A portfolio of recent projects (if applicable)</li>
                <li>Contact information for three professional references</li>
            </ul>
            
            <p>If you have any questions, feel free to contact us at <strong>{{company_email}}</strong> or call <strong>{{company_phone}}</strong>. If the scheduled time does not work for you, please inform us as soon as possible.</p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>HR Manager, <strong>{{company_name}}</strong></p>
        </div>
    </div>
</body>
</html>
