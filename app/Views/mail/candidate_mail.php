<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Hiring Process</title>
    <style>
        body {
            /* font-family: Arial, sans-serif; */
            font-family: 'Georgia', serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
            /* text-align: center; */
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        p {
            color: #555;
            line-height: 1.6;
            font-size: 16px;
            margin: 10px 0;
        }

        .button {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            font-weight: bold;
            transition: 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .logo {
            margin-bottom: 15px;
        }

        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #333;
            margin: 15px 0;
        }

        .info-box p {
            margin: 5px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{company_logo}}" alt="Company Logo" width="150" class="logo">
        <h2>Welcome, {{candidate_name}}!</h2>
        <p>Thank you for applying for the <strong>{{job_title}}</strong> position at <strong>{{company_name}}</strong>.</p>
        <p>We appreciate your interest and will review your application soon. Our team will contact you at <strong>{{email}}</strong> for further steps.</p>

        <div class="info-box">
            <p><strong>Resume:</strong> <a href="{{resume}}" target="_blank">View Resume</a></p>
        </div>
            <p>Best regards,<br><strong>HR Team, {{company_name}}</strong></p>
    </div>
</body>
</html> 