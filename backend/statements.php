<?php
	$page 			= 'statements';
	$schluessel 	= 'statements';

	require('header.php');

	if (isset($_POST['loeschen'])) {
		$stm_id 	= $_POST['statement_id'];
		$stm_delete = sql_delete('statements', 'stm_id', $stm_id);
		$stm_img_delete = sql_delete('images', array('img_schluessel', 'img_item_id'), array('statement', $stm_id));
	}

	if (isset($_POST['deaktivieren'])) {
		$stm_id 	= $_POST['statement_id'];
		$stm_update = sql_update('statements', array('stm_status', 'chg_user'), array('9', $user_email), 'stm_id', $stm_id);
	}

	if (isset($_POST['aktivieren'])) {
		$stm_id 	= $_POST['statement_id'];
		$stm_update = sql_update('statements', array('stm_status', 'chg_user'), array('1', $user_email), 'stm_id', $stm_id);
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Kundenprojekte</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Kundenprojekte</h1>
	<p class='lead'>Hier finden Sie eine Übersicht aller Kundenprojekte. Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<?php $statement_sql = sql_select_where('all', 'statements', 'stm_status', '1', '', ''); ?>
<?php if (mysqli_num_rows($statement_sql) == true && mysqli_num_rows($statement_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Kundenprojekte
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Text</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Text</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($statement_row = mysqli_fetch_assoc($statement_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $statement_row['stm_id']; ?></th>
							<td><?php echo $statement_row['stm_title']; ?></td>
							<td><?php echo $statement_row['stm_text']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="statement_id" value="<?php echo $statement_row['stm_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_statement.php?stm_id=<?php echo $statement_row['stm_id'] ?>" title='bearbeiten' class='btn btn-info btn-sm'>bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="statement_id" value="<?php echo $statement_row['stm_id']; ?>">
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

<?php $statement_sql = sql_select_where('all', 'statements', 'stm_status', '9', '', ''); ?>
<?php if (mysqli_num_rows($statement_sql) == true && mysqli_num_rows($statement_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Kundenprojekte
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Text</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Titel</th>
						<th>Text</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($statement_row = mysqli_fetch_assoc($statement_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $statement_row['stm_id']; ?></th>
							<td><?php echo $statement_row['stm_title']; ?></td>
							<td><?php echo $statement_row['stm_text']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="statement_id" value="<?php echo $statement_row['stm_id']; ?>">
									<button type="submit" name="aktivieren" class="btn btn-default btn-sm">aktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_statement.php?stm_id=<?php echo $statement_row['stm_id'] ?>" title='bearbeiten' class='btn btn-info btn-sm'>bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="statement_id" value="<?php echo $statement_row['stm_id']; ?>">
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
