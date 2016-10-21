function isAnInactiveDeadTime()
{
    var data = {
        "prod_model_id" : passData.prod_model_id
    };

    var isAnInactive = false;

    $.ajax({
        type:"POST",
        url:"/DeadTime/isAnInactiveDeadTime",
        data: data,
        dataType: 'json',
        async: false,
        success: function(data){
            if(parseInt(data.count) > 0)
            {
                isAnInactive = true;
            }
        }
    });

    return isAnInactive;
}

function canAddDeadTime(){
    var retData = false;

    var data = {
        "prod_model_id" : passData.prod_model_id
    };

    $.ajax({
        type:"POST",
        url:"/DeadTime/canAddDeadTime",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(parseInt(data.count) == 1)
            {
                retData = true;
            }
        }
    });

    return retData;
}

$(function(){
    $('#pageTitle').html("Tiempo Muerto");

    if(!canAddDeadTime())
    {
        $('#addNewDeadTimeBtn').hide();
        alert("No se puede agregar tiempo muerto: El tiempo actual no corresponde al modelo de producción");
        //window.location.replace(passData.prod_model_url);
    }

    $('#addNewDeadTimeBtn').click(function(event){
        if(isAnInactiveDeadTime())
        {
            event.preventDefault();
            alert("No es posible agregar un tiempo muerto ya que existe un tiempo muerto inactivo");
        }
    });
});