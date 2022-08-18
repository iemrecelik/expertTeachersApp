// Document ready start
$(function () {
	$('[data-toggle="tooltip"]').tooltip({
		trigger: "hover",
	});

	let sidebarActiveEl = $(`.sidebar li.nav-item a.nav-link[href="${location.href}"]`).addClass('active')
	sidebarActiveEl.parents('ul.nav-treeview').css("display", "block").prev('a[href="#"].nav-link').addClass('active');
	sidebarActiveEl.parents('li.data-menu-open').addClass('menu-open');
})
// Document ready end