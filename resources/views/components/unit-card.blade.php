@props(['unit'])

@php
    $isUnlocked = $unit->unlocked;
    $progress   = $unit->progress; 
    $isDone     = $progress >= 100;
@endphp

<div class="unit-card {{ $isUnlocked ? 'unlocked' : 'locked' }}">
    <div class="card-glow"></div>

    @if($isUnlocked)
        <a href="{{ route('units.show', $unit->id) }}" class="block p-6">
    @else
        <div class="p-6">
    @endif

        {{-- Header: icon + badge --}}
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                 style="background:rgba(108,99,255,.12); border:1px solid var(--border);">
                {{ $unit->icon ?? '📖' }}
            </div>

            @if(!$isUnlocked)
                <span class="badge border border-[var(--border)]" style="background:rgba(255,255,255,.05); color:var(--text-muted); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    🔒 Terkunci
                </span>
            @elseif($isDone)
                <span class="badge border border-[#06D6A0]/20" style="background:rgba(6,214,160,.12); color:var(--green); box-shadow: 0 2px 12px rgba(6,214,160,0.15);">
                    ✅ Selesai
                </span>
            @else
                <span class="badge border border-[var(--primary)]/20" style="background:rgba(79, 124, 255, .12); color:var(--primary); box-shadow: 0 2px 12px rgba(79, 124, 255, 0.15);">
                    ▶ Lanjutkan
                </span>
            @endif
        </div>

        {{-- Title & desc --}}
        <h3 class="font-syne font-extrabold text-themeText text-lg leading-snug mb-1.5 tracking-tight group-hover:text-[var(--purple)] transition-colors">
            {{ $unit->title }}
        </h3>
        @if($unit->description)
        <p class="text-sm leading-relaxed mb-5" style="color:var(--text-muted); opacity: 0.85;">
            {{ Str::limit($unit->description, 80) }}
        </p>
        @endif

        {{-- Lesson count --}}
        <div class="flex items-center gap-1 text-xs mb-4" style="color:var(--text-muted);">
            <span>📝</span>
            <span>{{ $unit->lessons->count() }} Lesson</span>
        </div>

        {{-- Progress bar --}}
        @if($isUnlocked)
        <div>
            <div class="flex justify-between text-xs mb-1.5 font-semibold" style="color:var(--text-muted);">
                <span>Progress</span>
                <span style="color:{{ $isDone ? 'var(--green)' : 'var(--primary)' }}; text-shadow: 0 0 8px {{ $isDone ? 'rgba(6,214,160,0.4)' : 'rgba(79, 124, 255, 0.4)' }};">
                    {{ $progress }}%
                </span>
            </div>
            <div class="unit-progress-bar" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $progress }}, 100)">
                <div class="unit-progress-fill glow-{{ $isDone ? 'green' : 'primary' }}"
                     :style="`width: ${width}%; background: {{ $isDone ? 'linear-gradient(90deg, #06d6a0, #0abf7e)' : 'linear-gradient(90deg, var(--primary), var(--primary-light))' }}`">
                </div>
            </div>
        </div>
        @else
        <p class="text-xs" style="color:var(--text-muted);">
            Selesaikan unit sebelumnya untuk membuka unit ini.
        </p>
        @endif

    @if($isUnlocked)
        </a>
    @else
        </div>
    @endif
</div>
