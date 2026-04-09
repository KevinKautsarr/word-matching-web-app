@extends('layouts.app')

@section('title', 'Dashboard — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    {{-- ========= DASHBOARD STYLES ========= --}}
    <style>
        .unit-card {
            background: rgba(255,255,255,0.03); backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.06); border-radius: 1.5rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.04);
            transition: all 0.18s ease-out; position: relative; overflow: hidden;
        }
        .unit-card.unlocked:hover {
            transform: translateY(-4px) scale(1.02); border-color: rgba(79, 124, 255, 0.3);
            box-shadow: 0 16px 45px rgba(0,0,0,0.35), 0 0 30px rgba(79, 124, 255, 0.15);
            background: rgba(255,255,255,0.05);
        }
        .unit-card.locked { opacity: .65; filter: grayscale(100%); }
        .unit-card .card-glow {
            position: absolute; inset: 0; pointer-events: none; opacity: 0.6;
            background: radial-gradient(circle at 50% 0%, rgba(79, 124, 255, .12) 0%, transparent 60%);
        }
        .unit-progress-bar { background: rgba(255,255,255,0.04); border-radius: 9999px; height: 10px; overflow: hidden; }
        .unit-progress-fill { height: 100%; border-radius: 9999px; transition: width 1.2s cubic-bezier(.4,0,.2,1); position: relative; overflow: hidden; box-shadow: 0 0 12px rgba(79, 124, 255, 0.4); }
        .unit-progress-fill::after { content:''; position:absolute; inset:0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: shimmer 2s infinite; }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        
        .stat-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.06); border-radius: 1.5rem; transition: all 0.18s ease-out; }
        .stat-card:hover { transform: translateY(-4px); border-color: rgba(6, 214, 160, 0.3); background: rgba(255,255,255,0.05); }

        .badge { font-family: 'Syne', sans-serif; font-size: .7rem; font-weight: 700; text-transform: uppercase; padding: .2rem .65rem; border-radius: 9999px; }
        
        @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .5s ease both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
    </style>

    <div class="px-4 py-6">

        <div class="fade-up mb-10">
            <p class="font-dm text-sm mb-1 font-semibold tracking-wide" style="color:var(--text-muted);">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM') }}
            </p>
            <h1 class="font-syne font-extrabold text-themeText leading-tight mb-2" style="font-size:clamp(1.8rem,4vw,2.5rem);">
                {{ $greeting }}, {{ explode(' ', $user->name)[0] }} 👋
            </h1>
            <p class="font-dm text-sm" style="color:var(--primary); opacity: 0.9;">
                {{ $randomMsg }}
            </p>
        </div>

        @php $goalDone = $dailyProgress >= $goalTarget; @endphp
        <div class="fade-up delay-1 mb-10 stat-card p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group cursor-default shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-[rgba(255,255,255,0.04)]">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl transition-transform group-hover:scale-105 duration-300
                    {{ $goalDone ? 'bg-[rgba(6,214,160,0.1)] text-[#06d6a0] glow-green' : 'bg-[rgba(79,124,255,0.1)] text-[var(--primary)] glow-primary' }}">
                    {{ $goalDone ? '🎉' : '🎯' }}
                </div>
                <div>
                    <h3 class="font-syne font-extrabold text-xl mb-1 text-[var(--text-muted)] group-hover:text-white transition-colors">Target Harian</h3>
                    <p class="text-sm font-medium" style="color:var(--text-muted); opacity: 0.85;">
                        {{ $goalDone ? 'Luar biasa! Target hari ini selesai. (+50 XP)' : 'Selesaikan ' . $goalTarget . ' lesson hari ini untuk bonus XP!' }}
                    </p>
                </div>
            </div>
            
            <div class="w-full sm:w-1/3 min-w-[200px]">
                <div class="flex justify-between text-xs mb-2 font-bold" style="color:var(--text-muted);">
                    <span>Progress</span>
                    <span style="color:{{ $goalDone ? 'var(--green)' : 'var(--primary)' }};">
                        {{ $dailyProgress }} / {{ $goalTarget }} Lesson
                    </span>
                </div>
                <div class="unit-progress-bar" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $goalPercent }}, 200)">
                    <div class="unit-progress-fill glow-{{ $goalDone ? 'green' : 'primary' }}"
                         :style="`width: ${width}%; background: {{ $goalDone ? 'linear-gradient(90deg, #06d6a0, #0abf7e)' : 'linear-gradient(90deg, var(--primary), var(--primary-light))' }}`">
                    </div>
                </div>
            </div>
        </div>

        {{-- Note: Flash messages are handled by <x-flash-messages /> in app.blade.php layout --}}

        {{-- ===== UNITS SECTION ===== --}}
        <div class="fade-up delay-2">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-syne font-extrabold text-themeText text-2xl tracking-tight">Unit Pembelajaran</h2>
                <span class="badge border border-[var(--border)] shadow-sm" style="background:rgba(79, 124, 255, .15); color:var(--primary);">
                    {{ $units->count() }} Unit
                </span>
            </div>

            @if($units->isEmpty())
                <div class="rounded-2xl p-10 text-center card">
                    <p class="text-4xl mb-3">📚</p>
                    <p class="font-syne font-semibold text-themeText">Belum ada unit tersedia</p>
                    <p class="text-sm mt-1" style="color:var(--text-muted);">Hubungi administrator untuk menambahkan konten.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($units as $unit)
                        <x-unit-card :unit="$unit" />
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== FOOTER AREA ===== --}}
        <div class="fade-up delay-3 mt-12 mb-6 flex justify-center items-center text-xs" style="color:var(--text-muted);">
            <span class="font-syne opacity-40">LEXORA &copy; {{ date('Y') }} — Premium Playful Learning</span>
        </div>

    </div>
</div>
@endsection