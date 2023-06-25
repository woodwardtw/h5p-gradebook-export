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
    eraseAllGradesButton();
} );



function eraseAllGradesButton(){
    if(document.querySelector('#erase-h5p')){
        const scaryErase = document.querySelector('#erase-h5p');
        scaryErase.addEventListener("click", doH5Perase);       
    }
    
}

function doH5Perase(){
        const actionUrl = window.location.href+'&action=clear_h5p_results_table';
        let eraseResponse = confirm("☠️ This will erase ALL grades! ☠️ \n☢️There is no going back!☢️ \n⚠️Highly suggested you download a backup before doing this!⚠️\nPress OK to do it!");
        if(eraseResponse === true){
            window.location.href = actionUrl;
        } else {
            console.log('bail out');
        }


}