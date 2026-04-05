<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address – BNC2026</title>
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

        .verify-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 28px 24px;
            margin: 28px 0;
            text-align: center;
        }

        .verify-box p {
            font-size: 15px;
            color: #374151;
            margin: 0 0 20px 0;
        }

        .btn-verify {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 8px;
            letter-spacing: 0.025em;
        }

        .url-fallback {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 20px 0 0 0;
            font-size: 13px;
            color: #6b7280;
            word-break: break-all;
        }

        .url-fallback a {
            color: #3b82f6;
            text-decoration: none;
        }

        .expiry-note {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 14px;
            color: #92400e;
            margin-top: 20px;
        }

        .disclaimer {
            font-size: 14px;
            color: #6b7280;
            font-style: italic;
            margin-top: 20px;
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
            .btn-verify {
                padding: 12px 24px;
                font-size: 15px;
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
                <p class="greeting">
                    Dear {{ $user->profile->first_name ?? $user->name }},
                </p>

                <span class="badge">✉ Email Verification Required</span>

                <p class="message-intro">
                    Thank you for registering with <strong>BNC2026</strong>. To complete your registration and gain full access to your account, please verify your email address by clicking the button below.
                </p>

                <div class="verify-box">
                    <p>Click the button below to verify your email address. This link will expire in <strong>60 minutes</strong>.</p>
                    <a href="{{ $url }}" class="btn-verify">Verify Email Address</a>

                    <div class="url-fallback">
                        <strong>If the button doesn't work,</strong> copy and paste this URL into your browser:<br>
                        <a href="{{ $url }}">{{ $url }}</a>
                    </div>
                </div>

                <div class="expiry-note">
                    ⚠️ This verification link will expire in <strong>60 minutes</strong>. If expired, please log in and request a new verification email.
                </div>

                <p class="disclaimer">
                    If you did not create an account at BNC2026, please ignore this email. No action is required on your part.
                </p>

                <p class="message-intro" style="margin-top: 24px;">
                    If you have any questions, please contact us at
                    <a href="mailto:bnc2026@diu.edu.bd" class="contact-info">bnc2026@diu.edu.bd</a>.
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
