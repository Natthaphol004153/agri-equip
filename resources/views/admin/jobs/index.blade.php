@extends('layouts.admin')

@section('title', 'รายการงาน')
@section('header', 'จัดการงาน (Jobs)')

@section('content')
<div class="max-w-7xl mx-auto pb-12">

    {{-- 1. Header & Action (ส่วนหัว + ปุ่ม Action) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-tractor text-agri-primary"></i> รายการจองคิวงาน
            </h2>
            <p class="text-sm text-gray-500 mt-1">ติดตามสถานะงาน ตรวจสอบ และมอบหมายพนักงาน</p>
        </div>

        {{-- ปุ่ม Actions Group --}}
        <div class="flex flex-wrap gap-3">
            {{-- ✅ ปุ่ม EXPORT EXCEL (เพิ่มใหม่) --}}
            <a href="{{ route('admin.export.jobs') }}" target="_blank" class="group bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-bold text-sm">
                <div class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                </div>
                Export Excel
            </a>

            {{-- ปุ่มเพิ่มงาน (ของเดิม) --}}
            <a href="{{ route('admin.jobs.create') }}" class="group bg-agri-primary text-white px-6 py-3 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-bold text-sm">
                <div class="bg-white/20 rounded-full w-6 h-6 flex items-center justify-center group-hover:rotate-90 transition-transform">
                    <i class="fa-solid fa-plus text-xs"></i>
                </div>
                เพิ่มงานใหม่
            </a>
        </div>
    </div>

    {{-- 2. Main Card Container --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        
        {{-- 🔥 Section A: Status Tabs (แถบเลือกสถานะแบบ Tab ด้านบน) --}}
        <div class="border-b border-gray-100 bg-gray-50/50">
            <div class="flex overflow-x-auto scrollbar-hide px-4 pt-4 pb-0 gap-1" id="status-tabs">
                @foreach([
                    'all' => ['label'=>'ทั้งหมด', 'icon'=>'fa-layer-group', 'color'=>'text-gray-500', 'active'=>'text-agri-primary border-agri-primary bg-white'],
                    'scheduled' => ['label'=>'นัดหมาย', 'icon'=>'fa-clock', 'color'=>'text-blue-500', 'active'=>'text-blue-600 border-blue-500 bg-blue-50/50'],
                    'in_progress' => ['label'=>'กำลังทำ', 'icon'=>'fa-tractor', 'color'=>'text-purple-500', 'active'=>'text-purple-600 border-purple-500 bg-purple-50/50'],
                    'completed_pending_approval' => ['label'=>'รอตรวจ', 'icon'=>'fa-clipboard-check', 'color'=>'text-orange-500', 'active'=>'text-orange-600 border-orange-500 bg-orange-50/50'],
                    'completed' => ['label'=>'เสร็จสิ้น', 'icon'=>'fa-circle-check', 'color'=>'text-green-500', 'active'=>'text-green-600 border-green-500 bg-green-50/50'],
                    'cancelled' => ['label'=>'ยกเลิก', 'icon'=>'fa-ban', 'color'=>'text-red-500', 'active'=>'text-red-600 border-red-500 bg-red-50/50'],
                ] as $key => $tab)
                    <button onclick="filterStatus('{{ $key }}')" 
                        class="status-tab relative px-5 py-3 rounded-t-lg border-b-2 text-sm font-bold transition-all duration-200 whitespace-nowrap flex items-center gap-2 hover:bg-white/60
                        {{ (request('status') ?? 'all') == $key ? $tab['active'] : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                        data-status="{{ $key }}">
                        <i class="fa-solid {{ $tab['icon'] }} {{ (request('status') ?? 'all') == $key ? '' : 'opacity-70' }}"></i> 
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- 🔥 Section B: Toolbar (Search & Filter - รวมไว้ในแถวเดียว) --}}
        <div class="p-4 border-b border-gray-100 bg-white flex flex-col md:flex-row gap-4 justify-between items-center sticky top-0 z-10">
            
            {{-- Left: Search --}}
            <div class="relative w-full md:w-80 group">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-agri-primary transition-colors"></i>
                <input type="text" id="search-input" placeholder="ค้นหา Job No., ชื่อลูกค้า, เบอร์โทร..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary focus:bg-white outline-none transition-all placeholder:text-gray-400">
            </div>

            {{-- Right: Machine Filter --}}
            <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto scrollbar-hide pb-1 md:pb-0">
                <span class="text-xs font-bold text-gray-400 whitespace-nowrap uppercase tracking-wider hidden md:block">
                    <i class="fa-solid fa-filter mr-1"></i>Filter:
                </span>
                
                <div class="flex bg-gray-100/80 p-1 rounded-lg">
                    @foreach([
                        'all' => 'ทั้งหมด',
                        'tractor' => 'รถไถ',
                        'drone' => 'โดรน',
                        'harvester' => 'รถเกี่ยว',
                        'excavator' => 'แม็คโคร',
                        'other' => 'อื่นๆ'
                    ] as $key => $label)
                        <button onclick="filterMachine('{{ $key }}')" 
                            class="machine-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all whitespace-nowrap
                            {{ (request('machine_type') ?? 'all') == $key ? 'bg-white text-gray-800 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50' }}"
                            data-machine="{{ $key }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. Table Container --}}
        <div id="table-container" class="relative min-h-[300px] bg-white">
            @include('admin.jobs.table')
            
            {{-- Loading State --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/90 backdrop-blur-[1px] flex flex-col items-center justify-center z-20 hidden transition-opacity duration-300">
                <div class="w-12 h-12 border-4 border-agri-primary/20 border-t-agri-primary rounded-full animate-spin mb-3"></div>
                <span class="text-sm text-gray-500 font-bold animate-pulse">กำลังโหลดข้อมูล...</span>
            </div>
        </div>
    </div>

    {{-- MODAL: มอบหมายงาน (Design เดิมแต่ Clean ขึ้น) --}}
    <div id="assignModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeAssignModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-sm border border-gray-100">
                    
                    <div class="bg-gray-50 px-5 py-4 flex justify-between items-center border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="bg-agri-primary/10 text-agri-primary p-2 rounded-lg"><i class="fa-solid fa-user-tag"></i></span>
                            มอบหมายงาน
                        </h3>
                        <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition bg-white hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="text-center mb-5">
                            <p class="text-sm text-gray-500">เลือกพนักงานสำหรับใบงาน</p>
                            <span id="modal-job-number" class="block mt-1 text-lg font-black text-agri-primary"></span>
                        </div>
                        
                        <input type="hidden" id="modal-job-id">
                        
                        <div class="space-y-2 max-h-[250px] overflow-y-auto custom-scrollbar pr-1">
                            @foreach($staffs as $staff)
                                <label class="group flex items-center gap-3 p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all has-[:checked]:border-agri-primary has-[:checked]:bg-green-50 has-[:checked]:ring-1 has-[:checked]:ring-agri-primary">
                                    <input type="radio" name="staff_select" value="{{ $staff->id }}" class="hidden peer">
                                    
                                    {{-- Custom Radio Indicator --}}
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-agri-primary peer-checked:bg-agri-primary flex items-center justify-center text-white text-[10px] transition-colors">
                                        <i class="fa-solid fa-check opacity-0 peer-checked:opacity-100"></i>
                                    </div>

                                    <div class="flex items-center gap-3 flex-1">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->name) }}&background=random" class="w-9 h-9 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $staff->name }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Staff</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <button onclick="submitAssign()" class="w-full mt-6 bg-agri-primary text-white py-3.5 rounded-xl font-bold shadow-lg shadow-agri-primary/20 hover:bg-agri-hover hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Scripts (ปรับปรุง Logic ให้ Smooth ขึ้น) --}}
<script>
    let currentStatus = '{{ request("status") ?? "all" }}';
    let currentMachineType = '{{ request("machine_type") ?? "all" }}';
    let searchTimeout = null;

    function fetchJobs(url = "{{ route('admin.jobs.index') }}") {
        const loading = document.getElementById('loading-overlay');
        const container = document.getElementById('table-container');
        const search = document.getElementById('search-input').value;

        loading.classList.remove('hidden');

        const fetchUrl = new URL(url);
        if(currentStatus !== 'all') fetchUrl.searchParams.set('status', currentStatus);
        if(currentMachineType !== 'all') fetchUrl.searchParams.set('machine_type', currentMachineType);
        if(search) fetchUrl.searchParams.set('search', search);

        fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            // Re-append loading overlay to keep it available
            container.insertAdjacentHTML('beforeend', `<div id="loading-overlay" class="absolute inset-0 bg-white/90 backdrop-blur-[1px] flex flex-col items-center justify-center z-20 hidden transition-opacity duration-300"><div class="w-12 h-12 border-4 border-agri-primary/20 border-t-agri-primary rounded-full animate-spin mb-3"></div><span class="text-sm text-gray-500 font-bold animate-pulse">กำลังโหลดข้อมูล...</span></div>`);
            initPagination();
        })
        .finally(() => {
            document.getElementById('loading-overlay').classList.add('hidden');
        });
    }

    function filterStatus(status) {
        currentStatus = status;
        
        // Update Tabs UI
        document.querySelectorAll('.status-tab').forEach(btn => {
            // Reset Styles
            btn.className = `status-tab relative px-5 py-3 rounded-t-lg border-b-2 text-sm font-bold transition-all duration-200 whitespace-nowrap flex items-center gap-2 hover:bg-white/60 border-transparent text-gray-500 hover:text-gray-700`;
            btn.querySelector('i').classList.add('opacity-70');
            
            // Active Style Logic (Hardcoded classes based on design above for simplicity in vanilla JS)
            if(btn.dataset.status === status) {
                btn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700');
                btn.querySelector('i').classList.remove('opacity-70');
                
                // Add specific active classes
                if(status === 'all') btn.classList.add('text-agri-primary', 'border-agri-primary', 'bg-white');
                if(status === 'scheduled') btn.classList.add('text-blue-600', 'border-blue-500', 'bg-blue-50/50');
                if(status === 'in_progress') btn.classList.add('text-purple-600', 'border-purple-500', 'bg-purple-50/50');
                if(status === 'completed_pending_approval') btn.classList.add('text-orange-600', 'border-orange-500', 'bg-orange-50/50');
                if(status === 'completed') btn.classList.add('text-green-600', 'border-green-500', 'bg-green-50/50');
                if(status === 'cancelled') btn.classList.add('text-red-600', 'border-red-500', 'bg-red-50/50');
            }
        });
        fetchJobs();
    }

    function filterMachine(type) {
        currentMachineType = type;
        document.querySelectorAll('.machine-filter-btn').forEach(btn => {
            if(btn.dataset.machine === type) {
                btn.className = `machine-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all whitespace-nowrap bg-white text-gray-800 shadow-sm ring-1 ring-black/5`;
            } else {
                btn.className = `machine-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all whitespace-nowrap text-gray-500 hover:text-gray-700 hover:bg-gray-200/50`;
            }
        });
        fetchJobs();
    }

    document.getElementById('search-input').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { fetchJobs(); }, 500);
    });

    function initPagination() {
        document.querySelectorAll('#table-container .pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchJobs(this.href);
            });
        });
    }

    function openAssignModal(jobId, jobNumber, currentStaffId = null) {
        document.getElementById('assignModal').classList.remove('hidden');
        document.getElementById('modal-job-id').value = jobId;
        document.getElementById('modal-job-number').innerText = jobNumber;
        document.querySelectorAll('input[name="staff_select"]').forEach(r => r.checked = false);
        if(currentStaffId) {
            const radio = document.querySelector(`input[name="staff_select"][value="${currentStaffId}"]`);
            if(radio) radio.checked = true;
        }
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
    }

    function submitAssign() {
        const jobId = document.getElementById('modal-job-id').value;
        const staffId = document.querySelector('input[name="staff_select"]:checked')?.value;
        if(!staffId) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือก', text: 'ต้องเลือกพนักงานก่อนบันทึก', confirmButtonColor: '#308e87' });
            return;
        }
        fetch(`/admin/jobs/${jobId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ assigned_staff_id: staffId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                closeAssignModal();
                Swal.fire({
                    icon: 'success',
                    title: 'มอบหมายสำเร็จ!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchJobs();
            }
        })
        .catch(err => Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error'));
    }

    function cancelJob(jobId, jobNumber) {
        Swal.fire({
            title: `ยกเลิกงาน ${jobNumber}?`,
            text: "คุณแน่ใจหรือไม่ที่จะยกเลิกงานนี้ การกระทำนี้ไม่สามารถย้อนกลับได้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#e5e7eb',
            cancelButtonText: '<span class="text-gray-600">ไม่</span>',
            confirmButtonText: 'ยืนยันยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/jobs/${jobId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({icon: 'success', title: 'เรียบร้อย', text: data.message, timer: 1500, showConfirmButton: false});
                        fetchJobs();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                    }
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => { initPagination(); });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection