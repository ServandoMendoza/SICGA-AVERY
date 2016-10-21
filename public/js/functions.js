var dayArr = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
var monthArr = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

function getCurrentDate(date){
    var dd = date.getDate();
    var mm = date.getMonth()+1; //January is 0!
    var yyyy = date.getFullYear();

    if(dd < 10) {
        dd = '0' + dd;
    }

    if(mm < 10) {
        mm = '0' + mm;
    }

    date = mm + '/' + dd + '/' + yyyy;
    return date;
}

function formatFinalHour(strHour)
{
    if(strHour == "24:00:00")
    {
        strHour = "00:00:00";
    }

    return strHour;
}

function reduceDecimals(i, digits) {
    var pow = Math.pow(10, digits);

    return Math.floor(i * pow) / pow;
}

function setColorProdStatus(percentaje){

    var styleClass = "";

    if(percentaje > 90)
    {
        styleClass = "progress-bar-success";
    }
    else if(percentaje >= 80 && percentaje <= 90 )
    {
        styleClass = "progress-bar-warning";
    }
    else{
        styleClass = "progress-bar-danger";
    }

    return styleClass;
}

function calculateStatusProd(stdProd,actProd){

    return ( actProd * 100 ) / stdProd;
}

function getTurnNameById(turnId){
    var turnName = "Not Found";

    switch (turnId)
    {
        case 1: turnName = "Matutino"; break;
        case 2: turnName = "Vespertino"; break;
        case 3: turnName = "Nocturno"; break;
    }

    return turnName;
}

function updateTime(){
    var nowMS = now.getTime();
    nowMS += 1000;
    now.setTime(nowMS);

    var dayNow = now.getDate();
    var dayNowNumber = now.getDay();
    var yearNow = now.getFullYear();
    var monthNow = now.getMonth();


    $('#localTime').html(now.toLocaleTimeString());//adjust to suit
    $('#dayNameTime').html(dayArr[dayNowNumber]);//adjust to suit
    $('#dateFormatTime').html(dayNow + " " + monthArr[monthNow] + " " + yearNow);//adjust to suit

}

function startInterval(){
    setInterval('updateTime();', 1000);
}

startInterval();//start it right away
