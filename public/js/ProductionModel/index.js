/**
 * Created by servandomac on 6/15/14.
 */

function geProductionModelActionBack()
{
    $(".info-shift").show();

    actualShiftCount--;

    if(actualShiftCount == 0)
    {
        dateJs.setDate(dateJs.getDate()-1);
        actualShiftCount = 3;
    }

    var dayNow = +dateJs.getFullYear()+'/'+(dateJs.getMonth()+1) +'/'+dateJs.getDate();


    var data = {
        "filter_date" : dayNow,
        "filter_shift" : actualShiftCount,
        "filter_date_unixtime" : (new Date(dateJs.getFullYear()+'.'+(dateJs.getMonth()+1)+'.'+dateJs.getDate()).getTime() / 1000)
    };

    getProductionFilteredPaginationAction(data);

    $('#shift_container').html(getTurnNameById(actualShiftCount));
    $('#date_container').html(dayNow);

    hideNextPaginationModel();
}

function geProductionModelActionForward()
{
    $(".info-shift").show();

    actualShiftCount++;

    if(actualShiftCount > 3)
    {
        dateJs.setDate(dateJs.getDate()+1);
        actualShiftCount = 1;
    }

    var dayNow = +dateJs.getFullYear()+'/'+(dateJs.getMonth()+1) +'/'+dateJs.getDate();

    var data = {
        "filter_date" : dayNow,
        "filter_shift" : actualShiftCount,
        "filter_date_unixtime" : (new Date(dateJs.getFullYear()+'.'+(dateJs.getMonth()+1)+'.'+dateJs.getDate()).getTime() / 1000)
    };

    getProductionFilteredPaginationAction(data);

    $('#shift_container').html(getTurnNameById(actualShiftCount));
    $('#date_container').html(dayNow);

    hideNextPaginationModel();
}

function getProductionFilteredPaginationAction(data){
    $.ajax({
        type:"POST",
        url:"/ProductionModel/geProductionFilteredPagination",
        dataType: 'json',
        data: data,
        async: false,
        success: function(prodModelObj){
            var stringHtml = "";
            var actualProdSum = 0;
            var stdProdSum = 0;

            if(prodModelObj.length > 0 )
            {
                $.each( prodModelObj, function( key, prodModel ) {

                    var progressPercentage = reduceDecimals(calculateStatusProd(prodModel.std_production,prodModel.actual_production),2);

                    actualProdSum += prodModel.actual_production * 1;
                    stdProdSum += prodModel.std_production * 1;

                    stringHtml += "<tr>";
                    stringHtml  += "<td id='sku-delete-"+prodModel.id+"'>"+prodModel.sku+"</td>";
                    stringHtml  += "<td>"+formatFinalHour(prodModel.start_hour)+" - "+formatFinalHour(prodModel.end_hour)+"</td>";
                    stringHtml  += "<td>"+prodModel.actual_production+"</td>";
                    stringHtml  += "<td>"+prodModel.std_production+"</td>";

                    if(progressPercentage > 0)
                        stringHtml  += "<td><div class='progress progress progress-striped active clr-margin'> <div class='progress-bar "+setColorProdStatus(progressPercentage)+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: "+ progressPercentage +"%'> </div> </div> <div class='progressPercentage'>" + progressPercentage + "%</div></td>";
                    else
                        stringHtml  += "<td><div class='progress progress progress-striped active clr-margin'> <div class='progress-bar "+setColorProdStatus(progressPercentage)+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: "+progressPercentage+"%'> </div> </div></td>";

                    stringHtml  += "<td><a href='"+editData.dt_url+'list/'+prodModel.id+"' class='btn btn-info btn-block'><span class='icon-clock-4'></span></a></td>";
                    stringHtml  += "<td><a href='"+editData.scrap_url+'list/'+prodModel.id+"' class='btn btn-warning btn-block'><span class='icon-trashcan'></span></a></td>";

                    if(!editData.is_admin && prodModel.actual_production > 0)
                    {
                        stringHtml  += "<td></td>";
                    }
                    else
                    {
                        stringHtml  += "<td><a href='"+editData.pmodel_edit_url+prodModel.id+"' class='btn btn-success btn-block'><span class='icon-pencil'></span></a></td>";
                    }

                    if(editData.is_admin)
                    {
                        stringHtml  += "<td><a href='javascript:deleteProductModel("+prodModel.id+")' class='btn btn-danger btn-block'><span class='glyphicon glyphicon-remove'></span></a></td>";
                    }

                    stringHtml += "</tr>";
                });

                stringHtml += "<tr>";
                stringHtml += "<td><span style='color:#013E6B;font-weight:bolder'>Total Prod:</span></td>";
                stringHtml += "<td></td>";
                stringHtml += "<td>"+actualProdSum+"</td>";
                stringHtml += "<td>"+stdProdSum+"</td>";

                var totalPercentage = reduceDecimals(calculateStatusProd(stdProdSum, actualProdSum),2);

                if(totalPercentage > 0)
                    stringHtml  += "<td><div class='progress progress progress-striped active clr-margin'> <div class='progress-bar "+setColorProdStatus(totalPercentage)+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: "+ totalPercentage +"%'> </div> </div> <div class='progressPercentage'>" + totalPercentage + "%</div></td>";
                else
                    stringHtml  += "<td><div class='progress progress progress-striped active clr-margin'> <div class='progress-bar "+setColorProdStatus(totalPercentage)+"' role='progressbar' aria-valuenow='60' aria-valuemin='0' aria-valuemax='100' style='width: "+totalPercentage+"%'> </div> </div></td>";

                stringHtml += "<td colspan='4'></td>"
                stringHtml += "</tr>";

                stringHtml += "<tr>";

                stringHtml += "<td colspan='9'><a href='"+editData.dt_url+'shift/'+data.filter_shift+'/date/'+data.filter_date_unixtime+"' class='btn btn-info btn-lg btn-add'>Tiempos Muertos <span class='icon-clock-4'></span></a>";
                stringHtml += "<a href='"+editData.scrap_url+'shift/'+data.filter_shift+'/date/'+data.filter_date_unixtime+"' class='btn btn-warning btn-lg btn-add'>Scraps <span class='icon-trashcan'></span></a></td>";

                stringHtml += "</tr>";


                $("#promotion-model-tbl tbody").html(stringHtml);
            }
            else{
                stringHtml = "<tr><td colspan='10'>No hay registros capturados</td></tr>";

                $("#promotion-model-tbl tbody").html(stringHtml);
            }
        }
    });
}

function canAddDeadTime(prodModelId){
    var retData = false;

    var data = {
        "prod_model_id" : prodModelId
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

function isAnInactiveDeadTime(prodModelId)
{
    var data = {
        "prod_model_id" : prodModelId
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

function addNoProdDt(prodModelId)
{
    var r = confirm("Desea asignar el tiempo muerto: 'Sin Programa' ?");

    //if (r == true) {
        if (!canAddDeadTime(prodModelId)) {
            alert("No se puede agregar tiempo muerto: El tiempo actual no corresponde al modelo de producción");
            return false;
        }

        if (isAnInactiveDeadTime(prodModelId)) {
            alert("No es posible agregar un tiempo muerto ya que existe un tiempo muerto inactivo");
            return false;
        }
    //}

    if (r == true) {

        var data = {
            "prod_model_id" : prodModelId
        };

        $.ajax({
            type:"POST",
            url:"/ProductionModel/setNoProductionDtAjax",
            dataType: 'json',
            data: data,
            async: false,
            success: function(data){
                if(data.result)
                {
                    alert("El tiempo muerto se ha asignado exitosamente.");
                }

            }
        });
    }
}

function deleteProductModel(prodModelId)
{
    $('#sku-delete-'+prodModelId).addClass('inactivo');
    var r = confirm("Esta seguro que desea borrar este registro?");

    if (r == true) {

        var data = {
            "prod_model_id" : prodModelId
        };

        $.ajax({
            type:"POST",
            url:"/ProductionModel/deleteProductionModelAjax",
            dataType: 'json',
            data: data,
            async: false,
            success: function(data){
                if(data.result)
                {
                    alert("El producto ha sido eliminado exitosamente");
                    location.reload();
                }

            }
        });
    }

    $('#sku-delete-'+prodModelId).removeClass('inactivo');
}


function hideNextPaginationModel(){
    $('li.next').removeClass('disabled');
    $('#nextShift').removeClass('disabled');

    if(getCurrentDate(now) == getCurrentDate(dateJs) && actualShiftCount == editData.shift_now)
    {
        $('li.next').addClass('disabled');
        $('#nextShift').addClass('disabled');
    }
}

function setNoProgramLbl(is_active){

    if(is_active){
        $('#setNoProgram').removeClass('btn-danger');
        $('#setNoProgram').addClass('btn-primary');
        $('#setNoProgram').html(
            "Quitar 'Sin Programa' <span class='glyphicon glyphicon-ok-sign'></span>"
        );
    }
    else{
        $('#setNoProgram').removeClass('btn-primary');
        $('#setNoProgram').addClass('btn-danger');
        $('#setNoProgram').html(
            "Sin Programa <span class='glyphicon glyphicon-remove-sign'></span>"
        );
    }

}

function setNoProgram(){
    var retData = false;
    var noProgId = 0;

    if(noProgramObj.id){
        noProgramObj.is_active = 0;
    }

    $.ajax({
        type:"POST",
        url:"/ProductionModel/setNoProgram",
        dataType: 'json',
        data: noProgramObj,
        async: false,
        success: function(data){
            if(data.success) {
                if(data.update){
                    alert('Se ha quitado a la máquina sin programa exitosamente.');
                    setNoProgramLbl(0);
                    noProgId = 0;

                }
                else {
                    alert('Se ha puesto la máquina sin programa exitosamente.');
                    setNoProgramLbl(1);
                    noProgId = data.id;
                }

                retData = true;
            }
            else{
                alert('Hubo un error en la operacion.');
            }
        }
    });

    noProgramObj.id = noProgId;

    return retData;
}

$(function(){
    $('#pageTitle').html("Status HR X HR");

    hideNextPaginationModel();

    $('#shift_container').html(getTurnNameById(editData.shift_now));
    $('#date_container').html(dateJs.getFullYear()+'/'+(dateJs.getMonth()+1) +'/'+dateJs.getDate());

    $('#prevShift').click(function(){
        geProductionModelActionBack();
    });
    $('#nextShift').click(function(){
        geProductionModelActionForward();
    });

    $('#setNoProgram').click(function(){
       setNoProgram()
    });

    if(noProgramObj.id){
        setNoProgramLbl(1);
    }

});