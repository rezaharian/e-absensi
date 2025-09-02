@extends('user.ly')

@section('content')
    <div class="container">
        <h1>Selamat datang, Admin {{ Auth::user()->name }}</h1>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-danger">
                Logout
            </button>
        </form>
    </div>
@endsection
