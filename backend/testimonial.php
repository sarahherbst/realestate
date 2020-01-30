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

	$schluessel 	= 'testimonial';

	if (isset($_POST['loeschen'])) {
		$txt_id = $_POST['txt_id'];
		$txt_delete = sql_delete('texte', 'txt_id', $txt_id);
		$img_delete = sql_select_where('img_bild, img_thumb', 'images', array('img_rubrik', 'img_schluessel', 'img_item_id'), array($rub_id, $schluessel, $txt_id), '', '');
		while ($img_delete_row = mysqli_fetch_assoc($img_delete)) {
			unlink($img_delete_row['img_bild']);
			unlink($img_delete_row['img_thumb']);
		}
		$img_delete = sql_delete('images', array('img_rubrik', 'img_schluessel', 'img_item_id'), array($rub_id, $schluessel, $txt_id));
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
	<li class="breadcrumb-item active"><a href="testimonial.php?rub_id=<?php echo $rub_id; ?>">Testimonial</a></li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Testimonial</h1>
	<p class="lead">Hier finden Sie eine Übersicht aller Testimonial der Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>.
	Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<?php $txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel'), array('1', $rub_id, $schluessel), '', ''); ?>
<?php if (mysqli_num_rows($txt_sql) == true && mysqli_num_rows($txt_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Testimonial
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Titel</th>
						<th>Statement</th>
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
						<th>Statement</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $txt_row['txt_id']; ?></th>
							<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_rubrik', 'img_item_id'), array('1', $rub_id, $txt_row['txt_id']), '', ''); ?>
							<?php $img_row = mysqli_fetch_assoc($img_sql); ?>
							<td><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail" width="200"></td>
							<td><?php echo $txt_row['txt_titel']; ?></td>
							<td><?php echo $txt_row['txt_auszug']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_testimonial.php?rub_id=<?php echo $rub_id; ?>&txt_id=<?php echo $txt_row['txt_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
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

<?php $txt_sql = sql_select_where('all', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel'), array('9', $rub_id, $schluessel), '', ''); ?>
<?php if (mysqli_num_rows($txt_sql) == true && mysqli_num_rows($txt_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Testimonial
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Titel</th>
						<th>Statement</th>
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
						<th>Statement</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($txt_row = mysqli_fetch_assoc($txt_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $txt_row['txt_id']; ?></th>
							<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_rubrik', 'img_item_id'), array('1', $rub_id, $txt_row['txt_id']), '', ''); ?>
							<?php $img_row = mysqli_fetch_assoc($img_sql); ?>
							<td><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail" width="200"></td>
							<td><?php echo $txt_row['txt_titel']; ?></td>
							<td><?php echo $txt_row['txt_auszug']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="txt_id" value="<?php echo $txt_row['txt_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_testimonial.php?rub_id=<?php echo $rub_id; ?>&txt_id=<?php echo $txt_row['txt_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
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
