$(document).ready(function(){
	//view/point/point/add.phtml
	$("[name='schedule']").mask('00:00');
	//view/point/report/index.phtml
	$('#datepicker-date-report').Zebra_DatePicker({
	  format: 'Y m'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});
	//view/point/point/index.phtml
	$('#datepicker-date-calendar').Zebra_DatePicker({
	  format: 'Y m'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});
	//view/point/day-not-worked/index.phtml
	$('#datepicker-end-period-not-worked').Zebra_DatePicker({
	  format: 'd/m/Y'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});
	/*$("[name='datepicker-end-period-not-worked']").mask('00/00/0000');*/
	//view/point/day-not-worked/index.phtml
	$('#datepicker-start-period-not-worked').Zebra_DatePicker({
	  format: 'd/m/Y'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});

});

/**
Page: point/index.phtml
Object: input datepicker-date-calendar
**/
function onChangeDateCalendar(input, url){
	//param (year month)- format YYYY mm
	param = input.value.replace(" ", "");
	window.location.href = url + param ;
}

