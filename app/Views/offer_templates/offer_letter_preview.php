<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Offer Letter</title>
    <style>
        @page {
            margin: 120px 40px 100px 40px;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
            line-height: 1.4;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        body {
            line-height: 1.5;
            color: #000000;
            background-color: #ffffff;
        }

        .content {
            margin-top: 20px;
        }

        .logo-wrapper img {
            height: 80px;
        }

        .company-info {
            text-align: left;
            font-size: 12px;
            color: #000;
        }

        .company-info h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .company-info p {
            margin: 2px 0;
        }

        p {
            margin: 10px 0;
            text-align: justify;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <header>
        <table width="100%">
            <tr>
                <td class="company-info" style="text-align: left;">
                    <h2><?= $company_name ?? 'Your Company Name' ?></h2>
                    <p><?= $company_address ?? 'Your Company Address' ?></p>
                    <p><?= $company_phone ?? 'Phone' ?> | <?= $company_email ?? 'Email' ?></p>
                </td>
                <td class="logo-wrapper" style="text-align: right;">
                    <?= $logo_img ?? '' ?>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table width="100%">
            <tr>
                <td style="text-align: left;">
                    <?= $company_name ?? 'Your Company Name' ?>
                </td>
                <td style="text-align: right;">
                    <?= $company_address ?? 'Your Company Address' ?>
                </td>
            </tr>
        </table>
    </footer>

    <div class="content">
        <?= $content ?>
    </div>

</body>

</html>
