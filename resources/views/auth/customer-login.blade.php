@extends('layouts.guest')

@section('content')
<div class="card shadow-lg border-0 rounded-lg mt-5">
    <div class="card-header bg-success text-white">
        <h3 class="text-center font-weight-light my-4">เข้าสู่ระบบลูกค้า (Customer)</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('customer.login.submit') }}">
            @csrf

            {{-- เบอร์โทรศัพท์ --}}
            <div class="form-floating mb-3">
                <input class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" type="text" name="phone" 
                       placeholder="08X-XXX-XXXX" value="{{ old('phone') }}" required autofocus />
                <label for="phone">เบอร์โทรศัพท์</label>
                @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- รหัสผ่าน --}}
            <div class="form-floating mb-3">
                <input class="form-control @error('password') is-invalid @enderror" 
                       id="password" type="password" name="password" 
                       placeholder="Password" required />
                <label for="password">รหัสผ่าน (4 ตัวท้ายเบอร์โทร)</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="form-check mb-3">
                <input class="form-check-input" id="remember" type="checkbox" name="remember" />
                <label class="form-check-label" for="remember">จำฉันไว้ในระบบ</label>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                <a class="small" href="#">ลืมรหัสผ่าน? (ติดต่อเจ้าหน้าที่)</a>
                <button type="submit" class="btn btn-success">เข้าสู่ระบบ</button>
            </div>
        </form>
    </div>
    <div class="card-footer text-center py-3">
        <div class="small"><a href="{{ route('login') }}">กลับหน้าหลัก / เข้าสู่ระบบเจ้าหน้าที่</a></div>
    </div>
</div>
@endsection