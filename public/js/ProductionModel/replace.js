/**
 * Created by servandomac on 8/7/14.
 */
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

function isDuplicateReplace()
{
    var retVal = false;

    if($('#to_replace_sku').val() == $('#product_sku').val())
    {
        retVal = true;
    }

    return retVal;
}


$(function(){

    $('#sku_size').attr("readonly","readonly");


    $('#guardarBtn').click(function(){
        if(!isValidSku()){
            alert("El sku: " + $('#product_sku').val() + " no es un sku válido");
            return false;
        }

        if(isDuplicateReplace())
        {
            alert("El sku a reemplazo debe ser diferente al original");
            return false;
        }

        $('#product_sku').val($('#sku_prefix').val() + $('#sku_right').val());

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
            console.log( ui.item ?  $('#sku_size').val(ui.item.size)
                : "Nothing selected, input was " + this.value);
        }
    });
}); // End doc-ready