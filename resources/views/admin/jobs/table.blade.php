{{-- 
    ไฟล์: resources/views/admin/jobs/table.blade.php 
--}}

{{-- ========================================================= --}}
{{-- 📱 1. MOBILE VIEW (แสดงเป็น Card เมื่อจอเล็ก) --}}
{{-- ========================================================= --}}
<div class="md:hidden space-y-3 p-4">
    @forelse($jobs as $job)
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 relative group">
            
            {{-- ส่วนหัว: เลข Job + สถานะ --}}
            <div class="flex justify-between items-start mb-3">
                <div>
                    <a href="{{ route('admin.jobs.show', $job->id) }}" class="font-bold text-agri-primary bg-agri-primary/10 px-2 py-1 rounded-md text-sm">
                        {{ $job->job_number }}
                    </a>
                </div>
                @php
                    $statusConfig = match($job->status) {
                        'scheduled' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'นัดหมาย'],
                        'in_progress' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'กำลังทำ 🚜'],
                        'completed_pending_approval' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'รอตรวจ'],
                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'เสร็จสิ้น ✅'],
                        'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'ยกเลิก'],
                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'label' => $job->status]
                    };
                @endphp
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border border-transparent {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    {{ $statusConfig['label'] }}
                </span>
            </div>

            {{-- ข้อมูลรายละเอียด --}}
            <div class="space-y-2 text-sm text-gray-600">
                {{-- ลูกค้า --}}
                <div class="flex items-start gap-3">
                    <div class="w-5 text-center text-gray-400"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <div class="font-bold text-gray-800">{{ $job->customer->name ?? '-' }}</div>
                        <a href="tel:{{ $job->customer->phone ?? '' }}" class="text-xs text-blue-500 flex items-center gap-1">
                            <i class="fa-solid fa-phone"></i> {{ $job->customer->phone ?? '' }}
                        </a>
                    </div>
                </div>

                {{-- เวลานัดหมาย --}}
                <div class="flex items-center gap-3">
                    <div class="w-5 text-center text-gray-400"><i class="fa-regular fa-clock"></i></div>
                    <div>
                        <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('d M Y') }}</span>
                        <span class="text-xs ml-1 bg-gray-100 px-1.5 py-0.5 rounded text-gray-500">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('H:i') }} น.</span>
                    </div>
                </div>

                {{-- เครื่องจักร --}}
                <div class="flex items-center gap-3">
                    <div class="w-5 text-center text-gray-400"><i class="fa-solid fa-tractor"></i></div>
                    <div class="text-gray-800">{{ $job->equipment->name ?? '-' }}</div>
                </div>

                {{-- พนักงาน (ปุ่ม Assign) --}}
                <div class="flex items-center gap-3 mt-1">
                    <div class="w-5 text-center text-gray-400"><i class="fa-solid fa-user-gear"></i></div>
                    <div class="flex-1">
                        @if($job->assignedStaff)
                            <button onclick="openAssignModal({{ $job->id }}, '{{ $job->job_number }}', {{ $job->assigned_staff_id }})" 
                                class="w-full text-left text-xs text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg flex items-center justify-between hover:bg-white hover:border-blue-300 transition">
                                <span>{{ $job->assignedStaff->name }}</span>
                                <i class="fa-solid fa-pen text-gray-400 text-[10px]"></i>
                            </button>
                        @else
                            <button onclick="openAssignModal({{ $job->id }}, '{{ $job->job_number }}')" 
                                class="w-full text-xs font-bold text-white bg-red-500 px-3 py-1.5 rounded-lg shadow-sm shadow-red-200 flex items-center justify-center gap-2 hover:bg-red-600 transition animate-pulse">
                                <i class="fa-solid fa-plus"></i> ระบุช่างตอนนี้
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ปุ่ม Action --}}
            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end gap-2">
                {{-- View --}}
                <a href="{{ route('admin.jobs.show', $job->id) }}" class="flex-1 bg-gray-50 text-gray-600 py-2 rounded-lg text-xs font-bold text-center border border-gray-200 hover:bg-gray-100">
                    <i class="fa-solid fa-eye mr-1"></i> ดู
                </a>

                {{-- Edit --}}
                @if(in_array($job->status, ['scheduled', 'in_progress']))
                    <a href="{{ route('admin.jobs.edit', $job->id) }}" class="flex-1 bg-blue-50 text-blue-600 py-2 rounded-lg text-xs font-bold text-center border border-blue-100 hover:bg-blue-100">
                        <i class="fa-solid fa-pen mr-1"></i> แก้ไข
                    </a>
                @endif

                {{-- Review --}}
                @if($job->status == 'completed_pending_approval')
                    <a href="{{ route('admin.jobs.review', $job->id) }}" class="flex-1 bg-orange-500 text-white py-2 rounded-lg text-xs font-bold text-center shadow-lg shadow-orange-200 hover:bg-orange-600 animate-bounce">
                        <i class="fa-solid fa-clipboard-check mr-1"></i> ตรวจงาน
                    </a>
                @endif
                
                 {{-- Cancel --}}
                 @if($job->status == 'scheduled')
                    <button onclick="cancelJob({{ $job->id }}, '{{ $job->job_number }}')" class="w-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg border border-red-100 hover:bg-red-100">
                        <i class="fa-solid fa-ban"></i>
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-200">
            <i class="fa-solid fa-box-open text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-400 text-sm">ไม่พบงาน</p>
        </div>
    @endforelse
</div>


{{-- ========================================================= --}}
{{-- 🖥️ 2. DESKTOP VIEW (แสดงเป็น Table เมื่อจอใหญ่) --}}
{{-- ========================================================= --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 font-medium">Job No.</th>
                <th class="px-6 py-4 font-medium">ลูกค้า</th>
                <th class="px-6 py-4 font-medium">เครื่องจักร/พนักงาน</th>
                <th class="px-6 py-4 font-medium">เวลานัดหมาย</th>
                <th class="px-6 py-4 font-medium">สถานะ</th>
                <th class="px-6 py-4 font-medium text-right">จัดการ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($jobs as $job)
                <tr class="hover:bg-blue-50/30 transition duration-150 group">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.jobs.show', $job->id) }}" class="font-bold text-agri-primary bg-agri-primary/10 px-2 py-1 rounded-md hover:bg-agri-primary hover:text-white transition inline-block">
                            {{ $job->job_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $job->customer->name ?? '-' }}</div>
                        <a href="tel:{{ $job->customer->phone ?? '' }}" class="text-xs text-gray-500 flex items-center gap-1 hover:text-blue-600 transition w-fit">
                            <i class="fa-solid fa-phone text-[10px]"></i> {{ $job->customer->phone ?? '' }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-800 font-medium flex items-center gap-1">
                            <i class="fa-solid fa-tractor text-gray-400 text-xs"></i> {{ $job->equipment->name ?? '-' }}
                        </div>
                        
                        {{-- Quick Assign Button --}}
                        <div class="mt-1">
                            @if($job->assignedStaff)
                                <button onclick="openAssignModal({{ $job->id }}, '{{ $job->job_number }}', {{ $job->assigned_staff_id }})" 
                                    class="text-xs text-gray-500 flex items-center gap-1 hover:text-blue-600 hover:bg-blue-50 px-1.5 py-0.5 rounded transition cursor-pointer border border-transparent hover:border-blue-100">
                                    <i class="fa-solid fa-user-gear text-[10px]"></i> {{ $job->assignedStaff->name }}
                                </button>
                            @else
                                <button onclick="openAssignModal({{ $job->id }}, '{{ $job->job_number }}')" 
                                    class="text-xs text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded flex items-center gap-1 hover:bg-red-100 transition animate-pulse cursor-pointer">
                                    <i class="fa-solid fa-user-plus"></i> ระบุช่าง
                                </button>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->scheduled_start)->format('H:i') }} น.</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusConfig = match($job->status) {
                                'scheduled' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100', 'label' => 'นัดหมาย'],
                                'in_progress' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'กำลังทำ 🚜'],
                                'completed_pending_approval' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'label' => 'รอตรวจสอบ'],
                                'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200', 'label' => 'เสร็จสิ้น ✅'],
                                'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100', 'label' => 'ยกเลิก ❌'],
                                default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'border' => '', 'label' => $job->status]
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} inline-block text-center min-w-[100px] shadow-sm">
                            {{ $statusConfig['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition" title="รายละเอียด">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($job->status == 'completed_pending_approval')
                                <a href="{{ route('admin.jobs.review', $job->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-orange-300 bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition shadow-sm animate-bounce" title="ตรวจสอบงาน">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </a>
                            @endif
                            @if(in_array($job->status, ['scheduled', 'in_progress']))
                                <a href="{{ route('admin.jobs.edit', $job->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-orange-500 hover:border-orange-500 hover:bg-orange-50 transition" title="แก้ไข">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            @endif
                            @if($job->status == 'scheduled')
                                <button onclick="cancelJob({{ $job->id }}, '{{ $job->job_number }}')" class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-100 text-red-400 hover:text-red-600 hover:bg-red-50 transition" title="ยกเลิกงาน">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center text-gray-300">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-regular fa-folder-open text-3xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">ไม่พบข้อมูลงาน</p>
                            @if(request('status') || request('search'))
                                <button onclick="window.location.reload()" class="text-xs text-agri-primary hover:underline mt-2">ล้างตัวกรอง</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
@if($jobs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        {{ $jobs->appends(request()->query())->links() }}
    </div>
@endif