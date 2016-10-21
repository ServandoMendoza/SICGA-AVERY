/**
 * Created by servandomac on 10/6/14.
 */
function getDeadCodeGroupByName()
{
    var name = $('#name').val();
    var retVal = false;

    var data = {
        "name" : name
    };

    $.ajax({
        type:"POST",
        url:"/DeadCode/getDeadCodeGroupByName",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(parseInt(data.count) == 1)
            {
                retVal = true;
            }
        }
    });

    return retVal
}

$(function(){
    $('#saveBtn').click(function(){
        if(getDeadCodeGroupByName())
        {
            alert('El nombre de este grupo ya existe');
            return false;
        }

    })
});

