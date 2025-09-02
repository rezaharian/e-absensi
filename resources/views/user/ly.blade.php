<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Absensi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Poppins Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #e8f4fa;
            letter-spacing: 0.5px;
        }


        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Background biru melengkung di atas */
        .header-bg {
            position: relative;
            height: 300px;
            background: linear-gradient(to bottom,
                    rgba(0, 123, 255, 1),
                    rgba(51, 153, 255, 0.979),
                    rgba(102, 204, 255, 0.8),
                    rgba(153, 230, 255, 0.7)),

                url('/batik3.jpg') left/cover no-repeat;
            background-blend-mode: normal;
        }


        @media (max-width: 768px) {
            .header-bg {
                height: 280px;
                border-bottom-left-radius: 50% 40%;
                border-bottom-right-radius: 50% 40%;
            }
        }

        /* Tampilan blokir untuk layar besar */
        #desktop-warning {
            display: none;
            background: white;
            color: black;
            height: 100vh;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        @media (min-width: 769px) {
            #desktop-warning {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                /* biar full layar */
                text-align: center;
                background-color: #ffffff;
                padding: 20px;
            }

            #desktop-warning img {
                margin-bottom: 20px;
            }

            #desktop-warning h2 {
                font-weight: 700;
                font-size: 1.5rem;
                color: #d9534f;
                margin-bottom: 10px;
            }

            #desktop-warning p {
                font-size: 1rem;
                color: #555;
            }

            #app-content {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Pesan khusus jika buka di laptop/pc/tablet -->
    <div id="desktop-warning">
        <img src="{{ asset('logo.png') }}" alt="MyApp Logo" height="80">

        <h2>MAAF RESOLUSI ANDA TERLALU BESAR</h2>
        <p>Silahkan buka menggunakan handphone</p>
    </div>

    <!-- Konten utama -->
    <div id="app-content">
        <div class="header-bg">
            @include('user.nav')
            <div class="pb-5">
                <div class="pb-5">
                    @yield('content')
                </div>
            </div>
            @include('user.menubawah')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function updateJam() {
            let now = new Date();
            let jam = String(now.getHours()).padStart(2, '0');
            let menit = String(now.getMinutes()).padStart(2, '0');
            let detik = String(now.getSeconds()).padStart(2, '0');
            let el = document.getElementById("jamSekarang");
            if (el) el.textContent = jam + ":" + menit + ":" + detik;
        }

        updateJam();
        setInterval(updateJam, 1000);
    </script>
</body>

</html>
