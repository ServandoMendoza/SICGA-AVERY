/**
* Created by servandomac on 6/16/14.
*/
function getScrapCodes(scrap_id)
{
    var data = {
        "scrap_code_id" : scrap_id
    };

    $.ajax({
        type:"POST",
        url:"/Scrap/getScrapDescription",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data)
            {
                $('#scrap_description').html(data.scrap_code.description);
            }
        }
    });
}

$(function(){
    $('#pageTitle').html("Agregar Scrap");

    getScrapCodes($('#scrap_code_id').val());

    $('#scrap_code_id').change(function(){
        getScrapCodes($(this).val());
    });
});