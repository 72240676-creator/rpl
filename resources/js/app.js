import './bootstrap';
import DataTable from 'datatables.net-dt';

const reportTable = document.querySelector('#transactionsTable');

if (reportTable) {
    new DataTable(reportTable, {
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[7, 'desc']],
        language: {
            search: 'Cari transaksi:',
            lengthMenu: 'Tampilkan _MENU_ transaksi',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi',
            infoEmpty: 'Belum ada transaksi',
            infoFiltered: '(dari _MAX_ transaksi)',
            zeroRecords: 'Transaksi tidak ditemukan',
            emptyTable: 'Belum ada data transaksi.',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya',
            },
        },
    });
}

const chargingSessionsTable = document.querySelector('#chargingSessionsTable');

if (chargingSessionsTable) {
    new DataTable(chargingSessionsTable, {
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'desc']],
        language: {
            search: 'Cari sesi:',
            lengthMenu: 'Tampilkan _MENU_ sesi',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ sesi',
            infoEmpty: 'Belum ada sesi',
            infoFiltered: '(dari _MAX_ sesi)',
            zeroRecords: 'Sesi tidak ditemukan',
            emptyTable: 'Belum ada aktivitas charging.',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya',
            },
        },
    });
}
