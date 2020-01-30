var kfForm 	= document.getElementById('kaeuferfinder').querySelector('form'),
	kfBtn 	= kfForm.querySelector('.btn-submit');

function sendBuyerData(data) {
	console.log('sendBuyerData ausgeführt');

	var dataRequest = new XMLHttpRequest(),
		form = document.getElementById('kaeuferfinder').querySelector('form'),
		formContainer = form.parentElement;

	function readBuyerData(evt) {
		var objectRequest = evt.currentTarget;

		if (objectRequest.readyState === 4 && objectRequest.status === 200) {
			var json = JSON.parse(objectRequest.responseText),
				error = json.error,
				alert = json.message,
				kaeufer = json.kaeufer,
				resultContainer = document.getElementById('kf-result');
				resultCardContainer = resultContainer.querySelector('.kf-resultcards');
				overlay = document.getElementById('kaeuferfinder').querySelector('form').querySelector('.overlay');

			if (error === 1) {
				setTimeout(function() {
					resultCardContainer.innerHTML = '';
					resultContainer.querySelector('.kf-result-title').innerHTML = 'Hoppla!';
					var resultCard = document.createElement('p');
					resultCardContainer.appendChild(resultCard);
					resultCard.innerHTML = alert;
					resultContainer.classList.remove('d-none');
					overlay.classList.add('d-none');
				}, 500);
			} else {
				setTimeout(function() {
					resultContainer.querySelector('.kf-result-title').innerHTML = alert;
					resultCardContainer.innerHTML = '';
					var i = 0;
					for (i = 0; i < kaeufer.length; i++) {
						var resultCard = document.createElement('div');
						resultCardContainer.appendChild(resultCard);
						resultCard.innerHTML = kaeufer[i];
					}
					resultContainer.classList.remove('d-none');
					overlay.classList.add('d-none');
					$('html, body').animate({
			            scrollTop: $(resultContainer).offset().top - 70
			        }, 750);
				}, 1000);
			}
		}
	}

	dataRequest.addEventListener('readystatechange', readBuyerData);
	dataRequest.open('POST', '../../../kaeuferfinder_validate.php', true);
	dataRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	dataRequest.send(data);
}

function validateBuyerData(evt) {
	var fail = 0,
		feedback,
		feedbackText,
		invalidFeedback,
		form = document.getElementById('kaeuferfinder').querySelector('form'),
		formContainerId = form.parentElement.id,
		overlay = form.querySelector('.overlay'),
		formData;

	for(i = 0; i < form.length; i++) {
		var field = form.elements[i];
		if(field.hasAttribute('required') && (field === '' || !field.checkValidity())) {
			fail += 1;

			field.classList.add('is-invalid');
			if(!field.parentElement.querySelector('.invalid-feedback')) {
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode(field.getAttribute('title'));
				feedback.appendChild(feedbackText);
				feedback.classList.add('invalid-feedback');
				field.parentElement.appendChild(feedback);
			}
		} else {
			field.classList.remove('is-invalid');
			invalidFeedback = field.parentElement.querySelector('.invalid-feedback');
			if(invalidFeedback) {
				invalidFeedback.parentNode.removeChild(invalidFeedback);
			}
		}
	}

	if (fail === 0) {
		for(i = 0; i < form.length; i++) {
			var field = form.elements[i],
				fieldName = form.elements[i].name,
				fieldVal = form.elements[i].value;

			if(field.name !== 'submit' && i !== 0) {
				formData = formData + '&' + fieldName + '=' + fieldVal;
			} else if (field.name !== 'submit' && i == 0) {
				formData = fieldName + '=' + fieldVal;
			}
		}

		console.log(formData);
		sendBuyerData(formData);
		overlay.classList.remove('d-none');
	}
}

kfBtn.addEventListener('click', validateBuyerData);

if (kfBtn.parentElement.addEventListener) {
	kfBtn.parentElement.addEventListener('submit', function(evt) {
		evt.preventDefault();
	}, true);
} else {
	kfBtn.parentElement.attachEvent('onsubmit', function(evt){
		evt.preventDefault();
	});
}

// wenn Modal aufgerufen wird
$('#kaeuferfinderModal').on('show.bs.modal', function (event) {
	console.log('Käuferfinder Modal show');
	var button 		= $(event.relatedTarget); 
	var id 			= button.data('id');
	var titel 		= button.data('titel');
	var modal 		= $(this);

	// Inputs befüllen
	modal.find('input[name=kf_id]').val(id);
	modal.find('.modal-title').html(titel);
});

// wenn Submit-Button im Modal geklickt wird
$('#kaeuferfinderModal').on('click','#kf_send', function(event) {
	var modal 	= document.getElementById('kaeuferfinderModal'),
		form 	= document.getElementById('kaeuferfinder-kontakt'),
		fail	= 0,
		feedback,
		feedbackText,
		formData;

	for(i = 0; i < form.length; i++) {
		var field = form.elements[i];
		if(field.hasAttribute('required') && (field === '' || !field.checkValidity())) {
			fail += 1;

			field.classList.add('is-invalid');
			if(!field.parentElement.querySelector('.invalid-feedback')) {
				feedback = document.createElement('DIV');
				feedbackText = document.createTextNode('Bitte füllen Sie das Feld aus.');
				feedback.appendChild(feedbackText);
				feedback.classList.add('invalid-feedback');
				field.parentElement.appendChild(feedback);
			}
		} else {
			field.classList.remove('is-invalid');
			invalidFeedback = field.parentElement.querySelector('.invalid-feedback');
			if(invalidFeedback) {
				invalidFeedback.parentNode.removeChild(invalidFeedback);
			}
		}
	}

	if (fail === 0) {
		for(i = 0; i < form.length; i++) {
			var field = form.elements[i],
				fieldName = form.elements[i].name,
				fieldVal = form.elements[i].value;

			if(field.name !== 'submit' && i !== 0) {
				formData = formData + '&' + fieldName + '=' + fieldVal;
			} else if (field.name !== 'submit' && i == 0) {
				formData = fieldName + '=' + fieldVal;
			}
		}
		
		modal.querySelector('.modal-text').innerHTML = '<div class="row"><div class="col-12"><div class="spinner"></div><h3 class="font-weight-light text-center text-primary mb-3">Senden ...</h3></div></div>';
		form.classList.add('d-none');
		var dataRequest = new XMLHttpRequest();

		function sendBuyerContact(evt) {
			var objectRequest = evt.currentTarget;

			if (objectRequest.readyState === 4 && objectRequest.status === 200) {
				var json = JSON.parse(objectRequest.responseText),
					error = json.error,
					alert = json.message

				if (error === 1) {
					setTimeout(function() {
						modal.querySelector('.modal-title').innerHTML = 'Hoppla!';
						modal.querySelector('.modal-text').innerHTML = alert;
					}, 500);
				} else {
					setTimeout(function() {
						modal.querySelector('.modal-title').innerHTML = 'Danke!';
						modal.querySelector('.modal-text').innerHTML = alert;
					}, 1000);
				}
			}
		}

		dataRequest.addEventListener('readystatechange', sendBuyerContact);
		dataRequest.open('POST', '../../../kaeuferfinder_validate.php', true);
		dataRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		dataRequest.send(formData);

	}
});

$('#kaeuferfinderModal').on('hide.show.bs.modal', function (event) {
	var modal 	= document.getElementById('kaeuferfinderModal'),
		form 	= document.getElementById('kaeuferfinder-kontakt');

	setTimeout(function() {
		form.classList.remove('d-none');
		form.reset();
		modal.querySelector('.modal-text').innerHTML = '';
	}, 500);

});