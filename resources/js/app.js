import './bootstrap';

import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table.datatable').forEach((table) => {
        if (table.dataset.datatableReady === 'true') {
            return;
        }

        new DataTable(table, {
            pageLength: 10,
            order: [],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya',
                },
            },
        });

        table.dataset.datatableReady = 'true';
    });
});
