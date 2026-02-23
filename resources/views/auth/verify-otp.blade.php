@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title"> Verifikasi OTP </h3>
    </div>

    <div class="card">
        <div class="card-body">

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verify.otp') }}">
                @csrf

                <div class="form-group">
                    <label>Masukkan 6 Digit OTP</label>
                    <input type="text" name="otp" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-gradient-primary mt-3">
                    Verifikasi
                </button>
            </form>

        </div>
    </div>
</div>
@endsection