<?php
	$page = 'new_berater';
	require('header.php');

	$alert 					= '';
	$schluessel 			= 'berater';

	$ber_gruppe 			= '';
	$ber_anrede 			= '';
	$ber_vorname 			= '';
	$ber_nachname 			= '';
	$ber_position 			= '';
	$ber_filiale 			= '';
	$ber_strasse 			= '';
	$ber_plz 				= '';
	$ber_ort 				= '';
	$ber_tel 				= '';
	$ber_fax 				= '';
	$ber_email 				= '';

	$img_thumb 				= '';
	$img_beschreibung 		= '';

	if (isset($_POST['submit'])) {
		//einlesen der im Formular angegebenen Werte*/
		$ber_gruppe 		= mysqli_real_escape_string($db, $_POST['ber_gruppe']);
		$ber_anrede 		= mysqli_real_escape_string($db, $_POST['ber_anrede']);
		$ber_vorname 		= mysqli_real_escape_string($db, $_POST['ber_vorname']);
		$ber_nachname 		= mysqli_real_escape_string($db, $_POST['ber_nachname']);
		$ber_position 		= mysqli_real_escape_string($db, $_POST['ber_position']);
		$ber_filiale 		= mysqli_real_escape_string($db, $_POST['ber_filiale']);
		$ber_strasse 		= mysqli_real_escape_string($db, $_POST['ber_strasse']);
		$ber_plz 			= mysqli_real_escape_string($db, $_POST['ber_plz']);
		$ber_ort 			= mysqli_real_escape_string($db, $_POST['ber_ort']);
		$ber_tel 			= mysqli_real_escape_string($db, $_POST['ber_tel']);
		$ber_fax 			= mysqli_real_escape_string($db, $_POST['ber_fax']);
		$ber_email 			= mysqli_real_escape_string($db, $_POST['ber_email']);
		$img_beschreibung 	= mysqli_real_escape_string($db, $_POST['img_beschreibung']);
		
		//Variablen für Fehlerprüfung
		$fehler 			= 0;

		//Berater in die Datenbank eintragen
		$berater_update 	= sql_insert('berater', array('ber_gruppe', 'ber_anrede', 'ber_vorname', 'ber_nachname', 'ber_position', 'ber_filiale', 'ber_strasse', 'ber_plz', 'ber_ort', 'ber_tel', 'ber_fax', 'ber_email', 'chg_user', 'new_user'), array($ber_gruppe, $ber_anrede, $ber_vorname, $ber_nachname, $ber_position, $ber_filiale, $ber_strasse, $ber_plz, $ber_ort, $ber_tel, $ber_fax, $ber_email, $user_email, $user_email));
		if ($berater_update == true) {
			$alert .= "<div class='alert alert-success alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Der Berater wurde erfolgreich hinzugefügt.</div>";
			$ber_anrede 			= '';
			$ber_vorname 			= '';
			$ber_nachname 			= '';
			$ber_position 			= '';
			$ber_filiale 			= '';
			$ber_strasse 			= '';
			$ber_plz 				= '';
			$ber_ort 				= '';
			$ber_tel 				= '';
			$ber_fax 				= '';
			$ber_email 				= '';

		} else {
			$fehler++;
			$alert .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Fehler! Der Berater konnte leider nicht hinzugefügt werden.</div>";
		}

		// Upload Bild
		if ($_FILES['motiv']['error'] <= 0) {
			$upload_folder 			= '../img/berater/'; //Das Upload-Verzeichnis
			$large_folder 			= '../img/berater/large/'; //Das Upload-Verzeichnis
			$thumbnail_folder 		= '../img/berater/thumb/'; //Das Upload-Verzeichnis
			// prüfen, ob Verzeichnis vorhanden ist, ansonsten erstellen
			if (!is_dir($upload_folder)) {
				if (!mkdir($upload_folder, 0777, true)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
				}
			}
			if (!is_dir($large_folder)) {
				if (!mkdir($large_folder, 0777, true)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
				}
			}
			if (!is_dir($thumbnail_folder)) {
				if (!mkdir($thumbnail_folder, 0777, true)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
				}
			}

			$filename 				= pathinfo($_FILES['motiv']['name'], PATHINFO_FILENAME);
			$extension 				= strtolower(pathinfo($_FILES['motiv']['name'], PATHINFO_EXTENSION));

			//Überprüfung der Dateiendung
			$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
			if (!in_array($extension, $allowed_extensions)) {
				$fehler++;
				$alert .= '<div class="alert alert-danger" role="alert">Ungültige Dateiendung. Nur png, jpg, jpeg und gif-Dateien sind erlaubt.</div>';
			}

			//Überprüfung der Dateigröße
			$max_size = 500*1024; //500 KB
			if ($_FILES['motiv']['size'] > $max_size) {
				$fehler++;
				$alert .= '<div class="alert alert-danger" role="alert">Bitte keine Dateien größer als 500kb hochladen</div>';
			}

			//Überprüfung dass das Bild keine Fehler enthält
			if (function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
				$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
				$detected_type = exif_imagetype($_FILES['motiv']['tmp_name']);

				if (!in_array($detected_type, $allowed_types)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Nur der Upload von Bilddateien (png, jpg, jpeg und gif-Dateien) ist gestattet.</div>';
				}
			}

			// Überprüfung, ob Dateiname bereits existiert
			// Pfad zum Upload
			$current_path 	= $upload_folder.$filename.'.'.$extension;
			$new_path 		= $large_folder.$filename.'.'.$extension;
			// Falls Datei existiert, hänge eine Zahl an den Dateinamen
			if (file_exists($new_path)) { 
				$img_id = 1;
				do {

					$current_path 	= $upload_folder.$filename.'_'.$img_id.'.'.$extension;
					$new_path 		= $large_folder.$filename.'_'.$img_id.'.'.$extension;

					$img_id++;
				}
				while(file_exists($new_path));
			}

			if ($fehler == 0) {
				//Alles okay, verschiebe Datei an neuen Pfad
				move_uploaded_file($_FILES['motiv']['tmp_name'], $current_path);
				$motiv = $current_path;

				// Bild resizen
				if ($motiv !== '') {
					$large = imgresize($motiv, $large_folder);
					if ($large == '' || $large == false) {
						$fehler++;
						$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' konnte nicht hinzugefügt werden. ';
					} else {
						$large = str_replace('../', '', $large);
						$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' wurde erfolgreich hochgeladen. ';
					}
				} else {
					$fehler++;
					$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' konnte nicht hochgeladen werden. ';
				}

				// Thumbnail erstellen & Datenbankeintrag
				if ($fehler == 0) {
					$thumbnail = imgthumb($motiv, $thumbnail_folder);
					if ($thumbnail == '' || $thumbnail == false) {
						$fehler++;
						$alert_upload .= 'Das Thumbnail zum Bild '.$_FILES['motiv']['name'].' konnte nicht hinzugefügt werden. ';
					} else {
						$thumbnail 	= str_replace('../', '', $thumbnail);
						$alert_upload .= 'Das Thumbnail zum Bild '.$_FILES['motiv']['name'].' wurde erfolgreich hochgeladen. ';

						// Datenbankeintrag
						$berater_sql 	= sql_select_where('all', 'berater', array('ber_institut', 'ber_email'), array($institut_id, $_POST['ber_email']), '', '');
						$berater_row 	= mysqli_fetch_assoc($berater_sql);
						$ber_id 		= $berater_row['ber_id'];
						$image_upload 	= sql_insert('images', array('img_institut', 'img_schluessel', 'img_item_id', 'img_bild', 'img_thumb', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $schluessel, $ber_id, $large, $thumbnail, $img_beschreibung, $user_email, $user_email));
						
						if ($image_upload == true) {
							$img_beschreibung 	= '';
							$img_thumb 			= '';
						} else {
							$fehler++;
							$alert_upload .= '<b>Fehler!</b> Das Bild konnte nicht in die Datenbank eingetragen werden. Bitte kontaktieren Sie den Websiteadministrator! ';
							$img_beschreibung = str_replace('<br />', '', $img_beschreibung);
							$img_beschreibung = $_POST['img_beschreibung'];
						}
					}
				}

				if ($fehler == 0) {
					$alert .= '<div class="alert alert-success" role="alert">'.$alert_upload.'</div>';

					// vorübergehendes Bild löschen
					unlink($motiv);
				} else {
					$alert .= '<div class="alert alert-danger" role="alert">'.$alert_upload.'</div>';
				}
			}
		}
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item">Berater</li>
	<li class="breadcrumb-item active">Berater hinzufügen</li>
</ol>

<div class="jumbotron">
	<h1>Berater hinzufügen</h1>
	<p class="lead">Hier können Sie einen weiteren Ansprechpartner hinzufügen.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-user-o" aria-hidden="true"></i> &nbsp; Beraterangaben
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="ber_gruppe">Team:</label>
									<select class="custom-select" name="ber_gruppe" required>
										<option value=0 selected disabled>Bitte wählen Sie ein Team</option>
										<?php $begr_sql = sql_select_where('all', 'beratergruppe', 'begr_institut', $institut_id, '', ''); ?>
										<?php while ($begr_row = mysqli_fetch_assoc($begr_sql)) { ?>
											<option value="<?php echo $begr_row['begr_id']; ?>"><?php echo $begr_row['begr_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-4">
									<label for="ber_anrede">Anrede:</label>
									<input type="text" class="form-control" name="ber_anrede" id="ber_anrede" placeholder="Anrede" value="<?php echo $ber_anrede; ?>">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="ber_vorname">Vorname:</label>
									<input type="text" class="form-control" name="ber_vorname" id="ber_vorname" placeholder="Vorname" value="<?php echo $ber_vorname; ?>" required>
								</div>

								<div class="form-group col-md-6">
									<label for="ber_nachname">Nachname:</label>
									<input type="text" class="form-control" name="ber_nachname" id="ber_nachname" placeholder="Nachname" value="<?php echo $ber_nachname; ?>" required>
								</div>
							</div>

							<div class="form-group">
								<label for="ber_position">Position:</label>
								<input type="text" class="form-control" name="ber_position" id="ber_position" placeholder="Position" value="<?php echo $ber_position; ?>" required>
							</div>

							<div class="form-group">
								<label for="ber_email">E-Mail:</label>
								<input type="ber_email" class="form-control" name="ber_email" id="ber_email" placeholder="E-Mail" value="<?php echo $ber_email; ?>" required>
							</div>

							<div class="form-group">
								<label for="ber_filiale">Zuständigkeitsbereiche:</label>
								<input type="text" class="form-control" name="ber_filiale" id="ber_filiale" placeholder="Zuständigkeitsbereiche" value="<?php echo $ber_filiale; ?>" required>
							</div>

							<div class="form-group">
								<label for="ber_strasse">Straße:</label>
								<input type="text" class="form-control" name="ber_strasse" id="ber_strasse" placeholder="Straße" value="<?php echo $ber_strasse; ?>">
							</div>

							<div class="form-row">
								<div class="form-group col-md-4">
									<label for="ber_plz">PLZ:</label>
									<input type="text" class="form-control" name="ber_plz" id="ber_plz" placeholder="PLZ" value="<?php echo $ber_plz; ?>">
								</div>

								<div class="form-group col-md-8">
									<label for="ber_ort">Ort:</label>
									<input type="text" class="form-control" name="ber_ort" id="ber_ort" placeholder="Ort" value="<?php echo $ber_ort; ?>">
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="ber_tel">Telefon:</label>
								<input type="text" class="form-control" name="ber_tel" id="ber_tel" placeholder="Telefonnummer" value="<?php echo $ber_tel; ?>" required>
							</div>
							<div class="form-group">
								<label for="ber_fax">Fax:</label>
								<input type="text" class="form-control" name="ber_fax" id="ber_fax" placeholder="Faxnummer" value="<?php echo $ber_fax; ?>">
							</div>

							<div class="form-group">
								<?php if ($img_thumb != '') { ?>
									<label>Aktuelles Bild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $img_thumb; ?>" alt="Bild" class="img-thumbnail" width="200">
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="motiv">Bild auswählen:</label>
								<p class="help-block">Laden Sie ein Bild des Beraters hoch.</p>
								<div class="input-group">
									<label class="input-group-btn mb-0">
										<span class="btn btn-primary">
											Durchsuchen &hellip; <input type="file" name="motiv" id="motiv" style="display: none;">
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="img_beschreibung">Bildbeschreibung:</label>
								<p class="help-block">Geben Sie eine Bildbeschreibung an.</p>
								<input type="text" class="form-control" placeholder="Bildbeschreibung" name="img_beschreibung" value="<?php echo $img_beschreibung; ?>">
							</div>
						</div>
					</div>
				</div>
				<!-- Submit Button -->
				<div class="card-footer pb-0">
					<div class="form-group">
						<button type="submit" class="btn btn-lg btn-success" name="submit">hinzufügen</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>
