<?php
	$page 			= 'beratergruppe';
	$schluessel 	= 'beratergruppe';

	require('header.php');

	if (isset($_POST['loeschen'])) {
		$begr_id 		= $_POST['begr_id'];
		$begr_delete 	= sql_delete('beratergruppe', 'begr_id', $begr_id);
	}

	if (isset($_POST['deaktivieren'])) {
		$begr_id 		= $_POST['begr_id'];
		$begr_update 	= sql_update('beratergruppe', array('begr_status', 'chg_user'), array('9', $user_email), 'begr_id', $begr_id);
	}

	if (isset($_POST['aktivieren'])) {
		$begr_id 		= $_POST['begr_id'];
		$begr_update 	= sql_update('beratergruppe', array('begr_status', 'chg_user'), array('1', $user_email), 'begr_id', $begr_id);
	}
?>

<!-- Breadcrumbs -->
<ol class='breadcrumb'>
	<li class='breadcrumb-item'>Berater</li>
	<li class='breadcrumb-item active'>Teams</li>
</ol>

<!-- Einleitung -->
<div class='jumbotron'>
	<h1>Teams</h1>
	<p class='lead'>Hier finden Sie eine Übersicht aller Beraterteams. Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<?php $begr_sql = sql_select_where('all', 'beratergruppe', array('begr_institut', 'begr_status'), array($institut_id, '1'), '', ''); ?>
<?php if (mysqli_num_rows($begr_sql) == true && mysqli_num_rows($begr_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Teams
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Bezeichnung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Bezeichnung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($begr_row = mysqli_fetch_assoc($begr_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $begr_row['begr_id']; ?></th>
							<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_schluessel', 'img_item_id'), array('1', $schluessel, $begr_row['begr_id']), '', ''); ?>
							<?php $img_row = mysqli_fetch_assoc($img_sql); ?>
							<td><?php if($img_row['img_thumb'] != '') { ?><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail" width="200"><?php } ?></td>
							<td><?php echo $begr_row['begr_name']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="begr_id" value="<?php echo $begr_row['begr_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_beratergruppe.php?begr_id=<?php echo $begr_row['begr_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="begr_id" value="<?php echo $begr_row['begr_id']; ?>">
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

<?php $begr_sql = sql_select_where('all', 'beratergruppe', array('begr_institut', 'begr_status'), array($institut_id, '9'), '', ''); ?>
<?php if (mysqli_num_rows($begr_sql) == true && mysqli_num_rows($begr_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Teams
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Bezeichnung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Thumbnail</th>
						<th>Bezeichnung</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($begr_row = mysqli_fetch_assoc($begr_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $begr_row['begr_id']; ?></th>
							<?php $img_sql = sql_select_where('all', 'images', array('img_status', 'img_schluessel', 'img_item_id'), array('1', $schluessel, $begr_row['begr_id']), '', ''); ?>
							<?php $img_row = mysqli_fetch_assoc($img_sql); ?>
							<td><img src="../<?php echo $img_row['img_thumb']; ?>" alt="Thumbnail" class="img-thumbnail" width="200"></td>
							<td><?php echo $begr_row['begr_name']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="begr_id" value="<?php echo $begr_row['begr_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_beratergruppe.php?begr_id=<?php echo $begr_row['begr_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="begr_id" value="<?php echo $begr_row['begr_id']; ?>">
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
