<?php
	$rubrik 				= $_GET['rub_id'];
	$page 					= 'rub_'.$rubrik;

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	// Rubrik Angaben auslesen
	if (isset($_GET['rub_id'])) {
		$rub_id 			= $_GET['rub_id'];
		$rubrik_sql 		= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
		$rubrik_row 		= mysqli_fetch_assoc($rubrik_sql);

		if ($rubrik_row['rub_name'] !== '') {
			$rubrik_mail 		= $rubrik_row['rub_name'];
		} else {
			$rubrik_mail 		= 'Kontakftformular';
		}

		if ($rubrik_row['rub_email_zu'] !== '') {
			$rub_email_zu 		= $rubrik_row['rub_email_zu'];
		}
		if ($rubrik_row['rub_email_kopie'] !== '') {
			$rub_email_kopie 	= $rubrik_row['rub_email_kopie'];
		}
	} else {
		$rubrik_mail 		= 'Kontakftformular';
	}
	$ite_rubrik 	= $rubrik_mail;

	//Mail-Footer
	$mailfooter_sql 		= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'mailfooter'), '', '');
	$mailfooter_row 		= mysqli_fetch_assoc($mailfooter_sql);
	$mailfooter 			= $mailfooter_row['txt_beitrag'];

	// Variablen setzen
	if (!isset($formular)) {
		$formular 		= '1';
	}
	if (!isset($strasse)) {
		$strasse 		= '';
	}
	if (!isset($plz)) {
		$plz 			= '';
	}
	if (!isset($ort)) {
		$ort 			= '';
	}
	if (!isset($besonderheiten)) {
		$besonderheiten = '';
	}
	if (!isset($baujahr)) {
		$baujahr 		= '';
	}
	if (!isset($grundflaeche)) {
		$grundflaeche 	= '';
	}
	if (!isset($wohnflaeche)) {
		$wohnflaeche 	= '';
	}
	if (!isset($zimmer)) {
		$zimmer 		= '';
	}
	if (!isset($geschosse)) {
		$geschosse 		= '';
	}
	if (!isset($besichtigung)) {
		$besichtigung 	= '';
	}
	if (!isset($energieausweis)) {
		$energieausweis = '';
	}
	if (!isset($bemerkungen)) {
		$bemerkungen 	= '';
	}

	// Formular 1/2 einlesen
	if ( isset($_POST['weiter']) ) {
		// Variablen einlesen
		$art 					= $_POST['art'];
		$strasse 				= $_POST['strasse'];
		$plz 					= $_POST['plz'];
		$ort 					= $_POST['ort'];
		$besonderheiten 		= implode(', ', $_POST['besonderheiten']);
		$baujahr 				= $_POST['baujahr'];
		$grundflaeche 			= $_POST['grundflaeche'];
		$wohnflaeche 			= $_POST['wohnflaeche'];
		$zimmer 				= $_POST['zimmer'];
		$geschosse 				= $_POST['geschosse'];
		$energieausweis 		= $_POST['energieausweis'];
		$besichtigung 			= $_POST['besichtigung'];
		$bemerkungen 			= $_POST['bemerkungen'];

		// Zeige Formular #2
		$formular 				= '2';
	}
	// Formular 2/2 absenden
	if ( isset($_POST['submit']) ) {
		// Zeige Formular #2
		$formular 				= '2';

		// Variablen einlesen
		$art 					= $_POST['art'];
		$strasse 				= $_POST['strasse'];
		$plz 					= $_POST['plz'];
		$ort 					= $_POST['ort'];
		$besonderheiten 		= $_POST['besonderheiten'];
		$baujahr 				= $_POST['baujahr'];
		$grundflaeche 			= $_POST['grundflaeche'];
		$wohnflaeche 			= $_POST['wohnflaeche'];
		$zimmer 				= $_POST['zimmer'];
		$geschosse 				= $_POST['geschosse'];
		$energieausweis 		= $_POST['energieausweis'];
		$besichtigung 			= $_POST['besichtigung'];
		$bemerkungen 			= $_POST['bemerkungen'];

		$kd_vorname 			= $_POST['kd_vorname'];
		$kd_nachname 			= $_POST['kd_nachname'];
		//$kd_geburtsdatum 		= $_POST['kd_geburtsdatum'];
		//$orderdate 				= explode('.', $kd_geburtsdatum);
		//$kd_geburtsdatum 		= $orderdate[2].'-'.$orderdate[1].'-'.$orderdate[0];
		$kd_strasse 			= $_POST['kd_strasse'];
		$kd_plz 				= $_POST['kd_plz'];
		$kd_ort 				= $_POST['kd_ort'];
		$kd_email 				= $_POST['kd_email'];
		$kd_tel 				= $_POST['kd_tel'];

		$fehler 				= 0;


		/*if (fc_alter($kd_geburtsdatum) < 18) {
			$fehler++;
			$alert .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Eine Immobilienbewertung kann erst ab 18 Jahren angefordert werden. <button type="button" class="close" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">&times;</span></button></div>';
			$kd_geburtsdatum 	= $_POST['kd_geburtsdatum'];
		} else {
			$kd_geburtsdatum 	= $_POST['kd_geburtsdatum'];
		}*/

		if ($fehler == 0) {
			// E-Mails versenden
			// Include PHPMailer class
			require('phpmailer/PHPMailerAutoload.php');

			// Template abrufen
			$message 			= file_get_contents('msg-template.html');
			$appUrl 			= $microsite_url;
			$appAktion 			= 'Immobilienbewertung';
			$mailHeader 		= 'Immobilienbewertung gefordert!';
			$mailText 			= 'Folgende Daten sind soeben eingegangen:<br><br>';
			$mailText 			.= '<b>Kontaktdaten:</b><br />';
			$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br />';
			if ($kd_strasse !== '') {
				$mailText 		.= $kd_strasse.'<br />';
			}
			if ($kd_plz = '') {
				$mailText 		.= $kd_plz.' '.$kd_ort.'<br />';
			}
			////$mailText 			.= 'Geburtsdatum: '.$kd_geburtsdatum.'<br />';
			if ($kd_tel !== '') {
				$mailText 		.= 'Telefon: '.$kd_tel.'<br />';
			}
			$mailText 			.= 'E-Mail: '.$kd_email.'<br><br>';
			$mailText 			.= '<b>Immobiliendaten:</b><br />';
			$mailText 			.= 'Art der Immobilie: '.$art.'<br />';
			$mailText 			.= 'Adresse: '.$strasse.', '.$plz.' '.$ort.'<br />';
			if ($besonderheiten !== '') {
				$mailText 		.= 'Besonderheiten: '.$besonderheiten.'<br />';
			}
			if ($art == 'Wohnung') {
				if ($wohnflaeche !== '') {
					$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
				}
				if ($zimmer !== '') {
					$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
				}
				if ($geschosse !== '') {
					$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
				}
			} elseif ($art == 'Haus') {
				if ($wohnflaeche !== '') {
					$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
				}
				if ($zimmer !== '') {
					$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
				}
				if ($geschosse !== '') {
					$mailText 	.= 'Geschosse: '.$geschosse.'<br />';
				}
				if ($energieausweis !== '') {
					$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
				}
			} elseif ($art == 'Grundstück') {
				if ($grundflaeche !== '') {
					$mailText 	.= 'Grundfläche: '.$grundflaeche.' m²<br /><br />';
				}
			} elseif ($art == 'Renditeobjekt') {
				if ($wohnflaeche !== '') {
					$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
				}
				if ($zimmer !== '') {
					$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
				}
				if ($geschosse !== '') {
					$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
				}
				if ($energieausweis !== '') {
					$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
				}
			} elseif ($art == 'Gewerbeimmobilie') {
				if ($grundflaeche !== '') {
					$mailText 	.= 'Grundfläche: '.$grundflaeche.' m²<br /><br />';
				}
				if ($geschosse !== '') {
					$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
				}
				if ($energieausweis !== '') {
					$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
				}
			}
			if ($besichtigung !== '') {
				$mailText 		.= '<b>Wann ist die Besichtigung gewünscht?</b><br />'.$besichtigung.'<br /><br />';
			}
			if ($bemerkungen !== '') {
				$mailText 		.= '<b>Weitere Bemerkungen:</b><br />'.$bemerkungen.'<br /><br />';
			}

			// Schlussfloskel
			$impressumUrl 		= $microsite_url.'/impressum.php';
			$datenschutzUrl 	= $microsite_url.'/datenschutz.php';
			$institutName 		= $institut;
			$institutUrl 		= $institut_url;
			$institutImg 		= $microsite_url.'/img/mail-logo.png';
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
			//$mail->SMTPDebug 	= 2; 					// Enable verbose debug output
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
			$mail->addReplyTo($email_von, $institut);

			if ($rubrik_row['rub_email_kopie'] !== '') {
				if ( strpos($rub_email_kopie, ',') !== false ) {
					$array_kopien = explode(',', $rub_email_kopie);
					foreach ($array_kopien as $kb_kopie) {
						$mail->addBCC($kb_kopie);
						$mail->addCustomHeader('BCC: '.$kb_kopie.'');
						//$mail->addCustomHeader('CC: '.$kb_kopie.'');
					}
				} else {
					$mail->addBCC($rub_email_kopie);
					$mail->addCustomHeader('BCC: '.$rub_email_kopie.'');
					//$mail->addCustomHeader('CC: '.$email_kopie.'');
				}
			} else {
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
			}

			//Empfänger
			$name_empfaenger 	= $institut;
			if ($rubrik_row['rub_email_zu'] !== '') {
				if ( strpos($rub_email_zu, ',') !== false ) {
					$array_emails = explode(',', $rub_email_zu);
					foreach ($array_emails as $kb_email) {
						$mail->addAddress($kb_email, $name_empfaenger);
					}
				} else {
					$mail->addAddress($rub_email_zu, $name_empfaenger);
				}
			} else {
				if ( strpos($email_zu, ',') !== false ) {
					$array_emails = explode(',', $email_zu);
					foreach ($array_emails as $kb_email) {
						$mail->addAddress($kb_email, $name_empfaenger);
					}
				} else {
					$mail->addAddress($email_zu, $name_empfaenger);
				}
			}

			//Betreff
			$mail->Subject 		= 'Immobilienbewertung angefordert';

			$mail->MsgHTML($message);
			$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

			//E-Mail versenden
			if( !$mail->Send() ) {
				$fehler++;
				$alert  .= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Fehler! Ihre Daten konnten leider nicht versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
			} else {
				// Template abrufen
				$message2 			= file_get_contents('msg-template.html');
				$appUrl 			= $microsite_url.'';
				$appAktion 			= '';
				$mailHeader 		= 'Bestätigung Ihrer Anfrage zur Immobilienbewertung';
				$mailText    	 	= 'Sehr geehrte(r) '.$kd_vorname.' '.$kd_nachname.', <br />';
				$mailText 			.= 'Wir haben Ihre Anfrage zur Bewertung Ihrer Immobilie in der '.$strasse.' in '.$plz.' '.$ort.' erhalten. Wir werden uns in Kürze mit Ihnen in Verbindung setzen.<br /><br />';
				$mailText 			.= '<hr style="border: #efefef solid 1px;"><br />';
				$mailText 			.= 'Folgende Daten haben Sie angegeben:<br /><br />';
				$mailText 			.= '<b>Kontaktdaten:</b><br />';
				$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br />';
				if ($kd_strasse !== '') {
					$mailText 		.= $kd_strasse.'<br />';
				}
				if ($kd_plz = '') {
					$mailText 		.= $kd_plz.' '.$kd_ort.'<br />';
				}
				//$mailText 			.= 'Geburtsdatum: '.$kd_geburtsdatum.'<br />';
				if ($kd_tel !== '') {
					$mailText 		.= 'Telefon: '.$kd_tel.'<br />';
				}
				$mailText 			.= 'E-Mail: '.$kd_email.'<br /><br />';
				$mailText 			.= '<b>Immobiliendaten:</b><br />';
				$mailText 			.= 'Art der Immobilie: '.$art.'<br />';
				$mailText 			.= 'Adresse: '.$strasse.', '.$plz.' '.$ort.'<br />';
				if ($besonderheiten !== '') {
					$mailText 		.= 'Besonderheiten: '.$besonderheiten.'<br />';
				}
				if ($art == 'Wohnung') {
					if ($wohnflaeche !== '') {
						$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
					}
					if ($zimmer !== '') {
						$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
					}
					if ($geschosse !== '') {
						$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
					}
				} elseif ($art == 'Haus') {
					if ($wohnflaeche !== '') {
						$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
					}
					if ($zimmer !== '') {
						$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
					}
					if ($geschosse !== '') {
						$mailText 	.= 'Geschosse: '.$geschosse.'<br />';
					}
					if ($energieausweis !== '') {
						$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
					}
				} elseif ($art == 'Grundstück') {
					if ($grundflaeche !== '') {
						$mailText 	.= 'Grundfläche: '.$grundflaeche.' m²<br /><br />';
					}
				} elseif ($art == 'Renditeobjekt') {
					if ($wohnflaeche !== '') {
						$mailText 	.= 'Wohnfläche: '.$wohnflaeche.' m²<br />';
					}
					if ($zimmer !== '') {
						$mailText 	.= 'Zimmer: '.$zimmer.'<br />';
					}
					if ($geschosse !== '') {
						$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
					}
					if ($energieausweis !== '') {
						$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
					}
				} elseif ($art == 'Gewerbeimmobilie') {
					if ($grundflaeche !== '') {
						$mailText 	.= 'Grundfläche: '.$grundflaeche.' m²<br /><br />';
					}
					if ($geschosse !== '') {
						$mailText 	.= 'Geschosse: '.$geschosse.'<br /><br />';
					}
					if ($energieausweis !== '') {
						$mailText 	.= 'Energieausweis: '.$energieausweis.'<br /><br />';
					}
				}
				if ($besichtigung !== '') {
					$mailText 		.= '<b>Wann ist die Besichtigung gewünscht?</b><br />'.$besichtigung.'<br /><br />';
				}
				if ($bemerkungen !== '') {
					$mailText 		.= '<b>Weitere Bemerkungen:</b><br />'.$bemerkungen.'<br /><br />';
				}
				// Schlussfloskel
				$impressumUrl 		= $microsite_url.'/impressum.php';
				$datenschutzUrl 	= $microsite_url.'/datenschutz.php';
				$institutName 		= $institut;
				$institutUrl 		= $institut_url;
				$institutImg 		= $microsite_url.'/img/mail-logo.png';
				$institutImpressum 	= $mailfooter;

				// Ersetzen von % mit Angaben
				$message2 			= str_replace("%betreff%", 				$c_subtitle, 		$message2);
				$message2 			= str_replace("%appUrl%", 				$appUrl, 			$message2);
				$message2 			= str_replace("%appAktion%", 			$appAktion, 		$message2);
				$message2 			= str_replace("%mailHeader%", 			$mailHeader, 		$message2);
				$message2 			= str_replace("%mailText%", 			$mailText, 			$message2);
				$message2 			= str_replace("%impressumUrl%", 		$impressumUrl, 		$message2);
				$message2 			= str_replace("%datenschutzUrl%", 		$datenschutzUrl, 	$message2);
				$message2 			= str_replace("%institutName%", 		$institutName, 		$message2);
				$message2 			= str_replace("%institutUrl%", 			$institutUrl, 		$message2);
				$message2 			= str_replace("%institutImg%", 			$institutImg, 		$message2);
				$message2 			= str_replace("%institutImpressum%", 	$institutImpressum, $message2);

				//Setup PHPMailer
				$mail2 				= new PHPMailer;
				$mail2->setLanguage('de', 'phpmailer/language/');
				$mail2->CharSet 	='UTF-8';
				//$mail2->SMTPDebug 		= 2; 					// Enable verbose debug output
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
				$mail2->addReplyTo($email_von, $institut);

				//Empfänger
				$name_empfaenger 	= $kd_vorname.' '.$kd_nachname;
				$mail2->addAddress($kd_email, $name_empfaenger);

				//Betreff
				$mail2->Subject 	= 'Bestätigung Immobilienbewertungsanfrage';

				$mail2->MsgHTML($message2);
				$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

				//Nachricht
				if( !$mail2->Send() ) {
					$fehler++;
					$alert  			.= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Fehler! Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail2->ErrorInfo.'</div>';
				} else {
					//Tracking in Datenbank eintragen
					$bezeichnung_mail 	= 'Anforderung Immobilienbewertung';
					$praefix_mail 		= '999';
					$url_mail 			= $microsite_url.'/form_immobilienbewertung.php';
					$sql_track_mail 	= sql_insert('item_tracking', array('ite_rubrik', 'ite_praefix', 'ite_name', 'ite_position', 'ite_url'), array($rubrik_mail, $page, $bezeichnung_mail, 'Formular', $url_mail));

					$alert_upload  			= 'Zusätzlich wurde Ihnen eine Bestätigungsmail an '.$kd_email.' gesendet.';
					// Zeige Bestätigung
					$formular 			= '3';
				}

				$alert  				.= '<div class="alert alert-success alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Ihre Angaben wurden erfolgreich übertragen. '.$alert_upload.'</div>';
			}
		}
	}

	include('header.php');
?>

<!-- Header (Logo, Überschrift, Einleitung) -->
<section class="bg-lightblue-gradient first-section">

	<!-- Überschrift & Einleitung-->
	<div class="container py-4">
		<span class="text-uppercase text-light"><?php echo $rubrik_row['rub_name']; ?></span>
		<h1 class="display-4 text-light">Jetzt meine Immobilie bewerten!</h1>
		<p class="h3 text-light font-weight-light">Nutzen Sie unsere professionelle Immobilienbewertung. Unsere Makler sind markterfahren und kennen die Preise in Ihrer Nähe. Sie können sicher sein: Wir ermitteln den für Sie optimalen Preis.</p>
	</div>
</section>


<!-- Inhalt -->
<div class="bg-white container p-5 mb-5">
	<?php if ($formular == 1) { ?>
		<?php echo $alert; ?>
		<form action="" method="post">
			<!-- Art der Immobilie -->
			<div class="description">
				<div class="form-group">
					<div class="form-check form-check-inline">
						<label class="form-check-label" for="art" class="control-label">Art der Immobilie: &nbsp;</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" name="art" id="wohnung" value="Wohnung" <?php echo check_art('Wohnung'); ?> required>
						<label class="custom-control-label" for="wohnung">Wohnung</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" name="art" id="haus" value="Haus" <?php echo check_art('Haus'); ?>>
						<label class="custom-control-label" for="haus">Haus</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" name="art" id="grundstueck" value="Grundstück" <?php echo check_art('Grundstück'); ?>>
						<label class="custom-control-label" for="grundstueck">Grundstück</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" name="art" id="renditeobjekt" value="Renditeobjekt" <?php echo check_art('Renditeobjekt'); ?>>
						<label class="custom-control-label" for="renditeobjekt">Renditeobjekt</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" name="art" id="gewerbeimmobilie" value="Gewerbeimmobilie" <?php echo check_art('Gewerbeimmobilie'); ?>>
						<label class="custom-control-label" for="gewerbeimmobilie">Gewerbeimmobilie</label>
					</div>
				</div>
			</div>

			<!-- Standort der Immobilie -->
			<div class="form-group description">
				<input type="text" class="form-control" name="strasse" placeholder="Straße, Hausnr. der Immobilie" value="<?php echo $strasse; ?>" required>
			</div>

			<div class="row description">
				<div class="col-md-4">
					<div class="form-group">
						<input type="text" class="form-control" name="plz" placeholder="PLZ" value="<?php echo $plz; ?>" pattern="[0-9]{5}" required>
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<input type="text" class="form-control" name="ort" placeholder="Ort" value="<?php echo $ort; ?>" required>
					</div>
				</div>
			</div>

			<!-- Besonderheiten -->
			<div class="description">
				<div class="form-group">
					<div class="form-check form-check-inline">
						<label class="form-check-label" for="besonderheiten[]" control-label">Besonderheiten: &nbsp;</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input class="custom-control-input" type="checkbox" name="besonderheiten[]" id="denkmalschutz" value="Denkmalschutz" <?php echo check_besonderheiten('Denkmalschutz'); ?>>
						<label class="custom-control-label" for="denkmalschutz">Denkmalschutz</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input class="custom-control-input" type="checkbox" name="besonderheiten[]" id="afa" value="AfA" <?php echo check_besonderheiten('AfA'); ?>>
						<label class="custom-control-label" for="afa">AfA</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input class="custom-control-input" type="checkbox" name="besonderheiten[]" id="kapitalanlage" value="Kapitalanlage" <?php echo check_besonderheiten('Kapitalanlage'); ?>>
						<label class="custom-control-label" for="kapitalanlage">Kapitalanlage</label>
					</div>
					<div class="custom-control custom-checkbox custom-control-inline">
						<input class="custom-control-input" type="checkbox" name="besonderheiten[]" id="sanierungs-afa" value="Sanierungs-Afa" <?php echo check_besonderheiten('Sanierungs-Afa'); ?>>
						<label class="custom-control-label" for="sanierungs-afa">Sanierungs-Afa</label>
					</div>
				</div>
			</div>

			<div class="row description">
				<!-- Baujahr -->
				<div class="col-md-6">
					<div class="form-group">
						<input type="text" class="form-control" name="baujahr" placeholder="Baujahr" value="<?php echo $baujahr; ?>">
					</div>
				</div>

				<!-- Grundfläche (Grundstück, Gewerbeimmobilie) -->
				<div class="col-md-6" id="grundflaeche">
					<div class="form-group">
						<input type="text" class="form-control" name="grundflaeche" placeholder="Grundfläche in m²" value="<?php echo $grundflaeche; ?>">
					</div>
				</div>

				<!-- Wohnfläche (Wohnung, Haus, Renditeobjekt) -->
				<div class="col-md-6" id="wohnflaeche">
					<div class="form-group">
						<input type="text" class="form-control" name="wohnflaeche" placeholder="Wohnfläche in m²" value="<?php echo $wohnflaeche; ?>">
					</div>
				</div>

				<!-- Anzahl der Zimmer (Wohnung, Haus, Renditeobjekt) -->
				<div class="col-md-6" id="zimmer">
					<div class="form-group">
						<input type="text" class="form-control" name="zimmer" placeholder="Anzahl der Zimmer" value="<?php echo $zimmer; ?>">
					</div>
				</div>

				<!-- Anzahl der Geschosse (Wohnung, Haus, Renditeobjekt, Gewerbeimmobilie) -->
				<div class="col-md-6" id="geschosse">
					<div class="form-group">
						<input type="text" class="form-control" name="geschosse" placeholder="Anzahl der Geschosse" value="<?php echo $geschosse; ?>">
					</div>
				</div>
			</div>

			<!-- Energieausweis -->
			<div class="description" id="energieausweis">
				<div class="form-group">
					<div class="form-check form-check-inline">
						<label class="form-check-label">Energieausweis: &nbsp;</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" class="custom-control-input" name="energieausweis" id="energieausweis_ja" value="ja" <?php echo check_art('ja'); ?>>
						<label class="custom-control-label" for="energieausweis_ja">ja</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" class="custom-control-input" name="energieausweis" id="energieausweis_nein" value="nein" <?php echo check_art('nein'); ?>>
						<label class="custom-control-label" for="energieausweis_nein">nein</label>
					</div>
				</div>
			</div>

			<!-- Besichtigung -->
			<div class="form-group description">
				<label for="besichtigung" control-label">Wann ist die Besichtigung gewünscht:</label>
				<textarea class="form-control" rows="3" name="besichtigung" placeholder="Wann ist die Besichtigung gewünscht?"><?php echo $besichtigung; ?></textarea>
			</div>

			<!-- Bemerkungen -->
			<div class="form-group description">
				<label for="bemerkungen" control-label">Weitere Bemerkungen:</label>
				<textarea class="form-control" rows="3" name="bemerkungen" placeholder="Weitere Bemerkungen ..."><?php echo $bemerkungen; ?></textarea>
			</div>

			<!-- Absenden -->
			<div class="text-right">
				<button type="submit" class="btn btn-primary" name="weiter" value="weiter">weiter zu Seite 2/2</button>
			</div>
		</form>
	<?php } elseif ($formular == 2) { ?>
		<?php if ($alert !== '') { ?>
			<div class="col-12">
				<div class="row">
					<?php echo $alert; ?>
				</div>
			</div>
		<?php } ?>
		<form action="" method="post">
			<!-- Kontaktdaten -->
			<!-- Vor- & Nachname -->
			<div class="form-row">
				<div class="form-group col-sm-6">
					<label class="sr-only" for="kd_vorname">Vorname</label>
					<input type="text" class="form-control" name="kd_vorname" id="kd_vorname" value="<?php echo $kd_vorname; ?>" title="Bitte geben Sie Ihren Vornamen an" autocomplete="given-name" placeholder="Vorname" required>
				</div>
				<div class="form-group col-sm-6">
					<label class="sr-only" for="kd_nachname">Nachname</label>
					<input type="text" class="form-control" name="kd_nachname" id="kd_nachname" value="<?php echo $kd_nachname; ?>" title="Bitte geben Sie Ihren Nachnamen an" autocomplete="gfamily-name" placeholder="Nachname" required>
				</div>
			</div>
			<!-- Gebrutsdatum -->
			<!-- <div class="form-row">
				<div class="form-group col-sm-12">
					<label class="sr-only" for="kd_geburtsdatum">Geburtsdatum</label>
					<input type="text" class="form-control" name="kd_geburtsdatum" id="kd_geburtsdatum" value="<?php //echo $kd_geburtsdatum; ?>" title="Bitte geben Sie Ihr Geburtsdatum im Format TT.MM.JJJJ an" autocomplete="bday" placeholder="Geburtsdatum" pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$" required>
				</div>
			</div> -->

			<!-- Straße, Hausnr. -->
			<div class="form-row">
				<div class="form-group col-sm-12">
					<label class="sr-only" for="kd_strasse">Straße, Hausnr.</label>
					<input type="text" class="form-control" name="kd_strasse" placeholder="Straße, Hausnr." value="<?php echo $kd_strasse; ?>">
				</div>
			</div>

			<div class="form-row">
				<!-- PLZ -->
				<div class="form-group col-md-4">
					<label class="sr-only" for="kd_plz">PLZ</label>
					<input type="text" class="form-control" name="kd_plz" placeholder="PLZ" value="<?php echo $kd_plz; ?>" pattern="[0-9]{5}" >
				</div>
				<!-- Ort -->
				<div class="form-group col-md-8">
					<label class="sr-only" for="kd_ort">Ort</label>
					<input type="text" class="form-control" name="kd_ort" placeholder="Ort" value="<?php echo $kd_ort; ?>">
				</div>
			</div>

			<!-- Telefon -->
			<div class="form-row">
				<div class="form-group col-sm-12">
					<label class="sr-only" for="kd_tel">Telefon</label>
					<input type="tel" class="form-control" name="kd_tel" id="kd_tel" value="<?php echo $kd_tel; ?>" title="Bitte geben Sie Ihre Telefonnummer an" autocomplete="tel" placeholder="Telefonnummer">
				</div>
			</div>
			<!-- E-Mail -->
			<div class="form-row">
				<div class="form-group col-sm-12">
					<label class="sr-only" for="kd_email">E-Mail</label>
					<input type="email" class="form-control" name="kd_email" id="kd_email" value="<?php echo $kd_email; ?>" placeholder="E-Mail" autocomplete="email" required>
				</div>
			</div>

			<!-- Datenschutz -->
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" name="datenschutz" id="datenschutz" value="Datenschutzbestimmungen" required>
				<label class="custom-control-label" for="datenschutz"><small>Ich habe die <a href="datenschutz.php" onclick="trackMenu(this,'Datenschutz');" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese.</small></label>
			</div>

			<input type="hidden" name="art" value="<?php echo $art; ?>">
			<input type="hidden" name="strasse" value="<?php echo $strasse; ?>">
			<input type="hidden" name="plz" value="<?php echo $plz; ?>">
			<input type="hidden" name="ort" value="<?php echo $ort; ?>">
			<input type="hidden" name="besonderheiten" value="<?php echo $besonderheiten; ?>">
			<input type="hidden" name="grundflaeche" value="<?php echo $grundflaeche; ?>">
			<input type="hidden" name="wohnflaeche" value="<?php echo $wohnflaeche; ?>">
			<input type="hidden" name="zimmer" value="<?php echo $zimmer; ?>">
			<input type="hidden" name="geschosse" value="<?php echo $geschosse; ?>">
			<input type="hidden" name="energieausweis" value="<?php echo $energieausweis; ?>">
			<input type="hidden" name="besichtigung" value="<?php echo $besichtigung; ?>">
			<input type="hidden" name="bemerkungen" value="<?php echo $bemerkungen; ?>">

			<!-- Absenden -->
			<div class="form-row">
				<div class="form-group col-sm-12 text-right">
					<button type="submit" class="btn btn-primary" name="submit" value="Jetzt absenden!">Jetzt absenden!</button>
				</div>
			</div>
		</form>
	<?php } elseif ($formular == 3) { ?>
		<?php if ($alert !== '') { ?>
			<div class="col-12">
				<div class="row">
					<?php echo $alert; ?>
				</div>
			</div>
		<?php } ?>

		<div class="col-12">
			<div class="row">
				<a href="index.php" class="btn btn-primary" title="Startseite">Zurück zur Startseite!</a>
			</div>
		</div>
	<?php } ?>
</div>


<?php
	require ('footer.php');
?>