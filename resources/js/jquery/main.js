// Document ready start
$(function () {
	/* jQueryKnob */

    $('.knob').knob({
		/*change : function (value) {
		 //console.log("change : " + value);
		 },
		 release : function (value) {
		 console.log("release : " + value);
		 },
		 cancel : function () {
		 console.log("cancel : " + this.value);
		 },*/
		draw: function () {
  
		  // "tron" case
		  if (this.$.data('skin') == 'tron') {
  
			var a   = this.angle(this.cv)  // Angle
			  ,
				sa  = this.startAngle          // Previous start angle
			  ,
				sat = this.startAngle         // Start angle
			  ,
				ea                            // Previous end angle
			  ,
				eat = sat + a                 // End angle
			  ,
				r   = true
  
			this.g.lineWidth = this.lineWidth
  
			this.o.cursor
			&& (sat = eat - 0.3)
			&& (eat = eat + 0.3)
  
			if (this.o.displayPrevious) {
			  ea = this.startAngle + this.angle(this.value)
			  this.o.cursor
			  && (sa = ea - 0.3)
			  && (ea = ea + 0.3)
			  this.g.beginPath()
			  this.g.strokeStyle = this.previousColor
			  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
			  this.g.stroke()
			}
  
			this.g.beginPath()
			this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
			this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
			this.g.stroke()
  
			this.g.lineWidth = 2
			this.g.beginPath()
			this.g.strokeStyle = this.o.fgColor
			this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
			this.g.stroke()
  
			return false
		  }
		}
	  })
	  /* END JQUERY KNOB */



	$('[data-toggle="tooltip"]').tooltip({
		trigger: "hover",
	});

	let sidebarActiveEl = $(`.sidebar li.nav-item a.nav-link[href="${location.href}"]`).addClass('active')
	sidebarActiveEl.parents('ul.nav-treeview').css("display", "block").prev('a[href="#"].nav-link').addClass('active');
	sidebarActiveEl.parents('li.data-menu-open').addClass('menu-open');


	//Date range picker #reservation
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

	/* Date range picker .reservation */
	$('.reservation').daterangepicker({
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

	$('.reservation').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format("DD/MM/YYYY") + ' - ' + picker.endDate.format("DD/MM/YYYY"));
	});

	$('.reservation').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	/* Single Date range picker*/
	$('#single-reservation').daterangepicker({
		"singleDatePicker": true,
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

	$('#single-reservation').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format("DD/MM/YYYY"));
	});

	$('#single-reservation').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	$.extend($.summernote.lang, {
    'tr-TR': {
      font: {
        bold: 'Kalın',
        italic: 'İtalik',
        underline: 'Altı çizili',
        strike: 'Üstü çizili',
        clear: 'Temizle',
        height: 'Satır yüksekliği',
        size: 'Yazı tipi boyutu',
				name: 'Yazı tipi'
      },
      image: {
        image: 'Resim',
        insert: 'Resim ekle',
        resizeFull: 'Orjinal boyut',
        resizeHalf: '1/2 boyut',
        resizeQuarter: '1/4 boyut',
        floatLeft: 'Sola hizala',
        floatRight: 'Sağa hizala',
        floatNone: 'Hizalamayı kaldır',
        dragImageHere: 'Buraya sürükleyin',
        selectFromFiles: 'Dosya seçin',
        url: 'Resim bağlantısı'
      },
      link: {
        link: 'Bağlantı',
        insert: 'Bağlantı ekle',
        unlink: 'Bağlantıyı kaldır',
        edit: 'Bağlantıyı düzenle',
        textToDisplay: 'Görüntülemek için',
        url: 'Bağlantı adresi?'
      },
      video: {
        video: 'Video',
        videoLink: 'Video bağlantısı',
        insert: 'Video ekle',
        url: 'Video bağlantısı?',
        providers: '(YouTube, Vimeo, Vine, Instagram veya DailyMotion)'
      },
      table: {
        table: 'Tablo'
      },
      hr: {
        insert: 'Yatay çizgi ekle'
      },
      style: {
        style: 'Biçim',
        normal: 'Normal',
        blockquote: 'Alıntı',
        pre: 'Önbiçimli',
        h1: 'Başlık 1',
        h2: 'Başlık 2',
        h3: 'Başlık 3',
        h4: 'Başlık 4',
        h5: 'Başlık 5',
        h6: 'Başlık 6'
      },
      lists: {
        unordered: 'Madde işaretli liste',
        ordered: 'Numaralı liste'
      },
      options: {
        help: 'Yardım',
        fullscreen: 'Tam ekran',
        codeview: 'HTML Kodu'
      },
      paragraph: {
        paragraph: 'Paragraf',
        outdent: 'Girintiyi artır',
        indent: 'Girintiyi azalt',
        left: 'Sola hizala',
        center: 'Ortaya hizala',
        right: 'Sağa hizala',
        justify: 'Yasla'
      },
      color: {
        recent: 'Son renk',
        more: 'Daha fazla renk',
        background: 'Arka plan rengi',
        foreground: 'Yazı rengi',
        transparent: 'Seffaflık',
        setTransparent: 'Şeffaflığı ayarla',
        reset: 'Sıfırla',
        resetToDefault: 'Varsayılanlara sıfırla'
      },
      shortcut: {
        shortcuts: 'Kısayollar',
        close: 'Kapat',
        textFormatting: 'Yazı biçimlendirme',
        action: 'Eylem',
        paragraphFormatting: 'Paragraf biçimlendirme',
        documentStyle: 'Biçim'
      },
      history: {
        undo: 'Geri al',
        redo: 'Yeniden yap'
      }

    }
  });
});
// Document ready end