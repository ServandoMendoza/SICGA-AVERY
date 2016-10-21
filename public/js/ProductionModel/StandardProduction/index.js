function removeStdProd(id)
{
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "id" : id
        };

        $.ajax({
            type:"POST",
            url:"/StandardProduction/deleteStandardProductionAjax",
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
    window.location = auxObj.edit_sp_url + id;
    return false;
}