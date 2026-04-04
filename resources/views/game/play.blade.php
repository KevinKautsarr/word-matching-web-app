@extends('layouts.app')

@section('title', 'Word Matching — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen" style="background:#0d0f1a; font-family:'DM Sans',sans-serif;">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:         #0d0f1a;
            --purple:     #6c63ff;
            --green:      #06d6a0;
            --gold:       #ffd166;
            --red:        #ff6b6b;
            --card:       #13162a;
            --border:     rgba(108,99,255,0.18);
            --text-muted: #8892b0;
        }
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
            font-weight:800;
            font-size:1.5rem;
            line-height:1;
        }
        .hud-label {
            font-size:.68rem;
            font-weight:600;
            letter-spacing:.07em;
            text-transform:uppercase;
            color:var(--text-muted);
        }

        /* timer warning pulse */
        @keyframes pulse-red {
            0%,100% { color:var(--red); }
            50%      { color:#ff9999; }
        }
        .timer-warning { animation:pulse-red .7s ease infinite; }

        /* ---------- WORD / MEANING BUTTONS ---------- */
        .match-btn {
            width:100%;
            padding:.75rem 1rem;
            border-radius:.875rem;
            border: 2px solid var(--border);
            background: var(--card);
            color: #c8d0e0;
            font-family:'DM Sans',sans-serif;
            font-size:.875rem;
            font-weight:500;
            text-align:center;
            cursor:pointer;
            transition: border-color .18s, background .18s, color .18s, transform .15s;
            word-break:break-word;
            line-height:1.4;
        }
        .match-btn:hover:not(:disabled):not(.selected):not(.correct):not(.wrong) {
            border-color: rgba(108,99,255,.5);
            background: rgba(108,99,255,.08);
            color: #fff;
            transform: translateY(-1px);
        }
        .match-btn.selected {
            border-color: var(--purple);
            background: rgba(108,99,255,.18);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(108,99,255,.25);
        }
        .match-btn.correct {
            border-color: var(--green);
            background: rgba(6,214,160,.12);
            color: var(--green);
            cursor:default;
        }
        .match-btn.wrong {
            border-color: var(--red);
            background: rgba(255,107,107,.12);
            color: var(--red);
        }
        .match-btn:disabled {
            cursor:default;
            opacity:.6;
        }

        /* ---------- SHAKE ANIMATION ---------- */
        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%      { transform:translateX(-7px); }
            40%      { transform:translateX( 7px); }
            60%      { transform:translateX(-5px); }
            80%      { transform:translateX( 5px); }
        }
        .shake { animation:shake .4s ease both; }

        /* ---------- SUBMIT OVERLAY ---------- */
        .submit-overlay {
            display:none;
            position:fixed; inset:0; z-index:50;
            background:rgba(13,15,26,.85);
            backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .submit-overlay.active { display:flex; }
        .submit-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius:1.5rem;
            padding:2.5rem 2rem;
            text-align:center;
            max-width:340px; width:90%;
        }
        @keyframes spin { to { transform:rotate(360deg); } }
        .spinner {
            width:2.5rem; height:2.5rem;
            border:3px solid var(--border);
            border-top-color: var(--purple);
            border-radius:50%;
            animation:spin .8s linear infinite;
            margin:0 auto 1rem;
        }

        /* ---------- EXIT MODAL ---------- */
        .exit-modal {
            display:none;
            position:fixed; inset:0; z-index:60;
            background:rgba(13,15,26,.85);
            backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .exit-modal.active { display:flex; }

        /* ---------- FADE IN ---------- */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation:fadeUp .4s ease both; }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    {{-- ===================== VOCAB DATA ===================== --}}
    <script>
        const vocab = @json($vocabularies->map(fn($v) => [
            'id'      => $v->id,
            'word'    => $v->word,
            'meaning' => $v->meaning,
        ]));
        const SUBMIT_URL = "{{ route('game.submit', $lesson->id) }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const EXIT_URL   = "{{ route('lessons.show', $lesson->id) }}";
    </script>

    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- ===== TOP BAR ===== --}}
        <div class="fade-up flex items-center justify-between mb-5">
            <div>
                <p class="text-xs font-dm" style="color:var(--text-muted);">
                    {{ $lesson->unit->title ?? 'Unit' }}
                </p>
                <h1 class="font-syne font-bold text-white text-lg leading-tight">
                    {{ $lesson->title }}
                </h1>
            </div>
            <button onclick="openExitModal()"
                    class="flex items-center gap-1.5 font-dm text-sm px-4 py-2 rounded-xl transition-all duration-200"
                    style="background:rgba(255,107,107,.08); border:1px solid rgba(255,107,107,.2); color:#ff9999;">
                ✕ Keluar
            </button>
        </div>

        {{-- ===== HUD ===== --}}
        <div class="hud fade-up flex items-center justify-around p-4 mb-6" style="animation-delay:.07s">
            <div class="hud-item">
                <span class="hud-value" id="hud-score" style="color:var(--gold);">0</span>
                <span class="hud-label">Skor</span>
            </div>
            <div style="width:1px; height:2.5rem; background:var(--border);"></div>
            <div class="hud-item">
                <span class="hud-value" id="hud-timer" style="color:var(--purple);">60</span>
                <span class="hud-label">Detik</span>
            </div>
            <div style="width:1px; height:2.5rem; background:var(--border);"></div>
            <div class="hud-item">
                <span class="hud-value" style="color:var(--green);">
                    <span id="hud-matched">0</span><span style="font-size:1rem; font-weight:600; color:var(--text-muted);">/{{ $vocabularies->count() }}</span>
                </span>
                <span class="hud-label">Pasangan</span>
            </div>
        </div>

        {{-- ===== GAME GRID ===== --}}
        @if($vocabularies->isEmpty())
            <div class="rounded-2xl p-10 text-center" style="background:var(--card); border:1px solid var(--border);">
                <p class="text-4xl mb-3">📭</p>
                <p class="font-syne font-semibold text-white">Tidak ada vocabulary</p>
                <p class="text-sm mt-1" style="color:var(--text-muted);">Lesson ini belum memiliki kosakata.</p>
                <a href="{{ route('lessons.show', $lesson->id) }}"
                   class="inline-block mt-4 font-dm text-sm px-5 py-2 rounded-xl transition-all"
                   style="background:rgba(108,99,255,.15); border:1px solid var(--border); color:var(--purple);">
                    ← Kembali
                </a>
            </div>
        @else
            <div class="fade-up grid grid-cols-2 gap-3" style="animation-delay:.14s">

                {{-- KIRI: Words --}}
                <div id="col-words" class="flex flex-col gap-2.5">
                    <p class="font-syne font-semibold text-xs text-center mb-1"
                       style="color:var(--text-muted); letter-spacing:.06em; text-transform:uppercase;">
                        🇬🇧 English
                    </p>
                    {{-- Rendered by JS --}}
                </div>

                {{-- KANAN: Meanings --}}
                <div id="col-meanings" class="flex flex-col gap-2.5">
                    <p class="font-syne font-semibold text-xs text-center mb-1"
                       style="color:var(--text-muted); letter-spacing:.06em; text-transform:uppercase;">
                        🇮🇩 Indonesian
                    </p>
                    {{-- Rendered by JS --}}
                </div>
            </div>
        @endif

    </div>

    {{-- ===== SUBMIT OVERLAY ===== --}}
    <div class="submit-overlay" id="submit-overlay">
        <div class="submit-card">
            <div class="spinner"></div>
            <p class="font-syne font-bold text-white text-lg mb-1">Menyimpan hasil...</p>
            <p class="font-dm text-sm" style="color:var(--text-muted);">Mohon tunggu sebentar</p>
        </div>
    </div>

    {{-- ===== EXIT MODAL ===== --}}
    <div class="exit-modal" id="exit-modal">
        <div class="submit-card">
            <p class="text-4xl mb-3">⚠️</p>
            <p class="font-syne font-bold text-white text-lg mb-2">Keluar dari game?</p>
            <p class="font-dm text-sm mb-6" style="color:var(--text-muted);">
                Progres kamu di sesi ini tidak akan disimpan.
            </p>
            <div class="flex gap-3">
                <button onclick="closeExitModal()"
                        class="flex-1 font-dm text-sm py-2.5 rounded-xl transition-all"
                        style="background:rgba(108,99,255,.12); border:1px solid var(--border); color:var(--purple);">
                    Lanjutkan
                </button>
                <a href="{{ route('lessons.show', $lesson->id) }}"
                   class="flex-1 font-dm text-sm py-2.5 rounded-xl text-center transition-all"
                   style="background:rgba(255,107,107,.1); border:1px solid rgba(255,107,107,.25); color:#ff9999;">
                    Ya, Keluar
                </a>
            </div>
        </div>
    </div>

    {{-- ===================== GAME LOGIC ===================== --}}
    <script>
    (function () {

        /* ---- state ---- */
        let score       = 0;
        let correct     = 0;
        let timeLeft    = 60;
        let startTime   = Date.now();
        let submitted   = false;
        let selectedWord = null;   // { id, btn }
        let locked       = false;  // prevent double-click during shake

        const total = vocab.length;

        /* ---- shuffle helper ---- */
        function shuffle(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        /* ---- render columns ---- */
        const colWords    = document.getElementById('col-words');
        const colMeanings = document.getElementById('col-meanings');

        if (colWords && colMeanings) {

            // Words: same order as vocab (already shuffled server-side)
            vocab.forEach(v => {
                const btn = document.createElement('button');
                btn.className   = 'match-btn';
                btn.dataset.id  = v.id;
                btn.dataset.col = 'word';
                btn.textContent = v.word;
                btn.addEventListener('click', () => handleWordClick(v.id, btn));
                colWords.appendChild(btn);
            });

            // Meanings: shuffled in JS
            shuffle(vocab).forEach(v => {
                const btn = document.createElement('button');
                btn.className   = 'match-btn';
                btn.dataset.id  = v.id;
                btn.dataset.col = 'meaning';
                btn.textContent = v.meaning;
                btn.addEventListener('click', () => handleMeaningClick(v.id, btn));
                colMeanings.appendChild(btn);
            });
        }

        /* ---- click handlers ---- */
        function handleWordClick(id, btn) {
            if (locked || btn.disabled) return;

            // deselect previous word
            if (selectedWord) {
                selectedWord.btn.classList.remove('selected');
            }

            // toggle off if same
            if (selectedWord && selectedWord.id === id) {
                selectedWord = null;
                return;
            }

            selectedWord = { id, btn };
            btn.classList.add('selected');
        }

        function handleMeaningClick(id, btn) {
            if (locked || btn.disabled || !selectedWord) return;

            locked = true;

            const wordBtn = selectedWord.btn;

            if (selectedWord.id === id) {
                // ✅ CORRECT
                wordBtn.classList.remove('selected');
                wordBtn.classList.add('correct');
                btn.classList.add('correct');
                wordBtn.disabled = true;
                btn.disabled     = true;

                score   += 100;
                correct += 1;

                updateHUD();
                selectedWord = null;
                locked = false;

                if (correct === total) {
                    submitGame();
                }

            } else {
                // ❌ WRONG
                wordBtn.classList.remove('selected');
                wordBtn.classList.add('wrong');
                btn.classList.add('wrong');

                // shake both
                wordBtn.classList.add('shake');
                btn.classList.add('shake');

                setTimeout(() => {
                    wordBtn.classList.remove('wrong', 'shake');
                    btn.classList.remove('wrong', 'shake');
                    selectedWord = null;
                    locked = false;
                }, 600);
            }
        }

        /* ---- HUD updates ---- */
        function updateHUD() {
            document.getElementById('hud-score').textContent   = score;
            document.getElementById('hud-matched').textContent = correct;
        }

        /* ---- TIMER ---- */
        const timerEl = document.getElementById('hud-timer');

        const timerInterval = setInterval(() => {
            timeLeft--;
            timerEl.textContent = timeLeft;

            if (timeLeft <= 10) {
                timerEl.classList.add('timer-warning');
            }
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                submitGame();
            }
        }, 1000);

        /* ---- SUBMIT ---- */
        function submitGame() {
            if (submitted) return;
            submitted = true;
            clearInterval(timerInterval);

            const timeSpent = Math.round((Date.now() - startTime) / 1000);

            document.getElementById('submit-overlay').classList.add('active');

            fetch(SUBMIT_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    score:      score,
                    correct:    correct,
                    total:      total,
                    time_spent: timeSpent,
                    matches:    [],
                }),
            })
            .then(r => r.json())
            .then(d => {
                if (d.redirect) {
                    window.location.href = d.redirect;
                }
            })
            .catch(() => {
                // fallback: redirect to lesson if fetch fails
                window.location.href = EXIT_URL;
            });
        }

        /* ---- EXIT MODAL ---- */
        window.openExitModal  = () => document.getElementById('exit-modal').classList.add('active');
        window.closeExitModal = () => document.getElementById('exit-modal').classList.remove('active');

    })();
    </script>

</div>
@endsection