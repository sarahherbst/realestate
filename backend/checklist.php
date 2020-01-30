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

	$schluessel 	= 'checkliste';
	$alias 			= 'checkpoint';

	if (isset($_POST['loeschen'])) {
		$txt_id = $_POST['txt_id'];
		$txt_delete = sql_delete('texte', 'txt_id', $txt_id);
	}

	if (isset($_POST['deaktivieren'])) {
		$txt_id = $_POST['txt_id'];
		$txt_update = sql_update('texte', array('txt_status', 'chg_user'), array('9', $user_email), 'txt_id', $txt_id);
	}

	if (isset($_POST['aktivieren'])) {
		$txt_id = $_POST['txt_id'];
		$txt_update = sql_update('texte', array('txt_status', 'chg_user'), array('1', $user_email), 'txt_id', $txt_id);
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item active">Checkliste</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Checkliste</h1>
	<p class="lead">Hier finden Sie eine Übersicht aller Checkpoints.
	Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-list" aria-hidden="true"></i> &nbsp; Checkliste für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
			</div>
			<div class="card-body">
				<div class="row">
					<?php
						$txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array('1', $rub_id, $schluessel, 'checklist'), '', '');

						if ( mysqli_num_rows($txt_sql) == 1 ) {
							while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
							<div class="col-md-6">
								<div class="form-group">
									<label for="txt_titel">Headline:</label>
									<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_row['txt_titel']; ?>" disabled>
								</div>

								<div class="form-group">
									<label for="txt_einleitung">Einleitung:</label>
									<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung" disabled><?php echo $txt_row['txt_einleitung']; ?></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="txt_auszug">Conversion-Ziel:</label><br>
									<a href="<?php echo $txt_row['txt_auszug']; ?>" target="_blank" class="btn btn-outline-primary">Link zum Conversionziel</a>
								</div>
								<div class="form-group">
									<label for="txt_beitrag">Button-Bezeichnung:</label>
									<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_beitrag" value="<?php echo $txt_row['txt_beitrag']; ?>" disabled>
								</div>

								<div class="form-group">
									<label>PDF-Dokument:</label>
									<a href="../<?php echo $txt_row['txt_conversion_ziel']; ?>" target="_blank" class="btn btn-outline-primary"><i class="fa fa-file" aria-hidden="true"></i> PDF öffnen</a>
								</div>

								<div class="form-group">
									<label for="txt_conversion_titel">Button-Bezeichnung:</label>
									<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_row['txt_conversion_titel']; ?>" disabled>
								</div>
							</div>
							
							<div class="col-12 text-right">
								<a href="edit_checklist.php?rub_id=<?php echo $rub_id; ?>&txt_id=<?php echo $txt_row['txt_id'] ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</div>
						<?php } ?>
					<?php } else { ?>
							<div class="col-md-12">
								<p>Es wurden bisher keine Angaben zur Checkliste gemacht.</p>
								<a href="edit_checklist.php?rub_id=<?php echo $rub_id; ?>" title="Angaben hinzufügen" class="btn btn-info btn-sm">Angaben hinzufügen</a>
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array('1', $rub_id, $schluessel, $alias), '', ''); ?>
<?php if (mysqli_num_rows($txt_sql) == true && mysqli_num_rows($txt_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Checkpoints
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $txt_row['txt_id']; ?></th>
							<td><?php echo $txt_row['txt_titel']; ?></td>
							<td><?php echo $txt_row['txt_einleitung']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_checkpoint.php?rub_id=<?php echo $rub_id; ?>&txt_id=<?php echo $txt_row['txt_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="loeschen" class="btn btn-danger btn-sm">löschen</button>
								</form>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } ?>

<?php $txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array('9', $rub_id, $schluessel, $alias), '', ''); ?>
<?php if (mysqli_num_rows($txt_sql) == true && mysqli_num_rows($txt_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Checkpoints
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $txt_row['txt_id']; ?></th>
							<td><?php echo $txt_row['txt_titel']; ?></td>
							<td><?php echo $txt_row['txt_einleitung']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_checkpoint.php?rub_id=<?php echo $rub_id; ?>&txt_id=<?php echo $txt_row['txt_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="loeschen" class="btn btn-danger btn-sm">löschen</button>
								</form>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } ?>

<?php
	include('footer.php');
?>
