<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration and Payment Confirmation – BNC2026</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        @font-face {
            font-family: 'edo';
            src: url('{{ config('app.url') }}/fonts/edo.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 40px;
            padding-top:10px;
        }

        .main-content {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header-conference {
            font-size: 11px;
            font-weight: 400;
            margin: 0 0 4px 0;
            opacity: 0.75;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .header-title {
            font-family: 'edo', sans-serif;
            font-size: 30px;
            font-weight: normal;
            margin: 0;
            letter-spacing: 0.04em;
        }

        .header-subtitle {
            font-size: 13px;
            font-weight: 400;
            margin: 6px 0 0 0;
            opacity: 0.85;
            letter-spacing: 0.02em;
        }

        .content {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }

        .message-intro {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 16px;
        }

        .badge {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
            letter-spacing: 0.04em;
        }

        .submission-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .detail-row {
            margin-bottom: 12px;
            display: table;
            width: 100%;
        }

        .detail-label {
            display: table-cell;
            width: 170px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .detail-value {
            display: table-cell;
            color: #111827;
            font-size: 14px;
        }

        .footer {
            padding: 28px 30px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 4px 0;
        }

        .footer-text strong {
            color: #374151;
        }

        .contact-info {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            .detail-label {
                width: 130px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="header">
                <p class="header-conference">International Conference on Beyond Nature and Culture</p>
                <h1 class="header-title">BNC2026</h1>
                <p class="header-subtitle">Planetary Precarity in Literary-Cultural-Linguistic Representations</p>
            </div>

            <div class="content">
                <p class="greeting">Dear {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }},</p>

                <span class="badge">✓ Registration Confirmed</span>

                <p class="message-intro">
                    Thank you for completing your registration for <strong>BNC2026</strong>. We confirm that your payment has been received successfully.
                </p>

                <div class="submission-card">
                    <div class="card-title">Registration Details</div>

                    <div class="detail-row">
                        <div class="detail-label">Name:</div>
                        <div class="detail-value">{{ $data['name'] ?? '' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Registration ID:</div>
                        <div class="detail-value">{{ $data['registration_id'] ?? '' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Category:</div>
                        <div class="detail-value">{{ $data['category'] ?? '' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Mode:</div>
                        <div class="detail-value">{{ $data['mode'] ?? '' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Amount Received:</div>
                        <div class="detail-value">{{ $data['amount'] ?? '' }} {{ $data['currency'] ?? '' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Transaction ID:</div>
                        <div class="detail-value">{{ $data['transaction_id'] ?? '' }}</div>
                    </div>
                </div>

                <p class="message-intro">
                    Your registration is now complete. Further updates regarding the conference schedule, presentation guidelines, and participation instructions will be shared in due course.
                </p>

                <p class="message-intro">
                    We look forward to welcoming you to BNC2026.
                </p>

                <p class="message-intro">
                    If you have any questions, please contact us at <a href="mailto:bnc2026@diu.edu.bd" class="contact-info">bnc2026@diu.edu.bd</a>.
                </p>
            </div>

            <div class="footer">
                <p class="footer-text">Warm regards,</p>
                <p class="footer-text"><strong>Conference Secretariat</strong></p>
                <p class="footer-text">BNC2026</p>
                <p class="footer-text">Department of English</p>
                <p class="footer-text">Daffodil International University</p>
            </div>
        </div>
    </div>
</body>
</html>
