/**
 * Created by servandomac on 11/26/14.
 */

function getMachineByArea()
{
    var id_area = $('#id_area').val();

    var data = {
        "id_area" : id_area
    };

    var retVal = false;

    $.ajax({
        type:"POST",
        url:"/Work/getMachineByArea",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            $('#id_machine').html('');

            if(data.length > 0)
            {
                $.each(data, function( key, value ) {
                    $('#id_machine').append('<option value='+value.id+'>'+value.name +'</option>');
                });
            }
        }
    });
}

function isTechBusy()
{
    var id_tech = $('#id_tech').val();

    var data = {
        "id_tech" : id_tech
    };

    var retVal = false;

    $.ajax({
        type:"POST",
        url:"/Requisition/isTechBusy",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data[0].count > 0) {
                retVal = true;
            }
            else{
                retVal = false;
            }
        }
    });

    return retVal;
}

function getTechByShift()
{
    var id_shift = $('#id_shift').val();

    var data = {
        "id_shift" : id_shift
    };

    var retVal = false;

    $.ajax({
        type:"POST",
        url:"/Work/getTechByShift",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            $('#id_tech').html('');

            if(data.length > 0)
            {
                $.each(data, function( key, value ) {
                    $('#id_tech').append('<option value='+value.id+'>'+value.name +'</option>');
                });
            }
        }
    });
}

$(function(){
    $('#create_date').datepicker({
        showOn: "button",
        buttonImage: "/img/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: 'yy-mm-dd',
        onSelect: function(datetext){
            var d = new Date(); // for now
            datetext=datetext+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
            $(this).val(datetext);
        }
    });
    $('#create_date').css({'display':'inline','width':'90%'});

    $('#id_area').change(function(){
        getMachineByArea();
    });

    getMachineByArea();

    $('#id_shift').change(function(){
        getTechByShift();
    });

    getTechByShift();

    $('#saveBtn').click(function(){
        // Si el tecnico es el mismo que ya se tenia, si se puede editar,
        if(isTechBusy() && $('#id_tech').val() != tech_selected) {
            alert("Este técnico se encuentra ocupado en otra actividad");
            return false;
        }
    });

    $('#finishBtn').click(function(){
        $('#free').val(1);
        $('#tech-work').submit();
    });

    $('#id_machine').val(machine_selected);
    $('#id_tech').val(tech_selected);

    // Desabilitar botones si ya fue liberado el trabajo
    if($('#free').val() > 0){
        $('#saveBtn, #finishBtn').remove();
    }

});