@extends('layouts.app')

@section('title', 'Hasil — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10"
     style="background:#0d0f1a; font-family:'DM Sans',sans-serif;">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:         #0F1220;
            --primary:    #4F7CFF;
            --primary-light: #6AA8FF;
            --green:      #06D6A0;
            --gold:       #FFD166;
            --red:        #FF6B6B;
            --card:       rgba(255, 255, 255, 0.03);
            --border:     rgba(255, 255, 255, 0.08);
            --text-muted: #94A3B8;
        }
        .font-syne { font-family:'Syne',sans-serif; }
        .font-dm   { font-family:'DM Sans',sans-serif; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes popIn {
            0%   { opacity:0; transform:scale(.6); }
            70%  { transform:scale(1.12); }
            100% { opacity:1; transform:scale(1); }
        }
        @keyframes shimmer {
            0%   { background-position:-400px 0; }
            100% { background-position: 400px 0; }
        }

        .fade-up  { animation:fadeUp .5s ease both; }
        .delay-1  { animation-delay:.1s; }
        .delay-2  { animation-delay:.2s; }
        .delay-3  { animation-delay:.3s; }
        .delay-4  { animation-delay:.4s; }

        .result-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            padding: 1.5rem;
            backdrop-filter: blur(24px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        .result-card::before {
            content:'';
            position:absolute; inset:0;
            background: radial-gradient(circle at 50% 0%, rgba(79, 124, 255, .15) 0%, transparent 65%);
            pointer-events:none;
        }

        .icon-pop {
            animation: popIn .55s cubic-bezier(.34,1.56,.64,1) both;
            animation-delay: .05s;
        }

        .stat-box {
            background: rgba(255,255,255,.03);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem .75rem;
            text-align: center;
            flex: 1;
        }
        .stat-value {
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:1.4rem;
            line-height:1;
        }
        .stat-label {
            font-size:.68rem;
            font-weight:600;
            letter-spacing:.06em;
            text-transform:uppercase;
            color:var(--text-muted);
            margin-top:.35rem;
        }

        .xp-badge {
            background: rgba(255,209,102,.1);
            border: 1px solid rgba(255,209,102,.25);
            border-radius: 9999px;
            padding: .5rem 1.4rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:1.1rem;
            color: var(--gold);
        }
        .xp-badge.no-xp {
            background: rgba(255,255,255,.04);
            border-color: var(--border);
            color: var(--text-muted);
            font-size:.9rem;
        }

        .btn-primary {
            flex:1;
            padding:.85rem 1rem;
            border-radius:1rem;
            font-family:'Syne',sans-serif;
            font-weight:700;
            font-size:.95rem;
            text-align:center;
            transition: transform .2s, box-shadow .2s, opacity .2s;
            cursor:pointer;
            text-decoration:none;
            display:block;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            opacity: .9;
        }
        .btn-retry {
            background: rgba(79, 124, 255, .12);
            border: 1px solid rgba(79, 124, 255, .3);
            color: var(--primary);
        }
        .btn-next-success {
            background: linear-gradient(135deg, var(--green) 0%, #0abf7e 100%);
            border: none;
            color: #0d1a15;
            box-shadow: 0 10px 20px rgba(6, 214, 160, 0.3);
        }
        .btn-next-unit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            color: #fff;
            box-shadow: 0 10px 20px rgba(79, 124, 255, 0.3);
        }

        .divider {
            height:1px;
            background: var(--border);
            margin: 1.5rem 0;
        }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    @php
        $isCompleted  = $result['completed'];
        $scoreVal     = $result['score']      ?? 0;
        $correctVal   = $result['correct']    ?? 0;
        $totalVal     = $result['total']      ?? 0;
        $timeVal      = $result['time_spent'] ?? 0;
        $xpEarned     = $result['xp_earned']  ?? 0;
        $accuracy     = $totalVal > 0 ? round(($correctVal / $totalVal) * 100) : 0;
    @endphp    <div class="result-card fade-up">
        <div class="p-8">

            <div class="text-center mb-6">
                <div class="icon-pop text-6xl mb-4 leading-none">
                    {{ $isCompleted ? '🎉' : '💪' }}
                </div>
                <h1 class="font-syne font-extrabold text-white text-2xl mb-1 fade-up delay-1">
                    {{ $isCompleted ? 'Lesson Selesai!' : 'Hampir Berhasil!' }}
                </h1>
                <p class="font-dm text-sm fade-up delay-1" style="color:var(--text-muted);">
                    @if($isCompleted)
                        Kamu berhasil menyelesaikan <strong class="text-white">{{ $lesson->title }}</strong>
                    @else
                        Skor minimum 70% diperlukan. Coba lagi, kamu pasti bisa!
                    @endif
                </p>
            </div>

            @php
                $target = $result['daily_target'];
                $current = $result['daily_current'];
                $wasCompletedNow = $result['completed'];
                $prevCount = $wasCompletedNow ? max(0, $current - 1) : $current;
                $prevPercent = ($prevCount / $target) * 100;
                $currPercent = ($current / $target) * 100;
            @endphp

            <div class="mb-8 fade-up delay-2" 
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
                
                <div class="flex justify-between items-end mb-2 px-1">
                    <span class="font-syne font-bold text-xs uppercase tracking-wider text-[var(--text-muted)]">Target Harian</span>
                    <span class="font-syne font-extrabold text-sm" 
                          :class="width >= 100 ? 'text-[var(--green)]' : 'text-[var(--primary)]'">
                        <span x-text="Math.round((width / 100) * {{ $target }})"></span> / {{ $target }}
                    </span>
                </div>

                <div class="relative h-4 w-full bg-white/5 rounded-full overflow-hidden border border-white/5 p-1">
                    <div class="h-full rounded-full transition-all duration-[1000ms] ease-out shadow-[0_0_15px_rgba(79,124,255,0.3)]"
                         :style="`width: ${width}%; background: ${width >= 100 ? 'linear-gradient(90deg, #06d6a0, #0abf7e)' : 'linear-gradient(90deg, var(--primary), var(--primary-light))'}`">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-[shimmer_2s_infinite]"></div>
                    </div>
                </div>

                <template x-if="showCheck">
                    <p class="text-center text-[var(--green)] font-bold text-xs mt-2 animate-bounce">
                        ✨ Target Hari Ini Tercapai! (+50 XP)
                    </p>
                </template>
            </div>

            <div class="text-center mb-8 fade-up delay-2">
                @if($xpEarned > 0)
                    <div class="xp-badge">
                        ⚡ +{{ $xpEarned }} XP
                    </div>
                @else
                    <div class="xp-badge no-xp">
                        XP sudah pernah diraih sebelumnya
                    </div>
                @endif
            </div>

            <div class="flex gap-4 mb-2 fade-up delay-2">
                <div class="stat-box">
                    <div class="stat-value" style="color:var(--green);">
                        {{ $correctVal }}<span style="font-size:1rem; color:var(--text-muted); opacity: 0.5;">/{{ $totalVal }}</span>
                    </div>
                    <div class="stat-label">Benar</div>
                </div>

                <div class="stat-box">
                    <div class="stat-value" style="color:var(--primary);">
                        {{ $timeVal }}<span style="font-size:.9rem; font-weight:600; color:var(--text-muted); opacity: 0.5;">s</span>
                    </div>
                    <div class="stat-label">Waktu</div>
                </div>
            </div>
      </div>

            {{-- Accuracy bar --}}
            <div class="fade-up delay-3 mb-1 mt-4">
                <div class="flex justify-between text-xs font-dm mb-1" style="color:var(--text-muted);">
                    <span>Akurasi</span>
                    <span style="color:{{ $accuracy >= 70 ? 'var(--green)' : 'var(--red)' }}">{{ $accuracy }}%</span>
                </div>
                <div class="rounded-full overflow-hidden" style="height:6px; background:rgba(255,255,255,.07);">
                    <div class="rounded-full transition-all duration-700"
                         style="width:{{ $accuracy }}%; height:100%;
                                background:{{ $accuracy >= 70
                                    ? 'linear-gradient(90deg,#06d6a0,#0abf7e)'
                                    : 'linear-gradient(90deg,#ff6b6b,#ff9999)' }};">
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- ===== ACTION BUTTONS ===== --}}
            <div class="flex gap-3 fade-up delay-4">

                {{-- Ulangi --}}
                <a href="{{ route('game.play', $lesson->id) }}" class="btn-primary btn-retry">
                    🔄 Ulangi
                </a>

                {{-- Lanjut --}}
                @if($nextLesson)
                    <a href="{{ route('lessons.show', $nextLesson->id) }}"
                       class="btn-primary btn-next-success">
                        Lanjut →
                    </a>
                @else
                    <a href="{{ route('units.show', $lesson->unit_id) }}"
                       class="btn-primary btn-next-unit">
                        ← Ke Unit
                    </a>
                @endif

            </div>

            {{-- Lesson info footer --}}
            <p class="text-center text-xs font-dm mt-5" style="color:var(--text-muted);">
                {{ $lesson->unit->title ?? '' }}
                @if($lesson->unit->title ?? false) · @endif
                {{ $lesson->title }}
            </p>

        </div>
    </div>

</div>
@endsection