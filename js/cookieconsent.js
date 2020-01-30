window.addEventListener('load', function(){
	window.cookieconsent.initialise({
		'palette': {
			'popup': {
				'background': '#edeff5',
				'text'		: '#838391'
			},
			'button': {
				'background': '#0066b3'
			}
		},
		'theme'			: 'edgeless',
		'position'		: 'top',
		'static'		: true,
 		'dismissOnScroll' : '150', 
		onPopupOpen 	: function () {pushDown(this.element.clientHeight);},
 		onPopupClose 	: function () {pushDown(0);},
		'type'			: 'opt-in',
		'content': {
			'message'	: 'Um unsere Webseite für Sie optimal zu gestalten und fortlaufend verbessern zu können, verwenden wir u. a. Cookies, Tracking- und Analyse-Dienste. Durch die weitere Nutzung der Webseite bzw. durch Bestätigung dieses Hinweises stimmen Sie der Verwendung von o. g. Diensten zu. Weitere Informationen dazu erhalten Sie in unserer Datenschutzerklärung.',
			'dismiss'	: 'Ok!',
			'deny'		: 'Nein.',
			'allow'		: 'Ok!',
			'policy'	: 'Datenschutzerklärung',
			'link'		: 'Mehr erfahren',
			'href'		: 'datenschutz.php'
		},

		onInitialise: function (status) {
			var type 		= this.options.type;
			var didConsent 	= this.hasConsented();
			if (type == 'opt-in' && didConsent) {
				// enable cookies
				enableCookies();
				console.log('Cookies wurden aktiviert.');
			}
			if (type == 'opt-out' && !didConsent) {
				// disable cookies
				disableCookies();
				console.log('Cookies wurden deaktiviert.');
			}
		},

		onStatusChange: function(status, chosenBefore) {
			var type 		= this.options.type;
			var didConsent 	= this.hasConsented();
			if (type == 'opt-in' && didConsent) {
				// enable cookies
				enableCookies();
				console.log('Cookies wurden aktiviert.');
			}
			if (type == 'opt-out' && !didConsent) {
				// disable cookies
				disableCookies();
				console.log('Cookies wurden deaktiviert.');
			}
		},

		onRevokeChoice: function() {
			var type 		= this.options.type;
			if (type == 'opt-in') {
				// disable cookies
				disableCookies();
				console.log('Cookies wurden deaktiviert.');
			}

			if (type == 'opt-out') {
				// enable cookies
				enableCookies();
				console.log('Cookies wurden aktiviert.');
			}
		}
	});
	function pushDown(height) {
		var navbar = document.getElementsByClassName('mobilemenu')[0];
		navbar.style.transition = 'all 1s';
		navbar.style.marginTop 	= height + 'px';
	};
});

window.onscroll = function() {
	if (document.getElementsByClassName('cc-grower')[0] && document.getElementsByClassName('mobilemenu')[0]) {
		var navbar 			= document.getElementsByClassName('mobilemenu')[0];
		var cookieconsent 	= document.getElementsByClassName('cc-grower')[0];

		if (document.body.scrollTop > cookieconsent.clientHeight || document.documentElement.scrollTop > cookieconsent.clientHeight) {
			navbar.style.transition = 'all 1s';
			navbar.style.marginTop 	= '0';
		} else {
			navbar.style.transition = 'all 1s';
			navbar.style.marginTop 	= cookieconsent.clientHeight + 'px';
			
		}
	}
};

$(document).ready(function() {
	$('.cc-allow').click(function(){
		enableCookies();
	});
});

$(document).ready(function() {
	$('.cc-revoke').css('fontSize', '14px');
});

$(window).resize(function() {
	if (document.getElementsByClassName('cc-grower')[0]) {
		var navbar = document.getElementsByClassName('mobilemenu')[0];
		var cookieconsent 	= document.getElementsByClassName('cc-grower')[0];
		var ccWindow 		= document.getElementsByClassName('cc-window')[0];
		var height 			= ccWindow.clientHeight;

		cookieconsent.style.transition 	= 'all 1s';
		cookieconsent.style.maxHeight 	= height + 'px';

		navbar.style.transition = 'all 1s';
		navbar.style.marginTop 	= height + 'px';
	}
});