
$(function(){
    $('#start_date, #end_date').datepicker({
        showOn: "button",
        buttonImage: "/img/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: 'yy-mm-dd'
    });
    $('#start_date, #end_date').css({'display':'inline','width':'90%'});


    $('#exportBtn').click(function(){
        var start_date = $('#start_date').datepicker( "getDate" );
        var end_date = $('#end_date').datepicker( "getDate" );

        if($('#start_date').val() == "")
        {
            alert('Debe existir al menos fecha inicial');
            return false;
        }

        if((start_date > end_date) && $('#end_date').val() != "")
        {
            alert("La fecha inicial no debe ser mayor al la fecha final");
            return false;
        }
    });

});