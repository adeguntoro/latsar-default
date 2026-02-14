import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

// manual initialization function
export function initDataTable(selector, options = {}) {
    const table = document.querySelector(selector);
    if (table) {
        $(table).DataTable(options);
    }
}

/*
import $ from 'jquery';
import 'datatables.net-bs5'; // Bootstrap 5 styling for DataTables

export function initDataTables() {
    // only run if table exists
    const table = document.querySelector('.datatable');
    if (table) {
        $(table).DataTable();
    }
}

*/

/*
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

export function initDataTable(selector, options = {}) {
    const table = document.querySelector(selector);
    if (table) {
        $(table).DataTable(options);
    }
}
*/

/*
export function initDataTables() {
    // only initialize tables if they exist
    const table = document.querySelector('.datatable');
    if (table) {
        $(table).DataTable();
    }
}
*/

//export function initDataTables() {

    /*
    const usersTable = document.querySelector('#myTable');
    if (usersTable) {
        $(usersTable).DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            responsive: true,
        });
    }

    
    const ordersTable = document.querySelector('#orders-table');
    if (ordersTable) {
        $(ordersTable).DataTable({
            pageLength: 50,
            order: [[0, 'desc']],
            responsive: true,
            searching: false,
        });
    }
    */
//}