/**
 * Created by servandomac on 3/5/15.
 */
function removeSubArea(id)
{
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "id" : id
        };

        $.ajax({
            type:"POST",
            url:"/Areas/deleteSubArea",
            dataType: 'json',
            data: data,
            async: false,
            success: function(data){
                if(data.result)
                {
                    alert("El registro ha sido eliminado exitosamente");
                    location.reload();
                }
            }
        });
    }
}