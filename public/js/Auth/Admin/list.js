/**
 * Created by servandomac on 10/10/14.
 */
function removeUser(id)
{
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "id" : id
        };

        $.ajax({
            type:"POST",
            url:"/auth/registration/deleteAjax",
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

function editUser(id)
{
    window.location = auxObj.edit_url + id;
    return false;
}
