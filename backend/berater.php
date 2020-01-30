<?php
	$page 			= 'berater';
	$schluessel 	= 'berater';

	require('header.php');

	if (isset($_POST['loeschen'])) {
		$ber_id 	= $_POST['berater_id'];
		$ber_delete = sql_delete('berater', 'ber_id', $ber_id);
	}

	if (isset($_POST['deaktivieren'])) {
		$ber_id 	= $_POST['berater_id'];
		$ber_update = sql_update('berater', array('ber_status', 'chg_user'), array('9', $user_email), 'ber_id', $ber_id);
	}

	if (isset($_POST['aktivieren'])) {
		$ber_id 	= $_POST['berater_id'];
		$ber_update = sql_update('berater', array('ber_status', 'chg_user'), array('1', $user_email), 'ber_id', $ber_id);
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Berater</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Berater</h1>
	<p class='lead'>Hier finden Sie eine Übersicht aller Berater, die als Ansprechpartner gelistet werden. Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-address-book" aria-hidden="true"></i> &nbsp; Beraterangaben</b>
			</div>
			<div class="card-body">
				<div class="row">
					<?php
						$txt_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_status', 'txt_schluessel'), array($institut_id, '1', $schluessel), '', '');

						if (mysqli_num_rows($txt_sql) == 1) {
							while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
							<div class="col-md-12">
								<div class="form-group">
									<label for="txt_titel">Headline:</label>
									<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_row['txt_titel']; ?>" disabled>
								</div>

								<div class="form-group">
									<label for="txt_einleitung">Einleitung:</label>
									<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung" disabled><?php echo $txt_row['txt_einleitung']; ?></textarea>
								</div>

								<div class="form-group">
									<label for="txt_conversion_ziel">Link zum Conversion-Ziel:</label><br>
									<a href="<?php echo $txt_row['txt_conversion_ziel']; ?>" target="_blank" class="btn btn-outline-primary">Link zum Conversionziel</a>
								</div>

								<div class="form-group">
									<label for="txt_conversion_titel">Button-Bezeichnung:</label>
									<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_row['txt_conversion_titel']; ?>" disabled>
								</div>
							</div>
							
							<div class="col-12 text-right">
								<a href="edit_berater_info.php" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</div>
						<?php } ?>
					<?php } else { ?>
							<div class="col-md-12">
								<p>Es wurden bisher keine Angaben zum Immobilientool (nach Beendigung) gemacht.</p>
								<a href="edit_berater_info.php" title="Angaben hinzufügen" class="btn btn-info btn-sm">Angaben hinzufügen</a>
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>


<?php $berater_sql = sql_select_where('all', 'berater', 'ber_status', '1', '', ''); ?>
<?php if (mysqli_num_rows($berater_sql) == true && mysqli_num_rows($berater_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Berater
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Team</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Team</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($berater_row = mysqli_fetch_assoc($berater_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $berater_row['ber_id']; ?></th>
							<?php
								$begr_sql 	= sql_select_where('all', 'beratergruppe', 'begr_id', $berater_row['ber_gruppe'], '', '');
								$begr_row 	= mysqli_fetch_assoc($begr_sql);
							?>
							<td><?php echo $begr_row['begr_name']; ?></td>
							<td><?php echo $berater_row['ber_vorname']; ?></td>
							<td><?php echo $berater_row['ber_nachname']; ?></td>
							<td><?php echo $berater_row['ber_email']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="berater_id" value="<?php echo $berater_row['ber_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_berater.php?ber_id=<?php echo $berater_row['ber_id'] ?>" title='bearbeiten' class='btn btn-info btn-sm'>bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="berater_id" value="<?php echo $berater_row['ber_id']; ?>">
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

<?php $berater_sql = sql_select_where('all', 'berater', 'ber_status', '9', '', ''); ?>
<?php if (mysqli_num_rows($berater_sql) == true && mysqli_num_rows($berater_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Berater
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Team</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Team</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($berater_row = mysqli_fetch_assoc($berater_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $berater_row['ber_id']; ?></th>
							<?php
								$begr_sql 	= sql_select_where('all', 'beratergruppe', 'begr_id', $berater_row['ber_gruppe'], '', '');
								$begr_row 	= mysqli_fetch_assoc($begr_sql);
							?>
							<td><?php echo $begr_row['begr_name']; ?></td>
							<td><?php echo $berater_row['ber_vorname']; ?></td>
							<td><?php echo $berater_row['ber_nachname']; ?></td>
							<td><?php echo $berater_row['ber_email']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="berater_id" value="<?php echo $berater_row['ber_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_berater.php?ber_id=<?php echo $berater_row['ber_id'] ?>" title='bearbeiten' class='btn btn-info btn-sm'>bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="berater_id" value="<?php echo $berater_row['ber_id']; ?>">
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
