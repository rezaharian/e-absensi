<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - E-Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-jQDfXgqFz8r5O0iVHxFh+TjB77hU/0G2ukrDsb7q9gqk2O0nBQ5/1Tqa28VVxUG3TkLztlPZ4l+M+5C6e3uW5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
        }

        .form-control:focus {
            border-color: #46a8e5;
            box-shadow: 0 0 0 0.2rem rgba(70, 181, 229, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #46abe5, #63e8f1);
            border: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        .date-inputs .form-control {
            text-align: center;
            font-weight: 500;
        }

        .card-footer {
            background: transparent;
            border-top: none;
        }

        h4 {
            color: #1e293b;
        }
    </style>
</head>

<body class="d-flex  align-items-center min-vh-80 pt-5">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('logo.png') }}" alt="MyApp Logo" height="80">

                </div>
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4 fw-bold"><i class="fas fa-user-circle me-2"></i>Login</h4>

                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- No Payroll -->
                            <div class="form-floating mb-3">
                                <input id="no_payroll" type="number" name="no_payroll" class="form-control"
                                    placeholder="Masukkan No. Payroll" required autofocus>
                                <label for="no_payroll">No. Payroll</label>
                            </div>

                            <!-- Tanggal Lahir -->
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <div class="row g-2 mb-3 date-inputs">
                                <div class="col-4">
                                    <input type="number" id="tgl" placeholder="DD" maxlength="2"
                                        class="form-control" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" id="bln" placeholder="MM" maxlength="2"
                                        class="form-control" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" id="thn" placeholder="YYYY" maxlength="4"
                                        class="form-control" required>
                                </div>
                            </div>

                            <input type="hidden" name="tgl_lahir" id="tgl_lahir">

                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 mt-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        &copy; {{ date('Y') }} E-Absensi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tglInput = document.getElementById('tgl');
        const blnInput = document.getElementById('bln');
        const thnInput = document.getElementById('thn');
        const hiddenInput = document.getElementById('tgl_lahir');
        const form = document.getElementById('loginForm');

        // Fungsi untuk auto pad dengan 0 dan pindah fokus
        function autoMoveAndPad(current, next, maxLength) {
            current.addEventListener('input', function() {
                if (current.value.length >= maxLength) {
                    // PadStart 2 digit untuk tgl & bln
                    if (maxLength === 2 && current.value.length === 1) {
                        current.value = current.value.padStart(2, '0');
                    }
                    if (next) next.focus();
                }
            });

            // Jika blur dan kurang digit, tetap padStart
            current.addEventListener('blur', function() {
                if (current.value.length < maxLength) {
                    current.value = current.value.padStart(maxLength, '0');
                }
            });
        }

        autoMoveAndPad(tglInput, blnInput, 2);
        autoMoveAndPad(blnInput, thnInput, 2);
        autoMoveAndPad(thnInput, null, 4);

        // Saat submit, gabungkan menjadi format YYYY-MM-DD
        form.addEventListener('submit', function(e) {
            const tgl = tglInput.value.padStart(2, '0');
            const bln = blnInput.value.padStart(2, '0');
            const thn = thnInput.value;
            hiddenInput.value = `${thn}-${bln}-${tgl}`;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
