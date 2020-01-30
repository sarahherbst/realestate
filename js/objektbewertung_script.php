<script type="text/javascript" defer>

	console.log('YES');

	var zurueck = document.getElementById('im_zurueck'),
		weiter = document.getElementById('im_weiter'),
		immoform = document.getElementById('immobewertung'),
		immotyp = document.getElementsByName('im_immotyp'),
		invalid = document.getElementsByClassName('invalid'),
		processBtn = document.getElementsByClassName('btn-circle'),
		responseDiv = document.getElementById('responsetitle'),
		alertDiv = document.getElementById('alert'),
		modal = document.getElementById('modalImmobilienbewertung'),
		max_preis,
		schrittzaehler = 1;

	// Daten auswerten lassen
	function validateHouseData(data, form) {

		var dataRequest = new XMLHttpRequest();
	
		function readHouseData(evt) {
			var objectRequest = evt.currentTarget;

			console.log(objectRequest);

			if (objectRequest.readyState === 4 && objectRequest.status === 200) {
				var json = JSON.parse(objectRequest.responseText),
					error = json.error,
					alert = json.message

				max_preis = json.max_preis;

				if (error === 1) {
					responseTitle = 'Hoppla!';
				} else {
					responseTitle = 'Ihre Bewertung';
				}

				// Form zurücksetzen
				responseDiv.innerHTML 	= responseTitle;
				alertDiv.innerHTML 	= alert;

			}
		}

		dataRequest.addEventListener('readystatechange', readHouseData);
		//erst öffnen
		dataRequest.open('POST', '../../../objektbewertung_validate.php', true);
		// dann Header setten
		dataRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		// und erst jetzt senden
		dataRequest.send(data);
	}

	// Daten verschicken
	function sendHouseData(data, form) {

		var dataRequest = new XMLHttpRequest();

		console.log('sendHouseData ausgeführt');
		console.log(data);
		console.log(form);
	
		function readHouseData(evt) {
			var objectRequest = evt.currentTarget;

			console.log(objectRequest);

			if (objectRequest.readyState === 4 && objectRequest.status === 200) {
				var json = JSON.parse(objectRequest.responseText),
					error = json.error,
					alert = json.message

				console.log(error);
				console.log(alert);

				if (error === 1) {
					responseTitle = 'Hoppla!';
				} else {
					responseTitle = 'Danke!';
				}

				// Modal konfigurieren
				modal.querySelector('.modal-title').innerHTML = responseTitle;
				modal.querySelector('.modal-text').innerHTML = alert;
			}

			// Form zurücksetzen
			immoform.reset();
			document.getElementById('step-'+schrittzaehler).classList.add('d-none');
			schrittzaehler = 1;
			document.getElementById('step-'+schrittzaehler).classList.remove('d-none');
			for (i = 0; i < processBtn.length; i += 1) {
				processBtn[i].classList.remove('btn-primary');
			}
			processBtn[schrittzaehler-1].classList.add('btn-primary');
			if(schrittzaehler <= 3) {
				weiter.innerHTML = 'weiter';
			}
			document.getElementById('showhaustyp').innerHTML = '';
		}

		dataRequest.addEventListener('readystatechange', readHouseData);
		//erst öffnen
		dataRequest.open('POST', '../../../objektbewertung_validate.php', true);
		// dann Header setten
		dataRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		// und erst jetzt senden
		dataRequest.send(data);
	}

    function assessHouseNext() {

    	// Formfields in Variablen packen
    	var		alert,
    			haustypHaus = document.getElementsByName('im_haustyp_haus'),
				haustypWohnung = document.getElementsByName('im_haustyp_wohnung'),
				wohnflaeche = document.getElementById('im_wohnflaeche'),
				grundstuecksflaeche = document.getElementById('im_grundstuecksflaeche'),
				baujahr = document.getElementById('im_baujahr'),
				strasse = document.getElementById('im_strasse'),
				plz = document.getElementById('im_plz'),
				ort = document.getElementById('im_ort'),
				vorname = document.getElementById('im_vorname'),
				nachname = document.getElementById('im_nachname'),
				email = document.getElementById('im_email'),
				tel = document.getElementById('im_tel'),
				kontakt = document.getElementById('im_kontakt'),
				datenschutz = document.getElementById('im_datenschutz');

		// Feedback löschen
		var feedback = document.getElementsByClassName('feedback');
		while(feedback.length) {
			feedback[0].parentNode.removeChild(feedback[0]);
		}
		var invalid = document.getElementsByClassName('invalid');
		while (invalid.length) {
			invalid[0].classList.remove('invalid');
		};

		// Bei der ersten Seite ausgefüllte Felder prüfen
    	if (schrittzaehler == 1) {
    		var failempty = false;

    		modal.querySelector('.modal-title').innerHTML = '';
			modal.querySelector('.modal-text').innerHTML = '<div class="row"><div class="col-12"><div class="spinner"></div><h3 class="font-weight-light text-center text-primary mb-3">Einen Moment ...</h3></div></div>';

			zurueck.classList.remove('disabled');


    		if (immotyp) {
				if (immotyp[0].checked || immotyp[1].checked || immotyp[2].checked) {
					if (immotyp[1].checked) {
						grundstuecksflaeche.classList.add('d-none');
					} else {
						grundstuecksflaeche.classList.remove('d-none');
					}
				} else {
					failempty = true;
					feedback = document.createElement('DIV');
					feedbackText = document.createTextNode('Bitte wählen Sie eine Immobilienart aus.');
					feedback.appendChild(feedbackText);
					feedback.classList.add('feedback');
					document.getElementById('step-1').appendChild(feedback);
				}
			};

			if (haustypHaus[0]) {
				if(haustypHaus[0].checked === false && haustypHaus[1].checked === false && haustypHaus[2].checked === false) {
					failempty = true;
					feedback = document.createElement('DIV');
					feedbackText = document.createTextNode('Bitte wählen Sie einen Haustyp aus.');
					feedback.appendChild(feedbackText);
					feedback.classList.add('feedback');
					document.getElementById('step-1').appendChild(feedback);
				}
			};

    		if (haustypWohnung[0]) {
				if (haustypWohnung[0].checked === false && haustypWohnung[1].checked === false && haustypWohnung[2].checked === false) {
					failempty = true;
					feedback = document.createElement('DIV');
					feedbackText = document.createTextNode('Bitte wählen Sie einen Wohnungstyp aus.');
					feedback.appendChild(feedbackText);
					feedback.classList.add('feedback');
					document.getElementById('step-1').appendChild(feedback);
				}
			};

			if (failempty == false) {
				for (i = 0; i < processBtn.length; i += 1) {
					processBtn[i].classList.remove('btn-primary');
				}
				zurueck.classList.remove('d-none');
				processBtn[schrittzaehler].classList.add('btn-primary');
				document.getElementById('step-'+schrittzaehler).classList.add('d-none');
				schrittzaehler += 1;
				document.getElementById('step-'+schrittzaehler).classList.remove('d-none');
				processBtn[0].scrollIntoView();
			} 

		} else if (schrittzaehler == 2) {
			var fail = 0,
				failempty = false;

			if (wohnflaeche) {
				if (wohnflaeche.value === '') {
					failempty = true;
					wohnflaeche.classList.add('invalid');
				} else {
					if (!wohnflaeche.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie einen Wert der Wohnfläche in ganzen Zahlen bis maximal 10.000 m² an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-2').appendChild(feedback);
						wohnflaeche.classList.add('invalid');
					}
				}
			}

			if (immotyp[1].checked === false) {
				if (grundstuecksflaeche) {
					if (grundstuecksflaeche.value === '') {
						failempty = true;
						grundstuecksflaeche.classList.add('invalid');
					} else {
						if (!grundstuecksflaeche.validity.valid) {
							fail += 1;
							feedback = document.createElement('DIV');
							feedbackText = document.createTextNode('Bitte geben Sie einen Wert der Grundstücksfläche in ganzen Zahlen bis maximal 10.000 m² an.');
							feedback.appendChild(feedbackText);
							feedback.classList.add('feedback');
							document.getElementById('step-2').appendChild(feedback);
							grundstuecksflaeche.classList.add('invalid');
						}
					}
				}
			}

			if (baujahr) {
				if (baujahr.value === '') {
					failempty = true;
					baujahr.classList.add('invalid');
				} else {
					if (!baujahr.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie ein Baujahr von 1800 bis zum aktuellen Jahr an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-2').appendChild(feedback);
						baujahr.classList.add('invalid');
					}
				}
			}

			if (fail == 0 && failempty == false) {
				for (i = 0; i < processBtn.length; i += 1) {
					processBtn[i].classList.remove('btn-primary');
				}
				processBtn[schrittzaehler].classList.add('btn-primary');
				document.getElementById('step-'+schrittzaehler).classList.add('d-none');
				schrittzaehler += 1;
				document.getElementById('step-'+schrittzaehler).classList.remove('d-none');
				processBtn[0].scrollIntoView();
			} else if (failempty == true) {
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Sie haben noch nicht alle Felder ausgefüllt.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('step-2').appendChild(feedback);
			}

		} else if (schrittzaehler == 3) {
			var fail = 0
				failempty = false;

			if (strasse) {
				if (strasse.value === '') {
					failempty = true;
					strasse.classList.add('invalid');
				} else {
					if (!strasse.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie eine Straße mit Hausnummer an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-3').appendChild(feedback);
						strasse.classList.add('invalid');
					}
				}
			}

			if (plz) {
				if (plz.value === '') {
					failempty = true;
					plz.classList.add('invalid');
				} else {
					if (!plz.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie eine gültige Postleitzahl an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-3').appendChild(feedback);
						plz.classList.add('invalid');
					}
				}
			}

			if (ort) {
				if (ort.value === '') {
					failempty = true;
					ort.classList.add('invalid');
				} else {
					if (!ort.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie einen Ort an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-3').appendChild(feedback);
						ort.classList.add('invalid');
					}
				}
			}

			if (fail == 0 && failempty == false) {
				document.getElementsByClassName('overlay')[0].classList.remove('d-none');
				var immotypVal = immoform.querySelector('input[name="im_immotyp"]:checked').value;

				if(haustypHaus[0]) {
					var haustypHausVal = immoform.querySelector('input[name="im_haustyp_haus"]:checked').value;
				} else {
					var haustypHausVal = '';
				}

				if(haustypWohnung[0]) {
					var haustypWohnungVal = immoform.querySelector('input[name="im_haustyp_wohnung"]:checked').value;
				} else {
					var haustypWohnungVal = '';
				}

				formData = 	'&im_immotyp=' + immotypVal + 
							'&im_haustyp_haus=' + haustypHausVal + 
							'&im_haustyp_wohnung=' + haustypWohnungVal + 
							'&im_wohnflaeche=' + wohnflaeche.value + 
							'&im_grundstuecksflaeche=' + grundstuecksflaeche.value + 
							'&im_baujahr=' + baujahr.value + 
							'&im_strasse=' + strasse.value + 
							'&im_plz=' + plz.value + 
							'&im_ort=' + ort.value +
							'&whichfunction=' + 'validate';

				validateHouseData(formData, immoform);

				setTimeout(function(){
					document.getElementsByClassName('overlay')[0].classList.add('d-none');				
					for (i = 0; i < processBtn.length; i += 1) {
						processBtn[i].classList.remove('btn-primary');
					}
					processBtn[schrittzaehler].classList.add('btn-primary');
					document.getElementById('step-'+schrittzaehler).classList.add('d-none');
					schrittzaehler += 1;
					document.getElementById('step-'+schrittzaehler).classList.remove('d-none');
					if(max_preis == null || max_preis == undefined) {
						weiter.innerHTML = 'Kontaktanfrage abschicken!';
					} else {
						weiter.innerHTML = 'Ergebnis erfahren!';
					}
					processBtn[0].scrollIntoView();
				}, 5000);
			} else if (failempty == true) {
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Sie haben noch nicht alle Felder ausgefüllt.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('step-3').appendChild(feedback);
			}

		} else if (schrittzaehler == 4) {
			var fail = 0
				failempty = false;

			 if (vorname) {
				if (vorname.value === '') {
					failempty = true;
					vorname.classList.add('invalid');
				} else {
					if (!vorname.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie einen Vornamen an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-4').appendChild(feedback);
						vorname.classList.add('invalid');
					}
				}
			}

			if (nachname) {
				if (nachname.value === '') {
					failempty = true;
					nachname.classList.add('invalid');
				} else {
					if (!nachname.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie einen Nachnamen an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-4').appendChild(feedback);
						nachname.classList.add('invalid');
					}
				}
			}

			if (email) {
				if (email.value === '') {
					failempty = true;
					email.classList.add('invalid');
				} else {
					if (!email.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie eine gültige E-Mail-Adresse an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-4').appendChild(feedback);
						email.classList.add('invalid');
					}
				}
			}

			if (tel) {
				if (tel.value === '') {
					if (!tel.validity.valid) {
						fail += 1;
						feedback = document.createElement('DIV');
						feedbackText = document.createTextNode('Bitte geben Sie eine gültige Telefonnummer an.');
						feedback.appendChild(feedbackText);
						feedback.classList.add('feedback');
						document.getElementById('step-4').appendChild(feedback);
						tel.classList.add('invalid');
					}
				}
			}

			if(kontakt.checked) {
				var kontakt = 'Ja';
			} else {
				var kontakt = 'Nein';
			}

			if(datenschutz.checked === false) {
				fail += 1;
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte akzeptieren Sie unsere Datenschutzbestimmungen.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('step-4').appendChild(feedback);
				datenschutz.classList.add('invalid');
			}

			if(fail == 0 && failempty == false) {


				var immotypVal = immoform.querySelector('input[name="im_immotyp"]:checked').value;

				if(haustypHaus[0]) {
					var haustypHausVal = immoform.querySelector('input[name="im_haustyp_haus"]:checked').value;
				} else {
					var haustypHausVal = '';
				}

				if(haustypWohnung[0]) {
					var haustypWohnungVal = immoform.querySelector('input[name="im_haustyp_wohnung"]:checked').value;
				} else {
					var haustypWohnungVal = '';
				}

				if(max_preis == null || max_preis == undefined) {
					formData = 	'&im_immotyp=' + immotypVal + 
							'&im_haustyp_haus=' + haustypHausVal + 
							'&im_haustyp_wohnung=' + haustypWohnungVal + 
							'&im_wohnflaeche=' + wohnflaeche.value + 
							'&im_grundstuecksflaeche=' + grundstuecksflaeche.value + 
							'&im_baujahr=' + baujahr.value + 
							'&im_strasse=' + strasse.value + 
							'&im_plz=' + plz.value + 
							'&im_ort=' + ort.value +
							'&im_vorname=' + vorname.value +
							'&im_nachname=' + nachname.value +
							'&im_email=' + email.value +
							'&im_tel=' + tel.value +
							'&im_kontakt=' + kontakt;
				} else {
					formData = 	'&im_immotyp=' + immotypVal + 
							'&im_haustyp_haus=' + haustypHausVal + 
							'&im_haustyp_wohnung=' + haustypWohnungVal + 
							'&im_wohnflaeche=' + wohnflaeche.value + 
							'&im_grundstuecksflaeche=' + grundstuecksflaeche.value + 
							'&im_baujahr=' + baujahr.value + 
							'&im_strasse=' + strasse.value + 
							'&im_plz=' + plz.value + 
							'&im_ort=' + ort.value +
							'&im_vorname=' + vorname.value +
							'&im_nachname=' + nachname.value +
							'&im_email=' + email.value +
							'&im_tel=' + tel.value +
							'&im_kontakt=' + kontakt +
							'&max_preis=' + max_preis;

				}

							
				// Modal aufrufen
				$('#modalImmobilienbewertung').modal('toggle');

				sendHouseData(formData, immoform);
			} else if (failempty == true) {
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Sie haben noch nicht alle Felder ausgefüllt.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('feedback');
				document.getElementById('step-4').appendChild(feedback);
			}

		}
    };

    function assessHouseBack() {
    	if (schrittzaehler > 1) {
	    	var	processBtn = document.getElementsByClassName('btn-circle');
			document.getElementById('step-'+schrittzaehler).classList.add('d-none');
			schrittzaehler -= 1;
			document.getElementById('step-'+schrittzaehler).classList.remove('d-none');
			for (i = 0; i < processBtn.length; i += 1) {
				processBtn[i].classList.remove('btn-primary');
			}
			processBtn[schrittzaehler-1].classList.add('btn-primary');
			if(schrittzaehler <= 3) {
				weiter.innerHTML = 'weiter';
			}
			if(schrittzaehler < 2) {
				zurueck.classList.add('disabled');
			}
			// Feedback löschen
			var feedback = document.getElementsByClassName('feedback');
			while(feedback[0]) {
				feedback[0].parentNode.removeChild(feedback[0]);
			}
		}
	}

	// Weiter und Zurück-Events hinzufügen
	weiter.addEventListener('click', assessHouseNext);
    zurueck.addEventListener('click', assessHouseBack);

    // normale Verarbeitung durch Reload der Seite verhindern
	if (weiter.parentElement.addEventListener) {
		weiter.parentElement.addEventListener('submit', function(evt) {
			evt.preventDefault();
		}, true);
	} else {
		weiter.parentElement.attachEvent('onsubmit', function(evt){
			evt.preventDefault();
		});
	}

	if (zurueck.parentElement.addEventListener) {
		zurueck.parentElement.addEventListener('submit', function(evt) {
			evt.preventDefault();
		}, true);
	} else {
		zurueck.parentElement.attachEvent('onsubmit', function(evt){
			evt.preventDefault();
		});
	}

	// Je nach Auswahl des Immobilientyps weitere Auswahlmöglichkeiten anzeigen
	function showHaustyp(evt) {
		var immoValue = evt.currentTarget.value;

		// Feedback löschen
		var feedback = document.getElementsByClassName('feedback');
		while(feedback[0]) {
			feedback[0].parentNode.removeChild(feedback[0]);
		}

		if (immoValue == 'Einfamilienhaus') {
			document.getElementById('showhaustyp').innerHTML = '<h5 class="card-title text-center mt-5">Haustyp</h5><div class="form-row align-items-center mt-3"><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="freistehend" name="im_haustyp_haus" id="freistehend" required><label class="custom-control-label text-center" for="freistehend">freistehend</label></div></div><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="Doppelhaushälfte" name="im_haustyp_haus" id="doppelhaushaelfte" required><label class="custom-control-label text-center" for="doppelhaushaelfte">Doppelhaushälfte</label></div></div><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="Reihenhaus" name="im_haustyp_haus" id="reihenhaus" required><label class="custom-control-label text-center" for="reihenhaus">Reihenhaus</label></div></div></div>';
				// Feedback löschen
				var feedback = document.getElementsByClassName('feedback');
				while(feedback[0]) {
					feedback[0].parentNode.removeChild(feedback[0]);
				}
		} else if (immoValue == 'Wohnung') {
			document.getElementById('showhaustyp').innerHTML = '<h5 class="card-title text-center mt-5">Wohnungstyp</h5><div class="form-row align-items-center mt-3"><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="Erdgeschoss" name="im_haustyp_wohnung" id="erdgeschoss" required><label class="custom-control-label text-center" for="erdgeschoss">Erdgeschoss</label></div></div><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="Etagenwohnung" name="im_haustyp_wohnung" id="etagenwohnung" required><label class="custom-control-label text-center" for="etagenwohnung">Etagenwohnung</label></div></div><div class="col text-center my-2"><div class="custom-control custom-radio img-checkbox"><input type="radio" class="custom-control-input" value="Dachgeschoss" name="im_haustyp_wohnung" id="dachgeschoss" required><label class="custom-control-label text-center" for="dachgeschoss">Dachgeschoss</label></div></div></div>';
		} else {
			document.getElementById('showhaustyp').innerHTML = '';
		}
	};

	for (i = 0; i < immotyp.length; i += 1) {
		immotyp[i].addEventListener('click', showHaustyp);
	}

	// Invalid-Klasse entfernen
	function removeInvalidSelect(evt) {
		var selectItem = evt.currentTarget;
		if (selectItem.value !== '') {
			if (selectItem.classList.contains('invalid')) {
				selectItem.classList.remove('invalid');
			}
		}
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

	for (i = 0; i < document.getElementsByTagName('select').length; i += 1) {
		document.getElementsByTagName('select')[i].addEventListener('input', removeInvalidSelect);
	}

	for (i = 0; i < document.getElementsByTagName('input').length; i += 1) {
		document.getElementsByTagName('input')[i].addEventListener('focusout', removeInvalidInput);
	}

</script>