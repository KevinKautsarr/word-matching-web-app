@extends('layouts.app')

@section('title', 'Word Matching — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        .font-syne { font-family:'Syne',sans-serif; }
        .font-dm   { font-family:'DM Sans',sans-serif; }

        /* ---------- HUD ---------- */
        .hud {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
        }
        .hud-item {
            display:flex; flex-direction:column;
            align-items:center; gap:.15rem;
        }
        .hud-value {
            font-family:'Syne',sans-serif;
            font-weight:800; font-size:1.5rem; line-height:1;
        }
        .hud-label {
            font-size:.67rem; font-weight:600;
            letter-spacing:.07em; text-transform:uppercase;
            color:var(--text-muted);
        }

        @keyframes pulse-red {
            0%,100% { color:var(--red); }
            50%      { color:#ff9999; }
        }
        .timer-warning { animation:pulse-red .7s ease infinite; }

        /* ---------- GAME GRID ---------- */
        .game-grid {
            display:grid;
            grid-template-columns: repeat(2, 1fr);
            gap:.75rem;
        }
        @media (min-width:640px) {
            .game-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width:1024px) {
            .game-grid { grid-template-columns: repeat(4, 1fr); }
        }

        /* ---------- GAME CARD ---------- */
        .game-card {
            background: var(--card);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: .875rem;
            color: #c8d0e0;
            text-align: center;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, background .18s, border-color .18s, color .18s;
            word-break: break-word;
            line-height: 1.4;
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .game-card:hover:not(:disabled):not(.matched) {
            transform: translateY(-3px);
            box-shadow: 0 6px 24px rgba(108,99,255,.25);
            border-color: rgba(108,99,255,.45);
            color: #fff;
        }
        .game-card.selected {
            background: rgba(108,99,255,.2);
            border-color: var(--purple);
            color: var(--purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(108,99,255,.3);
        }
        .game-card.correct {
            background: rgba(6,214,160,.15);
            border-color: var(--green);
            color: var(--green);
        }
        .game-card.wrong {
            background: rgba(255,99,99,.15);
            border-color: var(--red);
            color: var(--red);
        }
        .game-card:disabled { cursor: default; }

        /* ---------- ANIMATIONS ---------- */
        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%,60% { transform:translateX(-8px); }
            40%,80% { transform:translateX(8px); }
        }
        .shake { animation:shake .4s ease; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation:fadeUp .45s ease both; }
        .delay-1 { animation-delay:.07s; }
        .delay-2 { animation-delay:.14s; }

        /* ---------- OVERLAY ---------- */
        .submit-overlay {
            display:none; position:fixed; inset:0; z-index:50;
            background:rgba(13,15,26,.85); backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .submit-overlay.active { display:flex; }
        .submit-card {
            background:var(--card); border:1px solid var(--border);
            border-radius:1.5rem; padding:2.5rem 2rem;
            text-align:center; max-width:320px; width:90%;
        }
        @keyframes spin { to { transform:rotate(360deg); } }
        .spinner {
            width:2.5rem; height:2.5rem;
            border:3px solid var(--border);
            border-top-color:var(--purple);
            border-radius:50%;
            animation:spin .8s linear infinite;
            margin:0 auto 1rem;
        }

        /* ---------- EMPTY STATE ---------- */
        .empty-card {
            background:var(--card); border:1px solid var(--border);
            border-radius:1.25rem; padding:2.5rem; text-align:center;
        }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    {{-- ========= BLADE DATA → JS ========= --}}
    <script>
        const allVocab  = @json($vocabularies->map(fn($v) => ['id'=>$v->id,'word'=>$v->word,'meaning'=>$v->meaning]));
        const csrfToken = '{{ csrf_token() }}';
        const submitUrl = '{{ route("game.submit", $lesson->id) }}';
        const exitUrl   = '{{ route("lessons.show", $lesson->id) }}';
    </script>

    <div class="max-w-3xl mx-auto px-4 py-6">

        {{-- ========= TOP BAR ========= --}}
        <div class="fade-up flex items-center justify-between mb-5">
            <div>
                <p class="text-xs font-dm" style="color:var(--text-muted);">
                    {{ $lesson->unit->title ?? 'Unit' }}
                </p>
                <h1 class="font-syne font-bold text-white text-lg leading-tight">
                    {{ $lesson->title }}
                </h1>
            </div>
            <button onclick="confirmExit()"
                    class="flex items-center gap-1.5 font-dm text-sm px-4 py-2 rounded-xl transition-all"
                    style="background:rgba(255,99,99,.08); border:1px solid rgba(255,99,99,.2); color:#ff9999;">
                ✕ Keluar
            </button>
        </div>

        {{-- ========= HUD ========= --}}
        <div class="hud fade-up flex items-center justify-around p-4 mb-6" style="animation-delay:.07s;">
            <div class="hud-item">
                <span class="hud-value" id="hud-score" style="color:var(--gold);">0</span>
                <span class="hud-label">Skor</span>
            </div>
            <div style="width:1px;height:2.5rem;background:var(--border);"></div>
            <div class="hud-item">
                <span class="hud-value" id="hud-timer" style="color:var(--purple);">60</span>
                <span class="hud-label">Detik</span>
            </div>
            <div style="width:1px;height:2.5rem;background:var(--border);"></div>
            <div class="hud-item">
                <span class="hud-value" style="color:var(--green);">
                    <span id="hud-matched">0</span><span style="font-size:1rem;font-weight:600;color:var(--text-muted);" id="hud-total">/0</span>
                </span>
                <span class="hud-label">Pasangan</span>
            </div>
        </div>

        {{-- ========= GAME GRID ========= --}}
        @if($vocabularies->isEmpty())
            <div class="empty-card fade-up delay-2">
                <p class="text-4xl mb-3">📭</p>
                <p class="font-syne font-semibold text-white">Tidak ada vocabulary</p>
                <p class="text-sm mt-1" style="color:var(--text-muted);">Lesson ini belum memiliki kosakata.</p>
                <a href="{{ route('lessons.show', $lesson->id) }}"
                   class="inline-block mt-4 font-dm text-sm px-5 py-2 rounded-xl transition-all"
                   style="background:rgba(108,99,255,.15);border:1px solid var(--border);color:var(--purple);">
                    ← Kembali
                </a>
            </div>
        @else
            <div class="game-grid fade-up delay-2" id="game-grid">
                {{-- Rendered by JS --}}
            </div>
        @endif

    </div>

    {{-- ========= SUBMIT OVERLAY ========= --}}
    <div class="submit-overlay" id="submit-overlay">
        <div class="submit-card">
            <div class="spinner"></div>
            <p class="font-syne font-bold text-white text-lg mb-1">Menyimpan hasil...</p>
            <p class="font-dm text-sm" style="color:var(--text-muted);">Mohon tunggu sebentar</p>
        </div>
    </div>

    {{-- ========= GAME LOGIC ========= --}}
    <script>
    (function () {
        // ── Helpers ──────────────────────────────────────────────────────
        function shuffle(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        // ── Pick up to 8 pairs ───────────────────────────────────────────
        const vocab = shuffle(allVocab).slice(0, 8);
        const total = vocab.length;

        // ── Build flat cards array (word + meaning, then shuffle) ────────
        let cards = [];
        vocab.forEach(v => {
            cards.push({ id: v.id, text: v.word,    type: 'word'    });
            cards.push({ id: v.id, text: v.meaning, type: 'meaning' });
        });
        cards = shuffle(cards);

        // ── State ────────────────────────────────────────────────────────
        let firstCard  = null;
        let secondCard = null;
        let isLocked   = false;
        let score      = 0;
        let correct    = 0;
        let timeLeft   = 60;
        let startTime  = Date.now();
        let submitted  = false;

        // ── Update HUD total ─────────────────────────────────────────────
        document.getElementById('hud-total').textContent = '/' + total;

        // ── Render grid ──────────────────────────────────────────────────
        const grid = document.getElementById('game-grid');

        if (grid) {
            cards.forEach((card, index) => {
                const btn = document.createElement('button');
                btn.className        = 'game-card';
                btn.dataset.id       = card.id;
                btn.dataset.type     = card.type;
                btn.dataset.index    = index;
                btn.textContent      = card.text;
                btn.addEventListener('click', () => handleClick(btn, card));
                grid.appendChild(btn);
            });
        }

        // ── Click handler ────────────────────────────────────────────────
        function handleClick(btn, card) {
            if (isLocked)                          return;
            if (btn.classList.contains('matched')) return;
            if (btn.classList.contains('correct')) return;
            
            // Allow deselecting the first card
            if (firstCard && firstCard.btn === btn) {
                btn.classList.remove('selected');
                firstCard = null;
                return;
            }

            btn.classList.add('selected');

            if (!firstCard) {
                firstCard = { btn, card };
                return;
            }

            secondCard = { btn, card };
            isLocked   = true;
            checkMatch();
        }

        // ── Check match ──────────────────────────────────────────────────
        function checkMatch() {
            const sameId   = firstCard.card.id   === secondCard.card.id;
            const diffType = firstCard.card.type !== secondCard.card.type;

            if (sameId && diffType) {
                // ✅ CORRECT
                const isFinal = (correct + 1 === total);
                
                if (isFinal) {
                    new Audio('/sounds/success.mp3').play().catch(e => console.log('Audio disabled'));
                } else {
                    new Audio('/sounds/correct.mp3').play().catch(e => console.log('Audio disabled'));
                }

                const a = firstCard.btn;
                const b = secondCard.btn;

                a.classList.remove('selected');
                b.classList.remove('selected');
                a.classList.add('correct', 'matched');
                b.classList.add('correct', 'matched');
                a.disabled = true;
                b.disabled = true;

                score   += 100;
                correct += 1;
                updateHUD();

                // fade out after short delay
                setTimeout(() => {
                    [a, b].forEach(el => {
                        el.style.transition = 'all 0.3s ease';
                        el.style.transform  = 'scale(0)';
                        el.style.opacity    = '0';
                        setTimeout(() => { el.style.display = 'none'; }, 300);
                    });
                }, 400);

                reset();

                if (correct === total) {
                    setTimeout(submitGame, 1500); // Increased delay before submit
                }

            } else {
                // ❌ WRONG
                new Audio('/sounds/wrong.mp3').play().catch(e => console.log('Audio disabled'));

                const a = firstCard.btn;
                const b = secondCard.btn;

                a.classList.remove('selected');
                b.classList.remove('selected');
                a.classList.add('wrong', 'shake');
                b.classList.add('wrong', 'shake');

                // 500-800ms reset
                setTimeout(() => {
                    a.classList.remove('wrong', 'shake');
                    b.classList.remove('wrong', 'shake');
                    reset();
                }, 600);
            }
        }

        // ── Reset selection ──────────────────────────────────────────────
        function reset() {
            firstCard  = null;
            secondCard = null;
            isLocked   = false;
        }

        // ── HUD update ───────────────────────────────────────────────────
        function updateHUD() {
            document.getElementById('hud-score').textContent   = score;
            document.getElementById('hud-matched').textContent = correct;
        }

        // ── Timer ────────────────────────────────────────────────────────
        const timerEl = document.getElementById('hud-timer');

        const timerInterval = setInterval(() => {
            timeLeft--;
            timerEl.textContent = timeLeft;
            if (timeLeft <= 10) timerEl.classList.add('timer-warning');
            if (timeLeft <= 0)  { clearInterval(timerInterval); submitGame(); }
        }, 1000);

        // ── Submit ───────────────────────────────────────────────────────
        function submitGame() {
            if (submitted) return;
            submitted = true;

            clearInterval(timerInterval);

            document.querySelectorAll('.game-card').forEach(c => c.disabled = true);
            document.getElementById('submit-overlay').classList.add('active');

            const timeSpent = Math.round((Date.now() - startTime) / 1000);

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    score: score,
                    correct: correct,
                    total: total,
                    time_spent: timeSpent
                }),
            })
            .then(res => res.json())
            .then(data => {
                window.location.href = data.redirect;
            })
            .catch(err => {
                console.error('Submit error:', err);
                window.location.href = exitUrl;
            });
        }

        // ── Exit confirm ─────────────────────────────────────────────────
        window.confirmExit = function () {
            if (confirm('Yakin keluar? Progress tidak tersimpan.')) {
                window.location.href = exitUrl;
            }
        };

    })();
    </script>

</div>
@endsection