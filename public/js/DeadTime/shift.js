/**
 * Created by servandomac on 9/2/14.
 */
$(function(){
    $('#shift_container').html(getTurnNameById(actualShiftCount));
    $('#date_container').html(dateJs.getFullYear()+'/'+(dateJs.getMonth()+1) +'/'+dateJs.getDate());
});
