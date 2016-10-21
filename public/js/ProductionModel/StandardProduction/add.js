/**
 * Created by servandomac on 10/2/14.
 */
function isRepeatedRecord()
{
    var retVal = false;

    var data = {
        "machine_id" : $('#machine_id').val(),
        "size" : $('#size').val()
    };

    $.ajax({
        type:"POST",
        url:"/StandardProduction/isRepeatedAjax",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data.is_repeated)
            {
                retVal =  true
            }
        }
    });

    return retVal;
}

$(function(){
    $('#saveBtn').click(function() {
        if(isRepeatedRecord())
        {
            alert("Ya esta capturada una producción con esta máquina y tamaño.");
            return false;
        }
    });
});