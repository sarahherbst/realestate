<?php
	$page 	= 'rubrik';
	require('header.php');

	$alert 			= '';
	$schluessel 	= $page;

	$rub_name 			= '';
	$rub_title 			= '';
	$rub_description 	= '';
	$rub_email_zu 		= '';
	$rub_email_kopie 	= '';
	$rub_css_bg 		= '';
	$rub_css_txt 		= '';
	$rub_form_subline	= '';

	// submit
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$rub_name 				= mysqli_escape_string($db, $_POST['rub_name']);
		$rub_title 				= mysqli_escape_string($db, $_POST['rub_title']);
		$rub_description 		= mysqli_escape_string($db, $_POST['rub_description']);
		$rub_email_zu 			= mysqli_escape_string($db, $_POST['rub_email_zu']);
		$rub_email_kopie 		= mysqli_escape_string($db, $_POST['rub_email_kopie']);
		$rub_css_bg 			= mysqli_escape_string($db, $_POST['rub_css_bg']);
		$rub_css_txt 			= mysqli_escape_string($db, $_POST['rub_css_txt']);
		$rub_form_subline 		= mysqli_escape_string($db, $_POST['rub_form_subline']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler = 0;

		if ($fehler == 0) {
			$insert 			= sql_insert('rubrik', array('rub_institut', 'rub_name', 'rub_title', 'rub_description', 'rub_email_zu', 'rub_email_kopie', 'rub_css_bg', 'rub_css_txt', 'rub_form_subline', 'new_user', 'chg_user'), array($institut_id, $rub_name, $rub_title, $rub_description, $rub_email_zu, $rub_email_kopie, $rub_css_bg, $rub_css_txt, $rub_form_subline, $user_email, $user_email));
		}

		if ($insert == true) {
			$alert 				.= '<div class="alert alert-success" role="alert">Die Rubrik wurde erfolgreich hinzugefügt.</div>';

			$rub_name 			= '';
			$rub_title 			= '';
			$rub_description 	= '';
			$rub_email_zu 		= '';
			$rub_email_kopie 	= '';
			$rub_css_bg 		= '';
			$rub_css_txt 		= '';
			$rub_form_subline	= '';
		} else {
			$fehler++;
			$alert   			.= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Rubrik konnte nicht hinzugefügt werden.</div>';

			$rub_name 			= $_POST['rub_name'];
			$rub_title 			= $_POST['rub_title'];
			$rub_description 	= $_POST['rub_description'];
			$rub_email_zu 		= $_POST['rub_email_zu'];
			$rub_email_kopie 	= $_POST['rub_email_kopie'];
			$rub_css_bg 		= $_POST['rub_css_bg'];
			$rub_css_txt 		= $_POST['rub_css_txt'];
			$rub_form_subline 	= $_POST['rub_form_subline'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item">Rubrik</li>
	<li class="breadcrumb-item active">Rubrik hinzufügen</li>
</ol>

<div class="jumbotron">
	<h1>Rubrik hinzufügen</h1>
	<p class="lead">Hier haben Sie die Möglichkeit eine Rubrik zu der Seite hinzuzufügen.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Angaben</b>
				</div>
				<div class="card-body">
					<?php echo $alert; ?>

					<div class="form-group">
						<label for="rub_name">Bezeichnung:</label>
						<small class="form-text text-muted">Geben Sie eine Bezeichnung für die Rubrik an.</small>
						<input type="text" class="form-control" placeholder="Bezeichnung" name="rub_name" value="<?php echo $rub_name; ?>" required>
					</div>
					<div class="form-group">
						<label for="rub_title">SEO – Seitentitel:</label>
						<small class="form-text text-muted">Geben Sie einen Seitentitel für die Rubrik an (max. 60 Zeichen).</small>
						<input type="text" class="form-control" placeholder="Seitentitel" name="rub_title" value="<?php echo $rub_title; ?>" required>
					</div>
					<div class="form-group">
						<label for="rub_description">SEO – Beschreibung:</label>
						<small class="form-text text-muted">Geben Sie eine Beschreibung für die Rubrik an (max. 160 Zeichen).</small>
						<input type="text" class="form-control" placeholder="Beschreibung" name="rub_description" value="<?php echo $rub_description; ?>" required>
					</div>
					<div class="form-group">
						<label for="rub_email_zu">E-Mail (zu):</label>
						<small class="form-text text-muted">Geben Sie eine E-Mail an, zu der E-Mails geleitet werden sollen.</small>
						<input type="email" class="form-control" placeholder="E-Mail (zu)" name="rub_email_zu" value="<?php echo $rub_email_zu; ?>">
					</div>
					<div class="form-group">
						<label for="rub_email_kopie">E-Mail (bcc):</label>
						<small class="form-text text-muted">Geben Sie eine E-Mail (bcc) an, zu der E-Mails geleitet werden sollen.</small>
						<input type="email" class="form-control" placeholder="E-Mail (bcc)" name="rub_email_kopie" value="<?php echo $rub_email_kopie; ?>">
					</div>
					<div class="form-group">
						<label for="rub_css_bg">CSS-Klasse: Hintergrund</label>
						<small class="form-text text-muted">Geben Sie die CSS-Klasse an, die den Hintergrund bestimmt.</small>
						<input type="text" class="form-control" placeholder="bg-blue-gradient" name="rub_css_bg" value="<?php echo $rub_css_bg; ?>">
					</div>
					<div class="form-group">
						<label for="rub_css_txt">CSS-Klasse: Texthervorhebung</label>
						<small class="form-text text-muted">Geben Sie die CSS-Klasse an, die die Textfarbe bestimmt.</small>
						<input type="text" class="form-control" placeholder="primary" name="rub_css_txt" value="<?php echo $rub_css_txt; ?>">
					</div>
					<div class="form-group">
						<label for="rub_form_subline">Kontaktformular: Subline</label>
						<small class="form-text text-muted">Geben Sie eine Subline an, die im Kontaktformular der Rubrik angezeigtw erden soll.</small>
						<input type="text" class="form-control" placeholder="Subline" name="rub_form_subline" value="<?php echo $rub_form_subline; ?>">
					</div>
				</div>
				<div class="card-footer">
					<button type="submit" class="btn btn-success btn-lg" name="submit" value="submit">hinzufügen</button>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>
