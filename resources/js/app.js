import './bootstrap';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

function initDataTables() {
    document.querySelectorAll('.js-data-table').forEach((table) => {
        if (table.dataset.dtInitialized === '1') {
            return;
        }

        table.dataset.dtInitialized = '1';

        new DataTable(table, {
            responsive: true,
            pageLength: Number(table.dataset.pageLength || 10),
            order: table.dataset.order ? JSON.parse(table.dataset.order) : [],
            autoWidth: false,
            stateSave: table.dataset.stateSave === 'true',
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging',
            },
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Ketik kata kunci...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya',
                },
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', initDataTables);
document.addEventListener('livewire:navigated', initDataTables);
document.addEventListener('livewire:updated', () => setTimeout(initDataTables, 0));

document.addEventListener('livewire:init', () => {
    if (window.Livewire?.hook) {
        window.Livewire.hook('morph.updated', () => setTimeout(initDataTables, 0));
    }
});
