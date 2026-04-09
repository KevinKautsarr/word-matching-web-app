@props(['lesson', 'num'])

@php
    $isCompleted = $lesson->completed;
    $isUnlocked  = $lesson->unlocked;
@endphp

<div class="lesson-card {{ $isUnlocked ? 'unlocked' : 'locked' }}">
    <div class="card-glow"></div>

    @if($isUnlocked)
        <a href="{{ route('lessons.show', $lesson->id) }}" class="flex items-center gap-4 p-5 no-underline">
    @else
        <div class="flex items-center gap-4 p-5">
    @endif

        {{-- Number / status bubble --}}
         <div class="w-10 h-10 rounded-xl flex items-center justify-center font-syne font-bold text-sm flex-shrink-0"
             style="
                background: {{ $isCompleted
                    ? 'rgba(6,214,160,.15)'
                    : ($isUnlocked ? 'rgba(79,124,255,.15)' : 'rgba(255,255,255,.05)') }};
                border: 1px solid {{ $isCompleted
                    ? 'rgba(6,214,160,.3)'
                    : ($isUnlocked ? 'rgba(79,124,255,.3)' : 'rgba(255,255,255,.08)') }};
                color: {{ $isCompleted
                    ? 'var(--green)'
                    : ($isUnlocked ? 'var(--primary)' : 'var(--text-muted)') }};
             ">
            {{ $isCompleted ? '✓' : $num }}
        </div>

        {{-- Lesson info --}}
        <div class="flex-1 min-w-0">
            <p class="font-syne font-semibold text-sm leading-snug {{ $isUnlocked ? 'text-white' : '' }}"
               style="{{ !$isUnlocked ? 'color:var(--text-muted)' : '' }}">
                {{ $lesson->title }}
            </p>
            @if($lesson->description)
            <p class="text-xs mt-0.5 truncate" style="color:var(--text-muted);">
                {{ $lesson->description }}
            </p>
            @endif
        </div>

        {{-- Right side info --}}
        <div class="flex items-center gap-3 flex-shrink-0">
            @if(isset($lesson->xp_reward) && $lesson->xp_reward > 0)
            <span class="badge" style="background:rgba(255,209,102,.1); color:var(--gold);">
                +{{ $lesson->xp_reward }} XP
            </span>
            @endif

            @if(!$isUnlocked)
                <span class="text-lg">🔒</span>
            @elseif($isCompleted)
                <span class="text-lg">✅</span>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" style="color:var(--primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7"/>
                </svg>
            @endif
        </div>

    @if($isUnlocked)
        </a>
    @else
        </div>
    @endif
</div>
