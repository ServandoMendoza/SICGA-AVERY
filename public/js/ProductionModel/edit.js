function isAnInactiveDeadTime()
{
    var data = {
        "prod_model_id" : editData.prod_model_id
    };

    var isAnInactive = false;

    $.ajax({
        type:"POST",
        url:"/DeadTime/isAnInactiveDeadTime",
        data: data,
        dataType: 'json',
        async: false,
        success: function(data){
            if(parseInt(data.count) > 0)
            {
                isAnInactive = true;
            }
        }
    });

    return isAnInactive;
}

function hasReplaceModel()
{
    var data = {
        "product_model_id" : editData.prod_model_id
    };

    var retVal = false;

    $.ajax({
        type:"POST",
        url:"/ProductionModel/hasReplaceModel",
        data: data,
        dataType: 'json',
        async: false,
        success: function(data){
            if(parseInt(data.count) > 0)
            {
                retVal = true;
            }
        }
    });

    return retVal;
}

function setProduccionEstandar()
{
    var sku_size  = $('#sku_size').val();
    var program_time = $('#program_time').val();

    if($('#is_replace').val() == 1 || editData.is_replace_parent == 1)
    {
        program_time = editData.program_time;
    }


    var data = {
        "program_time" : program_time,
        "sku_size" : sku_size
    };

    $.ajax({
        type:"POST",
        url:"/ProductionModel/getStdProduction",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            $('#std_production').val(data.stdProduction);
            $('#machine_runtime').val(data.machine_runtime);
        }
    });
}

function isProductionShiftCapturedAction()
{
    // Start Hour string processing, if its the same that loaded, we can update
    var string_hour = editData.start_hour;

    // Get rid of first '0' if hour its 07:00:00
    if(string_hour.charAt(0) == '0')
        string_hour = string_hour.substring(1);

    // Remove miliseconds in time
    var start_hour = string_hour.substr(0, string_hour.length-3);

    if(start_hour == $('#start_hour').val())
    {
        return true;
    }

    var retVal;

    var data = {
        "start_hour" : $('#start_hour').val(),
        "end_hour" : $('#end_hour').val()
    };

    $.ajax({
        type:"POST",
        url:"/ProductionModel/isProductionShiftCaptured",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            retVal = (data[0].count < 1);
        }
    });

    return retVal;
}

function isValidSku()
{
    var product_sku_prefix = $('#sku_prefix').val();
    var product_sku_right = $('#sku_right').val();

    $('#product_sku').val(product_sku_prefix + product_sku_right);

    var product_sku = $('#product_sku').val();
    var retVal = false;
    var data = {
        "product_sku" : product_sku
    };

    $.ajax({
        type:"POST",
        url:"/ProductionModel/isValidSku",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data.sku != null)
            {
                retVal = true;
            }
        }
    });

    return retVal;
}

function setProductionHours()
{
    var shift_id = $('#shift_id').val();
    var data = {
        "shift_id" : shift_id
    };

    $.ajax({
        type:"POST",
        url:"/ProductionModel/getProductionHours",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            var horaProdDdl = $('#horaProduccion');

            horaProdDdl.html('');
            $.each(data.prodHours, function( key, value ) {
                horaProdDdl.append('<option value='+value+'>'+value+'</option>');
            });

            setHiddenProductionHours();
        }
    });
}

function setActualShiftTurn(shitNumber)
{
    if(shitNumber)
    {
        $('#shift_id').val(shitNumber);
        setProductionHours();
    }
}

function setHiddenProductionHours()
{
    var str = $('#horaProduccion').val();
    var res = str.split("-");

    $('#start_hour').val(res[0]);
    $('#end_hour').val(res[1]);
}

function setEditValues()
{
    var string_sku = editData.product_sku;
    var string_ph = "";

    $('#product_sku').val(editData.product_sku);
    $('#sku_prefix').val(string_sku.substring(0,5));
    $('#sku_right').val(string_sku.substring(5));

    // Production Hour string processing
    var string_val = "";
    var string_hour = editData.start_hour;

    // Get rid of first '0' if hour its 07:00:00
    if(string_hour.charAt(0) == '0')
        string_hour = string_hour.substring(1);

    // Remove miliseconds in time
    string_val += string_hour.substr(0, string_hour.length-3);
    string_ph += editData.start_hour.substr(0, editData.start_hour.length-3);
    $('#start_hour').val(string_val);

    string_hour = editData.end_hour;
    if(string_hour.charAt(0) == '0')
        string_hour = string_hour.substring(1);

    string_val += "-" + string_hour.substr(0, string_hour.length-3);
    string_ph += "-" + editData.end_hour.substr(0, editData.end_hour.length-3);
    $('#end_hour').val(string_hour.substr(0, string_hour.length-3));

    $("#shift_id").val(editData.shift_id);
    $("#horaProduccion").val(string_ph);
}

function disableEditValues()
{
    $('#sku_prefix option').not(':selected').attr("disabled", "disabled");
    $('#sku_prefix').attr("readonly","readonly");

    $('#sku_size option').not(':selected').attr("disabled", "disabled");
    $('#sku_size').attr("readonly","readonly");

    $('#shift_id option').not(':selected').attr("disabled", "disabled");
    $('#shift_id').attr("readonly","readonly");

    $('#horaProduccion option').not(':selected').attr("disabled", "disabled");
    $('#horaProduccion').attr("readonly","readonly");

    // El admin si puede editar los minutos programados
    if(!editData.is_admin_usr){
        $('#program_time option').not(':selected').attr("disabled", "disabled");
        $('#program_time').attr("readonly","readonly");
    }

    $('#sku_right').attr("readonly","readonly");

    // Si no hay tiempo muerto, no vamos a dar opcion de reemplazar modelo
    // Tampoco debe ser un producto reemplazo
    if(!isAnInactiveDeadTime() || $('#is_replace').val() == 1 || hasReplaceModel())
    {
        $('#replaceModelContainer').hide();
    }

}

$(function(){
    $('#pageTitle').html("Editar Modelo");

    setProductionHours();
    setProduccionEstandar();
    setActualShiftTurn($('#shift_now').val());
    setEditValues();

    $('#guardarBtn').click(function(){
        if(! isValidSku()){
            alert("El sku: " + $('#product_sku').val() + " no es un sku válido");
            return false;
        }

        if(!isProductionShiftCapturedAction()){
            alert("Captura de modelo en turno existente");
            return false;
        }
    });

    $("#program_time").change(function(){
        setProduccionEstandar()
    });
    $("#sku_size").change(function(){
        setProduccionEstandar()
    });
    $("#shift_id").change(function(){
        setProductionHours()
    });

    $('#horaProduccion').change(function(){
        setHiddenProductionHours();
    });

    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

    $( "#sku_right" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data( "ui-autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                $.getJSON( "/ProductionModel/getProducts", {
                    term: extractLast( request.term ),
                    sku_prefix: $('#sku_prefix').val()
                }, response );
            },
            select: function( event, ui ) {
//                console.log( ui.item ?
//                    "Selected: " + ui.item.label :
//                    "Nothing selected, input was " + this.value);
            }
        });

    disableEditValues();

    // Crear la opcion del modelo de produccion reemplazado, ya que no existe en el turno en si.
    if($('#is_replace').val() == 1 || editData.is_replace_parent == 1)
    {
        $('#horaProduccion').append('<option value='+$('#start_hour').val()+'-'+$('#end_hour').val()+'>'+$('#start_hour').val()+'-'+$('#end_hour').val()+'</option>');
        $('#program_time').html('');
        $('#program_time').append('<option value='+ editData.program_time +'>'+editData.program_time+'</option>');
        $('#program_time').attr("readonly","readonly");

    }

    if(editData.is_gerente_usr == 1)
    {
        $('#guardarBtn').remove();
    }
});