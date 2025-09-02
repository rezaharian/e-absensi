<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - E-Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-jQDfXgqFz8r5O0iVHxFh+TjB77hU/0G2ukrDsb7q9gqk2O0nBQ5/1Tqa28VVxUG3TkLztlPZ4l+M+5C6e3uW5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .welcome-card {
            background: #ffffff;
            border-radius: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            text-align: center;
            padding: 4rem 2rem;
            max-width: 400px;
            width: 90%;
            color: #1e293b;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
        }

        .welcome-card img {
            height: 100px;
            margin-bottom: 2rem;
            animation: popIn 0.8s ease forwards;
        }

        .welcome-card h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #0f172a;
        }

        .welcome-card p {
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 2.5rem;
            color: #475569;
        }

        /* Input focus */
        .form-control:focus {
            border-color: #46a8e5;
            box-shadow: 0 0 0 0.2rem rgba(70, 181, 229, 0.25);
        }

        /* Tombol gradient biru */
        .btn-primary {
            background: linear-gradient(135deg, #46abe5, #63e8f1);
            border: none;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
            color: #fff;
            box-shadow: 0 8px 20px rgba(70, 181, 229, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #63e8f1, #46abe5);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(70, 181, 229, 0.35);
        }

        .btn-outline-primary {
            border-radius: 50px;
            border: 2px solid #46abe5;
            color: #46abe5;
            padding: 0.8rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: rgba(70, 171, 229, 0.1);
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        nav a {
            margin: 0.5rem 0.5rem 0 0.5rem;
        }

        .footer {
            position: absolute;
            bottom: 1rem;
            width: 100%;
            text-align: center;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="welcome-card">
        <img src="{{ asset('logo.png') }}" alt="E-Absensi Logo">
        <h1>Selamat Datang</h1>
        <p>Absensi jadi lebih mudah dan efisien dengan E-Absensi
        </p>

        @if (Route::has('login'))
            <nav>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary w-100 mb-2"><i
                            class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2"><i
                            class="fas fa-sign-in-alt me-2"></i>Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100" hidden><i
                                class="fas fa-user-plus me-2"></i>Register</a>
                    @endif
                @endauth
            </nav>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} E-Absensi
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
