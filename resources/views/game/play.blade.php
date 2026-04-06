@extends('layouts.app')

@section('title', 'Word Matching — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        .font-syne { font-family:'Syne',sans-serif; }
        .font-dm   { font-family:'DM Sans',sans-serif; }

        /* ---------- HUD CLEAN (Premium) ---------- */
        .hud-clean {
            background: rgba(20, 24, 45, 0.6);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px;
            padding: 24px;
            backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: fadeUp 0.6s ease both;
        }

        .hud-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin: 14px 0 10px;
            padding: 0 4px;
        }

        .hud-timer {
            font-size: 2rem;
            font-weight: 800;
            color: #7c7cff;
            font-family: 'Syne', sans-serif;
            text-shadow: 0 0 10px rgba(124,124,255,0.3);
            line-height: 1;
        }

        .hud-progress {
            font-size: 1.2rem;
            font-weight: 700;
            color: #00d4ff;
            font-family: 'Syne', sans-serif;
        }

        @keyframes pulse-red {
            0%,100% { color:var(--red); }
            50%      { color:#ff6363; }
        }
        .timer-warning { animation:pulse-red .7s ease infinite; }

        /* ---------- GAME GRID ---------- */
        .game-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 20px;
        }

        .col-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ---------- GAME CARD ---------- */
        .card-slot {
            width: 100%;
            min-height: 76px; /* slightly taller to account for borders without collapsing */
        }
        .card-slot.empty {
            visibility: hidden;
        }

        .game-card {
            background: #1c1f35;
            border: 2px solid #2a2d45;
            border-radius: 18px;
            min-height: 72px;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            padding: 1.2rem;
            text-align: center;
            word-break: break-word;
            line-height: 1.4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .game-card:hover:not(:disabled):not(.matched) {
            background: #22264a;
            border-color: #6c63ff;
            transform: scale(1.03);
        }
        .game-card.selected {
            background: #252a50;
            border: 2px solid #6c63ff;
            color: #a09bff;
            box-shadow: 0 0 0 3px rgba(108,99,255,0.2);
            transform: scale(1.05);
        }
        .game-card.correct {
            background: rgba(6,214,160,0.2) !important;
            border-color: #06d6a0 !important;
            color: #06d6a0 !important;
            animation: correctPop 0.3s ease;
            box-shadow: 0 0 16px rgba(6,214,160,0.4);
            pointer-events: none;
            cursor: default;
        }
        .game-card.wrong {
            background: rgba(255,99,99,0.15) !important;
            border-color: #ff6363 !important;
            color: #ff6363 !important;
            animation: shake 0.4s ease;
        }
        .game-card:disabled { cursor: default; }

        /* ---------- ANIMATIONS ---------- */
        @keyframes correctPop {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        @keyframes fadeOut {
            0%   { opacity:1; transform:scale(1); }
            100% { opacity:0; transform:scale(0.8); }
        }
        .fade-out {
            animation: fadeOut 0.3s ease forwards;
        }

        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%,60% { transform:translateX(-8px); }
            40%,80% { transform:translateX(8px); }
        }
        .shake { animation:shake .4s ease; }

        @keyframes cardIn {
            from { opacity:0; transform:scale(0.85); }
            to   { opacity:1; transform:scale(1); }
        }
        .card-in { animation: cardIn 0.25s ease forwards; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation:fadeUp .45s ease both; }
        .delay-1 { animation-delay:.07s; }
        .delay-2 { animation-delay:.14s; }
 
        /* ---------- STAGE OVERLAY ---------- */
        .stage-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15,18,32,0.96);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            pointer-events: auto;
        }

        .stage-overlay:not(.active) {
            display: none;
        }

        .stage-title {
            font-family:'Syne',sans-serif; font-weight:800; font-size:4rem;
            color:white; text-transform:uppercase; letter-spacing:4px;
            margin-bottom:0.5rem; transform: scale(0.5); opacity:0;
        }
        .stage-overlay.active .stage-title {
            animation: stagePop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        @keyframes stagePop {
            to { transform: scale(1); opacity:1; }
        }

        /* ---------- STAGE INDICATOR ---------- */
        .stage-dots { display:flex; gap:10px; margin-top: 10px; }
        .dot { 
            width:10px; height:10px; border-radius:50%; 
            background:rgba(255,255,255,0.1); 
            border:1px solid rgba(255,255,255,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .dot.active { background: #6c63ff; box-shadow: 0 0 12px #6c63ff; transform: scale(1.3); }

        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

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

        .stage-badge {
            background: rgba(108, 99, 255, 0.15);
            border: 1px solid rgba(108, 99, 255, 0.3);
            color: white;
            padding: 5px 16px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            backdrop-filter: blur(8px);
            box-shadow: 0 0 15px rgba(108, 99, 255, 0.2);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-family: 'Syne', sans-serif;
        }
        .stage-badge.pop { transform: scale(1.2); box-shadow: 0 0 30px rgba(108, 99, 255, 0.5); }

        .stage-btn {
            margin-top: 2rem;
            padding: 1rem 3rem;
            background: var(--purple);
            color: white;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 9999px;
            border: 2px solid rgba(255,255,255,0.1);
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(108, 99, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            
            position: relative;
            z-index: 10000;
            pointer-events: auto;
        }
        .stage-overlay.active .stage-btn { opacity: 1; transform: translateY(0); } /* removed redundant delay */
        .stage-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(108, 99, 255, 0.6);
            background: #7d73ff;
        }
        .stage-btn:active { transform: scale(0.95); }

        /* ---------- PROGRESS BAR ---------- */
        .progress-container {
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease both;
            animation-delay: 0.2s;
        }
        .progress-bar {
            width: 100%;
            height: 10px;
            background: #1a1d33;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
        }
        #progress-fill {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #6c63ff, #00d4ff);
            box-shadow: 0 0 12px rgba(108,99,255,0.5);
            transition: width 0.4s ease;
        }
    </style>

    {{-- ========= BLADE DATA → JS ========= --}}
    <script>
        const allVocab  = @json($vocabularies->map(fn($v) => ['id'=>$v->id,'word'=>$v->word,'meaning'=>$v->meaning])->values());
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

        {{-- ========= HUD CLEAN (Premium) ========= --}}
        <div class="hud-clean fade-up">
            <div class="stage-badge" id="stage-badge">
                Stage <span id="stage-number">1</span>
            </div>

            <div class="stage-dots">
                <div class="dot active" id="dot-1"></div>
                <div class="dot" id="dot-2"></div>
                <div class="dot" id="dot-3"></div>
            </div>

            <div class="hud-row">
                <div class="hud-timer" id="hud-timer">60</div>
                <div class="hud-progress" id="hud-progress">0/20</div>
            </div>

            <div class="progress-bar">
                <div id="progress-fill"></div>
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
                <div>
                    <p class="text-xs text-center mb-2 font-dm uppercase tracking-wider" style="color:var(--text-muted);">🇮🇩 Indonesia</p>
                    <div id="col-left" class="col-container"></div>
                </div>
                <div>
                    <p class="text-xs text-center mb-2 font-dm uppercase tracking-wider" style="color:var(--text-muted);">🇬🇧 English</p>
                    <div id="col-right" class="col-container"></div>
                </div>
            </div>
        @endif

    </div>

    {{-- ========= SUBMIT OVERLAY ========= --}}
    <div class="submit-overlay" id="submit-overlay">
        <div class="submit-card">
            <div class="spinner"></div>
            <p class="font-syne font-bold text-white text-lg mb-1" id="submit-status">Menyimpan hasil...</p>
            <p class="font-dm text-sm" style="color:var(--text-muted);">Mohon tunggu sebentar</p>
        </div>
    </div>

    {{-- Overlay moved to bottom for proper render sorting --}}

    {{-- ========= STAGE OVERLAY (Final Layer) ========= --}}
    <div class="stage-overlay" id="stage-overlay">
        <h2 class="stage-title" id="stage-msg">Stage 1</h2>
        <p class="text-white opacity-60 font-dm tracking-widest uppercase text-center text-xs mb-1" id="stage-sub">Mulai Tantangan</p>
        <p id="stage-time-info" class="text-white mt-1 mb-4 font-dm text-sm opacity-70"></p>
        <button id="stage-start-btn" class="stage-btn" onclick="handleStartClick()">Mulai Stage 1</button>
    </div>

    {{-- ========= GAME LOGIC ========= --}}
    <script>
    (function () {
        const colLeft = document.getElementById('col-left');
        const colRight = document.getElementById('col-right');

        console.log('ENGINE: VOCAB DATA:', allVocab);

        // ── Helpers ──────────────────────────────────────────────────────
        function shuffle(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        // ── SOUND EFFECT ─────────────────────────────────────────────────
        function playSound(type) {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            if (type === 'correct') {
                osc.frequency.setValueAtTime(523, ctx.currentTime);
                osc.frequency.setValueAtTime(659, ctx.currentTime + 0.1);
                osc.frequency.setValueAtTime(784, ctx.currentTime + 0.2);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            } else {
                osc.frequency.setValueAtTime(300, ctx.currentTime);
                osc.frequency.setValueAtTime(200, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            }
        }

        // ── STAGE DATA ───────────────────────────────────────────────────
        const STAGE_CONFIG = {
            1: { time: 75, xp: 15, msg: "Stage 1", sub: "Learning Mode" },
            2: { time: 60, xp: 15, msg: "Stage 2", sub: "Normal Challenge" },
            3: { time: 50, xp: 20, msg: "Stage 3", sub: "Hard Mode" }
        };

        window.onerror = function (msg, url, line, col, error) {
            console.error('GLOBAL ERROR:', msg, 'at', line);
        };



        let currentStage = 1;
        let nextStageToStart = 1; 
        let totalCorrectMatches = 0;
        let currentStageCorrect = 0;
        let submitted = false;
        let isTransitioning = true; // Game dalam keadaan idle (overlay aktif)

        let queue = [];
        let totalPairsInStage = 0;
        let timeLeft = 60;
        let timerInterval = null;
        let startTime = Date.now();

        let matchedPairsBuffer = [];
        let selectedLeft = null;
        let selectedRight = null;

        function createSlot(idPrefix, index) {
            const slot = document.createElement('div');
            slot.className = 'card-slot empty';
            slot.id = `${idPrefix}-slot-${index}`;
            return slot;
        }

        function insertCard(slot, data) {
            const btn = document.createElement('button');
            btn.className = 'game-card card-in';
            btn.dataset.id = data.id;
            btn.dataset.type = data.type;
            btn.textContent = data.text;
            btn.addEventListener('click', () => handleClick(btn, data, slot));
            slot.innerHTML = '';
            slot.appendChild(btn);
            slot.classList.remove('empty');
        }

        function handleClick(btn, data, slot) {
            if (submitted || isTransitioning) return;
            if (btn.classList.contains('matched') || btn.classList.contains('correct')) return;
            if (data.type === 'left') {
                if (selectedLeft && selectedLeft.btn === btn) {
                    btn.classList.remove('selected');
                    selectedLeft = null;
                } else {
                    if (selectedLeft) selectedLeft.btn.classList.remove('selected');
                    btn.classList.add('selected');
                    selectedLeft = { btn, data, slot };
                }
            } else {
                if (selectedRight && selectedRight.btn === btn) {
                    btn.classList.remove('selected');
                    selectedRight = null;
                } else {
                    if (selectedRight) selectedRight.btn.classList.remove('selected');
                    btn.classList.add('selected');
                    selectedRight = { btn, data, slot };
                }
            }
            if (selectedLeft && selectedRight) checkMatch();
        }

        function startStage(stageNum) {
            try {
                console.log('START STAGE:', stageNum);

                isTransitioning = false;
                currentStage = stageNum;

                const config = STAGE_CONFIG[stageNum];

                timeLeft = config.time;
                currentStageCorrect = 0;
                updateProgressBar();

                document.getElementById('hud-timer').textContent = timeLeft;
                
                // Re-bind jika null (safe-guard)
                const cL = colLeft || document.getElementById('col-left');
                const cR = colRight || document.getElementById('col-right');

                if (!cL || !cR) {
                    console.error('ENGINE: Grid containers not found!');
                    return;
                }

                cL.innerHTML = '';
                cR.innerHTML = '';

                // 🔥 ambil vocab
                let basePool = shuffle(allVocab);

                if (basePool.length > 10) basePool = basePool.slice(0, 10);

                let doublePool = [...basePool, ...basePool];
                queue = shuffle(doublePool);

                totalPairsInStage = queue.length;
                updateProgressBar();

                const initialCount = Math.min(5, queue.length);
                const initialPairs = queue.splice(0, initialCount);

                let activeL = shuffle(initialPairs.map(p => ({
                    id: p.id,
                    text: p.meaning,
                    type: 'left'
                })));

                let activeR = shuffle(initialPairs.map(p => ({
                    id: p.id,
                    text: p.word,
                    type: 'right'
                })));

                for (let i = 0; i < 5; i++) {
                    const sL = createSlot('left', i);
                    if (i < activeL.length) insertCard(sL, activeL[i]);
                    cL.appendChild(sL);

                    const sR = createSlot('right', i);
                    if (i < activeR.length) insertCard(sR, activeR[i]);
                    cR.appendChild(sR);
                }

                for(let i=1; i<=3; i++) {
                    const dot = document.getElementById(`dot-${i}`);
                    if (i <= stageNum) dot.className = 'dot active';
                    else dot.className = 'dot';
                }

                // 🔥 TIMER FIX
                if (timerInterval) clearInterval(timerInterval);

                timerInterval = setInterval(updateTimer, 1000);

                // 🔥 TUTUP OVERLAY TERAKHIR
                document.getElementById('stage-overlay').classList.remove('active');

                const badge = document.getElementById('stage-badge');
                document.getElementById('stage-number').textContent = stageNum;
                badge.classList.add('pop');
                setTimeout(() => badge.classList.remove('pop'), 500);

                console.log('STAGE STARTED SUCCESS');

            } catch (err) {
                console.error('START STAGE ERROR:', err);
            }
        }

        function showStageComplete() {
            isTransitioning = true;
            clearInterval(timerInterval);
            playSound('correct');

            totalCorrectMatches += currentStageCorrect;

            if (currentStage < 3) {
                nextStageToStart = currentStage + 1;
                const config = STAGE_CONFIG[nextStageToStart];
                
                document.getElementById('stage-msg').textContent = "STAGE COMPLETE";
                document.getElementById('stage-sub').textContent = "Bagus! Kamu siap ke tantangan berikutnya?";
                document.getElementById('stage-time-info').textContent = "Waktu: " + config.time + " detik";
                document.getElementById('stage-start-btn').textContent = "Masuk Stage " + nextStageToStart;
                document.getElementById('stage-overlay').classList.add('active');
            } else {
                document.getElementById('stage-msg').textContent = "LESSON COMPLETE 🎉";
                document.getElementById('stage-sub').textContent = "Semua stage berhasil diselesaikan!";
                document.getElementById('stage-start-btn').style.display = "none";
                document.getElementById('stage-overlay').classList.add('active');
                setTimeout(submitGame, 1500);
            }
        }

        function showStageFailed() {
            isTransitioning = true;
            clearInterval(timerInterval);
            playSound('wrong');

            console.log('STAGE FAILED → EXIT');

            // tampilkan overlay sebentar (opsional UX)
            document.getElementById('stage-msg').textContent = "GAGAL ❌";
            document.getElementById('stage-sub').textContent = "Kembali ke daftar kosakata...";
            
            const btn = document.getElementById('stage-start-btn');
            if (btn) btn.style.display = "none";

            document.getElementById('stage-overlay').classList.add('active');

            // ⏳ delay 1.5 detik biar user lihat feedback
            setTimeout(() => {
                window.location.href = exitUrl;
            }, 1500);
        }

        function updateTimer() {
            if (submitted || isTransitioning) return;
            timeLeft--;
            const el = document.getElementById('hud-timer');
            el.textContent = timeLeft;
            el.style.color = (timeLeft <= 5) ? '#ff6363' : 'var(--purple)';
            if (timeLeft <= 0) showStageFailed();
        }

        function updateHUD() {
            updateProgressBar();
        }

        function updateProgressBar() {
            const percent = (currentStageCorrect / totalPairsInStage) * 100;
            const fill = document.getElementById('progress-fill');
            fill.style.width = percent + "%";
            
            document.getElementById('hud-progress').textContent = currentStageCorrect + "/" + totalPairsInStage;

            if (percent > 80) {
                fill.style.boxShadow = "0 0 20px rgba(0, 212, 255, 0.8)";
            } else {
                fill.style.boxShadow = "0 0 12px rgba(108, 99, 255, 0.5)";
            }
        }

        function checkMatch() {
            const sameId = selectedLeft.data.id === selectedRight.data.id;
            const a = selectedLeft.btn, b = selectedRight.btn;
            const sL = selectedLeft.slot, sR = selectedRight.slot;
            if (sameId) {
                playSound('correct');
                currentStageCorrect++;
                updateHUD();
                a.classList.add('correct'); b.classList.add('correct');
                setTimeout(() => {
                    a.classList.add('fade-out'); b.classList.add('fade-out');
                    setTimeout(() => { sL.innerHTML = ''; sL.classList.add('empty'); sR.innerHTML = ''; sR.classList.add('empty'); }, 250);
                }, 150);
                matchedPairsBuffer.push({ leftSlot: sL, rightSlot: sR });
                if (matchedPairsBuffer.length >= 2 || currentStageCorrect === totalPairsInStage) setTimeout(fillEmptySlots, 500);
                selectedLeft = null; selectedRight = null;
                if (currentStageCorrect === totalPairsInStage) setTimeout(showStageComplete, 1200);
            } else {
                playSound('wrong'); combo = 0; a.classList.add('wrong'); b.classList.add('wrong');
                const wA = a, wB = b;
                setTimeout(() => { wA.classList.remove('wrong'); wB.classList.remove('wrong', 'selected'); }, 400);
                selectedRight = null;
            }
        }

        function fillEmptySlots() {
            if (matchedPairsBuffer.length === 0) return;
            const batch = [...matchedPairsBuffer];
            matchedPairsBuffer = [];
            batch.forEach(p => {
                if (queue.length > 0) {
                    const next = queue.shift();
                    insertCard(p.leftSlot, { id: next.id, text: next.meaning, type: 'left' });
                    insertCard(p.rightSlot, { id: next.id, text: next.word, type: 'right' });
                }
            });
        }

        function submitGame() {
            if (submitted) return;
            submitted = true;
            clearInterval(timerInterval);
            document.getElementById('submit-overlay').classList.add('active');
            document.getElementById('submit-status').textContent = "Lesson Completed! 🎉";
            const timeSpent = Math.round((Date.now() - startTime) / 1000);
            fetch(submitUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    correct: totalCorrectMatches,
                    total: 60,
                    time_spent: timeSpent
                })
            })
            .then(res => res.json())
            .then(data => { if (data.redirect) window.location.href = data.redirect; })
            .catch(() => window.location.href = exitUrl);
        }

        document.addEventListener('DOMContentLoaded', () => {
             const config = STAGE_CONFIG[1];
             document.getElementById('stage-msg').textContent = config.msg;
             document.getElementById('stage-sub').textContent = config.sub;
             document.getElementById('stage-time-info').textContent = "Waktu: " + config.time + " detik";
             document.getElementById('stage-start-btn').textContent = "Mulai " + config.msg;
             document.getElementById('stage-overlay').classList.add('active');
        });

        window.handleStartClick = function() {
            console.log('START CLICKED - TRIGER ENGINE');
            const overlay = document.getElementById('stage-overlay');
            
            // 🔥 PREP MODE FEEL
            document.getElementById('stage-sub').textContent = "Bersiap...";
            document.getElementById('stage-time-info').textContent = "Game akan segera dimulai";
            
            setTimeout(() => {
                overlay.classList.remove('active');
                startStage(nextStageToStart);
            }, 600);
        };

        window.confirmExit = function () {
            if (confirm('Keluar? Progres stage akan hilang.')) window.location.href = exitUrl;
        };
    })();
    </script>

</div>
@endsection