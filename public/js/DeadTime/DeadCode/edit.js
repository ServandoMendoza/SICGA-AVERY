/**
 * Created by servandomac on 10/8/14.
 */
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

            if( data.exists && code != $('#loaded_code').val() )
            {
                retVal = true;
            }
        }
    });

    return retVal;
}

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

function loadValues()
{
    var loaded_machine_id = $('#loaded_machine_id').val();
    var loaded_group_id = $('#loaded_group').val();

    $('#machine_id').val(loaded_machine_id);
    setDeadCodeGroupDdw();

    $('#death_code_group_id').val(loaded_group_id);

}

function disableValues()
{
    $('#machine_id option').not(':selected').attr("disabled", "disabled");
    $('#machine_id').attr("readonly","readonly");

    $('#death_code_group_id option').not(':selected').attr("disabled", "disabled");
    $('#death_code_group_id').attr("readonly","readonly");
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


    loadValues();
    disableValues();
});