function setSpecificProduccionEstandar(time,size,idElement)
{
    var program_time = time;
    var sku_size = size;

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
            $('#pstd_txt_'+idElement).val(data.stdProduction);
        }
    });
}

function isProductionShiftCapturedAction(str_hr,end_hr)
{
    var retVal = 0;

    var data = {
        "start_hour" : str_hr,
        "end_hour" : end_hr
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
    var cantProdHours = 0;

    $.ajax({
        type:"POST",
        url:"/ProductionModel/getProductionHours",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            $.each(data.prodHours, function( key, value ) {
                var res = value.split("-");
                var str_hr = res[0];
                var end_hr = res[1];

                $('#phr1_txt_'+(key+1)).html("<label for='pchk_"+(key+1)+"'>" + str_hr + " - " + end_hr + " hrs </label>");
                $('#p_hr'+(key+1)).val(value);
                cantProdHours++;
            });
        }
    });

    for(var i=cantProdHours; i<=topCount; i++) {
        $('#phr1_div_'+(i+1)).remove();
    }
}

function getProductsByShift()
{
    $.ajax({
        type:"POST",
        url:"/ProductionModel/getProductsByShiftAjax",
        data: "",
        dataType: 'json',
        async: false,
        success: function(data){
            $.each(data, function( key, value ) {
                value.start_hour = value.start_hour.substr(0, value.start_hour.length-3);
                value.end_hour = value.end_hour.substr(0, value.end_hour.length-3);
                //var phr = value.start_hour + "-" + value.end_hour;
                var phr = value.start_hour.substr(0,2);

                for(var i=1; i<=topCount; i++)
                {
                    if (typeof $('#p_hr'+i).val() != 'undefined')
                    {
                        //console.log($('#p_hr'+i).val().substr(6,8));
                        //if()

                        if($('#p_hr'+i).val().substr(0,2) == phr)
                        {
                            $('#phr1_div_'+i).remove();
                            break;
                        }
                    }
                }
            });
        }
    });
}

function setProductSize()
{
    var product_sku_prefix = $('#sku_prefix').val();
    var product_sku_right = $('#sku_right').val();

    var data = {
        "product_sku" : product_sku_prefix + product_sku_right
    };

    $.ajax({
        type:"POST",
        url:"/ProductionModel/getProductSize",
        data: data,
        dataType: 'json',
        async: false,
        success: function(data){
            $('#sku_size').val(data.p_sku_size);
        }
    });
}

function addProductionModel(id)
{
    var product_sku_prefix = $('#sku_prefix').val();
    var product_sku_right = $('#sku_right').val();
    var product_sku = product_sku_prefix + product_sku_right;

    var res = $('#p_hr'+id).val().split("-");
    var str_hr = res[0];
    var end_hr = res[1];

    var productionModel = {
        "product_sku" : product_sku,
        "shift_id" : $('#shift_id').val(),
        "sku_size" : $('#sku_size').val(),
        "start_hour" : str_hr,
        "end_hour" : end_hr,
        "shift_now" : $('#shift_now').val(),
        "program_time" : $('#p_min_txt_'+id).val(),
        "production_hour" : $('#p_hr'+id).val(),
        "std_production" : $('#pstd_txt_'+id).val()
    };

    if(!isProductionShiftCapturedAction(str_hr,end_hr)){
        alert("Captura de modelo en turno existente");
        return false;
    }

    addPmByAjaxAction(productionModel);
}

function addPmByAjaxAction(prodModObj){
    $.ajax({
        type:"POST",
        url:"/ProductionModel/addByAjax",
        dataType: 'json',
        data: prodModObj,
        async: false,
        success: function(data){
        }
    });
}

function setNoProgram(){
    var retData = false;

    $.ajax({
        type:"POST",
        url:"/ProductionModel/setNoProgram",
        dataType: 'json',
        data: noProgramObj,
        async: false,
        success: function(data){
            if(data.success) {
                retData = true;
            }
            else{
                alert('Hubo un error en la operacion.');
            }
        }
    });

    return retData;
}

function setActualShiftTurn(shitNumber)
{
    if(shitNumber)
    {
        $('#shift_id').val(shitNumber);
        setProductionHours();
    }
}

function reloadAllProductionStandar(sizeVal)
{
    $('#sku_size').val(sizeVal);

    $( "select[name^='p_min_txt']").each(function(){
        var id = $(this).attr("id");
        var id = id.substr(id.length - 1);

        if($('#p_min_txt_'+id).val() > 0)
        {
            var time = $(this).val();
            var size = $('#sku_size').val();

            setSpecificProduccionEstandar(time,size,id)
        }
    });

}

function checkProgramTimeSelection()
{
    var retVar = true;

    $( "select[name^='p_min_txt']").each(function(){
        var id = $(this).attr("id");
        var id = id.substr(id.length - 1);

        if($(this).val() == 0 && $("#pchk_"+id).is(":checked"))
        {
            retVar = false;
            $(this).focus();

            return false;
        }
    });

    return retVar;
}


$(function(){
    $('#pageTitle').html("Agregar Modelo");
    setActualShiftTurn($('#shift_now').val());
    
    $('#guardarBtn').click(function(){

        if($( "input[pfix='multiple-prod-add']:checked").length == 0 )
        {
            alert("Porfavor seleccione un horario de producción");
            return false;
        }

        if(! isValidSku()){
            alert("El sku: " + $('#product_sku').val() + " no está registrado en el sistema");
            return false;
        }

        if(!checkProgramTimeSelection())
        {
            alert("Por favor seleccione minutos programados");
            return false;
        }

        $( "input[pfix='multiple-prod-add']:checked").each(function(){
            var id = $(this).attr("id");
            var id = id.substr(id.length - 1);

            addProductionModel(id);
        });

        if(noProgramObj.id)
            setNoProgram();
        window.location = addPostUrl;
    });

    $('#check-all').change(function() {
        if($(this).is(":checked")) {
            $( "input[pfix='multiple-prod-add']").each(function()
            {
                $(this).prop("checked",true);
            });
        }
        else
        {
            $( "input[pfix='multiple-prod-add']").each(function()
            {
                $(this).prop("checked",false);
            });
        }
    });

    $( "select[name^='p_min_txt']" ).change(function(){
        var id = $(this).attr("id");
        var id = id.substr(id.length - 1);

        var time = $(this).val();
        var size = $('#sku_size').val();

        setSpecificProduccionEstandar(time,size,id);
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
           console.log( ui.item ?  reloadAllProductionStandar(ui.item.size)
              : "Nothing selected, input was " + this.value);
        }
      });

    $('#shift_id option').not(':selected').attr("disabled", "disabled");
    $('#shift_id').attr("readonly","readonly");

    $('#sku_size option').not(':selected').attr("disabled", "disabled");
    $('#sku_size').attr("readonly","readonly");

    getProductsByShift();
    setProductSize();
});