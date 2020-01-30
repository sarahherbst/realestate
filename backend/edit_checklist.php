<?php
	//Datenbank einlesen
	require_once('../connection.inc.php');
	require_once('../function.inc.php');

	//Rubrik-ID
	$rub_id 	= $_GET['rub_id'];

	//Rubrik-Daten
	$sql_rubrik 	= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
	$row_rubrik 	= mysqli_fetch_assoc($sql_rubrik);
	$category_titel = strtolower(str_replace(' ', '-', $row_rubrik['rub_name']));

	require('header.php');

	$alert 	= '';
	$schluessel 	= 'checkliste';
	$alias 			= 'checklist';

	// Tabelle auslesen
	$checkliste_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array($institut_id, $rub_id, $schluessel, $alias), '', '');

	if (mysqli_num_rows($checkliste_sql) == 1) {
		$checkliste_row 		= mysqli_fetch_assoc($checkliste_sql);

		// Variablen vergeben
		$txt_titel 				= $checkliste_row['txt_titel'];
		$txt_einleitung 		= $checkliste_row['txt_einleitung'];
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_conversion_ziel 	= $checkliste_row['txt_conversion_ziel'];
		$txt_conversion_titel 	= $checkliste_row['txt_conversion_titel'];
		// Checkliste Conversion-URL
		$txt_beitrag 			= $checkliste_row['txt_beitrag'];
		// Checkliste Conversion-Beschreibung
		$txt_auszug 			= $checkliste_row['txt_auszug'];
	} else {
		$txt_titel 				= '';
		$txt_einleitung 		= '';
		$txt_conversion_ziel 	= '';
		$txt_conversion_titel 	= '';
		$txt_beitrag 			= '';
		$txt_auszug 			= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);
		$txt_beitrag 			= mysqli_real_escape_string($db, $_POST['txt_beitrag']);
		$txt_auszug 			= mysqli_real_escape_string($db, $_POST['txt_auszug']);


		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 			= 0;

		if ($fehler == 0) {
			if ( mysqli_num_rows($checkliste_sql) == 1 ) {
				$checkliste_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_beitrag', 'txt_auszug', 'txt_conversion_titel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_beitrag, $txt_auszug, $txt_conversion_titel, $user_email), array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array($institut_id, $rub_id, $schluessel, $alias));
			} else {
				$checkliste_update 	= sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_alias', 'txt_titel', 'txt_einleitung', 'txt_beitrag', 'txt_auszug', 'txt_conversion_titel', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $alias, $txt_titel, $txt_einleitung, $txt_beitrag, $txt_auszug, $txt_conversion_titel, $user_email, $user_email));
			}
		}

		if ($checkliste_update == true) {
			$alert .= '<div class="alert alert-success" role="alert">Die Checkliste für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> wurde erfolgreich gespeichert.</div>';
			$txt_einleitung = str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung = $_POST['txt_einleitung'];
		} else {
			$fehler++;
			$alert   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Checkliste für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> konnte nicht gespeichert werden.</div>';
			$txt_einleitung = str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung = $_POST['txt_einleitung'];
		}

		//Dokument hinzufügen
		if ($_FILES['file']['error'] <= 0) {
			$upload_folder 	= '../img/checkliste/'.$rub_id.'/'; //Das Upload-Verzeichnis
			// prüfen, ob Verzeichnis vorhanden ist, ansonsten erstellen
			if (!is_dir($upload_folder)) {
				if (!mkdir($upload_folder, 0777, true)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Dokument konnte nicht erstellt werden.</div>';
				}
			}

			$filename 			= pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
			$extension 			= strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

			$allowed_extensions = array('pdf', 'application/pdf');

			//Überprüfung der Dateiendung
			if (!in_array($extension, $allowed_extensions)) {
				$fehler++;
				$alert .= '<div class="alert alert-danger" role="alert">Ungültige Dateiendung. Nur PDF-Dateien sind erlaubt.</div>';
			} else {
				//Pfad zum Upload
				$new_path = $upload_folder.$filename.'.'.$extension;

				//Neuer Dateiname falls die Datei bereits existiert
				if (file_exists($new_path)) { //Falls Datei existiert, hänge eine Zahl an den Dateinamen
					$img_id = 1;
					do {
						$new_path = $upload_folder.$filename.'_'.$img_id.'.'.$extension;
						$img_id++;
					}
					while(file_exists($new_path));
				}
			}

			if ($fehler == 0) {
				//Alles okay, verschiebe Datei an neuen Pfad
				move_uploaded_file($_FILES['file']['tmp_name'], $new_path);
				$file = $new_path;
				$file = str_replace('../', '', $file);

				if ($file !== '') {
					$pdf_upload 	= sql_update('texte', array('txt_conversion_ziel', 'chg_user'), array($file, $user_email), array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array($institut_id, $rub_id, $schluessel, $alias));
					if ($pdf_upload == true) {
						$alert .= '<div class="alert alert-success" role="alert">Das Dokument wurde erfolgreich hinzugefügt.</div>';
					} else {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Das Dokument konnte nicht hinzugefügt werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
					}
				} else {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Das Dokument konnte nicht hinzugefügt werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
				}
			}
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item">Checkliste</li>
	<li class="breadcrumb-item active">Checkliste bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Checkliste bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Checkliste für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b> zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>

		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-list" aria-hidden="true"></i> &nbsp; Checkliste für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<p class="help-block">Geben Sie eine Headline zum Checkliste an.</p>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
							</div>

							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<p class="help-block">Verfassen Sie eine kurze Einleitung/Subline.</p>
								<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
							</div>
							<div class="form-group">
								<label for="txt_beitrag">Conversion-Ziel:</label>
								<p class="help-block">Geben Sie die URL zum Conversion-Ziel an.</p>
								<input type="text" class="form-control" placeholder="URL" name="txt_beitrag" value="<?php echo $txt_beitrag; ?>">
							</div>
							<div class="form-group">
								<label for="txt_auszug">Button-Bezeichnung:</label>
								<p class="help-block">Geben Sie eine Button-Bezeichnung zum Conversion-Ziel an.</p>
								<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_auszug" value="<?php echo $txt_auszug; ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?php if ($txt_conversion_ziel != '') { ?>
									<label>Aktuelles Dokument:</label>
									<div class="row">
										<div class="col-md-6">
											<a href="../<?php echo $txt_conversion_ziel; ?>" target="_blank" class="btn btn-outline-primary"><i class="fa fa-file" aria-hidden="true"></i> PDF öffnen</a>
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="file">Dokument auswählen:</label>
								<p class="help-block">Laden Sie ein PDF-Dokument zum Downloaden hoch.</p>
								<div class="input-group">
									<label class="input-group-btn mb-0">
										<span class="btn btn-primary">
											Durchsuchen &hellip; <input type="file" name="file" id="file" style="display: none;">
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="txt_conversion_titel">Button-Bezeichnung:</label>
								<p class="help-block">Geben Sie eine Button-Bezeichnung zum Download an.</p>
								<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_conversion_titel; ?>">
							</div>
						</div>
					</div>
				</div>
				<!-- Submit Button -->
				<div class="card-footer pb-0">
					<div class="form-group">
						<button type="submit" class="btn btn-lg btn-success" name="submit">speichern</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>
