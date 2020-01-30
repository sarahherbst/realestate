<?php
	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$error 				= 0;
	$alert 				= '';

	// Formular wird übertragen
	// Check ob die Daten erstmal nur validiert werden sollen (validateHouseData)
	if (isset($_POST['whichfunction'])) {
		// Daten einlesen
		$im_immotyp 			= mysqli_escape_string($db, $_POST['im_immotyp']);
		$im_haustyp_haus 		= mysqli_escape_string($db, $_POST['im_haustyp_haus']);
		$im_haustyp_wohnung 	= mysqli_escape_string($db, $_POST['im_haustyp_wohnung']);
		if(!$im_haustyp_haus == '') {
			$im_haustyp = $im_haustyp_haus;
		} else {
			$im_haustyp = $im_haustyp_wohnung;
		}
		$im_wohnflaeche 		= mysqli_escape_string($db, $_POST['im_wohnflaeche']);
		if(!$im_wohnflaeche == '') {
			$im_wohnflaeche = number_format(floatval($im_wohnflaeche),2,',','');
		}
		$im_grundstuecksflaeche = mysqli_escape_string($db, $_POST['im_grundstuecksflaeche']);
		if(!$im_grundstuecksflaeche == '') {
			$im_grundstuecksflaeche = number_format(floatval($im_grundstuecksflaeche),2,',','');
		}
		$im_baujahr 			= mysqli_escape_string($db, $_POST['im_baujahr']);
		$im_strasse 			= mysqli_escape_string($db, $_POST['im_strasse']);
		$im_plz 				= mysqli_escape_string($db, $_POST['im_plz']);
		$im_ort 				= mysqli_escape_string($db, $_POST['im_ort']);

		// Preis der Immobilie bestimmen
		$search_wohnflaeche_min = $im_wohnflaeche - 20;
		$search_wohnflaeche_max = $im_wohnflaeche + 20;

		$search_wohnflaeche 	= ' AND (ref_wfl BETWEEN "'.$search_wohnflaeche_min.'" AND "'.$search_wohnflaeche_max.'")';

		if($im_grundstuecksflaeche !== '') {

			$search_grundstuecksflaeche_min = $im_grundstuecksflaeche - 20;
			$search_grundstuecksflaeche_max = $im_grundstuecksflaeche + 20;

			$search_grundstuecksflaeche = ' AND (ref_grundstueck BETWEEN "'.$search_grundstuecksflaeche_min.'" AND "'.$search_grundstuecksflaeche_max.'")';
		}

		if($im_baujahr < 1960) {
			$search_baujahr_min = $im_baujahr - 20;
			$search_baujahr_max = $im_baujahr + 20;
		} elseif($im_baujahr >= 1960 AND $im_baujahr < 1990) {
			$search_baujahr_min = $im_baujahr - 10;
			$search_baujahr_max = $im_baujahr + 10;
		} elseif ($im_baujahr >= 1990) {
			$search_baujahr_min = $im_baujahr - 5;
			$search_baujahr_max = $im_baujahr + 5;
		}

		$search_baujahr = ' AND (ref_baujahr BETWEEN "'.$search_baujahr_min.'" AND "'.$search_baujahr_max.'")';

		// Werte für Immobiloientyp für die DB-Abfrage festlegen
		if($im_immotyp == 'Einfamilienhaus') {
			$search_immotyp = '(ref_typ LIKE "REH" OR ref_typ LIKE "DHH" OR ref_typ LIKE "EFH" OR ref_typ LIKE "RH")';
		} elseif ($im_immotyp == 'Wohnung') {
			$search_immotyp = '(ref_typ LIKE "ETW" OR ref_typ LIKE "ETW-GAR" OR ref_typ LIKE "PENTH" OR ref_typ LIKE "MAIS" OR ref_typ LIKE "DTW")';
		} else {
			$search_immotyp = '(ref_typ LIKE "ZFH" OR ref_typ LIKE "MFH")';
		}

		$ref_sql = mysqli_query($db, 'SELECT ref_kaufpreis FROM referenzen WHERE '.$search_immotyp.$search_wohnflaeche.$search_grundstuecksflaeche.$search_baujahr.' AND ref_plz LIKE "'.$im_plz.'" AND ref_kaufpreis > 0');

		if(mysqli_num_rows($ref_sql) < 3) {
			$ref_sql = mysqli_query($db, 'SELECT ref_kaufpreis FROM referenzen WHERE '.$search_immotyp.$search_wohnflaeche.$search_grundstuecksflaeche.$search_baujahr.' AND ref_kaufpreis > 0');
		}

		if($ref_sql == true and mysqli_num_rows($ref_sql) >= 2) {

			$ref_row = mysqli_fetch_assoc($ref_sql);

			// kleinsten und größten Wert ermitteln
			$min_preis = $ref_row['ref_kaufpreis'];
			$max_preis = $ref_row['ref_kaufpreis'];

			while($ref_row = mysqli_fetch_assoc($ref_sql)) {
				if($ref_row['ref_kaufpreis'] < $min_preis) {
					$min_preis = $ref_row['ref_kaufpreis'];
				}

				if($ref_row['ref_kaufpreis'] > $max_preis) {
					$max_preis = $ref_row['ref_kaufpreis'];
				}
			}

			$min_preis = number_format(floatval($min_preis),0,'','.');
			$max_preis = number_format(floatval($max_preis),0,'','.');

			$alert .= 'Um Ihnen die Ergebnisse unserer Analyse zukommen zu lassen, benötigen wir Ihre Kontaktdaten.';


		} else {

			$error++;
			$alert .= 'Leider weist unsere Datenbank zu wenig vergleichende Informationen zu Ihren Eingaben auf, sodass wir Ihnen hier keine realistische Einschätzung zu dessen Wert liefern können. Bitte füllen Sie folgende Kontaktfelder aus. Anschließend melden wir uns gerne persönlich bei Ihnen, um eine optimale Wertermittlung zu besprechen.';
		}

		$response = array(
			'error' => $error,
			'message' => $alert,
			'max_preis' => $max_preis
		);

		echo json_encode($response);

	} else {
		// Funktion sendHouseData, check ob Daten übergeben wurden
		if(isset($_POST['im_immotyp'])) {
			// Daten einlesen
			$im_immotyp 			= mysqli_escape_string($db, $_POST['im_immotyp']);
			$im_haustyp_haus 		= mysqli_escape_string($db, $_POST['im_haustyp_haus']);
			$im_haustyp_wohnung 	= mysqli_escape_string($db, $_POST['im_haustyp_wohnung']);
			if(!$im_haustyp_haus == '') {
				$im_haustyp = $im_haustyp_haus;
			} else {
				$im_haustyp = $im_haustyp_wohnung;
			}
			$im_wohnflaeche 		= mysqli_escape_string($db, $_POST['im_wohnflaeche']);
			if(!$im_wohnflaeche == '') {
				$im_wohnflaeche = number_format(floatval($im_wohnflaeche),2,',','');
			}
			$im_grundstuecksflaeche = mysqli_escape_string($db, $_POST['im_grundstuecksflaeche']);
			if(!$im_grundstuecksflaeche == '') {
				$im_grundstuecksflaeche = number_format(floatval($im_grundstuecksflaeche),2,',','');
			}
			$im_baujahr 			= mysqli_escape_string($db, $_POST['im_baujahr']);
			$im_strasse 			= mysqli_escape_string($db, $_POST['im_strasse']);
			$im_plz 				= mysqli_escape_string($db, $_POST['im_plz']);
			$im_ort 				= mysqli_escape_string($db, $_POST['im_ort']);
			$im_vorname 			= mysqli_escape_string($db, $_POST['im_vorname']);
			$im_nachname 			= mysqli_escape_string($db, $_POST['im_nachname']);
			$im_email 				= mysqli_escape_string($db, $_POST['im_email']);
			$im_tel 				= mysqli_escape_string($db, $_POST['im_tel']);
			$im_kontakt 			= mysqli_escape_string($db, $_POST['im_kontakt']);

			$datainsert = sql_insert('objektbewertung', array('obj_bew_immotyp', 'obj_bew_haustyp', 'obj_bew_wohnflaeche', 'obj_bew_grundstuecksflaeche', 'obj_bew_baujahr', 'obj_bew_strasse', 'obj_bew_plz', 'obj_bew_ort', 'obj_bew_vorname', 'obj_bew_nachname', 'obj_bew_email', 'obj_bew_tel', 'obj_bew_kontakt'), array($im_immotyp, $im_haustyp, $im_wohnflaeche, $im_grundstuecksflaeche, $im_baujahr, $im_strasse, $im_plz, $im_ort, $im_vorname, $im_nachname, $im_email, $im_tel, $im_kontakt));

			if ($datainsert != true) {
				$error++;
				$alert .= 'Die Datenübertragung hat leider nicht funktioniert. Bitte kontaktieren Sie den Websiteadministrator.';
			} else {
				// Check ob Bewertung vorgenommen werden konnte
				if(isset($_POST['max_preis'])) {

					$max_preis = mysqli_escape_string($db, $_POST['max_preis']);

					// PDF schreiben
					include 'objektbewertung_pdf.php';

					// Check ob PDF erstellt wurde
					if($pdf_link == '') {
						$error++;
						$alert .= 'Das PDF mit Ihrer Immobilienbewertung konnte leider nicht erstellt werden. Bitte kontaktieren Sie den Websiteadministrator.';
					} else {
						// E-Mails versenden
						// Include PHPMailer class
						require('phpmailer/PHPMailerAutoload.php');

						// Template abrufen
						$message 			= file_get_contents('msg-template.html');
						$appUrl 			= $microsite_url;
						$appAktion 			= 'Immobilienbewertung';

						$mailHeader 		= 'Neue Immobilienbewertung!';

						$mailText 			= 'Folgende Daten sind soeben eingegangen:<br><br>';
						$mailText 			= '<b>Es wurde eine neue Objektbewertung durchgeführt.</b><br>';
						$mailText 			.= 'Immobilientyp: '.$im_immotyp.'<br>';
						if(!$im_haustyp == '') {
							$mailText 			.= 'Immobilienart: '.$im_haustyp.'<br>';
						}
						$mailText 			.= 'Wohnfläche: '.$im_wohnflaeche.' m²<br>';
						if(!$im_grundstuecksflaeche == '') {
							$mailText 			.= 'Grundstücksfläche: '.$im_grundstuecksflaeche.' m²<br>';
						}
						$mailText 			.= 'Baujahr: '.$im_baujahr.'<br><br>';

						$mailText 			.= '<b>Kontaktdaten des/r Kunde/in:</b><br>';
						$mailText 			.= $im_vorname.' '.$im_nachname.'<br>';
						$mailText 			.= 'E-Mail: '.$im_email.'<br>';
						if ($im_tel !== '' ) {
							$mailText 			.= 'Telefon: '.$im_tel.'<br>';
						}
						$mailText 			.= 'Erlaubnis zur Kontaktaufnahme: '.$im_kontakt.'<br>';

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
						$mail->addAttachment('files/'.$pdf_name);
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

							$mailHeader 		= 'Ihre Immobilienbewertung';
							$mailText    	 	= 'Sehr geehrte(r) '.$im_vorname.' '.$im_nachname.', <br><br>';
							$mailText 			.= 'vielen Dank für Ihre Objektbewertung. Im Anhang finden Sie eine PDF mit den Ergebnissen.<br /><br />';

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
							$mail2->addAttachment('files/'.$pdf_name);
							$mail2->SMTPSecure 	= 'ssl'; 			// Enable TLS encryption, `ssl` also accepted
							$mail2->Port 		= $smtp_port; 		// TCP port to connect to
							//$mail2->isHTML(true);					// Set email format to html

							//Absender
							$mail2->SetFrom($email_von, $institut);
							$mail2->Sender 		= ($email_von);
							$mail2->addReplyTo($email_zu, $institut);

							//Empfänger
							$name_empfaenger 	= $im_vorname.' '.$im_nachname;
							$mail2->addAddress($im_email, $name_empfaenger);

							//Betreff
							$mail2->Subject 	= 'Ihre Immobilienbewertung';

							$mail2->MsgHTML($message2);
							$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine HTML-kompatible E-Mail-Anwendung!';

							//Nachricht
							if( !$mail2->Send() ) {
								$error++;
								$alert  		.= 'Fehler! Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo;
							} else {
								unlink('files/'.$pdf_name);
								$alert  		.= 'Ihre Immobilienbewertung wurde erfolgreich an die von Ihnen angegebene E-Mail-Adresse '.$im_email.' versendet.<br>';
							}
						}
					}
				} else {
					// E-Mails versenden
					// Include PHPMailer class
					require('phpmailer/PHPMailerAutoload.php');

					// Template abrufen
					$message 			= file_get_contents('msg-template.html');
					$appUrl 			= $microsite_url;
					$appAktion 			= 'Immobilienbewertung';

					$mailHeader 		= 'Neue Kontaktanfrage zur Immobilienbewertung!';

					$mailText 			= 'Es lagen keine ausreichenden Daten vor, um für folgenden Kunden eine Immobilienbewertung durchzuführen. Der Kunde möchte daher individuell kontaktiert werden:<br><br>';
					$mailText 			.= 'Immobilientyp: '.$im_immotyp.'<br>';
					if(!$im_haustyp == '') {
						$mailText 			.= 'Immobilienart: '.$im_haustyp.'<br>';
					}
					$mailText 			.= 'Wohnfläche: '.$im_wohnflaeche.' m²<br>';
					if(!$im_grundstuecksflaeche == '') {
						$mailText 			.= 'Grundstücksfläche: '.$im_grundstuecksflaeche.' m²<br>';
					}
					$mailText 			.= 'Baujahr: '.$im_baujahr.'<br><br>';

					$mailText 			.= '<b>Kontaktdaten des/r Kunde/in:</b><br>';
					$mailText 			.= $im_vorname.' '.$im_nachname.'<br>';
					$mailText 			.= 'E-Mail: '.$im_email.'<br>';
					if ($im_tel !== '' ) {
						$mailText 			.= 'Telefon: '.$im_tel.'<br>';
					}
					$mailText 			.= 'Erlaubnis zur Kontaktaufnahme: '.$im_kontakt.'<br>';

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
					// $mail_empfaenger	= $institut_mail;
					$mail_empfaenger	= $email_kopie;

					$mail->addAddress($mail_empfaenger, $name_empfaenger);

					//Betreff
					$mail->Subject 		= $mailHeader;

					$mail->MsgHTML($message);
					$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

					// E-Mail an Kunden versenden
					if( !$mail->Send() ) {
						$error++;
						$alert  .= 'Ihre Daten konnten leider nicht versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
					} else {
						// Template abrufen
						$message2 			= file_get_contents('msg-template.html');
						$appUrl 			= $microsite_url;
						$appAktion 			= '';

						$mailHeader 		= 'Ihre Immobilienbewertung';
						$mailText    	 	= 'Sehr geehrte(r) '.$im_vorname.' '.$im_nachname.', <br><br>';
						$mailText 			.= 'vielen Dank für Ihre Anfrage. Leider weist unsere Datenbank zu wenig vergleichende Informationen zu Ihren Eingaben auf, sodass wir Ihnen hier keine realistische Einschätzung zu dessen Wert liefern können. Selbstverständlich melden wir uns gern zeitnah bei Ihnen, um eine optimale Wertermittlung zu besprechen.<br /><br />';

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
						$name_empfaenger 	= $im_vorname.' '.$im_nachname;
						$mail2->addAddress($im_email, $name_empfaenger);

						//Betreff
						$mail2->Subject 	= 'Ihre Immobilienbewertung';

						$mail2->MsgHTML($message2);
						$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine HTML-kompatible E-Mail-Anwendung!';

						//Nachricht
						if( !$mail2->Send() ) {
							$error++;
							$alert  .= 'Fehler! Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo;
						} else {
							$alert 	.= 'Ihre Kontaktanfrage wurde erfolgreich versendet. Wir melden uns zeitnah bei Ihnen.';
						}
					}
				}
			} // Ende $datainsert != true

			$response = array(
				'error' => $error,
				'message' => $alert
			);
			echo json_encode($response);

		} else {
			$alert .= 'Da ist wohl etwas schief gelaufen. Die Daten konnten leider nicht übermittelt werden. Bitte kontaktieren Sie den Websiteadministrator.';

			$response = array(
				'error' => 1,
				'message' => $alert
			);
			echo json_encode($response);
		}
	}

?>