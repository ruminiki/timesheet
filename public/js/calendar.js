$(document).ready(function(){
	$("[name='schedule']").mask('00:00');


	$('#datepicker-date-report').Zebra_DatePicker({
	  format: 'Y m'   //  note that becase there's no day in the format
	                  //  users will not be able to select a day!
	});

});
