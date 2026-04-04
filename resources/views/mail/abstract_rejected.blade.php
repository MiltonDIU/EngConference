<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abstract Review Decision – BNC2026</title>
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

        .notice-box {
            background-color: #f9fafb;
            border-left: 4px solid #9ca3af;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin: 24px 0;
        }

        .notice-box p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
            font-style: italic;
        }

        .review-note {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }

        .review-note h3 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #991b1b;
        }

        .review-note p {
            margin: 0;
            font-size: 14px;
            color: #374151;
            white-space: pre-wrap;
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
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="header">
                <p class="header-conference">International Conference on</p>
                <h1 class="header-title">BNC2026</h1>
                <p class="header-subtitle">Beyond Nature and Culture: Planetary Precarity in Literary-Cultural-Linguistic Representations</p>
            </div>

            <div class="content">
                <p class="greeting">Dear {{ $paper->user?->profile?->first_name ?? '' }} {{ $paper->user?->profile?->last_name ?? '' }},</p>

                <p class="message-intro">
                    Thank you for submitting your abstract to the international conference
                    <strong>Beyond Nature and Culture: Planetary Precarity in Literary-Cultural-Linguistic Representations (BNC2026)</strong>.
                </p>

                <p class="message-intro">
                    After careful review by the programme committee, we regret to inform you that your abstract has <strong>not been selected</strong> for presentation at this conference.
                </p>

{{--                @if($paper->review_note)--}}
{{--                <div class="review-note">--}}
{{--                    <h3>Reviewer Comments</h3>--}}
{{--                    <p>{{ $paper->review_note }}</p>--}}
{{--                </div>--}}
{{--                @endif--}}

                <div class="notice-box">
                    <p>This decision was made after consideration of overall fit with the conference theme and tracks, clarity of argument, originality, and the volume of submissions received. Please note that the review process is highly competitive, and many strong submissions could not be accommodated.</p>
                </div>


                <p class="message-intro">
                    We sincerely appreciate your interest in BNC2026 and your willingness to share your scholarly work with us. We hope you will consider participating in the conference as a non-presenting participant, if you wish.
                </p>

                <p class="message-intro">
                    Thank you again for your interest, time, and understanding. We wish you the very best in your future research endeavors.
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
