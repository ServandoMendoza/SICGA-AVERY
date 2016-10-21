/**
 * Created by servandomac on 7/27/14.
 */
$(function(){
    $('#cprod-cbx').change(function(){
        $('#cprod-name').val($( "#cprod-cbx option:selected" ).text());
    });

    $('#cprod-name').val($( "#cprod-cbx option:selected" ).text());
});