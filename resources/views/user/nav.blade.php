<!-- Navbar Responsive -->
<nav class="navbar navbar-expand-lg w-100"
    style="background-color: rgba(255,255,255,0.0); transition: background-color 0.3s ease, box-shadow 0.3s ease;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('logo.png') }}" alt="MyApp Logo" height="40">
        </a>

        <!-- Desktop Menu -->
        <div class="d-none d-lg-flex ms-auto">
            <ul class="navbar-nav text-center">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>
        </div>

        <!-- Mobile Geolocation -->
        <div class="d-lg-none ms-auto d-flex align-items-center gap-2">
            {{-- @include('user.geolocation') --}}

            <!-- Tombol Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="btn w-100 d-flex me-2  p-2 align-items-center justify-content-center gap-2 fw-bold rounded-3 text-light"
                    style="background: transparent;
                    border: none;
                    padding: 8px 0;
                    transition: all 0.3s ease;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>

        </div>

    </div>
</nav>

<!-- =====================
     CSS Navbar
===================== -->
<style>
    /* Navbar link & brand */
    .navbar .nav-link {
        font-weight: 500;
        color: #000;
        margin-right: 15px;
    }

    .navbar .nav-link:hover {
        color: #007bff;
    }

    .navbar-brand {
        font-weight: 600;
        color: #000;
    }

    /* Navbar responsiveness */
    @media (max-width: 991px) {
        .navbar-nav {
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .nav-link {
            font-size: 16px;
            font-weight: 500;
        }
    }
</style>
