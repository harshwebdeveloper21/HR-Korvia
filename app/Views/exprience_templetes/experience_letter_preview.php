<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Employee Of The Month</title>
    <style>
        @page {
            margin: 120px 40px 100px 40px;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0px;
            right: 0px;
            height: 100px;
            text-align: center;
            line-height: 1.4;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0px;
            right: 0px;
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
            /* font-family: DejaVu Sans, sans-serif; */
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
            color: #000;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
            color: #333;
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
    <!-- Header (repeats on each page) -->
    <header>
        <table width="100%" style="vertical-align: top;">
            <tr>

                <td style="text-align: left ;" class="company-info">
                    <h2><?= $company_name ?? 'Your Company Name' ?></h2>
                    <p><?= $company_address ?? 'Your Company Address' ?></p>
                    <p><?= $company_phone ?? 'Phone' ?> | <?= $company_email ?? 'Email' ?></p>

                </td>
                <td style="text-align: right; vertical-align: top;" class="logo-wrapper">
                    <?php if (!empty($companyLogoBase64)) : ?>
                        <img src="<?= $companyLogoBase64 ?>" alt="Company Logo" class="" style="max-height: 100px;">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </header>


    <!-- Footer (repeats on each page) -->
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

    <!-- Main content -->
    <div class="content">
        <?= $content ?>
    </div>
</body>

</html>