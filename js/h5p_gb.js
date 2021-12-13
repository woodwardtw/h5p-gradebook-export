console.log('locked and loaded')

jQuery(document).ready(function() {
    jQuery('#h5p_grades').DataTable( {
        pageLength: 100,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
              extend: 'excelHtml5',
              header: false,
              title: null
            },{
              extend: 'csvHtml5',
              header: false,
              title: null
            }          
        ]
    } );
} );