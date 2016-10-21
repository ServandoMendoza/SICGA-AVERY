/**
 * Created by servandomac on 10/8/14.
 */

function setDeadCodeGroupDdw()
{
    var machine_id = $('#machine_id').val();

    var data = {
        "machine_id" : machine_id
    };

    $.ajax({
        type:"POST",
        url:"/DeadCode/getDeadCodeGroupByMachine",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){

            $('#death_code_group_id').html('');

            if(data.length > 0)
            {
                $('#group-container').show();
                $('#txt-msg').html('');

                $.each(data, function( key, value ) {
                    $('#death_code_group_id').append('<option value='+value.id+'>'+value.name +'</option>');
                });

            }
            else{
                $('#group-container').hide();
                if(machine_id > 0)
                {
                    $('#txt-msg').html('Esta máquina no tiene grupos, por lo tanto no se asociará a ningún grupo');
                }
            }
        }
    });
}

function deadCodeExists()
{
    var code = $('#code').val();

    var data = {
        "code" : code
    };

    var retVal = false;

    $.ajax({
        type:"POST",
        url:"/DeadCode/deadCodeExists",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){

            if(data.exists)
            {
                retVal = true;
            }
        }
    });

    return retVal;
}

$(function(){

    $('#group-container').hide();

    $('#machine_id').change(function(){
        setDeadCodeGroupDdw();

    });

    setDeadCodeGroupDdw();

    $('#saveBtn').click(function(){
       if(deadCodeExists())
       {
           alert("Este código, ya ha sido registrado");
           return false;
       }
    });

});