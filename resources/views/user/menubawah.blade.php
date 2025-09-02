<!-- Bottom Navbar -->
<div class="fixed-bottom bg-white shadow-lg">
    <div class="d-flex justify-content-around align-items-center py-2">

        <!-- Menu Kiri -->
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark">
            <div class="text-center">
                <i class="fas fa-home fa-lg"></i>
                <div style="font-size: 12px;">Beranda</div>
            </div>
        </a>


        <!-- Menu Tengah (Absensi - Icon besar & elegan) -->
        <div class="text-center">
            <a href="{{ route('attendance.create') }}" class="text-decoration-none text-dark">
                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                    style="width:70px; height:70px; margin-top:-30px; box-shadow:0 4px 8px rgba(0,0,0,0.3);">
                    <i class="fas fa-fingerprint fa-2x"></i>
                </div>
                <div style="font-size: 13px; margin-top:5px; font-weight:600;">Absensi</div>
            </a>
        </div>

        <!-- Menu Kanan -->
        <div class="text-center">
            <a href="{{ route('attendance.index') }}" class="text-decoration-none text-dark">
                <i class="fas fa-clipboard-list fa-lg"></i>
                <div style="font-size: 12px;">Riwayat</div>
            </a>
        </div>

    </div>
</div>

<!-- Tambahkan FontAwesome -->
{{-- <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script> --}}
