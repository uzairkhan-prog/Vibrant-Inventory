<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibrant Engineering Inventory - Login</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body,
        html {
            height: 100%;
            width: 100%;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* LEFT SECTION */
        .left {
            flex: 1;
            background: url("{{ asset('assets/images/logos/login-hero-banner.png') }}") no-repeat center center;
            background-size: cover;
            position: relative;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 35, 66, 0.7);
            z-index: 1;
        }

        .hero-text {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 400px;
        }

        .hero-text h1 {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #fff;
        }

        .hero-text p {
            font-size: 1rem;
            color: #dce4f0;
            line-height: 1.5;
        }

        /* RIGHT SECTION */
        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0a2342, #1b3c73, #0e5a8a);
            background-size: 300% 300%;
            animation: gradientShift 12s ease infinite;
            padding: 20px;
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

        .login-box {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0px 12px 30px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 200px;
            background: linear-gradient(90deg, #0a2342, #1b3c73);
            margin-bottom: 10px;
            border-radius: 5px;
            padding: 5px;
        }

        .login-box h2 {
            font-size: 1.4rem;
            margin-bottom: 25px;
            color: #0a2342;
        }

        .input-group {
            margin: 15px 0;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #0a2342;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border 0.3s, box-shadow 0.3s;
        }

        .input-group input:focus {
            border-color: #1b3c73;
            box-shadow: 0 0 6px rgba(27, 60, 115, 0.5);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #0a2342, #1b3c73);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover {
            background: linear-gradient(90deg, #1b3c73, #0e5a8a);
            transform: translateY(-2px);
        }

        .error {
            margin-top: 10px;
            color: red;
            font-size: 0.9rem;
            display: none;
        }

        footer {
            margin-top: 20px;
            font-size: 0.8rem;
            color: #555;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left {
                display: none;
            }

            .right {
                flex: none;
                height: 100vh;
            }

            .logo {
                width: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Image Section with Overlay Text -->
        <div class="left">
            <div class="overlay"></div>
            <div class="hero-text">
                <h1>Inventory System</h1>
                <p>Manage your stock, machinery & parts with <br> Vibrant Engineering</p>
            </div>
        </div>

        <!-- Right Form Section -->
        <div class="right">
            <div class="login-box">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Vibrant Engineering Logo" class="logo">
                <h2>Login to Inventory</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                    </div>

                    <button type="submit" class="btn">Login</button>

                    @if ($errors->any())
                    <p id="errorMsg" class="error" style="display: block;">❗ Invalid username or password</p>
                    @endif
                </form>

                <footer>© 2025 Vibrant Engineering</footer>
            </div>
        </div>
    </div>
</body>

</html>