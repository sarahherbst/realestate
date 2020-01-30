<?php
	$page = 'edit_datenschutz';
	require('header.php');

	$fehlerangabe = '';

	// Tabelle auslesen
	$datenschutz_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'datenschutz'), '', '');

	if ( mysqli_num_rows($datenschutz_sql) == 1 ) {
		$datenschutz_row = mysqli_fetch_object($datenschutz_sql);

		// Variablen vergeben
		$datenschutz = $datenschutz_row->txt_beitrag;
		$datenschutz = str_replace('<br />', '', $datenschutz);
	} else {
		$datenschutz = '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		/*einlesen der im Formular angegebenen Werte*/
		$datenschutz = $_POST['datenschutz'];
		$datenschutz = nl2br($datenschutz);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler = 0;

		if ( mysqli_num_rows($datenschutz_sql) == 1 ) {
			$aktualisierung = "UPDATE texte SET txt_beitrag = '$datenschutz', chg_user = '$user', chg_time = curtime(), chg_date = curdate() WHERE txt_institut = '$institut_id' AND txt_schluessel = 'datenschutz'";
			$speichern 		= mysqli_query($db,$aktualisierung);
		} else {
			$speichern 		= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_beitrag', 'new_user', 'chg_user'), array($institut_id, 'datenschutz', $datenschutz, $user_email, $user_email));
		}
		if ($speichern == true) {
			$fehlerangabe .= "<div class='alert alert-success' role='alert'>Die Datenschutzbestimmungen wurde erfolgreich geändert.</div>";
			$datenschutz = str_replace('<br />', '', $datenschutz);
			$datenschutz = $_POST['datenschutz'];
		}
		else {
			$fehler = 1;
			$fehlerangabe   .= "<div class='alert alert-danger alert-dismissible fade in' role='alert'><b>Fehler!</b> Die Datenschutzbestimmungen konnte nicht gespeichert werden.</div>";
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Datenschutz</li>
</ol>

<div class="jumbotron">
	<h1>Datenschutz bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Datenschutzbestimmungen zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Datenschutzbestimmungen
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="datenschutz">Text:</label>
								<textarea class="form-control" rows="25" name="datenschutz" id="datenschutz"><?php echo $datenschutz; ?></textarea>
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
