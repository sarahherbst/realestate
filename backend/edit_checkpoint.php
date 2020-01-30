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

	$alert 			= '';
	$schluessel 	= 'checkliste';
	$alias 			= 'checkpoint';

	// Tabelle auslesen
	$txt_id 		= $_GET['txt_id'];
	$checkliste_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_rubrik', 'txt_id'), array($institut_id, $rub_id, $txt_id), '', '');

	if (mysqli_num_rows($checkliste_sql) == 1) {
		$checkliste_row 	= mysqli_fetch_assoc($checkliste_sql);

		// Variablen vergeben
		$txt_titel 			= $checkliste_row['txt_titel'];
		$txt_einleitung 	= $checkliste_row['txt_einleitung'];
		$txt_einleitung 	= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 	= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 	= stripslashes($txt_einleitung);
	} else {
		$txt_titel 			= '';
		$txt_einleitung 	= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 			= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 	= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 	= nl2br($txt_einleitung);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 			= 0;

		if ($fehler == 0) {
			if ( mysqli_num_rows($checkliste_sql) == 1 ) {
				$checkliste_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'chg_user'), array($txt_titel, $txt_einleitung, $user_email), array('txt_institut', 'txt_rubrik', 'txt_id'), array($institut_id, $rub_id, $txt_id));
			} else {
				$checkliste_update 	= sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_alias', 'txt_titel', 'txt_einleitung', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $alias, $txt_titel, $txt_einleitung, $user_email, $user_email));
			}
		}

		if ($checkliste_update == true) {
			$alert 			.= '<div class="alert alert-success" role="alert">Der Checkpoint für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> wurde erfolgreich gespeichert.</div>';
			$txt_einleitung = str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung = $_POST['txt_einleitung'];
		} else {
			$fehler++;
			$alert   		.= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Der Checkpoint für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> konnte nicht gespeichert werden.</div>';
			$txt_einleitung = str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung = $_POST['txt_einleitung'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item"><a href="checklist.php?rub_id=<?php echo $rub_id; ?>">Checkliste</a></li>
	<li class="breadcrumb-item active">Checkpoint bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Checkpoint bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit den Checkpoint für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b> zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-list" aria-hidden="true"></i> &nbsp; Checkpoint für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="txt_titel">Headline:</label>
						<p class="help-block">Geben Sie eine Headline zum Checkpoint an.</p>
						<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
					</div>

					<div class="form-group">
						<label for="txt_einleitung">Beschreibung:</label>
						<p class="help-block">Verfassen Sie eine kurze Beschreibung.</p>
						<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
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
