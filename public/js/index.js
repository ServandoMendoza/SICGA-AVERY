$(document).on("ready", funcClicks);

function funcClicks()
{
	$(".quick-menu ul li").on("click", funcShowPage);
	$(".col-md-3 button").on("click", fAddRow);
}

function funcShowPage(data)
{
	var url = data.currentTarget.id;
	window.location.href = url;
}

function fAddRow(data)
{
	var row = "#row_minutes_" + data.currentTarget.id;
	var mostrar = {display : "inline"};
	$(row).css(mostrar);
}