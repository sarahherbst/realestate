<script type="text/javascript" defer>

	var immoform = document.getElementById('immobilienobjekt_form'),
		invalid = document.getElementsByClassName('invalid'),
		kd_vorname = document.getElementById('kd_vorname'),
		kd_nachname = document.getElementById('kd_nachname'),
		kd_email = document.getElementById('kd_email'),
		kd_tel = document.getElementById('kd_tel'),
		kd_bemerkung = document.getElementById('kd_bemerkung'),
		kd_datenschutz = document.getElementById('kd_datenschutz'),
		obj_id = document.getElementById('obj_id'),
		obj_objektnr_extern = document.getElementById('obj_objektnr_extern'),
		obj_titel = document.getElementById('obj_titel'),
		obj_art = document.getElementById('obj_art'),
		obj_unterart = document.getElementById('obj_unterart'),
		obj_vermarktungsart = document.getElementById('obj_vermarktungsart'),
		submit = document.getElementById('sendform'),
		modal = document.getElementById('immo_kontakformular'),
		modalbody = document.getElementById('immobilienobjekt_form_body'),
		honey = document.getElementById('kd_firstname'),
		alert;

	function sendData(data, form) {

		var dataRequest = new XMLHttpRequest();
		console.log('sendData ausgeführt');
	
		function readData(evt) {
			console.log('readData ausgeführt');
			var objectRequest = evt.currentTarget;

			if (objectRequest.readyState === 4 && objectRequest.status === 200) {
				var json = JSON.parse(objectRequest.responseText),
					error = json.error,
					alert = json.message

				// Modal konfigurieren
				setTimeout(function(){ modalbody.innerHTML = alert; }, 2000);

				setTimeout(function(){ location.reload(true); }, 5000);
			}
		}

		dataRequest.addEventListener('readystatechange', readData);
		//erst öffnen
		dataRequest.open('POST', 'immokontakt.php', true);
		// dann Header setten
		dataRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		// und erst jetzt senden
		dataRequest.send(data);
	}

    function assessData() {

    	var fail = 0;

		// Bestehendes Feedback löschen
		var feedback = document.getElementsByClassName('feedback');
		while(feedback[0]) {
			feedback[0].parentNode.removeChild(feedback[0]);
		}

		var invalid = document.getElementsByClassName('invalid');
		for (i = 0; i < invalid.length; i += 1) {
			invalid[i].classList.remove('invalid');
		};

		// Felder auswerten
		if (kd_vorname) {
			if (kd_vorname.value === '' || !kd_vorname.validity.valid) {
				fail += 1;
				kd_vorname.classList.add('invalid');
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte geben Sie einen gültigen Vornamen an.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('immobilienobjekt_form_body').appendChild(feedback);
			}
		}

		if (kd_nachname) {
			if (kd_nachname.value === '' || !kd_nachname.validity.valid) {
				fail += 1;
				kd_nachname.classList.add('invalid');
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte geben Sie einen gültigen Nachnamen an.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('immobilienobjekt_form_body').appendChild(feedback);
			}
		}

		if (kd_email) {
			if (kd_email.value === '' || !kd_email.validity.valid) {
				fail += 1;
				kd_email.classList.add('invalid');
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte geben Sie eine gültige E-Mail-Adresse an.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('immobilienobjekt_form_body').appendChild(feedback);
			}
		}

		if (!kd_datenschutz.checked) {
				fail += 1;
				kd_datenschutz.classList.add('invalid');
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte akzeptieren Sie unsere Datenschutzbestimmungen.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('immobilienobjekt_form_body').appendChild(feedback);
		}

		if(fail == 0) {
			formData = 	'&kd_vorname=' + kd_vorname.value + 
						'&kd_nachname=' + kd_nachname.value + 
						'&kd_email=' + kd_email.value + 
						'&kd_tel=' + kd_tel.value + 
						'&kd_bemerkung=' + kd_bemerkung.value +
						'&obj_id=' + obj_id.value +
						'&obj_objektnr_extern=' + obj_objektnr_extern.value +
						'&obj_titel=' + obj_titel.value +
						'&obj_art=' + obj_art.value +
						'&obj_unterart=' + obj_unterart.value +
						'&honey=' + honey.value +
						'&obj_vermarktungsart=' + obj_vermarktungsart.value;

			sendData(formData, immoform);
			setTimeout(function(){
				modalbody.innerHTML = '<div class="row"><div class="col-12"><div class="spinner"></div><h3 class="font-weight-light text-center text-primary mb-3">Senden ...</h3></div></div>';
			}, 1000);
		}

    };

	submit.addEventListener('click', assessData);

    // normale Verarbeitung durch Reload der Seite verhindern
	if (immoform.addEventListener) {
		immoform.addEventListener('submit', function(evt) {
			evt.preventDefault();
		}, true);
	} else {
		immoform.attachEvent('onsubmit', function(evt){
			evt.preventDefault();
		});
	}

	function removeInvalidInput(evt) {
		var selectItem = evt.currentTarget;
		if (selectItem.value !== '') {
			if (selectItem.validity.valid) {
				if (selectItem.classList.contains('invalid')) {
					selectItem.classList.remove('invalid');
				}
			}
		}
	}

	for (i = 0; i < document.getElementsByTagName('input').length; i += 1) {
		document.getElementsByTagName('input')[i].addEventListener('focusout', removeInvalidInput);
	}

</script>