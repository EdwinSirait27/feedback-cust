<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance — Bali in Bali</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #2C1A0E 0%, #3D2410 45%, #1a0f06 100%);
            position: relative;
            overflow: hidden;
        }

        .gold-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4A017, #F5C842, #D4A017, transparent);
        }

        .glow-1 {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 160, 23, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .glow-2 {
            position: absolute;
            bottom: 60px;
            left: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 160, 23, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(212, 160, 23, 0.12);
            border: 1px solid rgba(212, 160, 23, 0.3);
            border-radius: 20px;
            padding: 4px 16px;
            margin-bottom: 24px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #D4A017;
            animation: pulse 2s ease-in-out infinite;
        }

        .badge span {
            font-size: 11px;
            color: #D4A017;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-family: system-ui, sans-serif;
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: #D4A017;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 0 0 40px rgba(212, 160, 23, 0.3);
        }

        .title {
            font-size: 22px;
            color: #F5E6C0;
            margin-bottom: 12px;
            font-weight: 400;
        }

        .subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.45);
            line-height: 1.7;
            margin-bottom: 24px;
            font-family: system-ui, sans-serif;
        }

        .maintenance-box {
            background: rgba(212, 160, 23, 0.08);
            border: 1px solid rgba(212, 160, 23, 0.2);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 32px;
        }

        .maintenance-box p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            font-family: system-ui, sans-serif;
            line-height: 1.6;
        }

        .maintenance-box strong {
            color: #D4A017;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider-text {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.25);
            font-family: system-ui, sans-serif;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            font-family: system-ui, sans-serif;
            transition: opacity 0.15s;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F5C842, #D4A017);
            color: #1a0f02;
        }

        .footer-note {
            margin-top: 32px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            font-family: system-ui, sans-serif;
        }

        .dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 32px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #D4A017;
            animation: bounce 1.4s ease-in-out infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                opacity: 0.3;
                transform: scale(0.8);
            }

            40% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }
    </style>
</head>

<body>
    <div class="gold-line"></div>
    <div class="glow-1"></div>
    <div class="glow-2"></div>

    <div class="card">
        {{-- <div class="badge">
            <div class="badge-dot"></div>
            <span>Bali in Bali</span>
        </div> --}}

        <div class="error-code">503</div>
        <h1 class="title">Maintenance</h1>
        <p class="subtitle">
            Our system is currently under maintenance<br>
            to provide a better experience.


        </p>

        <div class="dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <div class="maintenance-box">
            <p>
                We'll be back soon.<br>
                Thank you for your patience. 🙏
            </p>
        </div>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">✦</span>
            <div class="divider-line"></div>
        </div>

       <a href="{{ route('home') }}" class="btn btn-primary">Back To Home</a>
<a href="javascript:location.reload()" class="btn btn-secondary">Try again</a>

        <p class="footer-note">© Bali in Bali · Maintenance Mode</p>
    </div>

    <script>
        // Auto refresh setiap 30 detik saat maintenance
        setTimeout(() => window.location.reload(), 30000);
    </script>
</body>

</html>
