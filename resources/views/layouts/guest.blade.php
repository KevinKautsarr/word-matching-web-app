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
        body { font-family: 'DM Sans', sans-serif; background-color: #0F1220; color: #F1F5F9; position: relative; overflow-x: hidden; }
        h1,h2,h3,h4,h5,h6,.font-syne { font-family: 'Syne', sans-serif; }
        .glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
        .gradient-text { background: linear-gradient(135deg, #4F7CFF 0%, #6AA8FF 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #4F7CFF, #6AA8FF); color: white; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); font-family: 'Syne', sans-serif; font-weight: 700; }
        .btn-primary:hover { box-shadow: 0 10px 30px rgba(79, 124, 255, 0.4); transform: translateY(-3px) scale(1.02); }
        .btn-primary:active { transform: scale(0.96); }
        .glow-primary { box-shadow: 0 0 30px rgba(79, 124, 255, 0.35); }
        .bg-orb { position: absolute; border-radius: 50%; filter: blur(100px); pointer-events: none; opacity: 0.15; z-index: -1; }
        .input-field {
            width: 100%; padding: 0.85rem 1.25rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem; color: #F1F5F9;
            font-size: 0.9375rem; transition: all 0.25s;
            outline: none;
        }
        .input-field:focus { border-color: #4F7CFF; box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.1); background: rgba(79, 124, 255, 0.05); }
        .input-field::placeholder { color: rgba(226,228,240,0.25); }
        .animate-in { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeSlideUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="min-h-screen bg-[#0F1220]">
    <div class="bg-orb w-[400px] h-[400px] bg-[#4F7CFF] -top-20 -left-20"></div>
    <div class="bg-orb w-[300px] h-[300px] bg-[#6C63FF] -bottom-20 -right-20"></div>
    @yield('content')
</body>
</html>
