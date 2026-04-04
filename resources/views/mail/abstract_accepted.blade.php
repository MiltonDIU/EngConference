<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abstract Accepted – BNC2026</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

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
            background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.025em;
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
            background-color: #d1fae5;
            color: #065f46;
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
            width: 150px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .detail-value {
            display: table-cell;
            color: #111827;
            font-size: 14px;
        }

        .next-steps {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin: 24px 0;
        }

        .next-steps h3 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #065f46;
        }

        .next-steps p {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #374151;
        }

        .next-steps ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
            font-size: 14px;
            color: #374151;
        }

        .next-steps ul li {
            margin-bottom: 6px;
        }

        .footer {
            padding: 28px 30px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .review-note {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }

        .review-note h3 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #065f46;
        }

        .review-note p {
            margin: 0;
            font-size: 14px;
            color: #374151;
            white-space: pre-wrap;
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
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            .detail-label {
                width: 110px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="header">
                <h1 class="header-title">BNC2026</h1>
                <p class="header-subtitle">Beyond Nature and Culture: Planetary Precarity in Literary-Cultural-Linguistic Representations</p>
            </div>

            <div class="content">
                <p class="greeting">Dear {{ $paper->user?->profile?->first_name ?? '' }} {{ $paper->user?->profile?->last_name ?? '' }},</p>

                <span class="badge">✓ Abstract Accepted</span>

                <p class="message-intro">
                    We are delighted to inform you that your abstract has been <strong>accepted for presentation</strong> at the international conference
                    <strong>Beyond Nature and Culture: Planetary Precarity in Literary-Cultural-Linguistic Representations (BNC2026)</strong>.
                </p>

                <div class="submission-card">
                    <div class="card-title">Accepted Submission Details</div>

                    <div class="detail-row">
                        <div class="detail-label">Paper ID:</div>
                        <div class="detail-value">{{ $paper->submission_id }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Paper Title:</div>
                        <div class="detail-value">{{ $paper->title }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Track:</div>
                        <div class="detail-value">{{ $paper->track->name ?? 'N/A' }}</div>
                    </div>

                    @if($paper->subTrack)
                    <div class="detail-row">
                        <div class="detail-label">Sub-Track:</div>
                        <div class="detail-value">{{ $paper->subTrack->name }}</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Mode:</div>
                        <div class="detail-value">{{ ucfirst($paper->mode_of_participation) }}</div>
                    </div>
                </div>

                <p class="message-intro">
                    Your abstract was positively evaluated by the review committee and has been selected for inclusion in the conference program, subject to completion of registration and payment within the deadline.
                </p>

                @if($paper->review_note)
                <div class="review-note">
                    <h3>Reviewer Comments</h3>
                    <p>{{ $paper->review_note }}</p>
                </div>
                @endif


                <div class="next-steps">
                    <h3>Next Step: Complete Your Registration</h3>
                    <p>To confirm your participation, please complete the registration form and submit the conference fee by <strong>{{ config('app.registration_deadline', 'the deadline') }}</strong>.</p>
                    <ul>
                        <li>Only registered presenters will be included in the final conference schedule.</li>
                        <li>Registration is mandatory for presentation.</li>
                        <li>Further details regarding schedule, session allocation, and presentation guidelines will be shared after registration is completed.</li>
                    </ul>
                </div>

                <p class="message-intro">
                    We congratulate you on the acceptance of your abstract and look forward to welcoming you to BNC2026.
                </p>

                <p class="message-intro">
                    If you have any questions, please contact the conference team at <a href="mailto:bnc2026@diu.edu.bd" class="contact-info">bnc2026@diu.edu.bd</a>.
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
