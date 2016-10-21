function removeScrap(id)
{
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "id" : id
        };

        $.ajax({
            type:"POST",
            url:"/ScrapCode/deleteScrapCodeAjax",
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

function editScrap(id)
{
    window.location = auxObj.edit_url + id;
    return false;
}