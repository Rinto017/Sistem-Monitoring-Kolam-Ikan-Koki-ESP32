<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Login demo
    $username_benar = "admin";
    $password_benar = "admin123";

    if ($username === $username_benar && $password === $password_benar) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kolam Ikan IoT</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #0b1248, #1f1b5c, #4c5dff);
            color: white;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            bottom: -80px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            animation: bubbleUp 8s infinite ease-in;
        }

        .bubble:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            left: 25%;
            width: 12px;
            height: 12px;
            animation-delay: 2s;
        }

        .bubble:nth-child(3) {
            left: 70%;
            width: 22px;
            height: 22px;
            animation-delay: 1s;
        }

        .bubble:nth-child(4) {
            left: 85%;
            width: 14px;
            height: 14px;
            animation-delay: 3s;
        }

        .bubble:nth-child(5) {
            left: 45%;
            width: 10px;
            height: 10px;
            animation-delay: 4s;
        }

        .bubble:nth-child(6) {
            left: 60%;
            width: 16px;
            height: 16px;
            animation-delay: 6s;
        }

        @keyframes bubbleUp {
            0% {
                transform: translateY(0);
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            100% {
                transform: translateY(-110vh);
                opacity: 0;
            }
        }

        /* ======================
           IKAN KOKI ANIMASI
        ====================== */
        .goldfish {
            position: absolute;
            width: 120px;
            height: 70px;
            opacity: 0.95;
            z-index: 1;
            animation: swimRight 16s infinite linear;
        }

        .goldfish-1 {
            left: -180px;
            bottom: 18%;
        }

        .goldfish-2 {
            left: -260px;
            bottom: 36%;
            width: 85px;
            height: 52px;
            animation-delay: 5s;
            animation-duration: 19s;
        }

        .goldfish-3 {
            left: -320px;
            bottom: 52%;
            width: 70px;
            height: 44px;
            animation-delay: 9s;
            animation-duration: 22s;
            opacity: 0.75;
        }

        .goldfish-body {
            position: absolute;
            width: 68%;
            height: 72%;
            right: 12px;
            top: 8px;
            background: radial-gradient(circle at 70% 35%, #fff6d6 0 8%, #ffb703 25%, #ff7a00 70%, #ff4d00 100%);
            border-radius: 55% 45% 45% 55%;
            box-shadow: 0 0 18px rgba(255, 145, 0, 0.35);
        }

        .goldfish-head {
            position: absolute;
            width: 34%;
            height: 62%;
            right: 0;
            top: 12px;
            background: radial-gradient(circle at 55% 35%, #ffe6b3 0 10%, #ff9f1c 45%, #ff4d00 100%);
            border-radius: 50%;
            z-index: 2;
        }

        .goldfish-tail {
            position: absolute;
            left: 0;
            top: 13px;
            width: 42px;
            height: 42px;
            animation: tailMove 0.8s infinite ease-in-out;
            transform-origin: right center;
        }

        .goldfish-tail::before,
        .goldfish-tail::after {
            content: "";
            position: absolute;
            left: 0;
            width: 42px;
            height: 24px;
            background: linear-gradient(135deg, #ffd166, #ff7a00);
            border-radius: 70% 20% 70% 20%;
        }

        .goldfish-tail::before {
            top: 0;
            transform: rotate(28deg);
        }

        .goldfish-tail::after {
            bottom: 0;
            transform: rotate(-28deg);
        }

        .goldfish-fin {
            position: absolute;
            width: 30px;
            height: 20px;
            left: 43%;
            bottom: 5px;
            background: linear-gradient(135deg, #ffd166, #ff7a00);
            border-radius: 70% 20% 70% 20%;
            transform: rotate(18deg);
            z-index: 3;
        }

        .goldfish-eye {
            position: absolute;
            right: 16px;
            top: 24px;
            width: 8px;
            height: 8px;
            background: #111;
            border-radius: 50%;
            z-index: 4;
        }

        .goldfish-eye::after {
            content: "";
            position: absolute;
            right: 1px;
            top: 1px;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
        }

        @keyframes swimRight {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(calc(100vw + 360px));
            }
        }

        @keyframes tailMove {
            0%, 100% {
                transform: rotate(0deg);
            }
            50% {
                transform: rotate(10deg);
            }
        }

        /* ======================
           IKAN KOKI LOGO FORM
        ====================== */
        .mini-goldfish {
            position: relative;
            width: 48px;
            height: 30px;
            margin: auto;
        }

        .mini-goldfish .mini-body {
            position: absolute;
            width: 32px;
            height: 24px;
            right: 3px;
            top: 3px;
            background: radial-gradient(circle at 65% 35%, #fff6d6 0 8%, #ffb703 30%, #ff6b00 100%);
            border-radius: 55% 45% 45% 55%;
        }

        .mini-goldfish .mini-tail {
            position: absolute;
            left: 0;
            top: 7px;
            width: 18px;
            height: 18px;
        }

        .mini-goldfish .mini-tail::before,
        .mini-goldfish .mini-tail::after {
            content: "";
            position: absolute;
            left: 0;
            width: 18px;
            height: 10px;
            background: linear-gradient(135deg, #ffd166, #ff7a00);
            border-radius: 70% 20% 70% 20%;
        }

        .mini-goldfish .mini-tail::before {
            top: 0;
            transform: rotate(28deg);
        }

        .mini-goldfish .mini-tail::after {
            bottom: 0;
            transform: rotate(-28deg);
        }

        .mini-goldfish .mini-eye {
            position: absolute;
            right: 8px;
            top: 11px;
            width: 4px;
            height: 4px;
            background: #111;
            border-radius: 50%;
        }

        .wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            padding: 60px 90px;
            gap: 50px;
            position: relative;
            z-index: 2;
        }

        .left h1 {
            font-size: 58px;
            line-height: 1.1;
            margin: 0;
        }

        .left h1 span {
            color: #35e6a1;
        }

        .left p {
            margin-top: 22px;
            font-size: 19px;
            color: #d7dbff;
            line-height: 1.7;
            max-width: 620px;
        }

        .feature-box {
            margin-top: 35px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .feature {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 16px 20px;
            border-radius: 14px;
            color: #d7dbff;
            min-width: 130px;
        }

        .feature b {
            display: block;
            color: #35e6a1;
            margin-bottom: 5px;
        }

        .login-card {
            background: rgba(21, 21, 71, 0.88);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 24px;
            padding: 42px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.35);
            backdrop-filter: blur(12px);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(53,230,161,0.12);
            border: 1px solid rgba(53,230,161,0.45);
            box-shadow: 0 0 30px rgba(53,230,161,0.2);
        }

        .login-card h2 {
            text-align: center;
            margin: 12px 0 8px;
            font-size: 34px;
        }

        .login-card p {
            text-align: center;
            color: #cfd3ff;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffffff;
        }

        input {
            width: 100%;
            padding: 16px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            margin-bottom: 18px;
            background: rgba(255,255,255,0.08);
            color: white;
            font-size: 15px;
            outline: none;
        }

        input::placeholder {
            color: #aab0ff;
        }

        input:focus {
            border-color: #35e6a1;
            box-shadow: 0 0 0 3px rgba(53,230,161,0.12);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, #35e6a1, #2ac7c9);
            color: #111;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(53,230,161,0.25);
        }

        .error {
            background: rgba(255,95,109,0.15);
            color: #ffb3ba;
            border: 1px solid rgba(255,95,109,0.4);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            text-align: center;
        }

        .info-login {
            margin-top: 20px;
            text-align: center;
            color: #aab0ff;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            body {
                overflow-y: auto;
            }

            .wrapper {
                grid-template-columns: 1fr;
                padding: 35px;
            }

            .left h1 {
                font-size: 42px;
            }

            .login-card {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

<div class="goldfish goldfish-1">
    <div class="goldfish-tail"></div>
    <div class="goldfish-body"></div>
    <div class="goldfish-head"></div>
    <div class="goldfish-fin"></div>
    <div class="goldfish-eye"></div>
</div>

<div class="goldfish goldfish-2">
    <div class="goldfish-tail"></div>
    <div class="goldfish-body"></div>
    <div class="goldfish-head"></div>
    <div class="goldfish-fin"></div>
    <div class="goldfish-eye"></div>
</div>

<div class="goldfish goldfish-3">
    <div class="goldfish-tail"></div>
    <div class="goldfish-body"></div>
    <div class="goldfish-head"></div>
    <div class="goldfish-fin"></div>
    <div class="goldfish-eye"></div>
</div>

<div class="wrapper">

    <div class="left">
        <h1>Sistem Monitoring<br><span>Kolam Ikan Koki</span></h1>
        <p>
            Pantau kondisi air kolam ikan koki secara real-time melalui dashboard web.
            Sistem membaca suhu, kekeruhan, TDS, serta status pompa filtrasi otomatis.
        </p>

        <div class="feature-box">
            <div class="feature">
                <b>Suhu Air</b>
                DS18B20
            </div>

            <div class="feature">
                <b>Kekeruhan</b>
                Turbidity Sensor
            </div>

            <div class="feature">
                <b>TDS</b>
                Zat Terlarut
            </div>
        </div>
    </div>

    <div class="login-card">
        <div class="logo">
            <div class="logo-icon">
                <div class="mini-goldfish">
                    <div class="mini-tail"></div>
                    <div class="mini-body"></div>
                    <div class="mini-eye"></div>
                </div>
            </div>
        </div>

        <h2>Login</h2>
        <p>Masuk untuk membuka dashboard monitoring.</p>

        <?php if ($error != "") : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login" class="btn-login">Masuk Dashboard</button>
        </form>

        <div class="info-login">
            Username: admin | Password: admin123
        </div>
    </div>

</div>

</body>
</html>