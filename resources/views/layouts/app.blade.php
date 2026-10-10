<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EV Charging - @yield('title')</title>



    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">



    <style>

        /* =========================================

           1. PENGATURAN DASAR & LAYOUT (Tema Terang)

           ========================================= */

        body {

            margin: 0;

            padding: 0;

            font-family: 'Inter', sans-serif;

            background-color: #f8fafc; 

            color: #334155;

            min-height: 100vh;

            display: flex;

            flex-direction: column;

        }

        .top-navbar {

            background: #ffffff;

            padding: 15px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);

        }

        .brand-container { display: flex; align-items: baseline; gap: 12px; }

        .brand-logo { font-size: 24px; font-weight: 700; color: #059669; margin: 0; }

        .brand-team { font-size: 12px; font-weight: 600; color: #94a3b8; letter-spacing: 1.5px; text-transform: uppercase; }

        main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }



        /* =========================================

           2. KOMPONEN KARTU FORMULIR (Clean Minimalist)

           ========================================= */

        .login-container {

            background: #ffffff;

            border-radius: 20px;

            padding: 40px;

            width: 100%;

            max-width: 450px;

            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); 

            text-align: center;

            border: 1px solid #f1f5f9;

        }

        .login-container h2 { margin-top: 0; margin-bottom: 25px; font-size: 24px; color: #1e293b; font-weight: 700; }

        .input-group { margin-bottom: 18px; text-align: left; }

        .input-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #64748b; }

        .input-group input { 

            width: 100%; 

            padding: 14px; 

            background: #f8fafc; 

            border: 1px solid #e2e8f0; 

            border-radius: 10px; 

            color: #334155; 

            font-size: 14px; 

            outline: none; 

            box-sizing: border-box; 

            transition: all 0.3s ease;

        }

        .input-group input:focus { 

            border-color: #059669; 

            background: #ffffff;

            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); 

        }



        .btn-submit { 

            width: 100%; 

            padding: 14px; 

            background: #10b981; 

            color: #ffffff; 

            border: none; 

            border-radius: 10px; 

            font-size: 16px; 

            font-weight: 600; 

            cursor: pointer; 

            transition: 0.3s; 

            margin-top: 10px; 

            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);

        }

        .btn-submit:hover { background: #059669; transform: translateY(-1px); }

        .error-text { color: #ef4444; font-size: 12px; margin-top: 6px; display: block; }

        .login-link { display: block; margin-top: 20px; color: #64748b; font-size: 14px; text-decoration: none; }

        .login-link span { color: #059669; font-weight: 600; }



        .divider { margin: 25px 0 15px 0; border-top: 1px solid #e2e8f0; }

        .btn-outline { 

            display: block; 

            width: 100%; 

            padding: 14px; 

            background: #ffffff; 

            color: #059669; 

            border: 1px solid #059669; 

            border-radius: 10px; 

            font-size: 14px; 

            font-weight: 600; 

            text-decoration: none; 

            transition: 0.3s; 

            box-sizing: border-box; 

        }

        .btn-outline:hover { background: #f0fdf4; }



        /* =========================================

           3. DASHBOARD (Mobile First - Layar HP)

           ========================================= */

        .dashboard-container {

            background: #f8fafc;

            border-radius: 24px;

            width: 100%;

            max-width: 480px;

            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);

            overflow: hidden;

            position: relative;

            border: 8px solid #ffffff;

        }

        .dash-header { padding: 25px 25px 15px; background: #ffffff; border-radius: 0 0 24px 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 20px; }

        .dash-title { font-size: 18px; color: #1e293b; margin: 0 0 5px 0; font-weight: 700; text-transform: capitalize; }

        .dash-subtitle { font-size: 13px; color: #64748b; margin: 0; }



        .stats-row { display: flex; gap: 10px; margin-top: 15px; }

        .stat-pill { flex: 1; background: #f1f5f9; padding: 10px 15px; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #334155; }

        .stat-pill span { font-size: 18px; } 



        .dash-body { 

            padding: 0 20px 100px 20px; 

            display: flex; 

            flex-direction: column; 

            gap: 20px; 

        } 



        .ev-banner { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; padding: 20px; border-radius: 16px; text-align: center; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }

        .ev-banner h3 { margin: 0 0 8px 0; font-size: 15px; font-weight: 700; }

        .ev-banner p { margin: 0 0 15px 0; font-size: 12px; opacity: 0.9; line-height: 1.5; }

        .ev-banner button { background: #ffffff; color: #059669; border: none; padding: 8px 25px; border-radius: 20px; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s; white-space: nowrap; }

        .ev-banner button:hover { transform: scale(1.05); }



        .location-card { background: #ffffff; padding: 15px 20px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }

        .loc-info h4 { margin: 0 0 5px 0; font-size: 14px; color: #1e293b; font-weight: 700;}

        .loc-info p { margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;}

        .btn-cari { background: #0f172a; color: #fff; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; text-align: center; }



        .services-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 15px; }

        .services-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }

        .service-item { background: #ffffff; padding: 15px 5px; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.02); cursor: pointer; transition: 0.2s; border: 1px solid #f1f5f9; }

        .service-item:hover { transform: translateY(-4px); border-color: #10b981; }

        .service-icon { font-size: 26px; margin-bottom: 8px; display: block; }

        .service-name { font-size: 11px; font-weight: 700; color: #64748b; }



        .floating-scan { position: absolute; bottom: 25px; left: 50%; transform: translateX(-50%); background: #10b981; color: #fff; border: none; padding: 15px 35px; border-radius: 30px; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4); cursor: pointer; transition: 0.3s; }

        .floating-scan:hover { background: #059669; box-shadow: 0 15px 30px rgba(5, 150, 105, 0.5); }



        /* =========================================

           4. RESPONSIVE DESIGN (Layar Laptop/Tablet)

           ========================================= */

        @media (min-width: 768px) {

            main { align-items: flex-start; padding: 30px 40px; }

            .dashboard-container { max-width: 100%; border: 1px solid #e2e8f0; }

            .dash-header { display: flex; justify-content: space-between; align-items: center; padding: 30px 40px 25px; }

            .stats-row { margin-top: 0; width: 450px; gap: 15px; }



            .dash-body {

                padding: 10px 40px 100px 40px;

                display: flex;

                flex-direction: column;

                align-items: stretch;

                gap: 25px;

            }



            .ev-banner { text-align: left; display: flex; justify-content: space-between; align-items: center; padding: 30px 40px; }

            .ev-banner p { margin-bottom: 0; font-size: 14px; }

            .ev-banner h3 { font-size: 18px; margin-bottom: 10px; }

            .ev-banner button { font-size: 15px; padding: 12px 30px; }



            .location-card { flex-direction: row; align-items: center; justify-content: space-between; padding: 25px 40px; margin-bottom: 0; }

            .btn-cari { width: auto; padding: 12px 30px; font-size: 14px; }



            .services-title { font-size: 18px; margin-bottom: 20px; }

            .services-grid { display: flex; gap: 20px; }

            .service-item { flex: 1; max-width: 160px; padding: 30px 10px; }

            .service-icon { font-size: 32px; }

            .service-name { font-size: 13px; }



            .floating-scan {

                bottom: 40px;

                right: 40px;

                left: auto;

                transform: none;

                padding: 18px 40px;

                font-size: 16px;

            }

        }



        .nav-profile-btn {

            background: rgba(16, 185, 129, 0.1);

            color: #059669;

            border: 1px solid #10b981;

            padding: 8px 16px;

            border-radius: 20px;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            display: flex;

            align-items: center;

            gap: 8px;

            transition: 0.3s;

        }

        .nav-profile-btn:hover {

            background: #10b981;

            color: #ffffff;

        }



        /* Style khusus untuk tombol Admin */

        .nav-admin-btn {

            background: rgba(37, 99, 235, 0.1);

            color: #2563eb;

            border: 1px solid #3b82f6;

            padding: 8px 16px;

            border-radius: 20px;

            font-size: 14px;

            font-weight: 600;

            text-decoration: none;

            display: flex;

            align-items: center;

            gap: 8px;

            transition: 0.3s;

        }

        .nav-admin-btn:hover {

            background: #2563eb;

            color: #ffffff;

        }


        /* =========================================
           5. NAVBAR ADMIN - BAGAS (FRONTEND)
           ========================================= */
        .top-navbar { gap: 20px; flex-wrap: wrap; position: relative; z-index: 10; }
        .brand-container { flex-wrap: wrap; align-items: center; }
        .nav-menu { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
        .nav-menu a, .nav-menu .nav-logout {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 7px; padding: 10px 14px; border-radius: 12px;
            text-decoration: none; font: 600 13px 'Inter', sans-serif;
            border: 1px solid transparent; cursor: pointer; transition: .2s;
            white-space: nowrap;
        }
        .nav-menu a { color: #475569; background: #f8fafc; }
        .nav-menu a:hover { background: #ecfdf5; color: #047857; }
        .nav-menu a.active { background: #d1fae5; color: #047857; border-color: #6ee7b7; }
        .nav-menu .nav-logout { color: #dc2626; background: #fef2f2; border-color: #fecaca; }
        .nav-menu .nav-logout:hover { background: #fee2e2; }
        @media (max-width: 800px) {
            .top-navbar { padding: 16px 20px; }
            .nav-menu { width: 100%; }
            .nav-menu a, .nav-menu form { flex: 1 1 auto; }
            .nav-menu .nav-logout { width: 100%; }
            .brand-team { font-size: 10px; }
        }
    </style>

</head>

<body>



    {{-- Navbar frontend Bagas: tidak mengubah controller atau dashboard Darryl --}}
    <nav class="top-navbar" aria-label="Navigasi utama">
        <div class="brand-container">
            <h1 class="brand-logo">⚡ EV Charge</h1>
            <span class="brand-team">By SUMBER JAYA REJEKI</span>
        </div>

        @auth
            <div class="nav-menu">
                @if(auth()->user()->peran === 'admin')
                    {{-- Dashboard tetap memakai halaman yang dikerjakan Darryl --}}
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('admin.stations.index') }}" class="{{ request()->routeIs('admin.stations.*') ? 'active' : '' }}">
                        ⚙️ Kelola SPKLU
                    </a>
                    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        🔔 Notifikasi
                    </a>
                @else
                    {{-- Menu user biasa tetap dipertahankan --}}
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">🔔 Pemberitahuan</a>
                    <a href="{{ url('/profile') }}" class="{{ request()->is('profile*') ? 'active' : '' }}">👤 Profil &amp; Kendaraan</a>
                @endif

                {{-- Logout harus POST dan memakai CSRF --}}
                <form action="{{ url('/logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button class="nav-logout" type="submit">🚪 Logout</button>
                </form>
            </div>
        @endauth
    </nav>



    <main>

        @yield('content')

    </main>



    @stack('scripts')

</body>

</html>