/**
 * Created by servandomac on 11/20/14.
 */
$(function() {

    $('#shiftSel').change(function(){

        if($(this).val() != "")
            window.location = redirect_url + $(this).val();
    });

    $('#shiftSel').val(shift_id);

});
