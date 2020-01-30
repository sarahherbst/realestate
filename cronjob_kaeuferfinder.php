#!/usr/local/bin/php
<?php

	chdir(dirname(__FILE__));

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$error = 0;
	$alert = '';

	$importfolder = 'kaeuferfinder';
	$i = 0;

	//FUNKTION 1 => Verzeichnis auslesen und nach XMLs suchen
	if ($handle = opendir($importfolder)) {
		while (false !== ($file = readdir($handle))) {
			// Zähle Dateien in Verzeichnis
			$files 	= scandir($importfolder);

			foreach($files as $file) {
				$file_extension = pathinfo($file);

				if($file_extension['extension'] == 'xml' OR $file_extension['extension'] == 'XML') {

					$i++;

					// Wird Datei noch übertragen?
					$filesize = filesize($source);
					clearstatcache();

					sleep(5);
					$filesize2 = filesize($source);

					if (!$filesize == $filesize2) {
						$error++;
						$alert .= 'XML-Datei wurde noch nicht vollständig übertragen. Später nochmal versuchen.<br>';
					}

					// wenn XML-Datei vorhanden, lese XML-Datei aus
					if($i > 0 && $error == 0) {

						// evtl. Apostrophe ersetzen
						$filename 		= __DIR__.'/'.$importfolder.'/'.$file;

						echo $filename.'<br>';

						$file_contents 	= file_get_contents($filename);
						$file_contents 	= str_replace("'", '‘', $file_contents);
						
						$xml = new SimpleXMLElement($file_contents);

						if($xml === false) {

							$error++;
							$alert .= 'XML-Datei konnte nicht gelesen werden.<br>';

							$movexml = rename(__DIR__ . '/'.$importfolder.'/' .$file, __DIR__ . '/error/'.$file);

						} elseif($error == 0) {

							$truncate_table  		= "TRUNCATE kaeufer";
							$truncate_table_sql 	= mysqli_query($db, $truncate_table) or die(mysqli_error($db));

							if ($truncate_table_sql == true) {

								$j = 0;

								// Einzelne Käufer durchgehen
								foreach ($xml->children() as $kaeufer) {

									$j++;

									$kf_dsn 			= $kaeufer -> DSN;
									$kf_titel 			= $kaeufer -> HeadlineGesuch;
									$kf_objektart 		= $kaeufer -> Objektart;

									if($kf_objektart == 'Wohnungen') {
										$kf_objektart = 'Wohnung';
									} else if($kf_objektart == 'Häuser' || $kf_objektart == 'Einfamilienhaus' || $kf_objektart == 'Zweifamilienhaus' || $kf_objektart == 'Mehrfamilienhaus') {
										$kf_objektart = 'Haus';
									} else if($kf_objektart == 'Grundstücke') {
										$kf_objektart = 'Grundstück';
									} else if($kf_objektart == 'Anlageobjekte') {
										$kf_objektart = 'Anlageobjekt';
									}

									$kf_ort 			= $kaeufer -> Geolage;

									$kf_preis 			= $kaeufer -> Kaufpreis;
									if($kf_preis !== '') {
										$kf_preis = floatval($kf_preis);
									}

									$kf_zimmer 			= $kaeufer -> ZimmerVon;
									if($kf_zimmer !== '') {
										$kf_zimmer = floatval($kf_zimmer);
									}

									$kf_wohnflaeche 	= $kaeufer -> WohnflaecheVon;
									if($kf_wohnflaeche !== '') {
										$kf_wohnflaeche = floatval($kf_wohnflaeche);
									}

									if($kf_ort == 'Herrenalb' || $kf_ort == 'Calw') {
										$kf_area = 1;
									} elseif(
										$kf_ort == 'Bad Schönborn' ||
										$kf_ort == 'Bretten' ||
										$kf_ort == 'Bruchsal' ||
										$kf_ort == 'Dettenheim' ||
										$kf_ort == 'Eggenstein-Leopoldshafen' ||
										$kf_ort == 'Ettlingen' ||
										$kf_ort == 'Forst' ||
										$kf_ort == 'Gondelsheim' ||
										$kf_ort == 'Graben-Neudorf' ||
										$kf_ort == 'Hambrücken' ||
										$kf_ort == 'Karlsbad' ||
										$kf_ort == 'Karlsdorf-Neuthard' ||
										$kf_ort == 'Karlsruhe' ||
										$kf_ort == 'Kraichtal' ||
										$kf_ort == 'Kronau' ||
										$kf_ort == 'Kürnbach' ||
										$kf_ort == 'Linkenheim-Hochstetten' ||
										$kf_ort == 'Malsch' ||
										$kf_ort == 'Marxzell' ||
										$kf_ort == 'Oberderdingen' ||
										$kf_ort == 'Oberhausen-Rheinhausen' ||
										$kf_ort == 'Östringen' ||
										$kf_ort == 'Pfinztal' ||
										$kf_ort == 'Philippsburg' ||
										$kf_ort == 'Rheinstetten' ||
										$kf_ort == 'Stutensee' ||
										$kf_ort == 'Sulzfeld' ||
										$kf_ort == 'Ubstadt-Weiher' ||
										$kf_ort == 'Waghäusel' ||
										$kf_ort == 'Waldbronn' ||
										$kf_ort == 'Walzbachtal' ||
										$kf_ort == 'Weingarten' ||
										$kf_ort == 'Zaisenhausen'
									) { 
										$kf_area = 2;
									} else {
										if($kf_ort !== '' && isset($kaeufer -> Geolage)) {
											$kf_area = 3;
										} else {
											$kf_area = 0;
										}
									}


									$kaeufer_insert  	= sql_insert('kaeufer', array('kf_dsn', 'kf_titel', 'kf_objektart', 'kf_preis', 'kf_ort', 'kf_area', 'kf_zimmer', 'kf_wohnflaeche'), array($kf_dsn, $kf_titel, $kf_objektart, $kf_preis, $kf_ort, $kf_area, $kf_zimmer, $kf_wohnflaeche));

									if($kaeufer_insert !== true) {
										$error++;
										$alert .= 'Der Käufer Nr. '.$j.' konnte nicht in die Datenbank eingetragen werden.';
									}
								}

								if($error == 0) {
									// XML löschen
									$deletexml = unlink($filename);

									if ($deletexml !== true) {
										$error++;
										$alert .= 'XML-Datei konnte nicht gelöscht werden.<br>';
									}
								}
							} else {
								$error++;
								$alert .= 'Tabelle "Käufer" konnte nicht geleert werden.<br>';
							} // Daten auslesen
						} // Tabelle leeren
					} // XML auslesen
				} // check file-extension
			} // foreach-Schleife as $file
		} // while-Schleife readdir handle

		closedir($handle);
	} // open importfolder

	if($i >= 1) {

		require('phpmailer/PHPMailerAutoload.php');

		// Template abrufen
		$message 			= 'Es gibt eine ZIP-Datei.<br><br>Fehler: '.$error.'<br><br>Alert:'.$alert;

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
		$mail->SetFrom('Immobilien Center Ettlingen');
		$mail->Sender 		= ('info@immobiliencenter-ettlingen.de');
		$mail->addReplyTo('Sarah Herbst', 'herbst@ffemedia.de');

		//Empfänger
		$mail->addAddress('herbst@ffemedia.de', 'Sarah Herbst');

		//Betreff
		$mail->Subject 		= 'Cronjob';

		$mail->MsgHTML($message);
		$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

		//E-Mail versenden
		$mail->Send();

	}

?>