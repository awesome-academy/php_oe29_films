$(document).ready(function () {
    function css() {
        $('#requests-table_length').addClass('main__table-text');
        $('#requests-table_paginate').addClass('paginator');
        $('#requests-table_length label select').select2();
    }

    function dataTable() {
        var myTable = $('#requests-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            destroy: true,
            ajax: {
                "url" : '/request/data',
            },
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false },
                { data: 'email', name: 'email', orderable: false, searchable: false },
                { data: 'content', name: 'content', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        css();
    }

    dataTable();
})
