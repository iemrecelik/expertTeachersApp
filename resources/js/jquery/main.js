// Document ready start
$(function () {
	$('[data-toggle="tooltip"]').tooltip({
		trigger: "hover",
	});

	let sidebarActiveEl = $(`.sidebar li.nav-item a.nav-link[href="${location.href}"]`).addClass('active')
	sidebarActiveEl.parents('ul.nav-treeview').css("display", "block").prev('a[href="#"].nav-link').addClass('active');
	sidebarActiveEl.parents('li.data-menu-open').addClass('menu-open');


	//Date range picker
    $('#reservation').daterangepicker({
		"autoUpdateInput": false,
		"locale": {
			"format": "DD/MM/YYYY",
			"separator": " - ",
			"applyLabel": "uygula",
			"cancelLabel": "Temizle",
			"fromLabel": "From",
			"toLabel": "To",
			"customRangeLabel": "Custom",
			"weekLabel": "W",
			"daysOfWeek": [
				"Paz",
				"Pzt",
				"Sal",
				"Çar",
				"Per",
				"Cum",
				"Cmt"
			],
			"monthNames": [
				"Ocak",
				"Şubat",
				"Mart",
				"Nisan",
				"Mayıs",
				"Haziran",
				"Temmuz",
				"Ağustos",
				"Eylül",
				"Ekim",
				"Kasım",
				"Aralık"
			],
			"firstDay": 1
		},
		/* "startDate": "DD/MM/YYYY",
		"endDate": "DD/MM/YYYY" */
	}, function(start, end, label) {
	//   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
	});

	$('#reservation').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format("DD/MM/YYYY") + ' - ' + picker.endDate.format("DD/MM/YYYY"));
	});

	$('#reservation').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});
})
// Document ready end

function myFunction() {
  alert('bgbgbgbg');
}