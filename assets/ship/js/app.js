$(document).foundation();

var mobileCheck = {
	ios: function(){
		return navigator.userAgent.match(/iPhone|iPad|iPod/i);
	},
	android: function(){
		return navigator.userAgent.match(/Android/i);
	},
	blackBerry: function(){
		return navigator.userAgent.match(/BB10|Tablet|Mobile/i);
	},
	windows: function(){
		return navigator.userAgent.match(/IEMobile/i);
	},
	smartphone: function(){
		return (window.innerWidth <= 384 && window.innerHeight <= 640);
	},
	tablet: function(){
		return (navigator.userAgent.match(/Tablet|iPad|iPod/i) && window.innerWidth <= 1280 && window.innerHeight <= 800);
	},
	all: function(){
		return navigator.userAgent.match(/Android|BlackBerry|Tablet|Mobile|iPhone|iPad|iPod|Opera Mini|IEMobile/i);
	}
};

var userAgent = window.navigator.userAgent;
var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
var isBadStockAndroid = (function() {
	// Android stock browser test derived from
	// http://stackoverflow.com/questions/24926221/distinguish-android-chrome-from-stock-browser-stock-browsers-user-agent-contai
	var isAndroid = userAgent.indexOf(' Android ') > -1;
	if (!isAndroid) {
		return false;
	}

	var isStockAndroid = userAgent.indexOf('Version/') > -1;
	if (!isStockAndroid) {
		return false;
	}

	var versionNumber = parseFloat((userAgent.match('Android ([0-9.]+)') || [])[1]);
	// anything below 4.4 uses WebKit without *any* viewport support,
	// 4.4 has issues with viewport units within calc()
	return versionNumber <= 4.4;
})();

var Devices = (function($, mobileCheck){

	var $html = $('html');

	var init = function() {
		initDevices();

		$(window).on('resize.device', 
			function(){
				reset();
				// setTimeout(function(){				
				initDevices();
				// }, 500);			
			}
		);
	}

	function initDevices() {
		// console.log(mobileCheck.all());
		if (mobileCheck.ios()) {
			$html.addClass('ios');
		}
		if (mobileCheck.android()) {
			$html.addClass('android');
		}
		if (mobileCheck.blackBerry()) {
			$html.addClass('blackBerry');
		}
		if (mobileCheck.windows()) {
			$html.addClass('windows');
		}
		if (mobileCheck.smartphone()) {			
			$html.addClass('smartphone');
		}
		// if (mobileCheck.tablet) {			
		// 	$html.addClass('tablet');
		// }
		if (mobileCheck.all()) {
			// console.log('all-devices');	
			$html.addClass('all-devices');
		}
		if (isSafari) {
			$html.addClass('safari');
		}
		// if (md.is('iPhone')) {
		// 	$html.addClass('iPhone');
		// }
		// if (md.is('iPad')) {
		// 	$html.addClass('iPad');
		// }

		var b = document.documentElement;
		b.className = b.className.replace('no-js', 'js');
		b.setAttribute("data-useragent",  navigator.userAgent);
		b.setAttribute("data-platform", navigator.platform );
	}

	var reset = function() {
		// console.log('reset');
		$html.removeClass('ios android blackBerry windows smartphone all-devices');
	}

	return {
		init: init,
		reset: reset
	}

})($, mobileCheck);

var Setup = (function(){

	var md;

	var init = function() {
		setupOrientation();
		initDevices();

		setupResponsive();

		$(window).on('resize.setup', 
			$.debounce(250, function(){
				setupOrientation();
				resetDevices();
				initDevices();
				setupResponsive()    
			})
		); 
	}

	function setupOrientation() {
		if ($(window).width() < $(window).height()) {
		   $('body').addClass('portrait');
		   $('body').removeClass('landscape');
		}
		else {
		   $('body').addClass('landscape');
		   $('body').removeClass('portrait');
		}
	}

	function initDevices() {
        var md = new MobileDetect(navigator.userAgent),        
        grade = md.mobileGrade();
        Modernizr.addTest({
            mobile: !!md.mobile(),
            phone: !!md.phone(),
            tablet: !!md.tablet(),
            mobilegradea: grade === 'A'
        });
    }

    function resetDevices() {
    	delete Modernizr.mobile;
    	delete Modernizr.phone;
    	delete Modernizr.tablet;
    	delete Modernizr.mobilegradea;
    	$('html').removeClass("mobile no-mobile phone no-phone tablet no-tablet mobilegradea");
    }

    function setupResponsive() {
    	// if not mobile or not devices    	
    	if ($('html').hasClass('no-mobile')) {    		
    		if (Foundation.MediaQuery.atLeast('large')) {
	    		$('body').removeClass('phone-view tablet-view');
	    		$('html').removeClass('all-devices');
	    	} else if (Foundation.MediaQuery.atLeast('medium')) {	    		
	    		$('body').removeClass('phone-view');
	    		$('body').addClass('tablet-view');
	    		$('html').addClass('all-devices');
	    	} else if (Foundation.MediaQuery.atLeast('small')) {	    		
	    		$('body').removeClass('tablet-view');
	    		$('body').addClass('phone-view');
	    		$('html').addClass('all-devices');
	    	}
    	}    	
    }

	return {
		init: init
	}

})();

var Modal = (function(){

	var init = function() {
		initLabelModal();
		initServiceModal();
		initCreatedModal();
	}

	function initLabelModal() {
		var $modal = $('#labelModal');

		$('.js-label-modal-trigger').on('click', function(e){
			e.preventDefault();
			var url = $(this).attr('href');
			$modal.find('.js-label-image').attr('src', url);
			$modal.find('.js-label-url').attr('src', url);
			$modal.foundation('open');
		});
	}

	function initServiceModal() {
		var $modal = $('#serviceModal');

		$('.js-service-modal-trigger').on('click', function(e){
			e.preventDefault();
			var value = $(this).data('value');
			console.log(value);
			$modal.find('#serviceSelect').val(value);
			$modal.foundation('open');
		});
	}

	function initCreatedModal() {		
		var $modal = $('#shipmentCreatedModal');

		$('.js-confirm-modal-trigger').on('click', function(e){
			e.preventDefault();
			$modal.foundation('open');
		});
	}

	return {
		init: init
	}

})();

var Datepicker = (function() {
	// var $datepicker  = $('.datepicker');

	var init = function() {
		if ($('.datepicker').length > 0) {			

			// var $label = $("<div>", {"class": "datepicker-label"});
			
			// var tommorow = new Date();
			// tommorow.setDate(tommorow.getDate() + 1);

			// var aYearFromNow = new Date();
			// aYearFromNow.setFullYear(aYearFromNow.getFullYear() + 1);

			// $('.datepicker-wrapper').append($label);

			var $datepicker = $('.datepicker').datepicker({
				// maxDate: aYearFromNow,
	    		// minDate: new Date(),
	    		beforeShow: function() {
	    			$selected = $(this);
					$selected.prop('disabled', true);
	    		},
	    		onClose: function() {
	    			$selected = $(this);
					$selected.prop('disabled', false);   				
	    		},
	    		onSelect: function(dateText, obj) {
	    			var suffix = "";
			        switch(obj.selectedDay) {
			            case '1': case '21': case '31': suffix = 'st'; break;
			            case '2': case '22': suffix = 'nd'; break;
			            case '3': case '23': suffix = 'rd'; break;
			            default: suffix = 'th';
			        }
		        	// var value = $.datepicker.formatDate( "D, M dd", new Date( dateText ) );
		        	var value = $.datepicker.formatDate( "DD, M d", new Date( dateText ) );
		        	var date = $(this).datepicker("getDate");

		        	// $(this).datepicker( "option", "altFormat", "DD, M d" );
		        	console.log(dateText);
		        	console.log(value + suffix);
		        	console.log(date);
		        	// $('#arrival-date').next().html(value);
		        		
		        	// if (moment(dateText).isSameOrAfter($('#departure-date').datepicker( "getDate" ))) {	
		        	// 	date.setDate(date.getDate() + 1);		        		
		        	// 	$('#departure-date').datepicker( "option", "minDate", date );		        		
		        		// $(this).datepicker( "setDate", date );

		        	// 	var valueDate = $.datepicker.formatDate( "D, M dd", date );

		        	// 	$('#departure-date').next().html(valueDate);
		        	// } else {
		        	// 	date.setDate(date.getDate() + 1);		        		
		        	// 	$('#departure-date').datepicker( "option", "minDate", date );
		        	// }
		        }
			});
		    

		    // $('#arrival-date').next('.datepicker-label').on('click', function(){
	    	//  	arrivalDate.datepicker( "show" );
		    // });



		    // $(document).on('click', '.datepicker', function(e){
		    // 	e.preventDefault();
		    // 	e.stopPropagation();
		    // 	$(this).trigger('blur');	
		    // });
		}
	}

	return {
		init: init
	}

})();

$(document).ready(function(){
	Devices.init();
	Setup.init();
	//  Object Fit - Polyfill
	objectFitImages(null, {watchMQ: true});

	Modal.init();
	Datepicker.init();

});