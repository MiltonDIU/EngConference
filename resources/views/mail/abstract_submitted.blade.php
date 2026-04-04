<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abstract Submission Received – BNC2026</title>
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
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
            margin-bottom: 24px;
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
        
        .disclaimer {
            font-size: 14px;
            color: #6b7280;
            font-style: italic;
            margin-top: 24px;
        }
        
        .footer {
            padding: 30px;
            text-align: center;
            background-color: #ffffff;
            border-top: 1px solid #f3f4f6;
        }
        
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 5px 0;
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
                width: 100px;
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
                
                <p class="message-intro">
                    Thank you for submitting your abstract to the international conference 
                    <strong>Beyond Nature and Culture: Planetary Precarity in Literary-Cultural-Linguistic Representations (BNC2026)</strong>.
                </p>
                
                <p class="message-intro">
                    We are pleased to confirm that your submission has been successfully received and has entered the review process.
                </p>

                <div class="submission-card">
                    <div class="card-title">Submission Details</div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Submission ID:</div>
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
                        <div class="detail-label">Sub-Theme:</div>
                        <div class="detail-value">{{ $paper->subTrack->name }}</div>
                    </div>
                    @endif
                    
                    <div class="detail-row">
                        <div class="detail-label">Corresponding Author:</div>
                        <div class="detail-value">{{ $paper->user->profile->first_name ?? '' }} {{ $paper->user->profile->last_name ?? '' }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Submission Date:</div>
                        <div class="detail-value">{{ $paper->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <p class="disclaimer">Please note that this message confirms receipt of your abstract only. The final decision will be communicated after the review process is completed.</p>
                
                <p class="message-intro">If you have any questions, please contact the conference team at <a href="mailto:bnc2026@diu.edu.bd" class="contact-info">bnc2026@diu.edu.bd</a>.</p>
                
                <p class="message-intro">Thank you for your interest in BNC2026.</p>
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
