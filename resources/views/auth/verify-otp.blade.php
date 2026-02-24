@extends('layouts.auth')

@section('content')
<div class="card">
    <div class="card-body">

        <h4 class="text-center mb-4">Verifikasi OTP</h4>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verify.otp') }}">
            @csrf

            <div class="form-group">
                <label>Masukkan 6 Digit OTP</label>
                <input type="text" name="otp" class="form-control text-center" maxlength="6" required>
            </div>

            <button type="submit" class="btn btn-gradient-primary btn-block mt-3">
                Verifikasi
            </button>
        </form>

    </div>
</div>
@endsection