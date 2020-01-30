<?php
	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$error 		= 0;
	$i 			= 0;
	$alert 		= '';
	$kaeufer 	= array();

	/*$_POST['kf_objektart'] = 'Wohnung';
	$_POST['kf_ort'] = '3';
	$_POST['kf_preis'] = '100';
	$_POST['kf_zimmer'] = '4';
	$_POST['kf_wohnflaeche'] = '100';*/

	// Formular wird übertragen
	if (isset($_POST['kf_objektart'])) {
		// Daten einlesen
		$kf_objektart 			= mysqli_escape_string($db, $_POST['kf_objektart']);
		$kf_ort 				= mysqli_escape_string($db, $_POST['kf_ort']);

		if($kf_ort == '1' ||$kf_ort == '2' || $kf_ort == '3') {
			$kf_area 	= 'LIKE "'.floatval($kf_ort).'"';
			$kf_ort 	= 'IS NOT NULL';
		} else {
			$kf_area 	= 'IS NOT NULL';
			$kf_ort 	= 'LIKE "'.$kf_ort.'"';
		}
		
		$kf_preis 				= mysqli_escape_string($db, $_POST['kf_preis']);
		if(!$kf_preis == '') {
			$kf_preis 	= floatval($kf_preis);
		}

		$kf_wohnflaeche 		= mysqli_escape_string($db, $_POST['kf_wohnflaeche']);
		if(!$kf_wohnflaeche == '') {
			$kf_wohnflaeche = floatval($kf_wohnflaeche);
		}

		$kf_zimmer 				= mysqli_escape_string($db, $_POST['kf_zimmer']);
		if(!$kf_zimmer == '') {
			$kf_zimmer = floatval($kf_zimmer);
		}

		// SELECT-Abfrage festlegen
		$kf_sql = mysqli_query($db, 'SELECT * FROM kaeufer WHERE kf_objektart LIKE "'.$kf_objektart.'" AND (kf_area '.$kf_area.' OR kf_area = 0) AND (kf_ort '.$kf_ort.' OR kf_ort = "") AND (kf_preis >= "'.$kf_preis.'" OR kf_preis = "0") AND (kf_wohnflaeche <= "'.$kf_wohnflaeche.'" OR kf_wohnflaeche = "0") AND (kf_zimmer <= "'.$kf_zimmer.'" OR kf_zimmer = "0.0")');

		if($kf_sql == true) {
			
			// wenn Käufer vorliegen
			if(mysqli_num_rows($kf_sql) >= 1 and $error == 0) {
				while($kf_row = mysqli_fetch_assoc($kf_sql)) {
					$new_kaeufer = '<h5>'.$kf_row['kf_titel'].'</h5>';
					$new_kaeufer .= '<p><span class="text-primary">Objektart:</span> '.$kf_row['kf_objektart'].'<br>';
					
					if($kf_row['kf_ort'] !== '') {
						$new_kaeufer .= '<span class="text-primary">Ort:</span> '.$kf_row['kf_ort'].'<br>';
					}

					if($kf_row['kf_preis'] !== '' && !$kf_row['kf_preis'] == '0') {
						$new_kaeufer .= '<span class="text-primary">Preis:</span> '.number_format($kf_row['kf_preis'],2,',','.').' €<br>';
					}

					$kf_zimmer = $kf_row['kf_zimmer'] + 0;

					if($kf_row['kf_zimmer'] !== '' && !$kf_zimmer == 0) {
						$new_kaeufer .= '<span class="text-primary">Zimmer:</span> '.$kf_zimmer = str_replace('.', ',', $kf_zimmer).'<br>';
					}

					if($kf_row['kf_wohnflaeche'] !== '' && !$kf_row['kf_wohnflaeche'] == '0') {
						$new_kaeufer .= '<span class="text-primary">Wohnfläche:</span> '.$kf_row['kf_wohnflaeche'].' m²<br>';
					}

					$new_kaeufer .= '</p>';

					$new_kaeufer .= '<button role="button" title="Jetzt Kontaktdaten anfordern" class="kf-kontaktbtn" data-toggle="modal" data-target="#kaeuferfinderModal" data-id="'.$kf_row['kf_id'].'" data-titel="'.$kf_row['kf_titel'].'">Anfragen</button>';


					array_push($kaeufer, $new_kaeufer);

					$i++;
				}

				$alert .= $i.' Interessenten gefunden';
			} else {
				$error++;
				$alert .= 'Leider weist unsere Datenbank zu wenig vergleichende Informationen zu Ihren Eingaben auf, sodass wir Ihnen hier keine potentiellen Käufer anzeigen können. Wir melden uns gerne persönlich bei Ihnen, um eine optimale Verkaufsstrategie zu besprechen:<div class="form-row mt-4 justify-content-center"><button role="button" title="Jetzt Kontaktdaten anfordern" class="kf-kontaktbtn no-buyer-btn" data-toggle="modal" data-target="#kaeuferfinderModal" data-id="no-buyer" data-titel="Kontaktanfrage">Kontakt aufnehmen</button></div>';
			} 
		} else {
			$error++;
			$alert .= 'Es gab einen Fehler bei der Datenübertragung. Bitte kontaktieren Sie den Webseitenadministrator.';
		}

	} elseif (isset($_POST['kf_vorname'])) {
		// Daten einlesen
		$kf_vorname			= mysqli_escape_string($db, $_POST['kf_vorname']);
		$kf_nachname		= mysqli_escape_string($db, $_POST['kf_nachname']);
		$kf_email			= mysqli_escape_string($db, $_POST['kf_email']);
		$kf_tel				= mysqli_escape_string($db, $_POST['kf_tel']);
		$kf_bemerkung		= mysqli_escape_string($db, $_POST['kf_bemerkung']);
		$kf_id				= mysqli_escape_string($db, $_POST['kf_id']);

		if($kf_id !== 'no-buyer') {

			$kf_obj_sql 		= sql_select_where('all', 'kaeufer', 'kf_id', $kf_id, '', '');
			$kf_obj_row 		= mysqli_fetch_assoc($kf_obj_sql);

			$kf_obj_dsn 		= $kf_obj_row['kf_dsn'];
			$kf_obj_objektart 	= $kf_obj_row['kf_objektart'];
			$kf_obj_ort 		= $kf_obj_row['kf_ort'];
			$kf_obj_preis 		= $kf_obj_row['kf_preis'];
			$kf_obj_zimmer 		= $kf_obj_row['kf_zimmer'];
			$kf_obj_wohnflaeche = $kf_obj_row['kf_wohnflaeche'];

		}


		// E-Mails versenden
		// Include PHPMailer class
		require('phpmailer/PHPMailerAutoload.php');

		// Template abrufen
		$message 			= file_get_contents('msg-template.html');
		$appUrl 			= $microsite_url;
		$appAktion 			= 'Käuferfinder';

		$mailHeader 		= 'Neue Kontaktanfrage über Käuferfinder!';

		$mailText 			= 'Folgende Daten sind soeben eingegangen:<br><br>';
		$mailText 			.= '<b>Kontaktdaten des Verkäufers</b><br>';
		$mailText 			.= 'Name: '.$kf_vorname.' '.$kf_nachname.'<br>';
		$mailText 			.= 'E-Mail-Adresse: '.$kf_email.'<br>';
		if(!$kf_tel == '') {
			$mailText 			.= 'Telefonnr.: '.$kf_tel.'<br>';
		}
		if(!$kf_bemerkung == '') {
			$mailText 			.= '<br>Bemerkung:<br>'.$kf_bemerkung.'<br>';
		}
		$mailText 			.= '<br>';

		if($kf_id !== 'no-buyer') {
			$mailText 			.= '<b>Interesse an Käufer:</b><br>';
			$mailText 			.= 'DSN: '.$kf_obj_dsn.'<br>';
			if(!$kf_obj_objektart == '') {
				$mailText 			.= 'Objektart: '.$kf_obj_objektart.'<br>';
			}

			if(!$kf_obj_ort == '') {
				$mailText 			.= 'Ort: '.$kf_obj_ort.'<br>';
			}

			if(!$kf_obj_preis == '' && !$kf_obj_preis == '0') {
				$mailText 			.= 'Preis: '.number_format($kf_obj_preis,2,',','.').' €<br>';
			}

			// Dezimalstelle ggf. entfernen
			$kf_obj_zimmer = $kf_obj_zimmer + 0;

			if(!$kf_obj_zimmer == '' && !$kf_obj_zimmer == 0) {
				$mailText 			.= 'Zimmeranzahl: '.$kf_obj_zimmer.'<br>';
			}

			if(!$kf_obj_wohnflaeche == '' && !$kf_obj_wohnflaeche == '0') {
				$mailText 			.= 'Wohnfläche: '.$kf_obj_wohnflaeche.' m²<br>';
			}
		} else {
			$mailText .= 'Zu der Immobilie des Verkäufers konnten keine passenden Gesuche gefunden werden. Der Kunde wünscht sich eine persönliche Kontaktaufnahme.';
		}

		//Mail-Footer
		$mailfooter_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'mailfooter'), '', '');
		$mailfooter_row 	= mysqli_fetch_assoc($mailfooter_sql);
		$mailfooter 		= $mailfooter_row['txt_beitrag'];

		// Schlussfloskel
		$impressumUrl 		= $microsite_url.'/impressum.php';
		$datenschutzUrl 	= $microsite_url.'/datenschutz.php';
		$institutName 		= $institut;
		$institutUrl 		= $institut_url;
		$institutImg 		= $microsite_url.'/img/mail-logo-neg.png';
		$institutImpressum 	= $mailfooter;

		// Ersetzen von % mit Angaben
		$message 			= str_replace("%betreff%", 				$c_subtitle, 		$message);
		$message 			= str_replace("%appUrl%", 				$appUrl, 			$message);
		$message 			= str_replace("%appAktion%", 			$appAktion, 		$message);
		$message 			= str_replace("%mailHeader%", 			$mailHeader, 		$message);
		$message 			= str_replace("%mailText%", 			$mailText, 			$message);
		$message 			= str_replace("%impressumUrl%", 		$impressumUrl, 		$message);
		$message 			= str_replace("%datenschutzUrl%", 		$datenschutzUrl, 	$message);
		$message 			= str_replace("%institutName%", 		$institutName, 		$message);
		$message 			= str_replace("%institutUrl%", 			$institutUrl, 		$message);
		$message 			= str_replace("%institutImg%", 			$institutImg, 		$message);
		$message 			= str_replace("%institutImpressum%", 	$institutImpressum, $message);

		//Setup PHPMailer
		$mail 				= new PHPMailer;
		$mail->setLanguage('de', 'phpmailer/language/');
		$mail->CharSet 		='UTF-8';
		//$mail->SMTPDebug 	= 2; 				// Enable verbose debug output
		$mail->isSMTP();						// Set mailer to use SMTP
		$mail->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
		$mail->SMTPOptions 	= array(
			'ssl' => array(
				'verify_peer' 		=> false,
				'verify_peer_name' 	=> false,
				'allow_self_signed' => true
			)
		);
		$mail->SMTPAuth 	= true; 			// Enable SMTP authentication
		$mail->Username 	= $smtp_user; 		// SMTP username
		$mail->Password 	= $smtp_passwort; 	// SMTP password
		$mail->SMTPSecure 	= 'ssl'; 			// Enable TLS encryption, `ssl` also accepted
		$mail->Port 		= $smtp_port; 		// TCP port to connect to
		//$mail->isHTML(true);					// Set email format to html

		//Absender
		$mail->SetFrom($email_von, $institut);
		$mail->Sender 		= ($email_von);
		$mail->addReplyTo($email_zu, $institut);

		if ( strpos($email_kopie, ',') !== false ) {
			$array_kopien = explode(',', $email_kopie);
			foreach ($array_kopien as $kb_kopie) {
				$mail->addBCC($kb_kopie);
				$mail->addCustomHeader('BCC: '.$kb_kopie.'');
				//$mail->addCustomHeader('CC: '.$kb_kopie.'');
			}
		} else {
			$mail->addBCC($email_kopie);
			$mail->addCustomHeader('BCC: '.$email_kopie.'');
			//$mail->addCustomHeader('CC: '.$email_kopie.'');

		}

		//Empfänger
		$name_empfaenger 	= $institut;
		//Überprüfen ob es mehr als einen Empfänger gibt
		if ( strpos($email_von, ',') !== false ) {
			$array_emails = explode(',', $email_von);
			foreach ($array_emails as $kb_email) {
				$mail->addAddress($kb_email, $name_empfaenger);
			}
		} else {
			$mail->addAddress($email_von, $name_empfaenger);

		}

		// $mail->addAddress('herbst@ffemedia.de', $name_empfaenger);

		//Betreff
		$mail->Subject 		= $mailHeader;

		$mail->MsgHTML($message);
		$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

		// E-Mail an Kunden versenden
		if( !$mail->Send() ) {
			$error++;
			$alert  .= 'Fehler! Ihre Daten konnten leider nicht versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
		} else {
			// Template abrufen
			$message2 			= file_get_contents('msg-template.html');
			$appUrl 			= $microsite_url;
			$appAktion 			= '';

			$mailHeader 		= 'Ihre Kontaktanfrage';
			$mailText    	 	= 'Sehr geehrte(r) '.$kf_vorname.' '.$kf_nachname.', <br><br>';
			$mailText 			.= 'vielen Dank für Ihre Kontaktanfrage über unseren Käuferfinder. Folgende Daten haben Sie uns übergeben:<br><br>';
			$mailText 			.= 'Name: '.$kf_vorname.' '.$kf_nachname.'<br>';
			$mailText 			.= 'E-Mail-Adresse: '.$kf_email.'<br>';
			if(!$kf_tel == '') {
				$mailText 			.= 'Telefonnr.: '.$kf_tel.'<br>';
			}
			if(!$kf_bemerkung == '') {
				$mailText 			.= '<br>Bemerkung:<br>'.$kf_bemerkung.'<br>';
			}

			$mailText 			.= '<br>';
			
			if($kf_id !== 'no-buyer') {
				$mailText 			.= '<b>Interesse an Käufer:</b><br>';
				if(!$kf_obj_objektart == '') {
					$mailText 			.= 'Objektart: '.$kf_obj_objektart.'<br>';
				}

				if(!$kf_obj_ort == '') {
					$mailText 			.= 'Ort: '.$kf_obj_ort.'<br>';
				}

				if(!$kf_obj_preis == '' && !$kf_obj_preis == '0') {
					$mailText 			.= 'Preis: '.number_format($kf_obj_preis,2,',','.').' €<br>';
				}

				// Dezimalstelle ggf. entfernen
				$kf_obj_zimmer = $kf_obj_zimmer + 0;

				if(!$kf_obj_zimmer == '' && !$kf_obj_zimmer == 0) {
					$mailText 			.= 'Zimmeranzahl: '.$kf_obj_zimmer.'<br>';
				}

				if(!$kf_obj_wohnflaeche == '' && !$kf_obj_wohnflaeche == '0') {
					$mailText 			.= 'Wohnfläche: '.$kf_obj_wohnflaeche.' m²<br>';
				}
			}

			$mailText 	.= '<br>';
			$mailText 	.= 'Wir werden uns schnellstmöglich bei Ihnen zurückmelden.';

			// Schlussfloskel
			$impressumUrl 		= $microsite_url.'/impressum.php';
			$datenschutzUrl 	= $microsite_url.'/datenschutz.php';
			$institutName 		= $institut;
			$institutUrl 		= $institut_url;
			$institutImg 		= $microsite_url.'/img/mail-logo-neg.png';
			$institutImpressum 	= $mailfooter;

			// Ersetzen von % mit Angaben
			$message2 			= str_replace("%betreff%", $c_subtitle, $message2);
			$message2 			= str_replace("%appUrl%", $appUrl, $message2);
			$message2 			= str_replace("%appAktion%", $appAktion, $message2);
			$message2 			= str_replace("%mailHeader%", $mailHeader, $message2);
			$message2 			= str_replace("%mailText%", $mailText, $message2);
			$message2 			= str_replace("%impressumUrl%", $impressumUrl, $message2);
			$message2 			= str_replace("%datenschutzUrl%", $datenschutzUrl, $message2);
			$message2 			= str_replace("%institutName%", $institutName, $message2);
			$message2 			= str_replace("%institutUrl%", $institutUrl, $message2);
			$message2 			= str_replace("%institutImg%", $institutImg, $message2);
			$message2 			= str_replace("%institutImpressum%", $institutImpressum, $message2);

			//Setup PHPMailer
			$mail2 				= new PHPMailer;
			$mail2->setLanguage('de', 'phpmailer/language/');
			$mail2->CharSet 	='UTF-8';
			//$mail2->SMTPDebug = 2; 					// Enable verbose debug output
			$mail2->isSMTP(); 						// Set mailer to use SMTP
			$mail2->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
			$mail2->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' 		=> false,
					'verify_peer_name' 	=> false,
					'allow_self_signed' => true
				)
			);
			$mail2->SMTPAuth 	= true; 			// Enable SMTP authentication
			$mail2->Username 	= $smtp_user; 		// SMTP username
			$mail2->Password 	= $smtp_passwort; 	// SMTP password
			$mail2->SMTPSecure 	= 'ssl'; 			// Enable TLS encryption, `ssl` also accepted
			$mail2->Port 		= $smtp_port; 		// TCP port to connect to
			//$mail2->isHTML(true);					// Set email format to html

			//Absender
			$mail2->SetFrom($email_von, $institut);
			$mail2->Sender 		= ($email_von);
			$mail2->addReplyTo($email_zu, $institut);

			//Empfänger
			$name_empfaenger 	= $kf_vorname.' '.$kf_nachname;
			$mail2->addAddress($kf_email, $name_empfaenger);

			//Betreff
			$mail2->Subject 	= 'Ihre Kontaktanfrage';

			$mail2->MsgHTML($message2);
			$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine HTML-kompatible E-Mail-Anwendung!';

			//Nachricht
			if( !$mail2->Send() ) {
				$error++;
				$alert  		.= 'Fehler! Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo;
			} else {
				$alert  		.= 'Ihre Kontaktanfrage wurde erfolgreich an die von Ihnen angegebene E-Mail-Adresse '.$kf_email.' versendet.<br>';
			}
		}
		

	} else {

		$error++;
		$alert .= 'Es gab einen Fehler bei der Datenübertragung. Bitte kontaktieren Sie den Webseitenadministrator.';
	}



	$response = array(
		'error' => $error,
		'message' => $alert,
		'kaeufer' => $kaeufer
	);

	echo json_encode($response);


?>