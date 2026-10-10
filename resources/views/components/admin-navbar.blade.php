
<style>
    .bagas-navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        padding: 18px 35px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        font-family: Arial, sans-serif;
    }

    .bagas-brand {
        color: #059669;
        font-size: 24px;
        font-weight: bold;
        text-decoration: none;
    }

    .bagas-menu {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .bagas-menu a,
    .bagas-menu button {
        padding: 11px 16px;
        border-radius: 9px;
        text-decoration: none;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .bagas-menu a:hover,
    .bagas-menu button:hover {
        background: #d1fae5;
        color: #047857;
    }

    .bagas-menu .active {
        background: #d1fae5;
        color: #047857;
    }

    .bagas-menu .logout {
        color: #dc2626;
        background: #fee2e2;
    }

    @media (max-width: 768px) {
        .bagas-navbar {
            padding: 16px;
        }

        .bagas-menu {
            width: 100%;
        }
    }
</style>

<nav class="bagas-navbar">
    <a href="{{ route('dashboard') }}" class="bagas-brand">
        ⚡ EV Charge
    </a>

    <div class="bagas-menu">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.stations.index') }}"
           class="{{ request()->routeIs('admin.stations.*') ? 'active' : '' }}">
            Kelola SPKLU
        </a>

        <a href="{{ route('notifications.index') }}"
           class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            Notifikasi
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="logout">
                Logout
            </button>
        </form>
    </div>
</nav>
