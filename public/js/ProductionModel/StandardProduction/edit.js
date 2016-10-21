/**
 * Created by servandomac on 10/2/14.
 */

$(function(){
    $('#machine_id option').not(':selected').attr("disabled", "disabled");
    $('#machine_id').attr("readonly","readonly");

    $('#size option').not(':selected').attr("disabled", "disabled");
    $('#size').attr("readonly","readonly");
});