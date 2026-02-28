@extends('layouts.admin')

@section('title', 'Customer Management')
@section('header', 'ข้อมูลลูกค้า')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-agri-primary focus:border-agri-primary sm:text-sm shadow-sm transition" 
                   placeholder="ค้นหาชื่อ, รหัสลูกค้า, หรือเบอร์โทร...">
        </form>

        <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-agri-primary hover:bg-agri-hover focus:outline-none transition-all transform hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus mr-2"></i> เพิ่มลูกค้าใหม่
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลูกค้า</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ประเภท / พื้นที่</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ข้อมูลติดต่อ</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ที่อยู่ & แผนที่</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($customers as $c)
                    <tr class="group hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($c->profile_image)
                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm" src="{{ asset('storage/' . $c->profile_image) }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-lg shadow-inner">
                                            {{ substr($c->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 group-hover:text-agri-primary transition">{{ $c->name }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $c->customer_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-start gap-1.5">
                                @if($c->customer_type == 'farm')
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200"><i class="fa-solid fa-leaf mr-1.5 mt-0.5"></i> ฟาร์มเกษตร</span>
                                @elseif($c->customer_type == 'company')
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200"><i class="fa-solid fa-building mr-1.5 mt-0.5"></i> บริษัท</span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200"><i class="fa-solid fa-user mr-1.5 mt-0.5"></i> บุคคลทั่วไป</span>
                                @endif

                                @if($c->farm_area > 0)
                                    <span class="px-2 py-0.5 inline-flex items-center text-[11px] font-medium rounded bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <i class="fa-solid fa-map text-emerald-400 mr-1"></i> พื้นที่: <strong class="ml-1">{{ number_format($c->farm_area, 1) }}</strong> ไร่
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 flex items-center gap-2"><i class="fa-solid fa-phone text-gray-400 text-xs"></i> {{ $c->phone }}</div>
                            @if($c->email)
                                <div class="text-xs text-gray-500 flex items-center gap-2 mt-1"><i class="fa-solid fa-envelope text-gray-400"></i> {{ $c->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 truncate max-w-[200px]" title="{{ $c->address ?? '-' }} {{ $c->subdistrict }} {{ $c->district }} {{ $c->province }}">
                                {{ $c->address ?? '-' }} {{ $c->subdistrict }} {{ $c->district }} {{ $c->province }}
                            </div>
                            
                            {{-- ✅ ปุ่มเปิดแผนที่ --}}
                            @php
                                $mapUrl = '';
                                if($c->latitude && $c->longitude) {
                                    $mapUrl = "https://www.google.com/maps/search/?api=1&query={$c->latitude},{$c->longitude}";
                                } elseif($c->address) {
                                    // ค้นหาแบบรวมชื่อที่อยู่เผื่อไม่มีละติจูด
                                    $mapUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($c->address . ' ' . $c->district . ' ' . $c->province);
                                }
                            @endphp
                            
                            @if($mapUrl)
                                <a href="{{ $mapUrl }}" target="_blank" class="mt-2 inline-flex items-center gap-1.5 text-[11px] font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md hover:bg-blue-600 hover:text-white transition border border-blue-100">
                                    <i class="fa-solid fa-map-location-dot"></i> เปิดแผนที่
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.customers.edit', $c->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition border border-yellow-200" title="แก้ไข">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบข้อมูลลูกค้านี้?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition border border-red-200" title="ลบข้อมูล">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">ไม่พบข้อมูลลูกค้า</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">{{ $customers->links() }}</div>
    </div>
</div>
@endsection