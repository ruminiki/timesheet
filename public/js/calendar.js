$(document).ready(function(){
	$("[name='schedule']").mask('00:00');

	$('#datepicker-date-report').Zebra_DatePicker({
	  format: 'Y m'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});

	$('#datepicker-date-calendar').Zebra_DatePicker({
	  format: 'Y m'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});

	$('#datepicker-end-period-not-worked').Zebra_DatePicker({
	  format: 'd/m/Y'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});
	/*$("[name='datepicker-end-period-not-worked']").mask('00/00/0000');*/

});

/**
Page: point/index.phtml
Object: input datepicker-date-calendar
**/
function onChangeDateCalendar(input, url){
	param = input.value.replace(" ", "");
	window.location.href = url + param ;
}

