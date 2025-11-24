<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        /* Body */
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container */
        .error-wrapper {
            text-align: center;
            max-width: 500px;
            padding: 40px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        /* Error Code */
        .error-code {
            font-size: 120px;
            font-weight: 900;
            color: #11142d;
            margin-bottom: 20px;
        }

        /* Title */
        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #11142d;
            margin-bottom: 15px;
        }

        /* Message */
        .error-msg {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* Back Button */
        .btn-back {
            background: #11142d;
            border-radius: 8px;
            color: #fff !important;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-back:hover {
            background: #2f3367;
        }

        /* Optional Image */
        .error-image {
            max-width: 280px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .error-code {
                font-size: 90px;
            }
            .error-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        {{-- Optional Image --}}
        {{-- <img src="{{ asset('assets/images/error-image.png') }}" alt="Error Image" class="error-image"> --}}

        <div class="error-code">404</div>

        <div class="error-title">Oops! Page Not Found</div>

        <p class="error-msg">
            The page you’re looking for doesn’t exist or has been moved.<br>
            Please check the URL or return to the Inventory.
        </p>

        <a href="{{ url('/') }}" class="btn-back">
            ⟵ Back to Inventory
        </a>
    </div>
</body>
</html>
