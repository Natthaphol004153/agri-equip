@extends('layouts.guest') {{-- หรือ Layout อื่นถ้ามี --}}

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4>ยินดีต้อนรับ, {{ Auth::guard('customer')->user()->name }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>เบอร์โทรศัพท์:</strong> {{ Auth::guard('customer')->user()->phone }}</p>
                    <p><strong>ประเภทลูกค้า:</strong> {{ Auth::guard('customer')->user()->customer_type }}</p>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary">ดูประวัติการเข้าใช้บริการ (Coming Soon)</a>
                        
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">ออกจากระบบ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection