$( document ).ready(function() {
    $('#select_all_top, #select_all_bottom').change( function() {
        if ($(this).is( ':checked' )) {
            $('.university_select').prop('checked', true);
        } else {
            $('.university_select').prop('checked', false);
        }
    });
    
    let $university_select = $('.university_select');
    $university_select.change( function() {
        let checked = false;
        $university_select.each( function() {
            if ($(this).is( ':checked' )) {
                checked = true;
                return;
            }
        });
        if (checked) {
            $('.btn-import-selected').removeAttr('disabled');
        } else {
            $('.btn-import-selected').attr('disabled', true);
        }
    });

});