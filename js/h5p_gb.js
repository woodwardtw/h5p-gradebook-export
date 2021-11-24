console.log('locked and loaded')

jQuery(document).ready(function() {
    jQuery('#h5p_grades').DataTable( {
        pageLength: 100,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5'
        ]
    } );
} );