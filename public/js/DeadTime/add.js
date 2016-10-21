/**
 * Created by servandomac on 6/10/14.
 */
var DeadCode;

function setDeadCodeByGroup()
{
    var dead_code_group_id = $('#dead_code_group').val();

    var data = {
        "dead_code_group_id" : dead_code_group_id
    };

    $.ajax({
        type:"POST",
        url:"/DeadTime/getDeadCodeByGroup",
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
                getDeadProblem($('#death_code_id').val());
            }
        }
    });
}

function setDeadCodeDescriptionById()
{
    var death_code_id = $('#death_code_id').val();

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

function getDeadProblem(death_code_id){
    var data = {
        "death_code_id" : death_code_id
    };

    $.ajax({
        type:"POST",
        url:"/DeadTime/getDeadProblem",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data.length > 0)
            {
                $('#sub-group-container').show();
                $('.alt-sec-prob').hide();

                $('#death_problem_id').html('');

                $.each(data, function( key, value ) {
                    $('#death_problem_id').append('<option value='+value.id+'>'+value.code+' - '+ value.description +'</option>');
                });
            }
            else{
                $('.alt-sec-prob').show();
                $('#sub-group-container').hide();
                $('#death_problem_id').html('');
            }

        }
    });
}

function canAddDeadTime(){
    var production_model_id = $('#production_model_id').val();
    var retData = false;

    var data = {
        "prod_model_id" : production_model_id
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

function clearDeadProblemSection()
{
    $('#sub-group-container').hide();
    $('#other-problem-container').hide();
    $('#death_problem_id').html("");
    $('.alt-sec-prob').show();
}

function showOtherProblem(){
    // Selecciono otros - en problema
    if($('#death_code_id').val() == 191 || $('#death_code_id').val() == 7) {
        $('#other-problem-container').show();
    }
    else{
        $('#other-problem-container').hide();
        $('#other_problem').val('');
    }
}

$(function(){
    $('#pageTitle').html("Agregar Tiempo Muerto");
    clearDeadProblemSection();
    setDeadCodeByGroup();

    $('#saveBtn').click(function(){
        if(!canAddDeadTime())
        {
            alert("No se puede agregar tiempo muerto: El tiempo actual no corresponde al modelo de producción")
            return false;
        }

        var problem = $("#death_problem_id option:selected").text();

        if(problem != "")
        {
            $('#problem').val(problem);
        }
    });

    $('#dead_code_group').change(function(){
        setDeadCodeByGroup();
        setDeadCodeDescriptionById();
        showOtherProblem();
    });
    $('#death_code_id').change(function(){
        setDeadCodeDescriptionById();
        //getDeadSection($('#dead_code_group').val());
        getDeadProblem($(this).val());

        showOtherProblem();
    });

    $('#machine_status').html('');
    $('#machine_status').append('<option value="0">inactivo</option>');

});
