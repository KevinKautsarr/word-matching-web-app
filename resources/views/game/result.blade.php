@extends('layouts.app')

@section('title', 'Hasil — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10">

    <style>
        /* ── Result page local vars ── */
        .result-page { --gold:#FFD166; --green:#06D6A0; --red:#FF6B6B; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes popIn {
            0%   { opacity:0; transform:scale(.6); }
            70%  { transform:scale(1.12); }
            100% { opacity:1; transform:scale(1); }
        }

        .fade-up  { animation:fadeUp .5s ease both; }
        .delay-1  { animation-delay:.1s; }
        .delay-2  { animation-delay:.2s; }
        .delay-3  { animation-delay:.3s; }
        .delay-4  { animation-delay:.4s; }

        .result-card {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            backdrop-filter: blur(24px);
            box-shadow: 0 20px 50px rgba(0,0,0,.35);
        }
        .result-card::before {
            content:'';
            position:absolute; inset:0;
            background: radial-gradient(circle at 50% 0%, rgba(79,124,255,.15) 0%, transparent 65%);
            pointer-events:none;
        }
        .icon-pop {
            animation: popIn .55s cubic-bezier(.34,1.56,.64,1) both;
            animation-delay:.05s;
        }
        .stat-box {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.08);
            border-radius:1rem;
            padding:1rem .6rem;
            text-align:center;
            flex:1;
        }
        .stat-value {
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:1.35rem;
            line-height:1;
            font-style:normal;
        }
        .stat-label {
            font-size:.65rem;
            font-weight:700;
            letter-spacing:.07em;
            text-transform:uppercase;
            color:#94A3B8;
            margin-top:.4rem;
            font-style:normal;
        }
        .xp-badge {
            background: rgba(255,209,102,.1);
            border: 1px solid rgba(255,209,102,.25);
            border-radius:9999px;
            padding:.45rem 1.4rem;
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:1.05rem;
            color:#FFD166;
            font-style:normal;
        }
        .xp-badge.no-xp {
            background:rgba(255,255,255,.04);
            border-color:rgba(255,255,255,.08);
            color:#94A3B8;
            font-size:.88rem;
        }
        .btn-action {
            flex:1;
            padding:.82rem .8rem;
            border-radius:1rem;
            font-family:'Syne',sans-serif;
            font-weight:700;
            font-size:.92rem;
            text-align:center;
            cursor:pointer;
            text-decoration:none;
            display:block;
            font-style:normal;
            transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
        }
        .btn-action:hover { transform:translateY(-2px); opacity:.9; }
        .btn-retry {
            background:rgba(79,124,255,.12);
            border:1px solid rgba(79,124,255,.3);
            color:#4F7CFF;
        }
        .btn-next-success {
            background:linear-gradient(135deg,#06D6A0 0%,#0abf7e 100%);
            border:none; color:#0d1a15;
            box-shadow:0 10px 20px rgba(6,214,160,.3);
        }
        .btn-next-unit {
            background:linear-gradient(135deg,#4F7CFF 0%,#6AA8FF 100%);
            border:none; color:#fff;
            box-shadow:0 10px 20px rgba(79,124,255,.3);
        }
        .divider { height:1px; background:rgba(255,255,255,.08); margin:1.5rem 0; }

        /* ── Mobile ── */
        @media (max-width:420px) {
            .result-card { border-radius:1.5rem; }
            .stat-value  { font-size:1.1rem; }
            .btn-action  { font-size:.8rem; padding:.7rem .4rem; }
            .xp-badge    { font-size:.95rem; }
        }
    </style>

    @php
        $isCompleted = $result['completed'];
        $correctVal  = $result['correct']    ?? 0;
        $totalVal    = $result['total']      ?? 0;
        $timeVal     = $result['time_spent'] ?? 0;
        $xpEarned    = $result['xp_earned']  ?? 0;
        $accuracy    = $totalVal > 0 ? round(($correctVal / $totalVal) * 100) : 0;

        $target      = $result['daily_target']  ?? 3;
        $current     = $result['daily_current'] ?? 0;
        $prevCount   = $isCompleted ? max(0, $current - 1) : $current;
        $prevPercent = $target > 0 ? min(100, ($prevCount / $target) * 100) : 0;
        $currPercent = $target > 0 ? min(100, ($current / $target) * 100) : 0;
    @endphp

    <div class="result-card fade-up result-page">
        <div class="p-6 md:p-8">

            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="icon-pop text-6xl mb-4 leading-none">
                    {{ $isCompleted ? '🎉' : '💪' }}
                </div>
                <h1 class="font-syne font-extrabold text-white text-2xl mb-1 fade-up delay-1">
                    {{ $isCompleted ? 'Lesson Selesai!' : 'Hampir Berhasil!' }}
                </h1>
                <p class="font-dm text-sm fade-up delay-1" style="color:#94A3B8;">
                    @if($isCompleted)
                        Kamu berhasil menyelesaikan <strong class="text-white">{{ $lesson->title }}</strong>
                    @else
                        Skor minimum 70% diperlukan. Coba lagi, kamu pasti bisa!
                    @endif
                </p>
            </div>

            {{-- Daily Target --}}
            <div class="mb-6 fade-up delay-2"
                 x-data="{ width: {{ $prevPercent }}, showCheck: false }"
                 x-init="setTimeout(() => {
                     width = {{ $currPercent }};
                     if({{ $current }} >= {{ $target }}) {
                         setTimeout(() => {
                             showCheck = true;
                             confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
                         }, 800);
                     }
                 }, 500)">

                <div class="flex justify-between items-center mb-2 px-1">
                    <span class="font-syne font-bold text-xs uppercase tracking-wider" style="color:#94A3B8;">Target Harian</span>
                    <span class="font-syne font-extrabold text-sm"
                          :class="width >= 100 ? 'text-green-400' : 'text-blue-400'">
                        <span x-text="Math.min(Math.round((width / 100) * {{ $target }}), {{ $target }})"></span> / {{ $target }}
                    </span>
                </div>

                <div class="relative h-3 w-full rounded-full overflow-hidden" style="background:rgba(255,255,255,.05);">
                    <div class="h-full rounded-full transition-all duration-[1000ms] ease-out"
                         :style="`width: ${width}%; background: ${width >= 100 ? 'linear-gradient(90deg,#06d6a0,#0abf7e)' : 'linear-gradient(90deg,#4F7CFF,#6AA8FF)'}`">
                    </div>
                </div>

                <template x-if="showCheck">
                    <p class="text-center font-bold text-xs mt-2 animate-bounce" style="color:#06D6A0;">
                        ✨ Target Hari Ini Tercapai! (+50 XP)
                    </p>
                </template>
            </div>

            {{-- XP Badge --}}
            <div class="text-center mb-6 fade-up delay-2">
                @if($xpEarned > 0)
                    <div class="xp-badge">⚡ +{{ $xpEarned }} XP</div>
                @else
                    <div class="xp-badge no-xp">XP sudah pernah diraih sebelumnya</div>
                @endif
            </div>

            {{-- Stats: 3 kolom --}}
            <div class="flex gap-3 mb-4 fade-up delay-2">
                <div class="stat-box">
                    <div class="stat-value" style="color:#06D6A0;">
                        {{ $correctVal }}<span style="font-size:.9rem;color:#94A3B8;opacity:.6;">/{{ $totalVal }}</span>
                    </div>
                    <div class="stat-label">Benar</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color:#4F7CFF;">
                        {{ $timeVal }}<span style="font-size:.85rem;font-weight:600;color:#94A3B8;opacity:.6;">s</span>
                    </div>
                    <div class="stat-label">Waktu</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color:{{ $accuracy >= 70 ? '#06D6A0' : '#FF6B6B' }};">
                        {{ $accuracy }}<span style="font-size:.85rem;font-weight:600;color:#94A3B8;opacity:.6;">%</span>
                    </div>
                    <div class="stat-label">Akurasi</div>
                </div>
            </div>

            {{-- Accuracy Bar --}}
            <div class="fade-up delay-3 mb-1">
                <div class="rounded-full overflow-hidden" style="height:5px;background:rgba(255,255,255,.06);">
                    <div class="rounded-full" style="width:{{ $accuracy }}%;height:100%;
                        background:{{ $accuracy >= 70
                            ? 'linear-gradient(90deg,#06d6a0,#0abf7e)'
                            : 'linear-gradient(90deg,#ff6b6b,#ff9999)' }};
                        transition:width .8s ease;">
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Buttons --}}
            <div class="flex gap-3 fade-up delay-4">
                <a href="{{ route('game.play', $lesson->id) }}" class="btn-action btn-retry">
                    🔄 Ulangi
                </a>
                @if($nextLesson)
                    <a href="{{ route('lessons.show', $nextLesson->id) }}" class="btn-action btn-next-success">
                        Lanjut →
                    </a>
                @else
                    <a href="{{ route('units.show', $lesson->unit_id) }}" class="btn-action btn-next-unit">
                        ← Ke Unit
                    </a>
                @endif
            </div>

            {{-- Footer Info --}}
            <p class="text-center text-xs font-dm mt-5" style="color:#94A3B8;">
                {{ $lesson->unit->title ?? '' }}
                @if($lesson->unit->title ?? false) · @endif
                {{ $lesson->title }}
            </p>

        </div>
    </div>

</div>
@endsection