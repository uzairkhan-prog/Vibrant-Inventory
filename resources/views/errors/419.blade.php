<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - Vibrant Engineering Inventory</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: linear-gradient(135deg, #0a2342, #1b3c73, #0e5a8a);
            background-size: 300% 300%;
            animation: gradientShift 12s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .card {
            background: #fff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            padding: 44px 36px;
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-wrap {
            width: 76px;
            height: 76px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #eaf1fb, #dbe8fa);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            stroke: #1b3c73;
        }

        .logo {
            width: 150px;
            background: linear-gradient(90deg, #0a2342, #1b3c73);
            margin: 0 auto 22px;
            border-radius: 5px;
            padding: 5px;
            display: block;
        }

        h1 {
            font-size: 1.5rem;
            color: #0a2342;
            margin-bottom: 10px;
        }

        p {
            font-size: 0.95rem;
            color: #5a6473;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #0a2342, #1b3c73);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: linear-gradient(90deg, #1b3c73, #0e5a8a);
            transform: translateY(-2px);
        }

        .btn svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
        }

        footer {
            margin-top: 22px;
            font-size: 0.78rem;
            color: #9aa3b1;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .card {
                padding: 34px 22px;
                border-radius: 12px;
            }

            h1 {
                font-size: 1.3rem;
            }

            p {
                font-size: 0.9rem;
            }

            .icon-wrap {
                width: 64px;
                height: 64px;
            }

            .icon-wrap svg {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Vibrant Engineering Logo" class="logo">

        <div class="icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <polyline points="12 7 12 12 15 15"></polyline>
            </svg>
        </div>

        <h1>Session Expired</h1>
        <p>
            For your security, you've been signed out after a period of
            inactivity. Please log in again to continue.
        </p>

        <a href="{{ route('login') }}" class="btn">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Go to Login
        </a>

        <footer>© {{ date('Y') }} Vibrant Engineering</footer>
    </div>
</body>

</html>
