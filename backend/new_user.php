<?php
	$page = "new_user";
	require("header.php");

	$fehlerangabe = "";

	$anrede 	= "";
	$vorname 	= "";
	$nachname 	= "";
	$position 	= "";
	$filiale 	= "";
	$strasse 	= "";
	$plz 		= "";
	$ort 		= "";
	$tel 		= "";
	$fax 		= "";
	$email 		= "";

	if (isset($_POST['erstellen'])) {
		//einlesen der im Formular angegebenen Werte*/
		$anrede 	= mysqli_real_escape_string($db, $_POST['anrede']);
		$vorname 	= mysqli_real_escape_string($db, $_POST['vorname']);
		$nachname 	= mysqli_real_escape_string($db, $_POST['nachname']);
		$position 	= mysqli_real_escape_string($db, $_POST['position']);
		$filiale 	= mysqli_real_escape_string($db, $_POST['filiale']);
		$strasse 	= mysqli_real_escape_string($db, $_POST['strasse']);
		$plz 		= mysqli_real_escape_string($db, $_POST['plz']);
		$ort 		= mysqli_real_escape_string($db, $_POST['ort']);
		$tel 		= mysqli_real_escape_string($db, $_POST['tel']);
		$fax 		= mysqli_real_escape_string($db, $_POST['fax']);
		$email 		= mysqli_real_escape_string($db, $_POST['email']);
		$access 	= mysqli_real_escape_string($db, $_POST['user_access']);
		//Registrierungscode erstellen
		$regcode 	= rand(1, 99999999);

		if ($access == 'Editor') {
			$access = 'editor';
		} elseif ($access == 'Admin') {
			$access = 'admin';
		}

		//Variablen für Fehlerprüfung
		$fehler     = 0;

		$email_sql = mysqli_query($db, "SELECT * FROM user WHERE use_email = '$email'");
		if (mysqli_num_rows($email_sql) == 1) {
			$fehler = 1;
			$fehlerangabe   .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Ein Benutzer mit dieser E-Mail existiert bereits.</div>";
		} else {
			//Benutzer in die Datenbank eintragen
			$use_eintrag 	= "INSERT INTO user";
			$use_eintrag 	.= "(use_anrede, use_vorname, use_nachname, use_position, use_filiale, use_strasse, use_plz, use_ort, use_tel, use_fax, use_email, use_access, use_regcode, new_user, new_time, new_date, chg_user, chg_time, chg_date)";
			$use_eintrag 	.= " VALUES ('$anrede', '$vorname', '$nachname', '$position', '$filiale', '$strasse', '$plz', '$ort', '$tel', '$fax', '$email', '$access', '$regcode', '$user_email', curtime(), curdate(), '$user_email', curtime(), curdate())";
			$use_query 		= mysqli_query($db, $use_eintrag) or die(mysqli_error($db));
			if ($use_query == true) {
				$fehlerangabe .= "<div class='alert alert-success alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Der Benutzer wurde erfolgreich erstellt.</div>";

				//User-ID abfragen für Registrierungsmail
				$use_sql = "SELECT * FROM user WHERE use_vorname = '$vorname' AND use_nachname = '$nachname'";
				$use_res = mysqli_query($db, $use_sql) or die(mysqli_error($db));
				$use_row = mysqli_fetch_object($use_res);
				$user_id = $use_row->use_id;

				//Mail-Footer abfragen
				$sql_mailfooter 	= "SELECT * FROM texte WHERE txt_institut = '$institut_id' AND txt_schluessel = 'mailfooter'";
				$result_mailfooter 	= mysqli_query($db, $sql_mailfooter) or die(mysqli_error($db));
				$row_mailfooter 	= mysqli_fetch_object($result_mailfooter);
				/*Variablen vergeben*/
				$mailfooter 		= $row_mailfooter->txt_text;

				//Registrierungsmail mit Aktivierungslink versenden
				// Include PHPMailer class
				require("phpmailer/PHPMailerAutoload.php");

				//Setup PHPMailer
				$mail 				= new PHPMailer;
				$mail->setLanguage("de", "phpmailer/language/");
				$mail->CharSet 		="UTF-8";
				//$mail ->SMTPDebug = 3; 					// Enable verbose debug output
				$mail->isSMTP(); 						// Set mailer to use SMTP
				$mail->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
				$mail->SMTPOptions 	= array(
					"ssl" => array(
						"verify_peer" => false,
						"verify_peer_name" => false,
						"allow_self_signed" => true
					)
				);
				$mail->SMTPAuth 	= true; 			// Enable SMTP authentication
				$mail->Username 	= $smtp_user; 		// SMTP username
				$mail->Password 	= $smtp_passwort; 	// SMTP password
				$mail->SMTPSecure 	= "ssl"; 			// Enable TLS encryption, `ssl` also accepted
				$mail->Port 		= $smtp_port; 		// TCP port to connect to
				$mail->isHTML(true);					// Set email format to html

				//Absender
				$mail->SetFrom($email_von, $institut);
				$mail->Sender 		= ($email_von);
				$mail->addReplyTo($email_zu, $institut);

				//Empfänger
				$name_empfaenger 	= $vorname." ".$nachname;
				$mail->addAddress($email, $name_empfaenger);

				//Betreff
				$mail->Subject 		= "Ihre Registrierung für das Backend ".$sitetitel."!";

				//Nachricht
				$mail->Body    		= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>Sehr geehrte(r) ".$vorname." ".$nachname.", </p>";
				$mail->Body 		.= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>";
				$mail->Body    		.= "Sie wurden für das Backend ".$sitetitel." unseres Institus registriert. Um die Registrierung vollständig abzuschließen, klicken Sie bitte <a href='".$microsite_url."/backend/aktivierung.php?id=".$user_id."&regcode=".$regcode."' alt='Registrierungscode' title='Registrierungscode'>diesen Link</a> zur Erstellung Ihres Passwortes und zur Aktivierung Ihres Accounts.</p><br><br><br>";
				$mail->Body 		.= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>".$mailfooter."</p>";

				$mail->AltBody 		= "Sehr geehrte(r) ".$vorname." ".$nachname.", \r\n Sie wurden für das Backend ".$sitetitel." unseres Institus registriert. Um die Registrierung vollständig abzuschließen, klicken Sie bitte folgenden Link zur Erstellung Ihres Passwortes und zur Aktivierung Ihres Accounts.\r\n \r\n Aktivierungslink: https://".$path_parts['dirname']."/aktivierung.php?id=".$user_id."&regcode=".$regcode."\r\n \r\n";
				$mail->AltBody 		.= "_______________________\r\n";
				$mailfooter = str_replace('<br />', '\r\n', $mailfooter);
				$mailfooter = str_replace('<hr>', '', $mailfooter);
				$mail->AltBody 		.= $mailfooter;
				
				//E-Mail versenden
				if( !$mail->Send() ) {
					$fehler 		= "1";
					$fehlerangabe  .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Fehler! Es konnte leider keine Registrierungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>".$mail->ErrorInfo."</div>";
				} else {
					$fehlerangabe  .= "<div class='alert alert-success alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Es wurde eine Aktivierungsmail an den neuen Benutzer gesandt.</div>";
				}
			} else {
				$fehler = 1;
				$fehlerangabe .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Fehler! Der Benutzer konnte leider nicht erstellt werden.</div>";
			} //Eintragung in die Datenbank

		} // Prüfung Mail & Eintragung in die Datenbank + Registrierungsmail
	} // Klick Btn "Benutzer erstellen"

	if (isset($_POST['multiple_erstellen'])) {
		//CSV Datei temporär hochladen
		$csv_datei = $_FILES["file"]["tmp_name"];

		if($_FILES["file"]["size"] > 0) {
			$fehler = 0;
			$datei_inhalt= fopen($csv_datei, 'r');
			/*fgetcsv ($datei_inhalt , 4096 , ";");*/
			$aufzaehlung = 1;
			if($_FILES["file"]["size"] > 0) {
				$fehler = 0;
				$aufzaehlung = 1;
				$datei_inhalt= fopen($csv_datei, 'r');
				while( ($inhalt = fgetcsv ($datei_inhalt , 110000 , ";")) !== false ) {
					$num = count($inhalt);
					$inhalt = array_map("utf8_encode", $inhalt);
					$inhalt = array_map("mysqli_real_escape_string", $inhalt);

					$aufzaehlung++;
					$anrede 		= $inhalt[0];
					$vorname 		= $inhalt[1];
					$nachname 		= $inhalt[2];
					$position 		= $inhalt[3];
					$filiale 		= $inhalt[4];
					$strasse 		= $inhalt[5];
					$plz 			= $inhalt[6];
					$ort 			= $inhalt[7];
					$tel 			= $inhalt[8];
					$fax 			= $inhalt[9];
					$email 			= $inhalt[10];
					$access 		= $inhalt[11];
					$regcode 		= rand(1, 99999999);

					if ($access == 'Editor') {
						$access = 'editor';
					} elseif ($access == 'Admin') {
						$access = 'admin';
					}

					// prüfen, ob Benutzer schon vorhanden ist
					$result = mysqli_query($db, "SELECT * FROM user WHERE use_email = 'email' ");
					$num_rows = mysqli_num_rows($result);

					if ($numrows) {
						// Wenn Benutzer vorhanden ist
						$fehler = 1;
						$multiple_fehlerangabe .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Ein Benutzer mit dieser E-Mail existiert bereits.</div>";
					} else {
						// Wenn Benutzer nicht vorhanden ist
						$kun_eintrag = "INSERT INTO user";
						$kun_eintrag .= "(use_anrede, use_vorname, use_nachname, use_position, use_filiale, use_strasse, use_plz, use_ort, use_tel, use_fax, use_email, use_access, use_regcode, ";
						$kun_eintrag .= "new_user, new_time, new_date, chg_user, chg_time, chg_date)";
						$kun_eintrag .= " VALUES ('$anrede', '$vorname', '$nachname', '$position', '$filiale', '$strasse', '$plz', '$ort', '$tel', '$fax', '$email', '$access', '$regcode', ";
						$kun_eintrag .= "'$user_email', curtime(), curdate(), '$user_email', curtime(), curdate() ) ";
						$kun_query = mysqli_query($db, $kun_eintrag) or die(mysqli_error($db));

						//Wenn Benutzer erstellt wurde, E-Mail raussenden
						if ($kun_query == true) {
							$x_multiple_fehlerangabe .= "<p>Der Benutzer ".$email." wurde erfolgreich erstellt.</p>";

							//User-ID abfragen für Registrierungsmail
							$use_sql = "SELECT * FROM user WHERE use_vorname = '$vorname' AND use_nachname = '$nachname'";
							$use_res = mysqli_query($db, $use_sql) or die(mysqli_error($db));
							$use_row = mysqli_fetch_object($use_res);
							$user_id = $use_row->use_id;

							//Mail-Footer abfragen
							$sql_mailfooter 	= "SELECT * FROM texte WHERE txt_institut = '$institut_id' AND txt_schluessel = 'mailfooter'";
							$result_mailfooter 	= mysqli_query($db, $sql_mailfooter) or die(mysqli_error($db));
							$row_mailfooter 	= mysqli_fetch_object($result_mailfooter);
							$mailfooter 		= $row_mailfooter->txt_text;

							//Registrierungsmail mit Aktivierungslink versenden
							// Include PHPMailer class
							require_once("phpmailer/PHPMailerAutoload.php");

							//Setup PHPMailer
							$mail 				= new PHPMailer;
							$mail->setLanguage("de", "phpmailer/language/");
							$mail->CharSet 		="UTF-8";
							//$mail ->SMTPDebug = 3; 					// Enable verbose debug output
							$mail->isSMTP(); 						// Set mailer to use SMTP
							$mail->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
							$mail->SMTPOptions 	= array(
								"ssl" => array(
									"verify_peer" => false,
									"verify_peer_name" => false,
									"allow_self_signed" => true
								)
							);
							$mail->SMTPAuth 	= true; 			// Enable SMTP authentication
							$mail->Username 	= $smtp_user; 		// SMTP username
							$mail->Password 	= $smtp_passwort; 	// SMTP password
							$mail->SMTPSecure 	= "tls"; 			// Enable TLS encryption, `ssl` also accepted
							$mail->Port 		= $smtp_port; 		// TCP port to connect to
							$mail->isHTML(true);					// Set email format to html

							//Absender
							$mail->SetFrom($email_von, $institut);
							$mail->Sender 		= ($email_von);
							$mail->addReplyTo($email_zu, $institut);

							//Empfänger
							$name_empfaenger 	= $vorname." ".$nachname;
							$mail->addAddress($email, $name_empfaenger);

							//Betreff
							$mail->Subject 		= "Ihre Registrierung für das Backend ".$sitetitel."!";

							//Nachricht
							$mail->Body    		= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>Sehr geehrte(r) ".$vorname." ".$nachname.", </p>";
							$mail->Body 		.= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>";
							$mail->Body    		.= "Sie wurden für das Backend ".$sitetitel." unseres Institus registriert. Um die Registrierung vollständig abzuschließen, klicken Sie bitte <a href='https://".$path_parts['dirname']."/aktivierung.php?id=".$user_id."&regcode=".$regcode."' alt='Registrierungscode' title='Registrierungscode'>diesen Link</a> zur Erstellung Ihres Passwortes und zur Aktivierung Ihres Accounts.</p><br><br><br>";
							$mail->Body 		.= "<p style='color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;'>".$mailfooter."</p>";

							$mail->AltBody 		= "Sehr geehrte(r) ".$vorname." ".$nachname.", \r\n Sie wurden für das Backend ".$sitetitel." unseres Institus registriert. Um die Registrierung vollständig abzuschließen, klicken Sie bitte folgenden Link zur Erstellung Ihres Passwortes und zur Aktivierung Ihres Accounts.\r\n \r\n Aktivierungslink: https://".$path_parts['dirname']."/aktivierung.php?id=".$user_id."&regcode=".$regcode."\r\n \r\n";
							$mail->AltBody 		.= "_______________________\r\n";
							$mail->AltBody 		.= $mailfooter;
							
							//E-Mail versenden
							if( !$mail->Send() ) {
								$fehler 		= "1";
								$multiple_fehlerangabe  .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Fehler! Es konnte leider keine Registrierungsmail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>".$mail->ErrorInfo."</div>";
							} else {
								$multiple_fehlerangabe  .= "<div class='alert alert-success alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Es wurde eine Aktivierungsmail an den neuen Benutzer gesandt.</div>";
							}
						} else {
							$fehler++;
						}
					} //Wenn Benutzer nicht vorhanden
				}
			}
			fclose($datei_inhalt);
			$aufzaehlung = $aufzaehlung - 1;
			if ($fehler == 0) {
				$multiple_fehlerangabe .= "<div class='alert alert-success' role='alert'>Es wurden insgesamt $aufzaehlung Benutzer der Datenbank hinzugefügt.</div>";
				$multiple_fehlerangabe .= "<div class='alert alert-success' role='alert'>".$x_multiple_fehlerangabe."</div>";
			} else {
				$multiple_fehlerangabe .= "<div class='alert alert-danger alert-dismissible' role='alert'><b>$fehler Fehler!</b> Es konnten keine Benutzer der Datenbank hinzugefügt werden.</div>";
			}
		} else {
			$info .= "<p class='bg-info'>Es wurde bisher keine CSV-Datei hochgeladen.</p>";
		}
	}// Klick "Benutzerliste hochladen"
?>

<!-- Breadcrumbs -->
<ol class='breadcrumb'>
	<li class='breadcrumb-item'><a href="user.php">Benutzer</a></li>
	<li class='breadcrumb-item active'>Benutzer hinzufügen</li>
</ol>

<div class='jumbotron'>
	<h1>Benutzer hinzufügen</h1>
	<p class='lead'>Hier können Sie neue Benutzer anlegen, um ihnen Zugriff auf das Backend zu gewähren.</p>
</div><!-- /.jumbotron -->


<?php
	echo "<form action='' method='post'>";
		echo "<div class='row'>";
			echo "<div class='col-md-12'>";
				echo "<div class='card mb-3'>";
					echo "<div class='card-header'>";
						echo "<i class='fa fa-user' aria-hidden='true'></i> &nbsp; Einzelner Benutzer";
					echo "</div>";
					echo "<div class='card-body'>";
						echo "<div class='row'>";
							echo $fehlerangabe;
							echo "<div class='col-md-6'>";
								echo "<div class='form-group'>";
									echo "<label for='user_access'>Zugriffrolle:</label>";
									echo "<select class='form-control' name='user_access'>";
										echo "<option value=0 disabled selected>Bitte wählen Sie die Zugriffsrolle</option>";
											echo "<option value='editor'>Editor</option>";
											echo "<option value='admin'>Admin</option>";
											if ($_SESSION["access"] == "superadmin") {
												echo "<option value='superadmin'>Superadmin</option>";
											}
									echo "</select>";
								echo "</div>";

								echo "<div class='row'>";
									echo "<div class='col-md-4'>";
										echo "<div class='form-group'>";
											echo "<label for='anrede'>Anrede:</label>";
											echo "<input type='text' class='form-control' name='anrede' id='anrede' placeholder='Anrede' value='$anrede' required>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-md-6'>";
										echo "<div class='form-group'>";
											echo "<label for='vorname'>Vorname:</label>";
											echo "<input type='text' class='form-control' name='vorname' id='vorname' placeholder='Vorname' value='$vorname' required>";
										echo "</div>";
									echo "</div>";
									echo "<div class='col-md-6'>";
										echo "<div class='form-group'>";
											echo "<label for='nachname'>Nachname:</label>";
											echo "<input type='text' class='form-control' name='nachname' id='nachname' placeholder='Nachname' value='$nachname' required>";
										echo "</div>";
									echo "</div>";
								echo "</div>";

								echo "<div class='form-group'>";
									echo "<label for='position'>Position:</label>";
									echo "<input type='text' class='form-control' name='position' id='position' placeholder='Position' value='$position' required>";
								echo "</div>";

								echo "<div class='form-group'>";
									echo "<label for='email'>E-Mail:</label>";
									echo "<input type='email' class='form-control' name='email' id='email' placeholder='E-Mail' value='$email' required>";
								echo "</div>";
							echo "</div>";

							echo "<div class='col-md-6'>";
								echo "<div class='form-group'>";
									echo "<label for='filiale'>Filiale:</label>";
									echo "<input type='text' class='form-control' name='filiale' id='filiale' placeholder='Filiale' value='$filiale' required>";
								echo "</div>";
								echo "<div class='form-group'>";
									echo "<label for='strasse'>Straße:</label>";
									echo "<input type='text' class='form-control' name='strasse' id='strasse' placeholder='Straße' value='$strasse' required>";
								echo "</div>";

								echo "<div class='row'>";
									echo "<div class='col-md-4'>";
										echo "<div class='form-group'>";
											echo "<label for='plz'>PLZ:</label>";
											echo "<input type='text' class='form-control' name='plz' id='plz' placeholder='PLZ' value='$plz' required>";
										echo "</div>";
									echo "</div>";
									echo "<div class='col-md-8'>";
										echo "<div class='form-group'>";
											echo "<label for='ort'>Ort:</label>";
											echo "<input type='text' class='form-control' name='ort' id='ort' placeholder='Ort' value='$ort' required>";
										echo "</div>";
									echo "</div>";
								echo "</div>";

								echo "<div class='form-group'>";
									echo "<label for='tel'>Telefon:</label>";
									echo "<input type='text' class='form-control' name='tel' id='tel' placeholder='Telefonnummer' value='$tel' required>";
								echo "</div>";
								echo "<div class='form-group'>";
									echo "<label for='fax'>Fax:</label>";
									echo "<input type='text' class='form-control' name='fax' id='fax' placeholder='Faxnummer' value='$fax' required>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
						echo "<div class='text-right'>";
							echo "<button type='submit' class='btn btn-success btn-lg' name='erstellen' value='erstellen'>Hinzufügen!</button>";	
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</form>";
?>

<?php
	echo "<div class='row'>";
		echo "<div class='col-md-12'>";
			echo "<div class='card mb-3'>";
				echo "<div class='card-header'>";
					echo "<i class='fa fa-users' aria-hidden='true'></i> &nbsp; Mehrere Benutzer";
				echo "</div>";
				echo "<div class='card-body'>";
					echo "<div class=''>";
						echo $multiple_fehlerangabe;

						echo "<label>Aufbau der CSV-Tabelle (mit Beispiel):</label>";
						echo "<table class='table'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>Anrede</th>";
								echo "<th>Vorname</th>";
								echo "<th>Nachname</th>";
								echo "<th>Position</th>";
								echo "<th>Filiale</th>";
								echo "<th>Straße</th>";
								echo "<th>PLZ</th>";
								echo "<th>Ort</th>";
								echo "<th>Telefon</th>";
								echo "<th>Fax</th>";
								echo "<th>E-Mail</th>";
								echo "<th>Zugriffsrolle</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
							echo "<tr>";
								echo "<td>Herr</td>";
								echo "<td>Max</td>";
								echo "<td>Mustermann</td>";
								echo "<td>Kundenberater</td>";
								echo "<td>Hauptfiliale</td>";
								echo "<td>Konsul-Uebele-Straße 11</td>";
								echo "<td>74653</td>";
								echo "<td>Künzelsau</td>";
								echo "<td>07940 120-0</td>";
								echo "<td>07940 120-178</td>";
								echo "<td>max.mustermann@mustermann.de</td>";
								echo "<td>editor</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td>Frau</td>";
								echo "<td>Maxi</td>";
								echo "<td>Mustermfrau</td>";
								echo "<td>Kundenberaterin</td>";
								echo "<td>Hauptfiliale</td>";
								echo "<td>Konsul-Uebele-Straße 11</td>";
								echo "<td>74653</td>";
								echo "<td>Künzelsau</td>";
								echo "<td>07940 120-0</td>";
								echo "<td>07940 120-178</td>";
								echo "<td>maxi.musterfrau@musterfrau.de</td>";
								echo "<td>admin</td>";
							echo "</tr>";
						echo "</tbody>";
						echo "</table>";
					echo "</div>";


					echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>";
						echo "<div class=''>";
							echo "<div class='form-group'>";
								echo "<label for='file'>CSV-Datei auswählen:</label>";
								echo "<div class='input-group'>";
									echo "<label class='input-group-btn' style='margin-bottom:0;'>";
										echo "<span class='btn btn-primary'>";
											echo "Durchsuchen&hellip; <input type='file' name='file' id='file' style='display: none;'>";
										echo "</span>";
									echo "</label>";
									echo "<input type='text' class='form-control' readonly>";
								echo "</div>";
								echo "<p class='help-block'>Bitte stellen Sie sicher, dass Sie Ihre Excel-Tabelle als &bdquo;CSV (Trennzeichen getrennt) (*.csv)&rdquo; abgespeichert haben. Beachten Sie bitte außerdem, dass die 1. Zeile Ihrer Excel-Tabelle bereits Benutzerdaten enthält.</p>";
							echo "</div>";
						echo "</div>";

						echo "<div class='text-right'>";
							echo "<button type='submit' class='btn btn-success btn-lg' name='multiple_erstellen' value='multiple_erstellen'>CSV-Datei hochladen!</button>";
						echo "</div>";
					echo "</form>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
?>

<?php
	include("footer.php");
?>
