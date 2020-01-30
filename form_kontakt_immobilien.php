<?php
	$rubrik 				= $_GET['rub_id'];
	$page 					= 'rub_'.$rubrik;

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	// CSS Styles
	$text_color				= 'primary';
	$bg_color 				= 'darkblue-gradient';
	$subline 				= 'Ob Sie ein Haus kaufen, Ihre Immobilie verkaufen, Ihr Eigentum modernisieren oder sich mit einer Baufinanzierung den Traum vom Hausbau erfüllen möchten. Unsere Experten sind für Sie vor Ort.';

	// Rubrik Angaben auslesen
	if (isset($_GET['rub_id'])) {
		$rub_id 			= $_GET['rub_id'];
		$rubrik 			= $rub_id;
		$rubrik_sql 		= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
		$rubrik_row 		= mysqli_fetch_assoc($rubrik_sql);

		if ($rubrik_row['rub_title'] !== '') {
			$sitetitel 	= $rubrik_row['rub_title'];
		}
		if ($rubrik_row['rub_description'] !== '') {
			$description 	= $rubrik_row['rub_description'];
		}
		if ($rubrik_row['rub_css_txt'] !== '') {
			$text_color 	= $rubrik_row['rub_css_txt'];
		}
		if ($rubrik_row['rub_css_bg'] !== '') {
			$bg_color 		= $rubrik_row['rub_css_bg'];
		}
		if ($rubrik_row['rub_form_subline'] !== '') {
			$subline 		= $rubrik_row['rub_form_subline'];
		}

		if ($rubrik_row['rub_name'] !== '') {
			$rubrik_mail 		= $rubrik_row['rub_name'];
		} else {
			$rubrik_mail 		= 'Allgemein';
		}

		if ($rubrik_row['rub_email_zu'] !== '') {
			$rub_email_zu 		= $rubrik_row['rub_email_zu'];
		}
		if ($rubrik_row['rub_email_kopie'] !== '') {
			$rub_email_kopie 	= $rubrik_row['rub_email_kopie'];
		}
	} else {
		$rubrik 			= 'allgemein';
		$rubrik_mail 		= 'Allgemein';
	}

	$ite_rubrik 			= $rubrik_mail;

	// Einstellung Inpufelder (ON/OFF)
	$terminwunsch 			= false;
	$themenauswahl 			= true;

	//Mail-Footer
	$mailfooter_sql 		= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'mailfooter'), '', '');
	$mailfooter_row 		= mysqli_fetch_assoc($mailfooter_sql);
	$mailfooter 			= $mailfooter_row['txt_beitrag'];

	// Zeitzone
	date_default_timezone_set('Europe/Berlin');
	setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
	$timestamp = time();

	// Variablen setzen
	$kd_vorname 			= '';
	$kd_nachname 			= '';
	//$kd_geburtsdatum 		= '';
	$kd_tel 				= '';
	$kd_email 				= '';
	if ($terminwunsch == true) {
		if (!isset($kd_termin_datum)) {
			$kd_termin_datum 	=  date('Y-m-d');
		}
		if (!isset($kd_termin_zeit)) {
			$kd_termin_zeit 	=  date("H:i",$timestamp);
		}
	}
	if ($themenauswahl == true) {
		if (!isset($kd_thema)) {
			$kd_thema = '';
		}
	}
	$kd_bemerkung 			= '';

	$alert 					= '';
	$form_send 				= false;

	// Formular absenden
	if ( isset($_POST['submit']) ) {
		//honey pot field
		$honeypot = $_POST['a_password'];

		//check if the honeypot field is filled out. If not, send a mail.
		if ( !empty($honeypot) ) {
			return; // you may add code here to echo an error etc.
		} else {
			// Variablen einlesen
			$kd_vorname 		= $_POST['kd_vorname'];
			$kd_nachname 		= $_POST['kd_nachname'];
			//$kd_geburtsdatum 	= $_POST['kd_geburtsdatum'];
			//$orderdate 			= explode('.', $kd_geburtsdatum);
			//$kd_geburtsdatum 	= $orderdate[2].'-'.$orderdate[1].'-'.$orderdate[0];
			$kd_tel 			= $_POST['kd_tel'];
			$kd_email 			= $_POST['kd_email'];
			if ($terminwunsch == true) {
				$kd_termin_datum 	= $_POST['kd_termin_datum'];
				$kd_termin_zeit 	= $_POST['kd_termin_zeit'];
			}
			if ($themenauswahl == true) {
				$kd_thema 		= $_POST['kd_thema'];
			}
			$kd_bemerkung 		= $_POST['kd_bemerkung'];

			$fehler 			= 0;

			/*if (fc_alter($kd_geburtsdatum) < 18) {
				$fehler++;
				$alert .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">Eine Terminvereinbarung ist erst ab 18 Jahren möglich. <button type="button" class="close" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">&times;</span></button></div>';
				$kd_geburtsdatum = $_POST['kd_geburtsdatum'];
			} else {
				$kd_geburtsdatum = $_POST['kd_geburtsdatum'];
			}*/

			if ($fehler == 0) {
				// E-Mails versenden
				// Include PHPMailer class
				require('phpmailer/PHPMailerAutoload.php');

				// Template abrufen
				$message 			= file_get_contents('msg-template.html');
				$appUrl 			= $microsite_url;
				if ($rubrik == 'allgemein') {
					$appAktion 		= 'Terminvereinbarung';
				} else {
					$appAktion 		= $rubrik_mail;
				}
				if ($rubrik == 'allgemein') {
					$mailHeader 	= 'Neue Terminvereinbarung!';
				} else {
					$mailHeader 	= 'Neue Terminvereinbarung: Bereich '.$rubrik_mail;
				}
				$mailText 			= 'Folgende Daten sind soeben eingegangen:<br><br>';
				$mailText 			.= '<b>Kontaktdaten:</b><br />';
				$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br />';
				//$mailText 			.= 'Geburtsdatum: '.$kd_geburtsdatum.'<br />';
				if ($kd_tel !== '' ) {
					$mailText 			.= 'Telefon: '.$kd_tel.'<br />';
				}
				$mailText 			.= 'E-Mail: '.$kd_email.'<br><br>';

				if ($terminwunsch == true) {
					$mailText 			.= '<b>Terminwunsch:</b><br />';
					$ordertermin 		= explode('-', $kd_termin_datum);
					$kd_termin_datum 	= $ordertermin[2].'.'.$ordertermin[1].'.'.$ordertermin[0];
					$mailText 			.= 'Datum: '.$kd_termin_datum.'<br />';
					$mailText 			.= 'Uhrzeit: '.$kd_termin_zeit.'<br /><br />';
				}

				if ($themenauswahl == true) {
					$mailText 			.= '<b>Themenauswahl:</b><br />';
					$mailText 			.= $kd_thema.'<br /><br />';
				}

				if ($kd_bemerkung !== '' ) {
					$mailText 		.= '<b>Weitere Bemerkungen:</b><br />'.$kd_bemerkung.'<br /><br />';
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

				//Überprüfen ob es mehr als einen Empfänger gibt
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
				$mail->Subject 		= $mailHeader;

				$mail->MsgHTML($message);
				$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

				//E-Mail versenden
				if( !$mail->Send() ) {
					$fehler++;
					$alert  .= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Fehler! Ihre Daten konnten leider nicht versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
				} else {
					// Template abrufen
					$message2 			= file_get_contents('msg-template.html');
					$appUrl 			= $microsite_url;
					if ($rubrik == 'allgemein') {
						$appAktion 		= '';
					} else {
						$appAktion 		= '';
					}
					$mailHeader 		= 'Bestätigung Ihres Terminwunsches';
					$mailText    	 	= 'Sehr geehrte(r) '.$kd_vorname.' '.$kd_nachname.', <br />';
					$mailText 			.= 'Wir haben Ihren Terminwunsch erhalten. Wir werden uns in Kürze mit Ihnen in Verbindung setzen.<br /><br />';
					$mailText 			.= '<hr style="border: #efefef solid 1px;"><br />';
					$mailText 			.= 'Folgende Daten haben Sie angegeben:<br /><br />';
					$mailText 			.= '<b>Kontaktdaten:</b><br />';
					$mailText 			.= $kd_vorname.' '.$kd_nachname.'<br />';
					//$mailText 			.= 'Geburtsdatum: '.$kd_geburtsdatum.'<br />';
					if ($kd_tel !== '') {
						$mailText 		.= 'Telefon: '.$kd_tel.'<br />';
					}
					$mailText 			.= 'E-Mail: '.$kd_email.'<br><br>';

					if ($terminwunsch == true) {
						$mailText 			.= '<b>Terminwunsch:</b><br />';
						$mailText 			.= 'Datum: '.$kd_termin_datum.'<br />';
						$mailText 			.= 'Uhrzeit: '.$kd_termin_zeit.'<br /><br />';
					}

					if ($themenauswahl == true) {
						$mailText 			.= '<b>Themenauswahl:</b><br />';
						$mailText 			.= $kd_thema.'<br /><br />';
					}

					if ( $kd_bemerkung !== '') {
						$mailText 		.= '<b>Weitere Bemerkungen:</b><br />'.$kd_bemerkung.'<br /><br />';
					}
					// Schlussfloskel
					$impressumUrl 		= $microsite_url.'/impressum.php';
					$datenschutzUrl 	= $microsite_url.'/datenschutz.php';
					$institutName 		= $institut;
					$institutUrl 		= $institut_url;
					$institutImg 		= $microsite_url.'/img/mail-logo.png';
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
					$mail2->addReplyTo($email_von, $institut);

					//Empfänger
					$name_empfaenger 	= $kd_vorname.' '.$kd_nachname;
					$mail2->addAddress($kd_email, $name_empfaenger);

					//Betreff
					$mail2->Subject 	= 'Bestätigung Ihres Terminwunsches';

					$mail2->MsgHTML($message2);
					$mail2->AltBody 	= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

					//Nachricht
					if( !$mail2->Send() ) {
						$fehler++;
						$alert  			.= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Fehler! Es konnte leider keine Bestätigungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
					} else {
						//Tracking in Datenbank eintragen
						$bezeichnung_mail 	= 'Anforderung Termin';

						if ($rubrik == 'allgemein') {
							$rubrik_mail 	= 'Ansprechpartner';
						} else {
							$rubrik_mail 	= $ite_rubrik;
						}

						$praefix_mail 		= '998';
						$url_mail 			= $microsite_url.'/form_kontakt.php?rub_id='.$rub_id;
						$sql_track_mail 	= sql_insert('item_tracking', array('ite_rubrik', 'ite_praefix', 'ite_name', 'ite_position', 'ite_url'), array($rubrik_mail, $page, $bezeichnung_mail, 'Formular', $url_mail));
						$alert_upload 		= 'Zusätzlich wurde Ihnen eine Bestätigungsmail an '.$kd_email.' gesendet.';
					}

					$alert  				.= '<div class="alert alert-success alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Ihr Terminwunsch wurde erfolgreich weitergeleitet. '.$alert_upload.'</div>';
					$form_send 				= true;
				}
			}
		}
	}

	include('header.php');
?>

<!-- Header (Logo, Überschrift, Einleitung) -->
<section class="bg-<?php echo $bg_color; ?> first-section">

	<!-- Überschrift & Einleitung-->
	<div class="container py-4">
		<?php if ($rubrik !== 'allgemein') { ?>
			<span class="text-uppercase text-light"><?php echo $rubrik_row['rub_name']; ?></span>
		<?php } ?>
		<h1 class="display-4 text-light">Jetzt Termin vereinbaren!</h1>
		<p class="h3 text-light font-weight-light"><?php echo $subline; ?></p>
	</div>
</section>


<!-- Inhalt -->
<div class="bg-white container p-5 mb-5">
	<?php if ($form_send !== true) { ?>
		<?php if ($alert !== '') { ?>
			<div class="col-12">
				<div class="row">
					<?php echo $alert; ?>
				</div>
			</div>
		<?php } ?>
		<form action="" method="post">
			<div class="row">
				<div class="col-12">
					<h4 class="font-weight-light text-<?php echo $text_color; ?>">Kontaktdaten</h4>
				</div>
			</div>

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

			<?php if ($terminwunsch == true) { ?>
			<div class="row">
				<div class="col-12 mt-3">
					<h4 class="font-weight-light text-<?php echo $text_color; ?>">Wunschtermin</h4>
				</div>
			</div>

			<!-- Terminwunsch -->
			<div class="form-row">
				<div class="form-group col-sm-6">
					<label for="kd_termin_datum">Datum:</label>
					<input type="date" class="form-control" name="kd_termin_datum" id="kd_termin_datum" value="<?php echo $kd_termin_datum; ?>" min="2018-01-01" required>
				</div>
				<div class="form-group col-sm-6">
					<label for="kd_termin_zeit">Uhrzeit:</label>
					<input type="time" class="form-control" name="kd_termin_zeit" id="kd_termin_zeit" value="<?php echo $kd_termin_zeit; ?>" required>
				</div>
			</div>
			<?php } ?>

			<?php if ($themenauswahl == true) { ?>
			<div class="row">
				<div class="col-12 mt-3">
					<h4 class="font-weight-light text-<?php echo $text_color; ?>">Themenauswahl</h4>
				</div>
			</div>

			<div class="custom-control custom-radio custom-control-inline">
				<input type="radio" class="custom-control-input" name="kd_thema" id="kd_thema1" value="Kaufen" <?php echo ($kd_thema == 'Kaufen' ? 'checked' : ''); ?> required>
				<label class="custom-control-label" for="kd_thema1">
					<small>Kaufen</small>
				</label>
			</div>
			<div class="custom-control custom-radio custom-control-inline">
				<input type="radio" class="custom-control-input" name="kd_thema" id="kd_thema2" value="Verkaufen" <?php echo ($kd_thema == 'Verkaufen' ? 'checked' : ''); ?>>
				<label class="custom-control-label" for="kd_thema2">
					<small>Verkaufen</small>
				</label>
			</div>
			<div class="custom-control custom-radio custom-control-inline">
				<input type="radio" class="custom-control-input" name="kd_thema" id="kd_thema3" value="Objektbewertung" <?php echo ($kd_thema == 'Objektbewertung' ? 'checked' : ''); ?>>
				<label class="custom-control-label" for="kd_thema3">
					<small>Objektbewertung</small>
				</label>
			</div>
			<?php } ?>

			<!-- Bemerkungen -->
			<div class="form-row">
				<div class="form-group col-12 mt-3">
					<label for="kd_bemerkung" control-label">Bemerkungen:</label>
					<textarea class="form-control" rows="3" name="kd_bemerkung" placeholder="Hier können Sie optional weitere Informationen verfassen."><?php echo $kd_bemerkung; ?></textarea>
				</div>
			</div>

			<!-- Datenschutz -->
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" name="datenschutz" id="datenschutz" value="Datenschutzbestimmungen" required>
				<label class="custom-control-label" for="datenschutz">
					<small>Ich habe die <a href="datenschutz.php" onclick="trackMenu(this,'Datenschutz');" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese.</small>
				</label>
			</div>

			<!-- Create fields for the honeypot -->
			<label class="nono" for="a_password">Password</label>
			<input name="a_password" type="text" id="a_password" class="nono" placeholder="Password" autocomplete="nope">
			<!-- honeypot fields end -->

			<!-- Absenden -->
			<div class="form-row">
				<div class="form-group col-sm-12 text-right">
					<button type="submit" class="btn btn-<?php echo $text_color; ?>" name="submit" value="Jetzt absenden!">Jetzt absenden!</button>
				</div>
			</div>
		</form>
	<?php } else { ?>
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
	require('footer.php');
?>