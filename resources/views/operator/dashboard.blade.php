@extends('layouts.app')

@section('title', 'Dashboard Operator')

@section('content')

<style>
<<<<<<< Updated upstream
    .operator-dashboard { width: min(1280px, 100%); margin: 0 auto; padding: 38px 28px 64px; color: #172554; }
    .operator-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 20px; }
    .heading-eyebrow { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 9px; color: #047857; font-size: 10px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; }
    .heading-eyebrow::before { width: 7px; height: 7px; border-radius: 50%; background: #10b981; content: ""; }
    .operator-heading h1 { margin: 0 0 7px; color: #0f172a; font-size: clamp(27px, 4vw, 35px); letter-spacing: -.8px; }
    .operator-heading p { margin: 0; color: #64748b; line-height: 1.6; }
    .heading-date { flex: 0 0 auto; padding: 11px 15px; color: #475569; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(15, 23, 42, .03); font-size: 12px; }
    .live-status { display: inline-flex; align-items: center; gap: 8px; margin: 13px 0 19px; padding: 7px 10px; color: #475569; background: #fff; border: 1px solid #e2e8f0; border-radius: 999px; box-shadow: 0 3px 10px rgba(15, 23, 42, .025); font-size: 10px; }
    .live-indicator { width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px #d1fae5; }
    .live-status.error { color: #b91c1c; }
    .live-status.error .live-indicator { background: #ef4444; box-shadow: 0 0 0 3px #fee2e2; }
    .welcome-card { position: relative; display: flex; justify-content: space-between; align-items: center; gap: 24px; overflow: hidden; margin-bottom: 22px; padding: 28px 30px; color: #fff; background: radial-gradient(ellipse at 82% 15%, rgba(52, 211, 153, .38), transparent 34%), linear-gradient(115deg, #065f46, #059669 65%, #10b981); border: 1px solid rgba(5, 150, 105, .25); border-radius: 20px; box-shadow: 0 14px 32px rgba(5, 150, 105, .16); }
    .welcome-card::after { position: absolute; right: 190px; bottom: -88px; width: 220px; height: 220px; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; box-shadow: 0 0 0 24px rgba(255,255,255,.035), 0 0 0 48px rgba(255,255,255,.025); content: ""; pointer-events: none; }
    .welcome-icon { display: grid; flex: 0 0 58px; width: 58px; height: 58px; place-items: center; color: #047857; background: rgba(255,255,255,.95); border: 1px solid rgba(255,255,255,.55); border-radius: 18px; box-shadow: 0 8px 18px rgba(6, 78, 59, .15); font-size: 28px; }
    .welcome-copy { position: relative; z-index: 1; flex: 1; }
    .welcome-kicker { margin-bottom: 7px; color: rgba(209, 250, 229, .9); font-size: 10px; font-weight: 800; letter-spacing: 1.25px; text-transform: uppercase; }
    .welcome-card h2 { margin: 0 0 7px; font-size: 22px; letter-spacing: -.25px; }
    .welcome-card p { margin: 0; color: rgba(255,255,255,.86); font-size: 14px; line-height: 1.6; }
    .quick-link { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 8px; flex: 0 0 auto; padding: 12px 16px; color: #047857; background: #fff; border: 1px solid rgba(255,255,255,.65); border-radius: 10px; box-shadow: 0 5px 12px rgba(6, 78, 59, .12); font-size: 13px; font-weight: 750; text-decoration: none; transition: transform .2s, background .2s, box-shadow .2s; }
    .quick-link:hover { transform: translateY(-2px); background: #ecfdf5; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 30px; }
    .stat-card { display: flex; align-items: center; gap: 15px; min-width: 0; min-height: 102px; padding: 18px 19px; background: linear-gradient(145deg, #fff, #fcfefd); border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 5px 18px rgba(15, 23, 42, .035); }
    .stat-icon { display: grid; flex: 0 0 46px; width: 46px; height: 46px; place-items: center; border-radius: 14px; font-size: 20px; }
    .stat-icon.green { color: #047857; background: #d1fae5; }
    .stat-icon.blue { color: #1d4ed8; background: #dbeafe; }
    .stat-icon.amber { color: #b45309; background: #fef3c7; }
    .stat-icon.red { color: #b91c1c; background: #fee2e2; }
    .stat-label { margin-bottom: 5px; color: #64748b; font-size: 11px; }
    .stat-value { color: #0f172a; font-size: 24px; font-weight: 750; line-height: 1.15; }
    .stat-note { margin-top: 5px; color: #94a3b8; font-size: 10px; }
    .section-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 15px; margin-bottom: 13px; }
    .section-heading h2 { margin: 0; color: #0f172a; font-size: 17px; letter-spacing: -.2px; }
    .section-heading p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
    .destination-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .destination-card { position: relative; display: flex; flex-direction: column; min-height: 205px; padding: 20px; overflow: hidden; color: inherit; background: #fff; border: 1px solid #e2e8f0; border-radius: 17px; box-shadow: 0 5px 18px rgba(15, 23, 42, .035); text-decoration: none; transition: transform .2s, border-color .2s, box-shadow .2s; }
    .destination-card:hover { transform: translateY(-3px); border-color: #6ee7b7; box-shadow: 0 14px 28px rgba(5, 150, 105, .10); }
    .destination-card:focus-visible { outline: 3px solid rgba(16, 185, 129, .3); outline-offset: 3px; }
    .destination-card::after { position: absolute; top: -34px; right: -34px; width: 100px; height: 100px; background: #f0fdf4; border-radius: 50%; content: ""; transition: transform .2s; }
    .destination-card:hover::after { transform: scale(1.2); }
    .destination-top, .destination-content, .destination-footer { position: relative; z-index: 1; }
    .destination-top { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; }
    .destination-icon { display: grid; width: 42px; height: 42px; place-items: center; color: #047857; background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 13px; font-size: 19px; }
    .destination-tag { padding: 5px 8px; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 999px; font-size: 9px; font-weight: 750; white-space: nowrap; }
    .destination-tag.warning { color: #b45309; background: #fffbeb; border-color: #fde68a; }
    .destination-tag.safe { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .destination-card h3 { margin: 0 0 6px; color: #0f172a; font-size: 15px; }
    .destination-card p { margin: 0; color: #64748b; font-size: 11px; line-height: 1.6; }
    .destination-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: auto; padding-top: 18px; }
    .destination-metric { color: #047857; font-size: 11px; font-weight: 700; }
    .destination-action { display: inline-flex; align-items: center; gap: 6px; color: #047857; font-size: 11px; font-weight: 750; }
    .alert { margin-bottom: 16px; padding: 13px 16px; border: 1px solid transparent; border-radius: 12px; font-size: 13px; }
    .alert.success { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
    .alert.error { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .destination-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) { .operator-dashboard { padding: 25px 14px 40px; } .operator-heading { align-items: flex-start; flex-direction: column; gap: 13px; } .welcome-card { align-items: flex-start; flex-wrap: wrap; gap: 14px; padding: 21px; } .welcome-icon { flex-basis: 46px; width: 46px; height: 46px; border-radius: 14px; } .welcome-copy { flex-basis: calc(100% - 62px); } .welcome-card h2 { font-size: 18px; } .welcome-card p { font-size: 12px; } .quick-link { margin-left: 60px; padding: 10px 13px; } .stats-grid { gap: 10px; margin-bottom: 24px; } .stat-card { align-items: flex-start; flex-direction: column; gap: 10px; min-height: 0; padding: 14px; } .stat-value { font-size: 21px; } .stat-icon { flex-basis: 40px; width: 40px; height: 40px; } .destination-grid { grid-template-columns: 1fr; gap: 11px; } .destination-card { min-height: 175px; padding: 17px; } .section-heading { align-items: flex-start; flex-direction: column; } }
=======
    .operator-dashboard {
        width: min(1200px, 100%);
        margin: 0 auto;
        padding: 34px 24px 56px;
        color: #172554;
    }

    .operator-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 24px;
    }

    .operator-heading h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(25px, 4vw, 32px);
        font-weight: 800;
        letter-spacing: -0.7px;
    }

    .operator-heading p {
        margin: 0;
        color: #64748b;
        line-height: 1.6;
    }

    .heading-date {
        flex: 0 0 auto;
        padding: 10px 14px;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
    }

    .welcome-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
        padding: 26px 28px;
        color: #fff;
        background: linear-gradient(115deg, #047857, #10b981);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(5, 150, 105, .13);
        transition: box-shadow .2s ease;
    }

    .welcome-card:hover {
        box-shadow: 0 14px 30px rgba(5, 150, 105, .18);
    }

    .welcome-card h2 {
        margin: 0 0 7px;
        font-size: 21px;
    }

    .welcome-card p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 14px;
        line-height: 1.6;
    }

    .welcome-icon {
        display: grid;
        flex: 0 0 54px;
        width: 54px;
        height: 54px;
        place-items: center;
        color: #047857;
        background: rgba(255,255,255,.92);
        border-radius: 17px;
        font-size: 27px;
    }

    .welcome-copy {
        flex: 1;
    }

    .quick-link {
        flex: 0 0 auto;
        padding: 12px 16px;
        color: #047857;
        background: #fff;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform .2s, background .2s;
    }

    .quick-link:hover {
        transform: translateY(-2px);
        background: #ecfdf5;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        padding: 19px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 17px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, .035);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 9px 22px rgba(15, 23, 42, .07);
    }

    .stat-card-link {
        color: inherit;
        text-decoration: none;
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }

    .stat-card-link:hover {
        border-color: #6ee7b7;
        box-shadow: 0 10px 24px rgba(5, 150, 105, .11);
        transform: translateY(-2px);
    }

    .stat-card-link:active {
        transform: translateY(0);
    }

    .stat-card-link:focus-visible {
        outline: 3px solid rgba(16, 185, 129, .3);
        outline-offset: 3px;
    }

    .stat-card-link .stat-note {
        color: #047857;
        font-weight: 650;
    }

    .stat-icon {
        display: grid;
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        place-items: center;
        border-radius: 14px;
        font-size: 21px;
    }

    .stat-icon.green {
        color: #047857;
        background: #d1fae5;
    }

    .stat-icon.blue {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .stat-icon.amber {
        color: #b45309;
        background: #fef3c7;
    }

    .stat-icon.red {
        color: #b91c1c;
        background: #fee2e2;
    }

    .stat-label {
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
    }

    .stat-value {
        color: #0f172a;
        font-size: 24px;
        font-weight: 750;
        line-height: 1.15;
        letter-spacing: -0.6px;
    }

    .stat-note {
        margin-top: 5px;
        color: #94a3b8;
        font-size: 11px;
    }

    .live-status {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 8px;
        color: #64748b;
        font-size: 11px;
    }

    .live-indicator {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 3px #d1fae5;
    }

    .live-status.error {
        color: #b91c1c;
    }

    .live-status.error .live-indicator {
        background: #ef4444;
        box-shadow: 0 0 0 3px #fee2e2;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(300px, .85fr);
        gap: 18px;
        align-items: start;
    }

    .panel {
        min-width: 0;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .035);
    }

    .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 20px 21px;
        border-bottom: 1px solid #eef2f7;
    }

    .panel-heading h2 {
        margin: 0;
        color: #0f172a;
        font-size: 16px;
        letter-spacing: -.2px;
    }

    .panel-heading p {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 12px;
    }

    .panel-link {
        flex: 0 0 auto;
        color: #047857;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .panel-link:hover {
        text-decoration: underline;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .operator-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .operator-table th {
        padding: 14px 16px;
        color: #64748b;
        background: #f8fafc;
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }

    .operator-table td {
        padding: 17px 16px;
        color: #334155;
        border-top: 1px solid #f1f5f9;
        font-size: 12px;
        vertical-align: middle;
    }

    .operator-table tbody tr {
        transition: background .2s ease;
    }

    .operator-table tbody tr:hover {
        background: #fbfefd;
    }

    .charger-name {
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }

    .charger-location {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 11px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        color: #047857;
        background: #d1fae5;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge.busy {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .status-badge.offline {
        color: #b91c1c;
        background: #fee2e2;
    }

    .status-badge.warning {
        color: #b45309;
        background: #fef3c7;
    }

    /* Tarif charger */
    .tariff-display {
        display: grid;
        gap: 9px;
        min-width: 155px;
    }

    .tariff-current {
        color: #047857;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    .charger-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .charger-action {
        display: inline-block;
        padding: 9px 12px;
        border-radius: 9px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        transition: background .2s ease, transform .2s ease;
    }

    .charger-action:hover {
        transform: translateY(-1px);
    }

    .charger-action.edit {
        color: #047857;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }

    .charger-action.edit:hover {
        background: #d1fae5;
    }

    .charger-action.report {
        color: #92400e;
        background: #fffbeb;
        border: 1px solid #fde68a;
    }

    .charger-action.report:hover {
        background: #fef3c7;
    }

    /* Aktivitas terbaru */
    .recent-list {
        padding: 7px 20px 10px;
    }

    .recent-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .recent-item:last-child {
        border-bottom: 0;
    }

    .recent-icon {
        display: grid;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        place-items: center;
        color: #047857;
        background: #ecfdf5;
        border-radius: 12px;
    }

    .recent-content {
        flex: 1;
        min-width: 0;
    }

    .recent-title {
        overflow: hidden;
        color: #1e293b;
        font-size: 12px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .recent-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }

    .empty-state {
        padding: 28px 20px;
        color: #94a3b8;
        text-align: center;
        font-size: 13px;
    }

    .alert {
        margin-bottom: 18px;
        padding: 12px 15px;
        border-radius: 10px;
        font-size: 13px;
    }

    .alert.success {
        color: #047857;
        background: #d1fae5;
    }

    .alert.error {
        color: #b91c1c;
        background: #fee2e2;
    }

    .attention-note {
        margin: 16px 20px 20px;
        padding: 13px 14px;
        color: #92400e;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.5;
    }

    /* Pemeriksaan charger */
    .inspection-flag {
        display: inline-flex;
        margin-top: 7px;
        padding: 4px 8px;
        color: #b45309;
        background: #fef3c7;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .inspection-note {
        max-width: 220px;
        margin-top: 5px;
        color: #92400e;
        font-size: 10px;
        line-height: 1.45;
    }

    .inspection-reported {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 10px;
    }

    .inspection-controls {
        min-width: 170px;
        margin-top: 9px;
    }

    .inspection-controls summary {
        color: #047857;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .inspection-form {
        display: grid;
        gap: 7px;
        margin-top: 8px;
    }

    .inspection-form textarea {
        width: 100%;
        min-height: 54px;
        padding: 7px 8px;
        box-sizing: border-box;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font: inherit;
        font-size: 11px;
        resize: vertical;
    }

    .inspection-form button {
        justify-self: start;
        padding: 7px 9px;
        color: #fff;
        background: #b45309;
        border: 0;
        border-radius: 7px;
        font: inherit;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .inspection-form button:hover {
        background: #92400e;
    }

    .inspection-clear-form {
        margin-top: 6px;
    }

    .inspection-clear-form button {
        padding: 5px 8px;
        color: #047857;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 7px;
        font: inherit;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .inspection-clear-form button:hover {
        background: #d1fae5;
    }

    /* Tampilan tablet */
    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Tampilan HP */
    @media (max-width: 600px) {
        .operator-dashboard {
            padding: 24px 14px 40px;
        }

        .operator-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .heading-date {
            align-self: flex-start;
        }

        .welcome-card {
            align-items: flex-start;
            flex-wrap: wrap;
            padding: 21px;
            border-radius: 16px;
        }

        .welcome-copy {
            flex-basis: calc(100% - 74px);
        }

        .quick-link {
            margin-left: 68px;
        }

        .stats-grid {
            gap: 10px;
        }

        .stat-card {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
            padding: 14px;
            border-radius: 14px;
        }

        .stat-value {
            font-size: 21px;
        }

        .panel {
            border-radius: 14px;
        }

        .panel-heading {
            padding: 16px;
        }

        .tariff-display {
            min-width: 140px;
        }
    }
>>>>>>> Stashed changes
</style>

<div class="operator-dashboard">
    <header class="operator-heading">
        <div>
            <div class="heading-eyebrow">Pusat kendali operasional</div>
            <h1>Dashboard Operator</h1>
            <p>Pantau stasiun, kesiapan charger, dan aktivitas pengisian EV dalam satu tempat.</p>
        </div>
        <time
            class="heading-date"
            id="operator-clock"
            data-timezone="Asia/Jakarta"
            aria-label="Tanggal dan waktu"
        >{{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</time>
    </header>

    <div class="live-status" id="operator-live-status" role="status" aria-live="polite">
        <span class="live-indicator" aria-hidden="true"></span>
        <span>Data diperbarui otomatis</span>
        <span id="operator-live-updated"></span>
    </div>

    @if (session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="welcome-card">
        <div class="welcome-icon" aria-hidden="true">⚡</div>
        <div class="welcome-copy">
            <div class="welcome-kicker">EV Charge · Operator</div>
            <h2>Selamat bertugas, {{ auth()->user()->nama }}.</h2>
            <p>Kelola ketersediaan stasiun, pantau pengisian aktif, dan tindak lanjuti laporan pemeriksaan.</p>
        </div>
        <a class="quick-link" href="{{ route('operator.stations.index') }}">Kelola SPKLU <span aria-hidden="true">→</span></a>
    </section>

    <section class="stats-grid" aria-label="Ringkasan operasional">
        <div class="stat-card">
            <div class="stat-icon green" aria-hidden="true">🏢</div>
            <div>
                <div class="stat-label">Stasiun aktif</div>
                <div class="stat-value">{{ $stats['active_locations'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['locations'] }}</span></div>
                <div class="stat-note">Lokasi berstatus operasional</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue" aria-hidden="true">🔌</div>
            <div>
                <div class="stat-label">Charger tersedia</div>
                <div class="stat-value">{{ $stats['available_chargers'] }}<span style="color:#94a3b8;font-size:15px;font-weight:500;"> / {{ $stats['chargers'] }}</span></div>
                <div class="stat-note">Siap digunakan pengemudi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber" aria-hidden="true">⚡</div>
<<<<<<< Updated upstream
            <div>
                <div class="stat-label">Sesi berlangsung</div>
                <div class="stat-value" data-live-stat="ongoing_sessions">{{ $stats['ongoing_sessions'] }}</div>
                <div class="stat-note"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi dimulai hari ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $stats['attention_chargers'] ? 'red' : 'green' }}" aria-hidden="true" data-inspection-icon>{{ $stats['attention_chargers'] ? '!' : '✓' }}</div>
            <div>
                <div class="stat-label">Perlu pemeriksaan</div>
                <div class="stat-value" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }}</div>
                <div class="stat-note">{{ $stats['attention_chargers'] ? 'Laporan menunggu tindak lanjut' : 'Tidak ada laporan tertunda' }}</div>
            </div>
        </div>
    </section>

    <section aria-labelledby="operator-menu-title">
        <div class="section-heading">
            <div>
                <h2 id="operator-menu-title">Menu operasional</h2>
                <p>Pilih area kerja untuk membuka data dan tindakan terkait.</p>
=======
            <div><div class="stat-label">Sesi berlangsung</div><div class="stat-value" data-live-stat="ongoing_sessions">{{ $stats['ongoing_sessions'] }}</div><div class="stat-note"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi hari ini · Lihat aktivitas →</div></div>
        </a>
        <a class="stat-card stat-card-link" href="{{ route('operator.reports.inspections') }}" aria-label="Lihat laporan dan kontrol pemeriksaan charger">
            <div class="stat-icon {{ $stats['attention_chargers'] ? 'red' : 'green' }}" aria-hidden="true" data-inspection-icon>{{ $stats['attention_chargers'] ? '⚠️' : '✓' }}</div>
            <div><div class="stat-label">Perlu pemeriksaan</div><div class="stat-value" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }}</div><div class="stat-note">Buka laporan pemeriksaan →</div></div>
        </a>
    </section>

    <section class="dashboard-grid">
        <article class="panel" id="charger-status" tabindex="-1">
            <header class="panel-heading">
    <div>
        <h2>Status charger & tarif</h2>
        <p>Pantau kesiapan unit dan tarif setiap charger.</p>
    </div>

    <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a class="panel-link" href="{{ route('operator.reports.inspections') }}">
            Laporan pemeriksaan →
        </a>

        <a class="panel-link" href="{{ route('operator.stations.index') }}">
            Semua stasiun →
        </a>
    </div>
</header>

            <div class="table-wrap">
                <table class="operator-table">
                    <thead>
                        <tr><th>Charger / lokasi</th><th>Status</th><th>Tarif & pemeriksaan</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($chargers as $charger)
                            @php
                                $chargerStatus = !$charger->is_online || $charger->status === 'rusak'
                                    ? 'offline'
                                    : ($charger->status === 'digunakan' ? 'busy' : 'available');
                                $chargerLabel = !$charger->is_online
                                    ? 'Offline'
                                    : match ($charger->status) {
                                        'rusak' => 'Rusak',
                                        'digunakan' => 'Digunakan',
                                        default => 'Tersedia',
                                    };
                            @endphp
                            <tr>
                                <td>
                                    <div class="charger-name">{{ $charger->device_number }}</div>
                                    <div class="charger-location">{{ $charger->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }} · {{ $charger->connector_type }}</div>
                                </td>
                                <td>
                                    <span class="status-badge {{ $chargerStatus }}">{{ $chargerLabel }}</span>
                                    <div data-inspection-display="{{ $charger->id_charger }}" @if(!$charger->needs_inspection) hidden @endif>
                                        <div><span class="inspection-flag">Perlu pemeriksaan</span></div>
                                        <div class="inspection-note" data-inspection-note>{{ $charger->inspection_note }}</div>
                                        <div class="inspection-reported" data-inspection-reported @if(!$charger->inspection_reported_at) hidden @endif>
                                            @if($charger->inspection_reported_at)
                                                Dilaporkan {{ $charger->inspection_reported_at->timezone('Asia/Jakarta')->diffForHumans() }}{{ $charger->inspectionReporter ? ' oleh ' . $charger->inspectionReporter->nama : '' }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tariff-display">
                                    <div>
                                        <div style="font-size:11px;color:#64748b">
                                            Tarif saat ini
                                        </div>

                                        <div class="tariff-current">
                                            Rp{{ number_format($charger->price_per_kwh, 0, ',', '.') }}/kWh
                                        </div>
                                    </div>

                                    <a class="charger-action edit"
                                    href="{{ route('operator.chargers.tariff.edit', $charger->id_charger) }}">
                                        ✎ Ubah Tarif
                                    </a>
                                                <a class="charger-action report"
                                                href="{{ route('operator.reports.inspections') }}">
                                                    ⚠ Laporan
                                                </a>
                                            </div>
                                        </div>
                                    <details class="inspection-controls" data-inspection-controls="{{ $charger->id_charger }}">
                                        <summary data-inspection-summary>{{ $charger->needs_inspection ? 'Perbarui catatan pemeriksaan' : 'Laporkan perlu pemeriksaan' }}</summary>
                                        <form class="inspection-form" action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="needs_inspection" value="1">
                                            <textarea data-inspection-textarea name="inspection_note" maxlength="1000" placeholder="Jelaskan masalah yang perlu diperiksa" required>{{ $charger->inspection_note }}</textarea>
                                            <button type="submit">{{ $charger->needs_inspection ? 'Simpan catatan' : 'Tandai untuk diperiksa' }}</button>
                                        </form>
                                        <form class="inspection-clear-form" data-inspection-clear-form @if(!$charger->needs_inspection) hidden @endif action="{{ route('operator.chargers.inspection.update', $charger->id_charger) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="needs_inspection" value="0">
                                            <button type="submit">Tandai pemeriksaan selesai</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">Belum ada unit charger. Tambahkan charger dari menu Kelola SPKLU.</td></tr>
                        @endforelse
                    </tbody>
                </table>
>>>>>>> Stashed changes
            </div>
        </div>

        <div class="destination-grid">
            <a class="destination-card" href="{{ route('operator.stations.index') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">🔌</span>
                    <span class="destination-tag">Stasiun & charger</span>
                </div>
                <div class="destination-content">
                    <h3>SPKLU & tarif charger</h3>
                    <p>Kelola lokasi, pantau status unit, dan perbarui tarif pengisian.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric">{{ $stats['chargers'] }} unit charger · {{ $stats['locations'] }} lokasi</span>
                    <span class="destination-action">Buka menu <span aria-hidden="true">→</span></span>
                </div>
            </a>

            <a class="destination-card" href="{{ route('operator.activity') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">⚡</span>
                    <span class="destination-tag">Aktivitas terbaru</span>
                </div>
                <div class="destination-content">
                    <h3>Aktivitas charging</h3>
                    <p>Tinjau sesi terbaru, status pembayaran, energi, dan waktu pengisian.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric"><span data-live-stat="sessions_today">{{ $stats['sessions_today'] }}</span> sesi hari ini · {{ number_format($stats['energy_today'], 1, ',', '.') }} kWh</span>
                    <span class="destination-action">Lihat aktivitas <span aria-hidden="true">→</span></span>
                </div>
            </a>

            <a class="destination-card" href="{{ route('operator.reports.inspections') }}">
                <div class="destination-top">
                    <span class="destination-icon" aria-hidden="true">🛠</span>
                    <span class="destination-tag {{ $stats['attention_chargers'] ? 'warning' : 'safe' }}">
                        {{ $stats['attention_chargers'] ? 'Perlu ditinjau' : 'Semua aman' }}
                    </span>
                </div>
                <div class="destination-content">
                    <h3>Laporan pemeriksaan</h3>
                    <p>Tinjau catatan kendala charger dan tandai pemeriksaan yang sudah selesai.</p>
                </div>
                <div class="destination-footer">
                    <span class="destination-metric" data-live-stat="attention_chargers">{{ $stats['attention_chargers'] }} laporan terbuka</span>
                    <span class="destination-action">Tinjau laporan <span aria-hidden="true">→</span></span>
                </div>
            </a>
        </div>
    </section>
</div>

<script>
    (() => {
        const liveStatus = document.getElementById('operator-live-status');
        const liveUrl = @json(route('operator.dashboard.live'));

        const refreshLiveStats = async () => {
            try {
                const response = await fetch(liveUrl, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error('Status server ' + response.status);

                const payload = await response.json();
                document.querySelectorAll('[data-live-stat]').forEach((element) => {
                    const value = payload.stats[element.dataset.liveStat];
                    if (value !== undefined) element.textContent = value;
                });

                const inspectionIcon = document.querySelector('[data-inspection-icon]');
                const needsAttention = Number(payload.stats.attention_chargers) > 0;
                if (inspectionIcon) {
                    inspectionIcon.textContent = needsAttention ? '!' : '✓';
                    inspectionIcon.classList.toggle('red', needsAttention);
                    inspectionIcon.classList.toggle('green', !needsAttention);
                }

                liveStatus.classList.remove('error');
                liveStatus.querySelector('span:last-child').textContent = 'Diperbarui ' +
                    new Intl.DateTimeFormat('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        timeZone: 'Asia/Jakarta',
                        hourCycle: 'h23',
                    }).format(new Date(payload.updated_at));
            } catch (error) {
                liveStatus.classList.add('error');
                liveStatus.querySelector('span:last-child').textContent = 'Koneksi terputus; mencoba kembali';
            }
        };

        refreshLiveStats();
        window.setInterval(refreshLiveStats, 15000);

        const clock = document.getElementById('operator-clock');
        if (!clock) return;

        const formatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: clock.dataset.timezone,
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23',
        });

        const renderClock = () => {
            clock.textContent = formatter.format(new Date());
        };

        renderClock();
        window.setInterval(renderClock, 1000);
    })();
</script>
@endsection
