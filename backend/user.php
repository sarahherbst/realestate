<?php
	$page = 'user';
	require('header.php');

	if (isset($_POST['loeschen'])) {
		$use_id = $_POST['user_id'];
		$use_delete = sql_delete('user', 'use_id', $use_id);
	}

	if (isset($_POST['deaktivieren'])) {
		$use_id = $_POST['user_id'];
		$use_update = sql_update('user', array('use_status', 'chg_user'), array('9', $user_email), 'use_id', $use_id);
	}

	if (isset($_POST['aktivieren'])) {
		$use_id = $_POST['user_id'];
		$use_update = sql_update('user', array('use_status', 'chg_user'), array('1', $user_email), 'use_id', $use_id);
	}
?>
<!-- Breadcrumbs -->
<ol class='breadcrumb'>
	<li class='breadcrumb-item active'>Benutzer</li>
</ol>

<!-- Einleitung -->
<div class='jumbotron'>
	<h1>Benutzer</h1>
	<p class='lead'>Hier finden Sie eine Übersicht aller Benutzer, die Zugriff auf dieses Backend haben.
	Desweiteren haben Sie die Möglichkeit Accounts zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->

<?php $user_sql = sql_select_where('all', 'user', 'use_status', '1', '', ''); ?>
<?php if (mysqli_num_rows($user_sql) == true && mysqli_num_rows($user_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Benutzer
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Zugriffsrolle</th>
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
						<th>Zugriffsrolle</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($user_row = mysqli_fetch_assoc($user_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $user_row['use_id']; ?></th>
							<td><?php echo ($user_row['use_access'] == 'admin' ? '<b>' : ''); ?><?php echo $user_row['use_access']; ?><?php echo ($user_row['use_access'] == 'admin' ? '</b>' : ''); ?></td>
							<td><?php echo $user_row['use_vorname']; ?></td>
							<td><?php echo $user_row['use_nachname']; ?></td>
							<td><?php echo $user_row['use_email']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="user_id" value="<?php echo $user_row['use_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_user.php?use_id=<?php echo $user_row['use_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="user_id" value="<?php echo $user_row['use_id']; ?>">
									<button type="submit" name="loeschen" class="btn btn-danger btn-sm">löschen</button>
								</form>
							</td>
						</tr>
					<?php }	?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php }	?>

<?php $user_sql = sql_select_where('all', 'user', 'use_status', '9', '', ''); ?>
<?php if (mysqli_num_rows($user_sql) == true && mysqli_num_rows($user_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Benutzer
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Zugriffsrolle</th>
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
						<th>Zugriffsrolle</th>
						<th>Vorname</th>
						<th>Nachname</th>
						<th>E-Mail</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($user_row = mysqli_fetch_assoc($user_sql)) { ?>
						<tr>
							<th scope="row"><?php echo $user_row['use_id']; ?></th>
							<td><?php echo ($user_row['use_access'] == 'admin' ? '<b>' : ''); ?><?php echo $user_row['use_access']; ?><?php echo ($user_row['use_access'] == 'admin' ? '</b>' : ''); ?></td>
							<td><?php echo $user_row['use_vorname']; ?></td>
							<td><?php echo $user_row['use_nachname']; ?></td>
							<td><?php echo $user_row['use_email']; ?></td>
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="user_id" value="<?php echo $user_row['use_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_user.php?use_id=<?php echo $user_row['use_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="user_id" value="<?php echo $user_row['use_id']; ?>">
									<button type="submit" name="loeschen" class="btn btn-danger btn-sm">löschen</button>
								</form>
							</td>
						</tr>
					<?php }	?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php }	?>


<?php
	include('footer.php');
?>
