<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance - EAY Sports</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #0f172a, #020617);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .container {
            max-width: 600px;
            padding: 40px;
            text-align: center;
            z-index: 10;
        }

        /* Pulsing abstract gear design */
        .icon-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glow-circle {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, rgba(37, 99, 235, 0) 70%);
            animation: pulseGlow 3s infinite ease-in-out;
        }

        .gear-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: spinGear 12s infinite linear;
        }

        h1 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 17px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 30px;
            font-weight: 300;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.2);
            color: #60a5fa;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .progress-bar-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .progress-bar-fill {
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            border-radius: 50px;
            animation: loadingProgress 4s infinite ease-in-out;
        }

        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        /* Animations */
        @keyframes spinGear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.3); opacity: 0.8; }
        }

        @keyframes loadingProgress {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(120%); }
            100% { transform: translateX(-100%); }
        }

        /* Abstract backdrop blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            z-index: 1;
        }

        .blob-1 {
            width: 300px;
            height: 300px;
            background: #2563eb;
            top: -100px;
            left: -100px;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: #1e3a8a;
            bottom: -150px;
            right: -100px;
        }
    </style>
</head>
<body>

    <!-- Decorative Backdrop Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="badge">
            <i class="bi bi-shield-fill-exclamation"></i> Undergoing Maintenance
        </div>

        <div class="icon-wrapper">
            <div class="glow-circle"></div>
            <i class="bi bi-gear-fill gear-icon"></i>
        </div>

        <h1>We'll Be Back Shortly!</h1>
        <p>
            EAY Sports is currently performing scheduled system updates to improve your storefront experience. We appreciate your patience and will return online shortly.
        </p>

        <div class="progress-bar-container">
            <div class="progress-bar-fill"></div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} EAY Sports. All rights reserved.
        </div>
    </div>

</body>
</html>
