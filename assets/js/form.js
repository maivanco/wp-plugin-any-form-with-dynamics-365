jQuery(document).ready(function($){
    $('select[name=selectedEntity]').on('change', function(){
        let currentVal = $(this).val();
        if( currentVal == '' ) {
            return false;
        }
        $('.entity_list').hide();
        $('#' + currentVal).show();

    });
})