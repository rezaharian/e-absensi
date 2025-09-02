<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container my-5 text-muted">
    <div class="row g-3">

        <div class="col-3 col-md-3">
            <div class="menu-card">
                <a href="{{ route('user.dashboard.ipamdt') }}">
                    <div class="icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <p>Telat</p>
                </a>
            </div>
        </div>

        <div class="col-3 col-md-3">
            <div class="menu-card">
                <a href="{{ route('user.dashboard.cuti') }}">
                    <div class="icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <p>Cuti</p>
                </a>
            </div>
        </div>

        <div class="col-3 col-md-3">
            <div class="menu-card">
                <a href="{{ route('user.dashboard.shift') }}">
                    <div class="icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <p>Shift</p>
                </a>
            </div>
        </div>
        <div class="col-3 col-md-3 ">
            <div class="menu-card ">
                <a href="{{ route('attendance.index') }}">
                    <div class="icon">
                        <i class="bi bi-activity"></i>
                    </div>
                    <p>Aktifitas</p>
                </a>
            </div>
        </div>
        <div class="col-3 col-md-3 ">
            <div class="menu-card " style="background-color: #4950573c">
                <div class="icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <p>Rembuse</p>
            </div>
        </div>

        <div class="col-3 col-md-3 ">
            <div class="menu-card " style="background-color: #4950573c">
                <div class="icon">
                    <i class="bi bi-newspaper"></i>
                </div>
                <p>Berita</p>
            </div>
        </div>

        <div class="col-3 col-md-3 ">
            <div class="menu-card " style="background-color: #4950573c">
                <div class="icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <p>Slip Gaji</p>
            </div>
        </div>

        <div class="col-3 col-md-3 ">
            <div class="menu-card " style="background-color: #4950573c">
                <div class="icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <p>Info</p>
            </div>
        </div>

        @if (Auth::user() && Auth::user()->bagian === 'EDP')
            <div class="col-3 col-md-3 ">
                <div class="menu-card " style="background-color: #8cff0079">
                    <a href="{{ route('halamanadmin.index') }}">
                        <div class="icon">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <p>Admin</p>
                        <smallv class="m-0 p-0" style="font-size: 6pt">Hanya EDP </smallv>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Container menu-card */
    .menu-card {
        background: #fff;
        border-radius: 16px;
        padding: 12px 5px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        text-decoration: none
    }

    .menu-card a {
        display: block;
        /* biar seluruh card bisa di-klik */
        text-decoration: none;
        /* hilangkan underline */
        color: inherit;
        /* pakai warna dari parent */
    }

    /* Efek hover */
    .menu-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        background: #f8f9fa;
    }

    /* Icon dalam card - semuanya biru soft */
    .menu-card .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 24px;
        transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease;

        background-color: rgba(0, 123, 255, 0.1);
        /* biru soft */
        color: #007bff;
        /* biru utama */
    }

    /* Animasi ikon saat hover */
    .menu-card:hover .icon {
        transform: rotate(10deg) scale(1.1);
    }

    /* Teks label */
    .menu-card p {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        transition: color 0.3s ease;
        text-decoration: none
    }

    /* Ubah warna teks saat hover */
    .menu-card:hover p {
        color: #007bff;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .menu-card {
            padding: 10px 5px;
        }

        .menu-card .icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
            margin-bottom: 8px;
        }

        .menu-card p {
            font-size: 12px;
        }
    }
</style>
