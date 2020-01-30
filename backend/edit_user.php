<?php
	$page = 'edit_user';
	require('header.php');

	$fehlerangabe 		= '';
	$use_passwort 		= '';
	$pw_wiederholung 	= '';

	// User-ID auslesen
	$use_id = $_GET['use_id'];

	// Tabelle User auslesen
	$user_sql = sql_select_where('all', 'user', 'use_id', $use_id, '', '');
	$user_row = mysqli_fetch_assoc($user_sql);

	$use_anrede 				= $user_row['use_anrede'];
	$use_vorname 				= $user_row['use_vorname'];
	$use_nachname 				= $user_row['use_nachname'];
	$use_position 				= $user_row['use_position'];
	$use_filiale 				= $user_row['use_filiale'];
	$use_strasse 				= $user_row['use_strasse'];
	$use_plz 					= $user_row['use_plz'];
	$use_ort 					= $user_row['use_ort'];
	$use_tel 					= $user_row['use_tel'];
	$use_fax 					= $user_row['use_fax'];
	$use_email					= $user_row['use_email'];

	if ($_SESSION['user_email'] == $use_email) {
		$current_user = true;
	} else {
		$current_user = false;
	}
	
	//Speichern
	if (isset($_POST['speichern'])) {
		// einlesen der im Formular angegebenen Werte
		$use_anrede       		= mysqli_real_escape_string($db, $_POST['use_anrede']);
		$use_vorname       		= mysqli_real_escape_string($db, $_POST['use_vorname']);
		$use_nachname       	= mysqli_real_escape_string($db, $_POST['use_nachname']);
		$use_position       	= mysqli_real_escape_string($db, $_POST['use_position']);
		$use_filiale       		= mysqli_real_escape_string($db, $_POST['use_filiale']);
		$use_strasse       		= mysqli_real_escape_string($db, $_POST['use_strasse']);
		$use_plz       			= mysqli_real_escape_string($db, $_POST['use_plz']);
		$use_ort       			= mysqli_real_escape_string($db, $_POST['use_ort']);
		$use_tel       			= mysqli_real_escape_string($db, $_POST['use_tel']);
		$use_fax       			= mysqli_real_escape_string($db, $_POST['use_fax']);

		$use_email   			= mysqli_real_escape_string($db, $_POST['use_email']);
		$use_passwort           = mysqli_real_escape_string($db, $_POST['use_passwort']);
		$pw_wiederholung   		= mysqli_real_escape_string($db, $_POST['pw_wiederholung']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler = 0;

		// Passworteingabe prüfen
		if ($use_passwort !== '' AND $use_passwort == $pw_wiederholung) {
			$pw_md5 	= md5($use_passwort);
			$pw_update	= sql_update('user', 'use_passwort', $use_passwort, 'use_id', $use_id);
			if ($pw_update == true) {
				$fehlerangabe 	.= '<div class="alert alert-success" role="alert">Das Passwort wurde erfolgreich geändert.</div>';
			} else {
				$fehler++;
				$fehlerangabe   .= '<div class="alert alert-danger" role="alert">Das Passowrt konnte leider nicht geändert werden.</div>';
			}
		} elseif ($use_passwort == '') {
		} else {
			$fehler++;
			$fehlerangabe   .= "<div class='alert alert-danger' role='alert'>Passwort bitte korrekt wiederholen.</div>";
		}

		if ($fehler == 0) {
			$user_update = sql_update('user', array('use_anrede', 'use_vorname', 'use_nachname', 'use_position', 'use_filiale', 'use_strasse', 'use_plz', 'use_ort', 'use_tel', 'use_fax', 'use_email', 'chg_user'), array($use_anrede, $use_vorname, $use_nachname, $use_position, $use_filiale, $use_strasse, $use_plz, $use_ort, $use_tel, $use_fax, $use_email, $user_email), 'use_id', $use_id);
			if ($user_update == true) {
				$fehlerangabe .= '<div class="alert alert-success" role="alert">Die E-Mail-Adresse wurde erfolgreich geändert.</div>';
				if ($current_user == true) {
					$_SESSION['user_email'] = $use_email;
				}
			} else {
				$fehler++;
				$fehlerangabe .= "<div class='alert alert-danger' role='alert'>Die Profilangaben konnten nicht geändert werden.</div>";
			}
		}
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item">Benutzer</li>
	<li class="breadcrumb-item active">Profil bearbeiten</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Mein Profil bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit Ihre Angaben zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-user" aria-hidden="true"></i> &nbsp; Profil
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="use_anrede">Anrede:</label>
								<input type="text" class="form-control" name="use_anrede" id="use_anrede" placeholder="Anrede" value="<?php echo $use_anrede; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_vorname">Vorname:</label>
								<input type="text" class="form-control" name="use_vorname" id="use_vorname" placeholder="Vorname" value="<?php echo $use_vorname; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_nachname">Nachname:</label>
								<input type="text" class="form-control" name="use_nachname" id="use_nachname" placeholder="Nachname" value="<?php echo $use_nachname; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_position">Position:</label>
								<input type="text" class="form-control" name="use_position" id="use_position" placeholder="Position" value="<?php echo $use_position; ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="use_email">E-Mail-Adresse:</label>
								<input type="email" class="form-control" name="use_email" id="use_email" placeholder="E-Mail-Adresse" value="<?php echo $use_email; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_passwort">Neues Passwort:</label>
								<input type="password" class="form-control" name="use_passwort" id="use_passwort" value="<?php echo $use_passwort; ?>">
							</div>
							<div class="form-group">
								<label for="pw_wiederholung">Wiederholung:</label>
								<input type="password" class="form-control" name="pw_wiederholung" id="pw_wiederholung" value="<?php echo $pw_wiederholung; ?>">
							</div>
						</div>
					</div>
					<br><br>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="use_filiale">Filiale:</label>
								<input type="text" class="form-control" name="use_filiale" id="use_filiale" placeholder="Filiale" value="<?php echo $use_filiale; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_strasse">Straße:</label>
								<input type="text" class="form-control" name="use_strasse" id="use_strasse" placeholder="Straße" value="<?php echo $use_strasse; ?>" required>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="use_plz">PLZ:</label>
										<input type="text" class="form-control" name="use_plz" id="use_plz" placeholder="PLZ" value="<?php echo $use_plz; ?>" required>
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group">
										<label for="use_ort">Ort:</label>
										<input type="text" class="form-control" name="use_ort" id="use_ort" placeholder="Ort" value="<?php echo $use_ort; ?>" required>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="use_tel">Telefon:</label>
								<input type="text" class="form-control" name="use_tel" id="use_tel" placeholder="Telefon" value="<?php echo $use_tel; ?>" required>
							</div>
							<div class="form-group">
								<label for="use_fax">Fax:</label>
								<input type="text" class="form-control" name="use_fax" id="use_fax" placeholder="Fax" value="<?php echo $use_fax; ?>" required>
							</div>
						</div>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-success btn-lg navbar-right" name="speichern" value="Speichern">Speichern!</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>
