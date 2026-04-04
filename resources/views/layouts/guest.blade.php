<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'LEXORA – Platform Pembelajaran Kosakata Bahasa Inggris Interaktif dengan Gamifikasi')">
    <title>@yield('title', 'LEXORA') – Pembelajaran Kosakata</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        syne: ['Syne', 'sans-serif'],
                        dm:   ['DM Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'DM Sans', sans-serif; background-color: #0d0f1a; color: #e2e4f0; }
        h1,h2,h3,h4,h5,h6,.font-syne { font-family: 'Syne', sans-serif; }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); }
        .gradient-text { background: linear-gradient(135deg, #6c63ff 0%, #06d6a0 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #6c63ff, #8b85ff); color: white; transition: all 0.25s ease; }
        .btn-primary:hover { box-shadow: 0 0 24px rgba(108,99,255,0.5); transform: translateY(-1px); }
        .glow-purple { box-shadow: 0 0 24px rgba(108,99,255,0.35); }
        .bg-orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; }
        .input-field {
            width: 100%; padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem; color: #e2e4f0;
            font-size: 0.875rem; transition: all 0.2s;
            outline: none;
        }
        .input-field:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.15); background: rgba(108,99,255,0.05); }
        .input-field::placeholder { color: rgba(226,228,240,0.3); }
        .animate-in { animation: fadeSlideUp 0.5s ease forwards; }
        @keyframes fadeSlideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="min-h-screen bg-[#0d0f1a]">
    @yield('content')
</body>
</html>
