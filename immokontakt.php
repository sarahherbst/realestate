<?php
	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$fehler 		= 0;
	$alert 			= '';

	// Formular wird übertragen
	if (isset($_POST['kd_vorname'])) {

		// Daten einlesen
		$kd_vorname 			= mysqli_escape_string($db, $_POST['kd_vorname']);
		$kd_nachname 			= mysqli_escape_string($db, $_POST['kd_nachname']);
		$kd_email 				= mysqli_escape_string($db, $_POST['kd_email']);
		$kd_tel 				= mysqli_escape_string($db, $_POST['kd_tel']);
		$kd_bemerkung 			= mysqli_escape_string($db, $_POST['kd_bemerkung']);
		$obj_id 				= mysqli_escape_string($db, $_POST['obj_id']);
		$obj_objektnr_extern 	= mysqli_escape_string($db, $_POST['obj_objektnr_extern']);
		$obj_titel 				= mysqli_escape_string($db, $_POST['obj_titel']);
		$obj_art 				= mysqli_escape_string($db, $_POST['obj_art']);
		$obj_unterart 			= mysqli_escape_string($db, $_POST['obj_unterart']);
		$obj_vermarktungsart 	= mysqli_escape_string($db, $_POST['obj_vermarktungsart']);
		$honeypot 				= mysqli_escape_string($db, $_POST['honey']);

		if ($fehler == 0) {

			// Daten für XML-Objekt auslesen
			$objekt_sql 	= sql_select_where('all', 'objekte', 'obj_id', $obj_id, '', '');
			$objekt_row 	= mysqli_fetch_assoc($objekt_sql);

			$obj_anid 		= $objekt_row['obj_openimmo_anid'];
			$date 			= date('d.m.Y');

			$obj_anbieter	= $objekt_row['obj_anbieternr'];
			$obj_immo_id	= $objekt_row['obj_openimmo_obid'];
			$siteurl 		= $microsite_url.$_SERVER['REQUEST_URI'];
			$obj_strasse	= $objekt_row['obj_strasse'];
			$obj_ort		= $objekt_row['obj_ort'];
			$obj_land		= $objekt_row['obj_land'];

			// XML-Objekt erzeugen
			$xml_header = '<?xml version="1.0" encoding="ISO-8859-15"?><openimmo_feedback></openimmo_feedback>';
			$xml = new SimpleXMLElement($xml_header);

			$xml->addChild('version', "1.2.5");

			$sender 	= $xml->addChild('sender');
			if($obj_anid !== '') {
				$sender->addChild('openimmo_anid', "$obj_anid");
			}
			if($date !== '') {
				$sender->addChild('datum', "$date");
			 }

			$objekt 	= $xml->addChild('objekt');
			if($obj_anbieter !== '') {	
				$objekt->addChild('anbieter_id', "$obj_anbieter");
			}
			if($obj_immo_id !== '') {	
				$objekt->addChild('oobj_id', "$obj_immo_id");
			}
			if($siteurl !== '') {	
				$objekt->addChild('expose_url', "$siteurl");
			}
			if($obj_vermarktungsart !== '') {	
				$objekt->addChild('vermarktungsart', "$obj_vermarktungsart");
			}
			if($obj_titel !== '') {	
				$objekt->addChild('bezeichnung', "$obj_titel");
			}
			if($obj_strasse !== '') {	
				$objekt->addChild('strasse', "$obj_strasse");
			}
			if($obj_ort !== '') {	
				$objekt->addChild('ort', "$obj_ort");
			}
			if($obj_land !== '') {	
				$objekt->addChild('land', "$obj_land");
			}

			$interessent = $objekt->addChild('interessent');
			if($kd_vorname !== '') {
				$interessent->addChild('vorname', "$kd_vorname");
			}
			if($kd_nachname !== '') {
				$interessent->addChild('nachname', "$kd_nachname");
			}
			if($kd_tel !== '') {
				$interessent->addChild('tel', "$kd_tel");
			}
			if($kd_email !== '') {
				$interessent->addChild('email', "$kd_email");
			}
			if($kd_bemerkung !== '') {
				$interessent->addChild('anfrage', "$kd_bemerkung");
			}

			Header('Content-type: text/xml');
			$printxml = $xml->asXML();

			// Honeypot checken
			if ( !empty($honeypot) ) {
				return; // evtl. Code für Error einfügen
			} else {
				// E-Mails versenden
				// Include PHPMailer class
				require('phpmailer/PHPMailerAutoload.php');

				// Template abrufen
				$message 			= file_get_contents('msg-template.html');
				$appUrl 			= $microsite_url;
				$appAktion 			= 'Kontaktanfrage zu Objekt';

				$mailHeader 		= 'Neue Kontaktanfrage zu Objekt '.$obj_objektnr_extern;

				$mailText 			= '<b>Es besteht Interesse an folgendem Exposé:</b><br>';
				if($obj_objektnr_extern !== '') {
					$mailText 			.= 'Objektnummer (extern): '.$obj_objektnr_extern.'<br>';
				}
				if($obj_titel !== '') {
					$mailText 			.= 'Objekttitel: '.$obj_titel.'<br>';
				}
				if($obj_art !== '') {
					$mailText 			.= 'Objektart: '.$obj_art.'<br>';
				}
				if($obj_unterart !== '') {
					$mailText 			.= 'Objekttyp: '.$obj_unterart.'<br>';
				}
				if($obj_vermarktungsart !== '') {
					$mailText 			.= 'Vermarktungsart: '.$obj_vermarktungsart.'<br><br>';
				}

				$mailText 			.= '<b>Kontaktdaten des/r Kunde/in:</b><br>';
				$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br>';
				$mailText 			.= 'E-Mail: '.$kd_email.'<br>';
				if ($kd_tel !== '' ) {
					$mailText 			.= 'Telefon: '.$kd_tel.'<br>';
				}
				if ($kd_bemerkung !== '' ) {
					$mailText 		.= '<br><b>Weitere Bemerkungen:</b><br />'.$kd_bemerkung.'<br /><br />';
				}

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
				
				$mail->addStringAttachment($printxml, 'neuekontaktanfrage.xml');

				//Absender
				$mail->SetFrom($kd_email, $kd_vorname.' '.$kd_nachname);
				$mail->Sender 		= ($email_von);
				$mail->addReplyTo($email_zu, $institut);

				if ( strpos($email_kopie, ',') !== false ) {
					$array_kopien = explode(',', $email_kopie);
					foreach ($array_kopien as $kb_kopie) {
						$mail->addBCC($kb_kopie);
						$mail->addCustomHeader('BCC: '.$kb_kopie.'');
					}
				} else {
					$mail->addBCC($email_kopie);
					$mail->addCustomHeader('BCC: '.$email_kopie.'');
				}

				//Empfänger
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

				//E-Mail versenden
				if( !$mail->Send() ) {
					$fehler++;
					$alert  .= '<h3 class="font-weight-light text-primary mb-3">Hoppla!</h3>Ihre Daten konnten leider nicht versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo;
				} else {
					// Template abrufen
					$message2 			= file_get_contents('msg-template.html');
					$appUrl 			= $microsite_url;
					$appAktion 			= '';
					
					$mailHeader 		= 'Bestätigung Ihrer Kontaktanfrage';
					$mailText    	 	= 'Sehr geehrte(r) '.$kd_vorname.' '.$kd_nachname.', <br>';
					$mailText 			.= 'Wir haben Ihre Kontaktanfrage erhalten. Wir werden uns in Kürze mit Ihnen in Verbindung setzen.<br /><br />';
					$mailText 			.= '<hr style="border: #efefef solid 1px;"><br>';
					$mailText 			.= 'Folgende Daten haben Sie angegeben:<br><br>';
					$mailText 			.= '<b>Kontaktdaten:</b><br>';
					$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br>';
					//$mailText 			.= 'Geburtsdatum: '.$kd_geburtsdatum.'<br />';
					$mailText 			.= 'E-Mail: '.$kd_email.'<br>';
					if ($kd_tel !== '') {
						$mailText 		.= 'Telefon: '.$kd_tel.'<br>';
					}
					if ( $kd_bemerkung !== '') {
						$mailText 		.= '<br><b>Weitere Bemerkungen:</b><br>'.$kd_bemerkung.'<br>';
					}
					$mailText 			.= '<br>';
					$mailText 			.= '<b>Es besteht Interesse an folgendem Exposé:</b><br>';
					if($obj_objektnr_extern !== '') {
						$mailText 			.= 'Objektnummer (extern): '.$obj_objektnr_extern.'<br>';
					}
					if($obj_titel !== '') {
						$mailText 			.= 'Objekttitel: '.$obj_titel.'<br>';
					}
					if($obj_art !== '') {
						$mailText 			.= 'Objektart: '.$obj_art.'<br>';
					}
					if($obj_unterart !== '') {
						$mailText 			.= 'Objekttyp: '.$obj_unterart.'<br>';
					}
					if($obj_vermarktungsart !== '') {
						$mailText 			.= 'Vermarktungsart: '.$obj_vermarktungsart.'<br><br>';
					}

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
					$name_empfaenger 	= $kd_vorname.' '.$kd_nachname;
					$mail2->addAddress($kd_email, $name_empfaenger);

					//Betreff
					$mail2->Subject 	= 'Bestätigung Ihrer Kontaktanfrage';

					$mail2->MsgHTML($message2);
					$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

					//Nachricht
					if( !$mail2->Send() ) {
						$fehler++;
						$alert  			.= '<h3 class="font-weight-light text-primary mb-3">Hoppla!</h3>Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo;
					} else {
						$alert 				.= '<h3 class="font-weight-light text-primary mb-3">Vielen Dank!</h3>Ihre Kontaktanfrage wurde erfolgreich weitergeleitet. Zusätzlich wurde Ihnen eine Bestätigungsmail an '.$kd_email.' gesendet.';
					}
				}
			}
		}

		if($fehler == 0) {
			$response = array(
				'error' => 0,
				'message' => $alert
			);

			echo json_encode($response);
		}
	} else {
		$response = array(
			'error' => 1,
			'message' => $alert
		);
		echo json_encode($response);
	}

?>