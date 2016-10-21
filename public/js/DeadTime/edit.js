/**
 * Created by Servando on 6/11/14.
 */
/**
 * Created by servandomac on 6/10/14.
 */
var DeadCode;

function setDeadCodeByMachineId()
{
    var dead_code_group_id = $('#dead_code_group').val();

    var data = {
        "dead_code_group_id" : dead_code_group_id
    };

    $.ajax({
        type:"POST",
        url:"/DeadTime/getDeadCodeByMachineId",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){

            if(data)
            {
                DeadCode = data;
                $('#death_code_id').html('');

                $.each(data, function( key, value ) {
                    $('#death_code_id').append('<option value='+value.id+'>'+value.code+' - '+ value.description +'</option>');
                });

                setDeadCodeDescriptionById();
            }
        }
    });
}

function setDeadCodeDescriptionById()
{
    var death_code_id = $('#dead_code_selected').val();

    if(death_code_id != null && death_code_id != ""){
        $.each(DeadCode, function( key, value ) {
            if(value.id == death_code_id)
            {
                $('#code_description').html(value.description);
                return false;
            }
        });
    }
    else{
        $('#code_description').html('');
    }
}

function clearSubCodeSection()
{
    $('#sub-group-container').hide();
    $('#death_subcode_group_id').html("");
    $('#death_subcode_id').html("");
    $('.alt-sec-prob').show();
}

function disableEditValues()
{
    $('#dead_code_group option').not(':selected').attr("disabled", "disabled");
    $('#dead_code_group').attr("readonly","readonly");

    $('#death_code_id option').not(':selected').attr("disabled", "disabled");
    $('#death_code_id').attr("readonly","readonly");

    $('#death_problem_id option').not(':selected').attr("disabled", "disabled");
    $('#death_problem_id').attr("readonly","readonly");
}

function chgOthersResponsible(responsible)
{
    console.log(responsible);
    if(responsible == 'Otros') {
        $('#others-container').show();
    }
    else{
        $('#others-container').hide();
        $('#others_responsible').val('');
    }
}

$(function(){
    $('#pageTitle').html("Editar Tiempo Muerto");
    setDeadCodeByMachineId();

    $('.alt-sec-prob').hide();

    $('#dead_code_group').change(function(){
        setDeadCodeByMachineId();
        setDeadCodeDescriptionById();
    });
    $('#death_code_id').change(function(){
        setDeadCodeDescriptionById();
    });

    if($('#dead_code_selected').val() != null && $('#dead_code_selected').val() != "")
    {
        $('#death_code_id').val($('#dead_code_selected').val());
    }

    if($('#dead_code_group_selected').val() != null && $('#dead_code_group_selected').val() != "")
    {
        $('#dead_code_group').val($('#dead_code_group_selected').val());
    }

    if($('#death_problem_id').val() == null || $('#death_problem_id').val() == "")
    {
        clearSubCodeSection();
    }

    $('#responsible').change(function(){
        chgOthersResponsible($(this).val())
    });

    disableEditValues();
    chgOthersResponsible($('#responsible').val());

    if($('#other_problem').val() == '')
        $('#other-problem-container').hide();

});
