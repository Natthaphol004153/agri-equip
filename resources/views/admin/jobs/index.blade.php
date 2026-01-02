@extends('layouts.admin')

@section('title', 'รายการงาน')
@section('header', 'จัดการงาน (Jobs)')

@section('content')
<div class="max-w-7xl mx-auto pb-12">

    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-tractor text-agri-primary"></i> รายการจองคิวงาน
            </h2>
            <p class="text-sm text-gray-500">มอบหมายงานและติดตามสถานะแบบ Real-time</p>
        </div>
        <a href="{{ route('admin.jobs.create') }}" class="bg-agri-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-agri-primary/30 hover:bg-agri-hover hover:-translate-y-0.5 transition flex items-center gap-2 font-medium">
            <i class="fa-solid fa-plus"></i> เพิ่มงานใหม่
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Filter Bar --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col gap-4">
            
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                {{-- 1. Status Tabs (แถวบน: สถานะงาน) --}}
                <div class="flex gap-2 overflow-x-auto scrollbar-hide w-full md:w-auto" id="filter-tabs">
                    @foreach([
                        'all' => ['icon'=>'fa-layer-group', 'label'=>'ทั้งหมด', 'color'=>'text-gray-600'],
                        'scheduled' => ['icon'=>'fa-clock', 'label'=>'นัดหมาย', 'color'=>'text-blue-600'],
                        'in_progress' => ['icon'=>'fa-tractor', 'label'=>'กำลังทำ', 'color'=>'text-purple-600'],
                        'completed_pending_approval' => ['icon'=>'fa-clipboard-check', 'label'=>'รอตรวจ', 'color'=>'text-orange-600'],
                        'completed' => ['icon'=>'fa-check-circle', 'label'=>'เสร็จสิ้น', 'color'=>'text-green-600'],
                        'cancelled' => ['icon'=>'fa-ban', 'label'=>'ยกเลิก', 'color'=>'text-red-500'],
                    ] as $key => $tab)
                        <button onclick="filterStatus('{{ $key }}')" 
                            class="filter-btn px-4 py-1.5 rounded-lg border text-xs font-bold transition shadow-sm whitespace-nowrap flex items-center gap-1
                            {{ (request('status') ?? 'all') == $key ? 'bg-agri-primary text-white border-agri-primary' : 'bg-white border-gray-200 text-gray-500 hover:border-gray-400' }}"
                            data-status="{{ $key }}">
                            <i class="fa-solid {{ $tab['icon'] }} {{ (request('status') ?? 'all') == $key ? 'text-white' : $tab['color'] }}"></i> {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Search --}}
                <div class="relative w-full md:w-64">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="search-input" placeholder="ค้นหา Job No. หรือ ชื่อลูกค้า..." 
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-agri-primary/20 focus:border-agri-primary outline-none transition">
                </div>
            </div>

            {{-- 2. 🔥 Machine Type Tabs (แถวล่าง: ประเภทรถ) [เพิ่มใหม่] --}}
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide w-full pb-1">
                <span class="text-xs font-bold text-gray-400 whitespace-nowrap"><i class="fa-solid fa-filter"></i> ประเภทรถ:</span>
                
                @foreach([
                    'all' => 'ทั้งหมด',
                    'tractor' => 'รถไถ',
                    'drone' => 'โดรน',
                    'harvester' => 'รถเกี่ยว',
                    'sprayer' => 'รถพ่นยา',
                    'excavator' => 'แม็คโคร',
                    'other' => 'อื่นๆ'
                ] as $key => $label)
                    <button onclick="filterMachine('{{ $key }}')" 
                        class="machine-filter-btn px-3 py-1 rounded-full border text-[11px] font-bold transition whitespace-nowrap
                        {{ (request('machine_type') ?? 'all') == $key ? 'bg-gray-700 text-white border-gray-700' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-100' }}"
                        data-machine="{{ $key }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

        </div>

        {{-- ⚡ TABLE CONTAINER (โหลดแยก) --}}
        <div id="table-container" class="relative min-h-[200px]">
            @include('admin.jobs.table')
            
            {{-- Loading Overlay --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 hidden">
                <div class="flex flex-col items-center">
                    <i class="fa-solid fa-circle-notch fa-spin text-agri-primary text-3xl mb-2"></i>
                    <span class="text-sm text-gray-500 font-medium">กำลังโหลดข้อมูล...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: มอบหมายงาน --}}
    <div id="assignModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" onclick="closeAssignModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all w-full max-w-sm">
                    <div class="bg-agri-primary px-4 py-4 flex justify-between items-center text-white">
                        <h3 class="font-bold flex items-center gap-2"><i class="fa-solid fa-user-tag"></i> มอบหมายงาน</h3>
                        <button onclick="closeAssignModal()" class="text-white/70 hover:text-white"><i class="fa-solid fa-times text-lg"></i></button>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4 text-center">เลือกพนักงานสำหรับงาน <span id="modal-job-number" class="font-bold text-agri-primary bg-agri-primary/10 px-2 py-0.5 rounded"></span></p>
                        <input type="hidden" id="modal-job-id">
                        <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar px-1">
                            @foreach($staffs as $staff)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-agri-primary has-[:checked]:bg-green-50 has-[:checked]:ring-1 has-[:checked]:ring-agri-primary">
                                    <input type="radio" name="staff_select" value="{{ $staff->id }}" class="text-agri-primary focus:ring-agri-primary w-4 h-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->name) }}&background=random" class="w-8 h-8 rounded-full border border-gray-200">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $staff->name }}</p>
                                            <p class="text-[10px] text-gray-500">พนักงาน (Staff)</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <button onclick="submitAssign()" class="w-full mt-6 bg-agri-primary text-white py-3 rounded-xl font-bold shadow-lg shadow-agri-primary/30 hover:bg-agri-hover transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-save"></i> บันทึกการมอบหมาย
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    let currentStatus = 'all';
    let currentMachineType = 'all'; // 🔥 เพิ่มตัวแปรเก็บค่าประเภทรถ
    let searchTimeout = null;

    // 1. ฟังก์ชันโหลดข้อมูล AJAX
    function fetchJobs(url = "{{ route('admin.jobs.index') }}") {
        const loading = document.getElementById('loading-overlay');
        const container = document.getElementById('table-container');
        const search = document.getElementById('search-input').value;

        loading.classList.remove('hidden');

        const fetchUrl = new URL(url);
        if(currentStatus !== 'all') fetchUrl.searchParams.set('status', currentStatus);
        if(currentMachineType !== 'all') fetchUrl.searchParams.set('machine_type', currentMachineType); // 🔥 ส่งค่าไป Controller
        if(search) fetchUrl.searchParams.set('search', search);

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            container.insertAdjacentHTML('beforeend', `<div id="loading-overlay" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 hidden"><div class="flex flex-col items-center"><i class="fa-solid fa-circle-notch fa-spin text-agri-primary text-3xl mb-2"></i><span class="text-sm text-gray-500 font-medium">กำลังโหลดข้อมูล...</span></div></div>`);
            initPagination();
        })
        .finally(() => {
            document.getElementById('loading-overlay').classList.add('hidden');
        });
    }

    // 2. จัดการ Filter Status (เหมือนเดิม)
    function filterStatus(status) {
        currentStatus = status;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            const icon = btn.querySelector('i');
            if(btn.dataset.status === status) {
                btn.className = `filter-btn px-4 py-1.5 rounded-lg border text-xs font-bold transition shadow-sm whitespace-nowrap flex items-center gap-1 bg-agri-primary text-white border-agri-primary`;
                icon.className = icon.className.replace(/text-\w+-\d+/g, 'text-white');
            } else {
                btn.className = `filter-btn px-4 py-1.5 rounded-lg border text-xs font-bold transition shadow-sm whitespace-nowrap flex items-center gap-1 bg-white border-gray-200 text-gray-500 hover:border-gray-400`;
                let colorClass = 'text-gray-600';
                if(btn.dataset.status === 'scheduled') colorClass = 'text-blue-600';
                if(btn.dataset.status === 'in_progress') colorClass = 'text-purple-600';
                if(btn.dataset.status === 'completed_pending_approval') colorClass = 'text-orange-600';
                if(btn.dataset.status === 'completed') colorClass = 'text-green-600';
                if(btn.dataset.status === 'cancelled') colorClass = 'text-red-500';
                icon.className = `fa-solid ${getIconClass(btn.dataset.status)} ${colorClass}`;
            }
        });
        fetchJobs();
    }

    // 3. 🔥 จัดการ Filter Machine Type (เพิ่มใหม่)
    function filterMachine(type) {
        currentMachineType = type;
        document.querySelectorAll('.machine-filter-btn').forEach(btn => {
            if(btn.dataset.machine === type) {
                btn.className = `machine-filter-btn px-3 py-1 rounded-full border text-[11px] font-bold transition whitespace-nowrap bg-gray-700 text-white border-gray-700`;
            } else {
                btn.className = `machine-filter-btn px-3 py-1 rounded-full border text-[11px] font-bold transition whitespace-nowrap bg-white border-gray-200 text-gray-500 hover:bg-gray-100`;
            }
        });
        fetchJobs();
    }

    function getIconClass(status) {
        if(status === 'all') return 'fa-layer-group';
        if(status === 'scheduled') return 'fa-clock';
        if(status === 'in_progress') return 'fa-tractor';
        if(status === 'completed_pending_approval') return 'fa-clipboard-check';
        if(status === 'completed') return 'fa-check-circle';
        if(status === 'cancelled') return 'fa-ban';
        return 'fa-circle';
    }

    document.getElementById('search-input').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchJobs();
        }, 500);
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
            Swal.fire('กรุณาเลือก', 'ต้องเลือกพนักงานก่อนบันทึก', 'warning');
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
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'ยืนยันยกเลิก',
            cancelButtonText: 'ไม่'
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
                        Swal.fire('เรียบร้อย', data.message, 'success');
                        fetchJobs();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                    }
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initPagination();
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
</style>
@endsection