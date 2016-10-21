function validCells(arrCells){
    var retVal = true;

    $.each(arrCells, function(i, objCell){
        if(!(objCell.machines.length > 0)){
            alert('La celda "'+objCell.name +'" no tiene máquinas.');
            retVal = false;
            return false;
        }

    });

    return retVal;
}

function validMachines(arrMachineIds){
    var sorted_arr = arrMachineIds.sort();
    var founded = false;
    var retVal = true;

    for (var i = 0; i < arrMachineIds.length - 1; i++) {
        if (sorted_arr[i + 1] == sorted_arr[i]) {
            $.each($('[name="machine-sel"]'), function(){
                if ( $(this).val() == sorted_arr[i] ) {
                    if(!founded){
                        alert('La máquina "' + $(this).find('option:selected').text() + '" se encuentra repetida.');
                        founded = true;
                        retVal = false;
                    }

                    $(this).parent().css( "background-color", "red" );
                }
            });
            return false;
        }
    }

    return retVal;
}

function assignCells(arrCells)
{
    var data = {
        info :   arrCells,
        edit :   1,
        id_sub_area :   $('#id_sub_area').val()
    };

    $.ajax({
        type:"POST",
        url:"/Areas/assignCells",
        dataType: 'json',
        data: data,
        async: false,
        success: function(data){
            if(data.result) {
                alert("La operación fue exitosa.");
                window.location = editData.returnUrl;
            }
            else {
                alert("Esta máquina ya esta asignada en otra celda.");

                $.each($('[name="machine-sel"]'), function(){
                    if ( $(this).val() == data.repeated_machine ) {
                        $(this).parent().css( "background-color", "red" );
                    }
                });
            }
        }
    });
}

$(function(){
    var elmNum = 1;

    $("#cellList").on('click', '.delete', function () {
        $(this).parent().remove();
        elmNum--;
    });

    $("#cellList").on('click', '.deleteMachine', function () {
        $(this).parent().remove();
    });

    $("#addCell").click(function () {
        elmNum++;
        $('#cellList').append("<li class='cell'> <input type='text' id='cell-name-" + elmNum + "' cidx='0'" +
        " name='cell-name' value='New Cell "+elmNum+"' pfix='"+elmNum+"' class='form-control c-cell-input'/> " +
        "<button class='delete btn btn-info btn-lg btn-add b-cell-input'>Delete</button> " +
        "<button class='addMachine btn btn-info btn-lg btn-add b-cell-input' pfx-am='"+elmNum+"'>Add Machine</button>" +
        "<ul class='machine-list-"+elmNum+"'></ul>");

    });

    $(".container").on('click', '.addMachine', function() {
        var id = $(this).attr("pfx-am");

        $('.machine-list-' + id).append("<li class='machine' pfix='hlp'></li>");

        $('select#base-machine-sel').clone().attr('name', 'machine-sel').attr('class', 'form-control b-cell-input').removeAttr("id").appendTo($('li[pfix="hlp"]'));
        $('li[pfix="hlp"]').append(" <button class='deleteMachine btn btn-danger btn-lg btn-add b-cell-input'>Delete</button> ");

        $('[pfix="hlp"]').removeAttr("pfix");
        $('[name="machine-sel"]').show();
    });

    $('#assign-btn').click(function(){
        var arrCells = [];
        var arrMachineIds = [];

        $('[name="cell-name"]').each(function(){
            var cellNum = $(this).attr("pfix");
            var cellId = $(this).attr("cidx");
            var objCell = {};
            var arrMachines = [];

            objCell.name = $(this).val();
            objCell.id = cellId;

            $('.machine-list-'+cellNum).find('li').each(function(){
                $(this).find('[name="machine-sel"]').each(function(){
                    arrMachines.push($(this).val());
                })
            });

            objCell.machines = arrMachines;
            arrCells.push(objCell);

        });

        if(arrCells.length > 0)
            if(validMachines(arrMachineIds) && validCells(arrCells)){
                assignCells(arrCells);
            }
        else
            alert("Debe agregar una celda");

    });

});