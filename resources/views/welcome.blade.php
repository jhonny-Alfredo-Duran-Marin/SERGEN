<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso | Ser.Gen Telecomunicación & Construcción</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --sergen-navy: #001f3f;
            --sergen-gray: #f4f6f9;
        }
        body { font-family: 'Instrument Sans', sans-serif; background-color: var(--sergen-gray); }
        .bg-sergen-navy { background-color: var(--sergen-navy); }
        .text-sergen-navy { color: var(--sergen-navy); }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .hero-pattern { background-image: radial-gradient(#001f3f 0.5px, transparent 0.5px); background-size: 24px 24px; opacity: 0.05; }
    </style>
</head>
<body class="min-h-screen flex flex-col relative overflow-x-hidden">
    <div class="absolute inset-0 hero-pattern pointer-events-none"></div>

    <header class="w-full max-w-7xl mx-auto px-8 py-8 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-4">
            <img src="{{ asset('vendor/adminlte/dist/img/logoSer_Gen2.jpg') }}" alt="Logo Ser.Gen" class="h-16 w-auto rounded shadow-sm border border-white">
            <div class="hidden md:block border-l pl-4 border-slate-300">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest leading-none">Gestión Estratégica</h2>
                <span class="text-lg font-black text-sergen-navy tracking-tighter">ALMACÉN CENTRAL</span>
            </div>
        </div>

        @if (Route::has('login'))
            <nav>
                @auth
                    <a href="{{ url('/home') }}" class="px-6 py-2.5 bg-sergen-navy text-white rounded font-bold hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2">
                        <i class="fas fa-th-large"></i> DASHBOARD
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-sergen-navy text-white rounded font-bold hover:bg-slate-800 transition-all shadow-xl flex items-center gap-2">
                        <i class="fas fa-lock"></i> INICIAR SESIÓN
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <main class="flex-1 flex items-center justify-center px-8 relative z-10">
        <div class="max-w-6xl w-full grid lg:grid-cols-2 gap-16 items-center">

            <div class="space-y-10">
                <div class="space-y-6">
                    <div class="flex items-center gap-2 text-blue-600 font-bold text-sm tracking-widest uppercase">
                        <span class="h-1 w-8 bg-blue-600 rounded-full"></span>
                        Infraestructura de Datos
                    </div>
                    <h1 class="text-6xl font-black text-sergen-navy leading-[1.1] tracking-tight">
                        Sistema Integrado de <br><span class="text-blue-700 font-light">Control de Activos</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-lg leading-relaxed font-medium">
                        Optimización operativa para proyectos de telecomunicación y construcción civil. Trazabilidad absoluta en inventario y préstamos.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 group hover:border-blue-500 transition-colors">
                        <i class="fas fa-boxes-stacked text-sergen-navy text-2xl mb-3"></i>
                        <h4 class="font-bold text-sergen-navy">Stock en Tiempo Real</h4>
                        <p class="text-xs text-slate-500">Monitoreo constante de existencias y reabastecimiento.</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 group hover:border-blue-500 transition-colors">
                        <i class="fas fa-file-signature text-sergen-navy text-2xl mb-3"></i>
                        <h4 class="font-bold text-sergen-navy">Reportes Detallados</h4>
                        <p class="text-xs text-slate-500">Generación de informes de consumo y préstamos.</p>
                    </div>
                </div>
            </div>

            <div class="relative hidden lg:block animate-pulse-slow">
                <div class="absolute -inset-10 bg-blue-500/10 rounded-full blur-[80px]"></div>
                <div class="glass-card p-1 border border-white/50 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                    <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <span class="text-[10px] font-bold opacity-50 uppercase tracking-widest">Ser.Gen Control Panel v4.0</span>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="h-4 w-40 bg-slate-200 rounded"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-32 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col items-center justify-center gap-2">
                                <span class="text-2xl font-bold text-sergen-navy">100%</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Disponibilidad</span>
                            </div>
                            <div class="h-32 bg-sergen-navy rounded-2xl flex flex-col items-center justify-center gap-2 shadow-inner">
                                <i class="fas fa-shield-check text-blue-400 text-2xl"></i>
                                <span class="text-[10px] text-blue-200 font-bold uppercase tracking-tighter text-center px-2">Encriptación de Datos de Obra</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-2 w-full bg-slate-100 rounded"></div>
                            <div class="h-2 w-full bg-slate-100 rounded"></div>
                            <div class="h-2 w-2/3 bg-slate-100 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="w-full max-w-7xl mx-auto px-8 py-10 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center relative z-10">
        <div class="text-slate-500 text-sm">
            &copy; {{ date('Y') }} <strong>SER.GEN TELECOMUNICACIÓN & CONSTRUCCIÓN</strong>.
        </div>
        <div class="flex gap-8 text-xs font-bold text-slate-400 uppercase tracking-widest">
            <span class="hover:text-sergen-navy transition-colors cursor-help">Santa Cruz - Bolivia</span>
            <span class="hover:text-sergen-navy transition-colors cursor-help">Soporte Técnico</span>
        </div>
    </footer>

    <style>
        @keyframes pulse-slow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-pulse-slow { animation: pulse-slow 6s ease-in-out infinite; }
    </style>
</body>
</html>
