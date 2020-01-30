<script type="text/javascript">
	// GoogleAnalytics
	var gaProperty = 'UA-138975839-1';
	var ga_disableStr = 'ga-disable-' + gaProperty;
	if (document.cookie.indexOf(ga_disableStr + '=true') > -1) {
		window[ga_disableStr] = true;
	}
	// FacebookPixel
	var fpProperty = 'mde-service';
	var fp_disableStr = 'fp-disable-' + fpProperty;
    if (document.cookie.indexOf(fp_disableStr + '=true') > -1) {
		window[fp_disableStr] = true;
    }

	// Opt-out function
	function disableCookies() {
		// GoogleAnalytics
		document.cookie 		= ga_disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
		window[ga_disableStr] 		= true;

		// FacebookPixel
		document.cookie 		= fp_disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
		window[fp_disableStr] 	= true;
		localStorage.setItem('opt-out', 'true');

		var cookies = document.cookie.split(";");
		for (var i = 0; i < cookies.length; i++) {
			var cookie = cookies[i];
			var eqPos = cookie.indexOf("=");
			var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
			document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
		}
		
		location.reload();
	}

	// enable Cookies
	function enableCookies() {
		// GoogleAnalytics
		window.dataLayer = window.dataLayer || [];
		function gtag() {
			dataLayer.push(arguments)
		}

		gtag('js', new Date());
		gtag('config', 'UA-138975839-1', { 'anonymize_ip': true });
		window[ga_disableStr] = false;
		
		// FacebookPixel
		!function(f,b,e,v,n,t,s) {
	    	if(f.fbq)return;n=f.fbq=function(){
	    		n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)
	    	};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)
		}
		(window,document,'script', 'https://connect.facebook.net/en_US/fbevents.js');

		fbq('init', '574523779703333');
		fbq('track', 'PageView');
		window[fp_disableStr] = false;

		localStorage.removeItem('opt-out');
	}
</script>

<noscript>
	<img height="1" width="1" src="https://www.facebook.com/tr?id=574523779703333&ev=PageView&noscript=1"/>
</noscript>


<link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js" defer></script>
<script type="text/javascript" src="/js/cookieconsent.js" defer></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-138975839-1"></script>
