$(document).ready( function () {
    $('#table_id').DataTable( {
        "order": [[0, "desc" ]]
    } );
} );

$('#table_id').dataTable( {
    "columns": [
        { "searchable": false },
        null,
        { "searchable": false },
        { "searchable": false },
        { "searchable": false },
        { "searchable": false },
        { "searchable": false },
        { "searchable": false },
    ] } );

