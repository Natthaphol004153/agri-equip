@extends('layouts.staff')

@section('title', 'หน้าหลัก')
@section('header', 'ภาพรวมการทำงาน')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800;900&family=Sarabun:wght@300;400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Sarabun', sans-serif;
        background: linear-gradient(135deg, #f0fdf4 0%, #dbeafe 50%, #fef3c7 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        background-attachment: fixed;
        overflow-x: hidden;
    }

    .dashboard-header {
        font-family: 'Kanit', sans-serif;
    }

    /* Animated gradient background */
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Floating particles */
    @keyframes floatParticle {
        0%, 100% { 
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.6;
        }
        25% { 
            transform: translate(20px, -20px) rotate(90deg);
            opacity: 0.8;
        }
        50% { 
            transform: translate(-10px, -40px) rotate(180deg);
            opacity: 1;
        }
        75% { 
            transform: translate(-30px, -20px) rotate(270deg);
            opacity: 0.8;
        }
    }

    .particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: linear-gradient(135deg, #10b981, #3b82f6);
        border-radius: 50%;
        pointer-events: none;
        animation: floatParticle 20s ease-in-out infinite;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }

    .particle:nth-child(1) { top: 10%; left: 20%; animation-delay: 0s; }
    .particle:nth-child(2) { top: 30%; left: 80%; animation-delay: 2s; }
    .particle:nth-child(3) { top: 50%; left: 10%; animation-delay: 4s; }
    .particle:nth-child(4) { top: 70%; left: 70%; animation-delay: 6s; }
    .particle:nth-child(5) { top: 20%; left: 50%; animation-delay: 8s; }
    .particle:nth-child(6) { top: 80%; left: 30%; animation-delay: 10s; }

    /* Stats card animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    @keyframes rotate3D {
        0% { transform: perspective(1000px) rotateY(0deg); }
        100% { transform: perspective(1000px) rotateY(360deg); }
    }

    @keyframes glow {
        0%, 100% { 
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4),
                        0 0 40px rgba(59, 130, 246, 0.2),
                        0 10px 30px rgba(0, 0, 0, 0.1);
        }
        50% { 
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.6),
                        0 0 60px rgba(59, 130, 246, 0.4),
                        0 15px 40px rgba(0, 0, 0, 0.15);
        }
    }

    .stat-card {
        animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        opacity: 0;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(20px);
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-style: preserve-3d;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.3s; }
    .stat-card:nth-child(3) { animation-delay: 0.5s; }

    /* Multi-layer shimmer effect */
    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255,255,255,0.1) 40%,
            rgba(255,255,255,0.3) 50%,
            rgba(255,255,255,0.1) 60%,
            transparent 70%
        );
        transform: rotate(45deg);
        animation: shimmerMove 3s ease-in-out infinite;
    }

    @keyframes shimmerMove {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        transform: scaleX(0);
        transition: transform 0.5s ease;
    }

    .stat-card:hover::after {
        transform: scaleX(1);
    }

    .stat-card:hover {
        transform: translateY(-12px) scale(1.03) perspective(1000px) rotateX(5deg);
        animation: glow 2s ease-in-out infinite;
    }

    /* Icon animation with 3D effect */
    @keyframes float3D {
        0%, 100% { 
            transform: translateY(0px) translateZ(0px) rotateY(0deg);
        }
        25% {
            transform: translateY(-8px) translateZ(20px) rotateY(5deg);
        }
        50% { 
            transform: translateY(-15px) translateZ(40px) rotateY(0deg);
        }
        75% {
            transform: translateY(-8px) translateZ(20px) rotateY(-5deg);
        }
    }

    .stat-icon {
        animation: float3D 4s ease-in-out infinite;
        transform-style: preserve-3d;
    }

    .stat-icon > div {
        position: relative;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .stat-card:hover .stat-icon > div {
        transform: scale(1.2) rotate(360deg);
        filter: drop-shadow(0 8px 16px rgba(0,0,0,0.2));
    }

    /* Ripple effect on icon */
    @keyframes ripple {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1rem;
        background: currentColor;
        opacity: 0.2;
        animation: ripple 2s ease-out infinite;
    }

    /* Quick action cards with morphing */
    .quick-action {
        animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        opacity: 0;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-style: preserve-3d;
    }

    .quick-action:nth-child(1) { animation-delay: 0.6s; }
    .quick-action:nth-child(2) { animation-delay: 0.8s; }

    /* Multiple overlay effects */
    .quick-action::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        transform: scale(0);
        transition: transform 0.6s ease;
    }

    .quick-action:hover::before {
        transform: scale(1);
    }

    .quick-action::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.6), transparent);
        transform: translate(-50%, -50%);
        transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    height 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .quick-action:hover::after {
        width: 400px;
        height: 400px;
    }

    .quick-action:hover {
        transform: translateX(12px) scale(1.02) perspective(1000px) rotateY(-5deg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2),
                    0 0 0 1px rgba(255,255,255,0.5) inset;
    }

    /* Icon bounce on hover */
    @keyframes iconBounce {
        0%, 100% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.2) rotate(-10deg); }
        50% { transform: scale(1.1) rotate(10deg); }
        75% { transform: scale(1.2) rotate(-5deg); }
    }

    .quick-action:hover .w-14 {
        animation: iconBounce 0.6s ease-in-out;
    }

    /* Notification badge pulse */
    @keyframes badgePulse {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        50% { 
            transform: scale(1.1);
            box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
        }
    }

    .animate-pulse {
        animation: badgePulse 2s ease-in-out infinite;
    }

    /* Job cards with slide and morph */
    .job-card {
        animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        transform-style: preserve-3d;
    }

    .job-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: currentColor;
        transform: scaleY(0);
        transform-origin: top;
        transition: transform 0.4s ease;
    }

    .job-card:hover::before {
        transform: scaleY(1);
    }

    .job-card:hover {
        transform: translateX(16px) scale(1.02) perspective(1000px) rotateY(-3deg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15),
                    0 0 0 1px rgba(255,255,255,0.5) inset;
    }

    /* Number counter effect with bounce */
    @keyframes countUp {
        0% { 
            opacity: 0; 
            transform: translateY(30px) scale(0.5);
        }
        60% {
            opacity: 1;
            transform: translateY(-5px) scale(1.1);
        }
        80% {
            transform: translateY(2px) scale(0.95);
        }
        100% { 
            opacity: 1; 
            transform: translateY(0) scale(1);
        }
    }

    .stat-number {
        animation: countUp 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-block;
        transform-style: preserve-3d;
    }

    /* Enhanced glassmorphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08),
                    0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    }

    /* Text shimmer effect */
    @keyframes textShimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    .gradient-text {
        background: linear-gradient(
            90deg,
            #10b981 0%,
            #3b82f6 25%,
            #f59e0b 50%,
            #3b82f6 75%,
            #10b981 100%
        );
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: textShimmer 3s linear infinite;
    }

    /* Decorative elements with parallax */
    .decorative-circle {
        position: absolute;
        border-radius: 50%;
        opacity: 0.15;
        filter: blur(60px);
        animation: pulse 4s ease-in-out infinite, float 20s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    /* Section headers with animated underline */
    .section-header {
        position: relative;
        display: inline-block;
    }

    .section-header::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #10b981, #3b82f6, #f59e0b);
        background-size: 200% 100%;
        animation: gradientSlide 3s ease infinite;
        border-radius: 2px;
    }

    @keyframes gradientSlide {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Status badge with glow */
    .status-badge {
        position: relative;
        animation: badgeGlow 2s ease-in-out infinite;
    }

    @keyframes badgeGlow {
        0%, 100% { 
            filter: drop-shadow(0 0 4px currentColor);
        }
        50% { 
            filter: drop-shadow(0 0 12px currentColor);
        }
    }

    /* Empty state with breathing effect */
    @keyframes breathe {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }

    .empty-state {
        animation: breathe 3s ease-in-out infinite;
    }

    /* Chevron animation */
    @keyframes chevronBounce {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(4px); }
    }

    .group:hover .chevron-icon {
        animation: chevronBounce 0.6s ease-in-out infinite;
    }

    /* Loading shimmer for cards */
    @keyframes cardShimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    /* Tractor icon animation */
    @keyframes tractorMove {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(4px); }
    }

    .job-card:hover .fa-tractor {
        animation: tractorMove 0.8s ease-in-out infinite;
    }

    /* 3D perspective container */
    .perspective-container {
        perspective: 2000px;
        perspective-origin: center;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto relative perspective-container">
    
    {{-- Floating particles (subtle) --}}
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    
    {{-- 1. การ์ดสรุปสถานะที่สวยงาม --}}
    <div class="grid grid-cols-3 gap-4 mb-8 relative z-10">
        {{-- In Progress Card --}}
        <div class="stat-card glass-effect p-6 rounded-2xl text-center shadow-lg hover:shadow-2xl group">
            <div class="stat-icon mb-3 relative">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 flex items-center justify-center shadow-xl transform group-hover:rotate-12 transition-transform duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent"></div>
                    <i class="fa-solid fa-spinner fa-spin text-white text-2xl relative z-10"></i>
                </div>
            </div>
            <h3 class="stat-number text-4xl font-bold mb-2" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8, #4f46e5); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto;">
                {{ $counts['in_progress'] }}
            </h3>
            <p class="text-sm text-blue-700 font-semibold tracking-wide">กำลังดำเนินการ</p>
            <div class="mt-3 h-1 w-16 mx-auto bg-gradient-to-r from-blue-400 via-blue-600 to-indigo-600 rounded-full relative overflow-hidden">
                <div class="absolute inset-0 bg-white/50 animate-pulse"></div>
            </div>
        </div>

        {{-- Scheduled Card --}}
        <div class="stat-card glass-effect p-6 rounded-2xl text-center shadow-lg hover:shadow-2xl group">
            <div class="stat-icon mb-3 relative" style="animation-delay: 1s;">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 flex items-center justify-center shadow-xl transform group-hover:rotate-12 transition-transform duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent"></div>
                    <i class="fa-solid fa-clock text-white text-2xl relative z-10"></i>
                </div>
            </div>
            <h3 class="stat-number text-4xl font-bold mb-2" style="background: linear-gradient(135deg, #f59e0b, #d97706, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto;">
                {{ $counts['scheduled'] }}
            </h3>
            <p class="text-sm text-amber-700 font-semibold tracking-wide">รอดำเนินการ</p>
            <div class="mt-3 h-1 w-16 mx-auto bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 rounded-full relative overflow-hidden">
                <div class="absolute inset-0 bg-white/50 animate-pulse"></div>
            </div>
        </div>

        {{-- Completed Card --}}
        <div class="stat-card glass-effect p-6 rounded-2xl text-center shadow-lg hover:shadow-2xl group">
            <div class="stat-icon mb-3 relative" style="animation-delay: 2s;">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-emerald-500 via-green-600 to-teal-600 flex items-center justify-center shadow-xl transform group-hover:rotate-12 transition-transform duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent"></div>
                    <i class="fa-solid fa-circle-check text-white text-2xl relative z-10"></i>
                </div>
            </div>
            <h3 class="stat-number text-4xl font-bold mb-2" style="background: linear-gradient(135deg, #10b981, #059669, #0d9488); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto;">
                {{ $counts['completed'] }}
            </h3>
            <p class="text-sm text-emerald-700 font-semibold tracking-wide">เสร็จสิ้นเดือนนี้</p>
            <div class="mt-3 h-1 w-16 mx-auto bg-gradient-to-r from-emerald-400 via-green-600 to-teal-600 rounded-full relative overflow-hidden">
                <div class="absolute inset-0 bg-white/50 animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- 2. เมนูลัด (Quick Actions) --}}
    <div class="relative z-10">
        <div class="flex items-center gap-3 mb-4 px-1">
            <div class="h-8 w-1 bg-gradient-to-b from-green-500 via-blue-500 to-purple-500 rounded-full shadow-lg"></div>
            <h3 class="dashboard-header text-xl font-bold text-gray-800 section-header">เมนูด่วน</h3>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-8">
            <a href="{{ route('staff.jobs.index') }}" class="quick-action glass-effect p-5 rounded-2xl shadow-md hover:shadow-xl flex items-center gap-4 group">
                <div class="flex-shrink-0 relative">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 via-emerald-600 to-teal-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/30 to-transparent"></div>
                        <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <i class="fa-solid fa-clipboard-list text-white text-xl relative z-10"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-pulse shadow-lg"></div>
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-400 rounded-full animate-ping"></div>
                </div>
                <div class="text-left flex-grow relative z-10">
                    <p class="text-base font-bold text-gray-800 mb-1">งานทั้งหมด</p>
                    <p class="text-xs text-gray-500">ดูรายการงานของคุณ</p>
                </div>
                <div class="text-green-600 transform group-hover:translate-x-2 transition-transform chevron-icon">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>

            <a href="{{ route('staff.maintenance.create') }}" class="quick-action glass-effect p-5 rounded-2xl shadow-md hover:shadow-xl flex items-center gap-4 group">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 via-pink-600 to-rose-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/30 to-transparent"></div>
                        <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <i class="fa-solid fa-screwdriver-wrench text-white text-xl relative z-10"></i>
                    </div>
                </div>
                <div class="text-left flex-grow relative z-10">
                    <p class="text-base font-bold text-gray-800 mb-1">แจ้งซ่อม</p>
                    <p class="text-xs text-gray-500">รายงานปัญหาอุปกรณ์</p>
                </div>
                <div class="text-red-600 transform group-hover:translate-x-2 transition-transform chevron-icon">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
        </div>
    </div>

    {{-- 3. งานที่ต้องทำด่วน --}}
    <div class="relative z-10">
        <div class="flex justify-between items-center mb-4 px-1">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 bg-gradient-to-b from-orange-500 via-red-500 to-pink-500 rounded-full shadow-lg"></div>
                <h3 class="dashboard-header text-xl font-bold text-gray-800 section-header">งานที่ต้องทำตอนนี้</h3>
            </div>
            <a href="{{ route('staff.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2 group">
                ดูทั้งหมด 
                <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="space-y-3">
            @forelse($urgentJobs as $index => $job)
                <div class="job-card glass-effect p-5 rounded-2xl shadow-md hover:shadow-xl border-l-4 {{ $job->status == 'in_progress' ? 'border-blue-500' : 'border-amber-400' }} relative overflow-hidden group" style="animation-delay: {{ 1.0 + ($index * 0.15) }}s;">
                    
                    {{-- Animated gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-r {{ $job->status == 'in_progress' ? 'from-blue-50 via-blue-100 to-transparent' : 'from-amber-50 via-orange-50 to-transparent' }} opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    {{-- Shine effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    
                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                @if($job->status == 'in_progress')
                                    <span class="status-badge inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 text-white text-xs font-bold rounded-lg shadow-md relative overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        <i class="fa-solid fa-circle-notch fa-spin relative z-10"></i>
                                        <span class="relative z-10">กำลังทำงาน</span>
                                    </span>
                                @else
                                    <span class="status-badge inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 text-white text-xs font-bold rounded-lg shadow-md relative overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        <i class="fa-solid fa-clock relative z-10"></i>
                                        <span class="relative z-10">รอดำเนินการ</span>
                                    </span>
                                @endif
                            </div>
                            
                            <h4 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-user text-gray-400 text-sm group-hover:text-blue-500 transition-colors"></i>
                                {{ $job->customer->name }}
                            </h4>
                            
                            <p class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fa-solid fa-tractor text-green-600"></i> 
                                <span class="font-medium">{{ $job->equipment->name ?? '-' }}</span>
                            </p>
                        </div>

                        <div class="text-right flex flex-col items-end gap-2">
                            @if($job->status != 'in_progress')
                                <div class="text-right bg-white/80 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm border border-gray-100 hover:border-orange-300 transition-all group-hover:scale-110">
                                    <div class="text-base font-bold text-orange-600">
                                        {{ \Carbon\Carbon::parse($job->scheduled_start)->format('H:i') }} น.
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($job->scheduled_start)->locale('th')->isoFormat('D MMM') }}
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-center gap-2 text-blue-600 font-medium text-sm opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                                ดูรายละเอียด <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('staff.jobs.show', $job->id) }}" class="absolute inset-0 z-20"></a>
                </div>
            @empty
                <div class="empty-state text-center py-12 glass-effect rounded-2xl border-2 border-dashed border-gray-300 relative overflow-hidden shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-blue-50 to-green-50 opacity-50"></div>
                    
                    {{-- Animated circles in background --}}
                    <div class="absolute top-0 left-0 w-32 h-32 bg-blue-200 rounded-full opacity-20 blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 w-40 h-40 bg-green-200 rounded-full opacity-20 blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-gray-200 via-gray-300 to-gray-200 flex items-center justify-center shadow-lg relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-tr from-white/40 to-transparent"></div>
                            <i class="fa-solid fa-inbox text-gray-400 text-3xl relative z-10"></i>
                        </div>
                        <p class="text-gray-600 font-semibold text-lg mb-1">ไม่มีงานด่วนในขณะนี้</p>
                        <p class="text-gray-400 text-sm">คุณทำงานเสร็จหมดแล้ว! 🎉✨</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection