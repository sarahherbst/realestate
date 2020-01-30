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

	$schluessel 	= 'galerie';

	if (isset($_POST['loeschen'])) {
		$img_id = $_POST['img_id'];
		$img_delete = sql_delete('images', 'img_id', $img_id);
		$img_delete = sql_select_where('img_bild, img_thumb', 'images', array('img_rubrik', 'img_schluessel', 'img_item_id'), array($rub_id, $schluessel, $txt_id), '', '');
		while ($img_delete_row = mysqli_fetch_assoc($img_delete)) {
			unlink($img_delete_row['img_bild']);
			unlink($img_delete_row['img_thumb']);
		}
		$img_delete = sql_delete('images', array('img_rubrik', 'img_schluessel', 'img_item_id'), array($rub_id, 'artikel', $txt_id));
	}

	if (isset($_POST['deaktivieren'])) {
		$img_id = $_POST['img_id'];
		$img_update = sql_update('images', array('img_status', 'chg_user'), array('9', $user_email), 'img_id', $img_id);
	}

	if (isset($_POST['aktivieren'])) {
		$img_id = $_POST['img_id'];
		$img_update = sql_update('images', array('img_status', 'chg_user'), array('1', $user_email), 'img_id', $img_id);
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item">Galeriebilder</li>
</ol>

<div class="jumbotron">
	<h1>Galeriebilder</h1>
	<p class="lead">Hier finden Sie eine Übersicht aller Bilder für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>.</p>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-image" aria-hidden="true"></i> &nbsp; Galerieangaben</b>
			</div>
			<div class="card-body">
				<div class="row">
					<?php
						$txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_schluessel'), array('1', $schluessel), '', '');
						if ( mysqli_num_rows($txt_sql) == 1 ) {
							while ($txt_row = mysqli_fetch_object($txt_sql)) { ?>
							<div class="col-md-6">
								<div class="form-group">
									<label for="txt_titel">Headline:</label>
									<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_row->txt_titel; ?>" disabled>
								</div>

								<div class="form-group">
									<label for="txt_einleitung">Einleitung:</label>
									<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung" disabled><?php echo $txt_row->txt_einleitung; ?></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="txt_conversion_ziel">Link zum Conversion-Ziel:</label><br>
									<a href="<?php echo $txt_row->txt_conversion_ziel; ?>" target="_blank" class="btn btn-outline-primary">Link zum Conversionziel</a>
								</div>

								<div class="form-group">
									<label for="txt_conversion_titel">Button-Bezeichnung:</label>
									<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_row->txt_conversion_titel; ?>" disabled>
								</div>
							</div>
							
							<div class="col-12 text-right">
								<a href="edit_gallery_info.php?rub_id=<?php echo $rub_id; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</div>
						<?php } ?>
					<?php } else { ?>
							<div class="col-md-12">
								<p>Es wurden bisher keine Angaben zur Galerie gemacht.</p>
								<a href="edit_gallery_info.php?rub_id=<?php echo $rub_id; ?>" title="Angaben hinzufügen" class="btn btn-info btn-sm">Angaben hinzufügen</a>
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_rubrik', 'img_schluessel'), array('1', $rub_id, $schluessel), '', ''); ?>
<?php if (mysqli_num_rows($img_sql) == true && mysqli_num_rows($img_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Galeriebilder
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
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
						<th>Thumbnail</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($img_row = mysqli_fetch_assoc($img_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $img_row['img_id']; ?></th>
							<td><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail"></td>
							<td><?php echo $img_row['img_titel']; ?></td>
							<td><?php echo $img_row['img_beschreibung']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="img_id" value="<?php echo $img_row['img_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_gallery.php?rub_id=<?php echo $rub_id; ?>&img_id=<?php echo $img_row['img_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="img_id" value="<?php echo $img_row['img_id']; ?>">
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

<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_rubrik', 'img_schluessel'), array('9', $rub_id, $schluessel), '', ''); ?>
<?php if (mysqli_num_rows($img_sql) == true && mysqli_num_rows($img_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Galeriebilder
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
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
						<th>Thumbnail</th>
						<th>Titel</th>
						<th>Beschreibung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($img_row = mysqli_fetch_assoc($img_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $img_row['img_id']; ?></th>
							<td><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail"></td>
							<td><?php echo $img_row['img_titel']; ?></td>
							<td><?php echo $img_row['img_beschreibung']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="img_id" value="<?php echo $img_row['img_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_gallery.php?rub_id=<?php echo $rub_id; ?>&img_id=<?php echo $img_row['img_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="img_id" value="<?php echo $img_row['img_id']; ?>">
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