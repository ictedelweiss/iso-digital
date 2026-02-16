<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#005f89">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ISO Digital">

    <title>@yield('title', 'ISO Digital')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --c-dark-navy: #08182b;
            --c-deep-teal: #003755;
            --c-primary-blue: #005f89;
            --c-gold: #fca311;
            --c-light-gold: #fccb6e;
            --c-white: #ffffff;
            --c-text-light: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            color: var(--c-text-light);
            /* Palette Gradient */
            background: linear-gradient(135deg, var(--c-dark-navy), var(--c-deep-teal), #1e293b);
            background-size: 200% 200%;
            animation: gradient-animation 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradient-animation {
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

        /* Gold Particles */
        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(252, 163, 17, 0.15);
            /* Gold tint */
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }

        .circles li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .circles li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles li:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .circles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles li:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        .container {
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: card-entry 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes card-entry {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            /* Gold Gradient for Logo */
            background: linear-gradient(135deg, var(--c-primary-blue) 0%, var(--c-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .card-header p {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            margin-left: 4px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            font-family: inherit;
            background: #f8fafc;
            color: #0f172a;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--c-primary-blue);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 95, 137, 0.1);
        }

        .meeting-badge {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .meeting-badge-label {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            color: var(--c-primary-blue);
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .meeting-badge-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--c-deep-teal);
        }

        .signature-area {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 4px;
            position: relative;
            transition: border-color 0.2s;
        }

        .signature-area:hover {
            border-color: var(--c-gold);
        }

        .signature-canvas {
            width: 100%;
            height: 180px;
            border-radius: 12px;
            background: #ffffff;
            cursor: crosshair;
            touch-action: none;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-family: inherit;
        }

        .btn-primary {
            /* Blue to Teal Gradient Default */
            background: linear-gradient(135deg, var(--c-primary-blue) 0%, var(--c-deep-teal) 100%);
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 95, 137, 0.2);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-primary:hover {
            /* Gold Hover */
            background: linear-gradient(135deg, var(--c-gold) 0%, var(--c-light-gold) 100%);
            color: var(--c-dark-navy);
            box-shadow: 0 10px 15px -3px rgba(252, 163, 17, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            font-size: 13px;
            padding: 10px;
            margin-top: 8px;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Custom Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .checkbox-wrapper:hover {
            border-color: var(--c-gold);
            background: #fffbeb;
        }

        .checkbox-wrapper input {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            accent-color: var(--c-primary-blue);
            cursor: pointer;
        }

        .checkbox-label {
            font-weight: 600;
            color: #334155;
            font-size: 14px;
            cursor: pointer;
        }

        /* Success Animation */
        .success-state {
            display: none;
            text-align: center;
            padding: 40px 20px;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            position: relative;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 20px;
        }

        .checkmark-circle .background {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--c-deep-teal);
            position: absolute;
        }

        .checkmark-circle .checkmark {
            border-radius: 5px;
        }

        .checkmark-circle .checkmark.draw:after {
            animation-delay: 100ms;
            animation-duration: 1s;
            animation-timing-function: ease;
            animation-name: checkmark;
            transform: scaleX(-1) rotate(135deg);
            animation-fill-mode: forwards;
        }

        .checkmark-circle .checkmark:after {
            opacity: 1;
            height: 40px;
            width: 20px;
            transform-origin: left top;
            border-right: 4px solid var(--c-gold);
            border-top: 4px solid var(--c-gold);
            content: '';
            left: 20px;
            top: 40px;
            position: absolute;
        }

        @keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }

            20% {
                height: 0;
                width: 20px;
                opacity: 1;
            }

            40% {
                height: 40px;
                width: 20px;
                opacity: 1;
            }

            100% {
                height: 40px;
                width: 20px;
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 12px;
                align-items: flex-start;
            }

            .card {
                padding: 24px 20px;
                margin-top: 20px;
            }

            .logo-text {
                font-size: 24px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="container">
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>