function removeDcGroup(id)
{
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "id" : id
        };

        $.ajax({
            type:"POST",
            url:"/DeadCode/deleteDeadCodeGroupAjax",
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

function editar(id)
{
    window.location = auxObj.edit_dcg_url + id;
    return false;
}
/**
 * Created by servandomac on 2/26/15.
 */
