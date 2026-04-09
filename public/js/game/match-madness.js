(function () {
    const config = window.GameConfig;
    if (!config) {
        console.error('ENGINE: GameConfig not found!');
        return;
    }

    const colLeft = document.getElementById('col-left');
    const colRight = document.getElementById('col-right');

    // ── Helpers ──────────────────────────────────────────────────────
    function shuffle(arr) {
        const a = [...arr];
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    // ── SOUND EFFECT (With Combo Pitch) ──────────────────────────────────
    let comboCount = 0;
    let lastCorrectTime = 0;

    function playSound(type) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            if (ctx.state === 'suspended') ctx.resume();

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            if (type === 'correct') {
                const now = Date.now();
                if (now - lastCorrectTime < 2000) {
                    comboCount = Math.min(comboCount + 1, 8);
                } else {
                    comboCount = 0;
                }
                lastCorrectTime = now;

                const baseFreq = 523.25; // C5
                const multiplier = 1 + (comboCount * 0.12);
                
                osc.frequency.setValueAtTime(baseFreq * multiplier, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(baseFreq * multiplier * 1.5, ctx.currentTime + 0.1);
                
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            } else {
                comboCount = 0;
                osc.frequency.setValueAtTime(250, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(100, ctx.currentTime + 0.2);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch (e) { console.warn("Audio error:", e); }
    }

    // ── STAGE DATA ───────────────────────────────────────────────────
    const STAGE_CONFIG = {
        1: { time: 50, xp: 15, msg: "Stage 1", sub: "Learning Mode" },
        2: { time: 40, xp: 15, msg: "Stage 2", sub: "Normal Challenge" },
        3: { time: 30, xp: 20, msg: "Stage 3", sub: "Hard Mode" }
    };

    let currentStage = 1;
    let nextStageToStart = 1; 
    let totalCorrectMatches = 0;
    let totalPairsToAttempt = 0;
    let currentStageCorrect = 0;
    let submitted = false;
    let isTransitioning = true; 
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
        const animClass = data.type === 'left' ? 'fly-in-left' : 'fly-in-right';
        btn.className = `game-card ${animClass}`;
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
            isTransitioning = false;
            currentStage = stageNum;
            const stageConfig = STAGE_CONFIG[stageNum];

            timeLeft = stageConfig.time;
            currentStageCorrect = 0;
            updateProgressBar();

            document.getElementById('hud-timer').textContent = timeLeft;

            colLeft.innerHTML = '';
            colRight.innerHTML = '';

            let basePool = shuffle(config.allVocab);
            let combinedPool = [...basePool, ...basePool];
            if (combinedPool.length < 10) {
                combinedPool = [...combinedPool, ...combinedPool, ...combinedPool];
            }
            
            queue = shuffle(combinedPool).slice(0, 10); 
            totalPairsInStage = queue.length;
            totalPairsToAttempt += totalPairsInStage;
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
                colLeft.appendChild(sL);

                const sR = createSlot('right', i);
                if (i < activeR.length) insertCard(sR, activeR[i]);
                colRight.appendChild(sR);
            }

            for(let i=1; i<=3; i++) {
                const dot = document.getElementById(`dot-${i}`);
                if (i <= stageNum) dot.className = 'dot active';
                else dot.className = 'dot';
            }

            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);

            document.getElementById('stage-overlay').classList.remove('active');
            const badge = document.getElementById('stage-badge');
            document.getElementById('stage-number').textContent = stageNum;
            badge.classList.add('pop');
            setTimeout(() => badge.classList.remove('pop'), 500);

        } catch (err) { console.error('START STAGE ERROR:', err); }
    }

    function showStageComplete() {
        isTransitioning = true;
        clearInterval(timerInterval);
        totalCorrectMatches += currentStageCorrect;

        if (currentStage < 3) {
            nextStageToStart = currentStage + 1;
            const stageConfig = STAGE_CONFIG[nextStageToStart];
            
            document.getElementById('stage-msg').textContent = "STAGE " + nextStageToStart;
            document.getElementById('stage-sub').textContent = "Bagus! Kamu siap ke tantangan berikutnya?";
            document.getElementById('stage-time-info').textContent = "Waktu: " + stageConfig.time + " detik";
            document.getElementById('stage-start-btn').textContent = "Mulai Stage " + nextStageToStart;
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
        document.getElementById('stage-msg').textContent = "GAGAL ❌";
        document.getElementById('stage-sub').textContent = "Kembali ke daftar kosakata...";
        const btn = document.getElementById('stage-start-btn');
        if (btn) btn.style.display = "none";
        document.getElementById('stage-overlay').classList.add('active');
        setTimeout(() => { window.location.href = config.exitUrl; }, 1500);
    }

    function updateTimer() {
        if (submitted || isTransitioning) return;
        timeLeft--;
        const el = document.getElementById('hud-timer');
        el.textContent = timeLeft;
        el.style.color = (timeLeft <= 5) ? 'var(--red)' : 'var(--primary)';
        if (timeLeft <= 0) showStageFailed();
    }

    function updateProgressBar() {
        const percent = (currentStageCorrect / totalPairsInStage) * 100;
        const fill = document.getElementById('progress-fill');
        fill.style.width = percent + "%";
        document.getElementById('hud-progress').textContent = currentStageCorrect + "/" + totalPairsInStage;
        if (percent > 80) fill.style.boxShadow = "0 0 30px rgba(79, 124, 255, 0.8)";
        else fill.style.boxShadow = "0 0 15px rgba(79, 124, 255, 0.4)";
    }

    function checkMatch() {
        const sameId = selectedLeft.data.id === selectedRight.data.id;
        const a = selectedLeft.btn, b = selectedRight.btn;
        const sL = selectedLeft.slot, sR = selectedRight.slot;
        
        if (sameId) {
            playSound('correct');
            currentStageCorrect++;
            updateProgressBar();
            a.classList.add('correct'); 
            b.classList.add('correct');
            matchedPairsBuffer.push({ leftSlot: sL, rightSlot: sR });

            setTimeout(() => {
                a.classList.add('fade-out'); 
                b.classList.add('fade-out');
                setTimeout(() => { 
                    sL.innerHTML = ''; sL.classList.add('empty'); 
                    sR.innerHTML = ''; sR.classList.add('empty'); 
                    const isLastStretch = (totalPairsInStage - currentStageCorrect) <= 5;
                    const shouldRefill = (matchedPairsBuffer.length >= 2) || isLastStretch || (queue.length === 0);
                    if (shouldRefill && matchedPairsBuffer.length > 0) fillEmptySlotsCarefully(); 
                }, 250);
            }, 100);

            selectedLeft = null; 
            selectedRight = null;
            if (currentStageCorrect === totalPairsInStage) setTimeout(showStageComplete, 1200);
        } else {
            playSound('wrong'); 
            a.classList.add('wrong'); 
            b.classList.add('wrong');
            const wA = a, wB = b;
            setTimeout(() => { 
                wA.classList.remove('wrong', 'selected'); 
                wB.classList.remove('wrong', 'selected'); 
            }, 400);
            selectedRight = null;
        }
    }

    function fillEmptySlotsCarefully() {
        if (matchedPairsBuffer.length === 0) return;
        const batch = [...matchedPairsBuffer];
        matchedPairsBuffer = []; 
        let pairsToAdd = [];
        while(pairsToAdd.length < batch.length && queue.length > 0) pairsToAdd.push(queue.shift());
        if (pairsToAdd.length === 0) return;

        const availableLeftSlots = batch.slice(0, pairsToAdd.length).map(b => b.leftSlot);
        const availableRightSlots = shuffle(batch.slice(0, pairsToAdd.length).map(b => b.rightSlot));

        pairsToAdd.forEach((pair, idx) => {
            insertCard(availableLeftSlots[idx], { id: pair.id, text: pair.meaning, type: 'left' });
            insertCard(availableRightSlots[idx], { id: pair.id, text: pair.word, type: 'right' });
        });
    }

    function submitGame() {
        if (submitted) return;
        submitted = true;
        clearInterval(timerInterval);
        document.getElementById('submit-overlay').classList.add('active');
        document.getElementById('submit-status').textContent = "Lesson Completed! 🎉";
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        
        fetch(config.submitUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': config.csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                score: totalCorrectMatches * 10,
                correct: totalCorrectMatches,
                total: totalPairsToAttempt,
                time_spent: timeSpent
            })
        })
        .then(res => res.json())
        .then(data => { if (data.redirect) window.location.replace(data.redirect); })
        .catch(() => window.location.replace(config.exitUrl));
    }

    document.addEventListener('DOMContentLoaded', () => {
         const stageConfig = STAGE_CONFIG[1];
         const startBtn = document.getElementById('stage-start-btn');
         if (!startBtn) return;
         document.getElementById('stage-msg').textContent = stageConfig.msg;
         document.getElementById('stage-sub').textContent = stageConfig.sub;
         document.getElementById('stage-time-info').textContent = "Waktu: " + stageConfig.time + " detik";
         startBtn.textContent = "Mulai " + stageConfig.msg;
         document.getElementById('stage-overlay').classList.add('active');
    });

    window.handleStartClick = function() {
        const overlay = document.getElementById('stage-overlay');
        const startBtn = document.getElementById('stage-start-btn');
        const countdownArea = document.getElementById('countdown-area');
        const countdownNum = document.getElementById('countdown-number');
        const stageMsg = document.getElementById('stage-msg');
        const stageSub = document.getElementById('stage-sub');
        const stageTimeInfo = document.getElementById('stage-time-info');
        
        startBtn.classList.add('hidden');
        stageMsg.classList.add('hidden');
        stageSub.classList.add('hidden');
        stageTimeInfo.classList.add('hidden');
        countdownArea.classList.remove('hidden');

        let count = 3;
        countdownNum.textContent = count;
        const timer = setInterval(() => {
            count--;
            if (count > 0) {
                countdownNum.textContent = count;
                countdownNum.style.animation = 'none';
                void countdownNum.offsetWidth; 
                countdownNum.style.animation = 'stagePop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
            } else {
                clearInterval(timer);
                overlay.classList.remove('active');
                setTimeout(() => {
                    startBtn.classList.remove('hidden');
                    stageMsg.classList.remove('hidden');
                    stageSub.classList.remove('hidden');
                    stageTimeInfo.classList.remove('hidden');
                    countdownArea.classList.add('hidden');
                }, 500);
                startStage(nextStageToStart);
            }
        }, 800);
    };

    window.confirmExit = function () {
        if (confirm('Keluar? Progres stage akan hilang.')) window.location.href = config.exitUrl;
    };
})();
