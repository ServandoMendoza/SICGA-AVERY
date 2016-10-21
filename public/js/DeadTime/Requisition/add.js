/**
 * Created by servandomac on 11/5/14.
 */
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

$(function(){
    $('#fix_time, #assign_time').datepicker({
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
    $('#fix_time, #assign_time').css({'display':'inline','width':'90%'});

    if(hasAssignDate > 0) {
        $('#assign_time').datepicker('destroy');
        $('#assign_time').attr("readonly","readonly");
    }


    $('#id_area option').not(':selected').attr("disabled", "disabled");
    $('#id_area').attr("readonly","readonly");

    $('#id_machine option').not(':selected').attr("disabled", "disabled");
    $('#id_machine').attr("readonly","readonly");

    $('#machine_status option').not(':selected').attr("disabled", "disabled");
    $('#machine_status').attr("readonly","readonly");

    $('#responsible option').not(':selected').attr("disabled", "disabled");
    $('#responsible').attr("readonly","readonly");

    $('#id_shift option').not(':selected').attr("disabled", "disabled");
    $('#id_shift').attr("readonly","readonly");

    $('#assignTechBtn').click(function(){
        if(isTechBusy()) {
            alert("Este técnico se encuentra ocupado en otra actividad");
            return false;
        }

       $('#requisition-form').submit();
    });

    $('#freeReqBtn').click(function(){
        $('#modify').val('free');
        $('#requisition-form').submit();
    });

    $('#id_shift').change(function(){
        getTechByShift();
    });

    getTechByShift();

    if($('#free').val() == 1) {
        $('#req-msg').show();
        $('#assignTechBtn').hide();
        $('#freeReqBtn').hide();
    }

    if((hasOpenOrder) > 0) {
        $('#foaBtn').hide();
    }

    if(techCount == 3) {
        $('#assignTechBtn').hide();

        $('#id_tech option').not(':selected').attr("disabled", "disabled");
        $('#id_tech').attr("readonly","readonly");
    }

});