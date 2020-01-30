<?php
	$page 	= 'rubrik';
	require('header.php');

	$schluessel 	= $page;

	if (isset($_POST['loeschen'])) {
		$rub_id 	= $_POST['rub_id'];
		$rub_delete = sql_delete('rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id));
		$rub_delete = sql_delete('images', array('img_institut', 'img_rubrik'), array($institut_id, $rub_id));
		$rub_delete = sql_delete('texte', array('txt_institut', 'txt_rubrik'), array($institut_id, $rub_id));
		$img_delete = sql_select_where('img_bild, img_thumb', 'images', array('img_institut, img_rubrik'), array($institut_id, $rub_id), '', '');
		while ($img_delete_row = mysqli_fetch_assoc($img_delete)) {
			unlink($img_delete_row['img_bild']);
			unlink($img_delete_row['img_thumb']);
		}
		$img_delete = sql_delete('images', array('img_institut', 'img_rubrik'), array($institut_id, $rub_id));
	}

	if (isset($_POST['deaktivieren'])) {
		$rub_id = $_POST['rub_id'];
		$rub_update = sql_update('rubrik', array('rub_status', 'chg_user'), array('9', $user_email), 'rub_id', $rub_id);
	}

	if (isset($_POST['aktivieren'])) {
		$rub_id = $_POST['rub_id'];
		$rub_update = sql_update('rubrik', array('rub_status', 'chg_user'), array('1', $user_email), 'rub_id', $rub_id);
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Rubriken</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Rubriken Übersicht</h1>
	<p class="lead">Hier finden Sie eine Übersicht aller Rubriken.
	Desweiteren haben Sie die Möglichkeit einzelne zu deaktivieren oder auch zu löschen.</p>
</div><!-- /.jumbotron -->


<!-- Aktive Rubriken -->
<?php $rub_sql = sql_select_where('all', 'rubrik', array('rub_status'), array('1'), '', ''); ?>
<?php if (mysqli_num_rows($rub_sql) == true && mysqli_num_rows($rub_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Aktive Rubriken
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Bezeichnung</th>
						<th>SEO – Seitentitel</th>
						<th>SEO – Beschreibung</th>
						<th>CSS-Klasse: Hintergrund</th>
						<th>CSS-Klasse: Hervorhebung</th>
						<th>E-Mail (Empfänger)</th>
						<th>E-Mail (BCC)</th>
						<th>Subline (Kontaktformular)</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Bezeichnung</th>
						<th>SEO – Seitentitel</th>
						<th>SEO – Beschreibung</th>
						<th>CSS-Klasse: Hintergrund</th>
						<th>CSS-Klasse: Hervorhebung</th>
						<th>E-Mail (Empfänger)</th>
						<th>E-Mail (BCC)</th>
						<th>Subline (Kontaktformular)</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($rub_row = mysqli_fetch_assoc($rub_sql)) { ?>
						<tr>
							<td scope="row"><?php echo $rub_row['rub_id']; ?></td>
							<td><?php echo $rub_row['rub_name']; ?></td>
							<td><?php echo $rub_row['rub_title']; ?></td>
							<td><?php echo $rub_row['rub_description']; ?></td>
							<td><?php echo $rub_row['rub_css_bg']; ?></td>
							<td><?php echo $rub_row['rub_css_txt']; ?></td>
							<td><?php echo $rub_row['rub_email_zu']; ?></td>
							<td><?php echo $rub_row['rub_email_kopie']; ?></td>
							<td><?php echo $rub_row['rub_form_subline']; ?></td>
							
							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="rub_id" value="<?php echo $rub_row['rub_id']; ?>">
									<button type="submit" name="deaktivieren" class="btn btn-default btn-sm">deaktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_category.php?rub_id=<?php echo $rub_row['rub_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="rub_id" value="<?php echo $rub_row['rub_id']; ?>">
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

<!-- Deaktivierte Rubriken -->
<?php $rub_sql = sql_select_where('all', 'rubrik', array('rub_status'), array('9'), '', ''); ?>
<?php if (mysqli_num_rows($rub_sql) == true && mysqli_num_rows($rub_sql) >= 1) { ?>
<div class="card mb-3">
	<div class="card-header">
		<i class="fas fa-table"></i>
		Deaktivierte Rubriken
	</div>
	
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>#</th>
						<th>Bezeichnung</th>
						<th>SEO – Seitentitel</th>
						<th>SEO – Beschreibung</th>
						<th>CSS-Klasse: Hintergrund</th>
						<th>CSS-Klasse: Hervorhebung</th>
						<th>E-Mail (Empfänger)</th>
						<th>E-Mail (BCC)</th>
						<th>Subline (Kontaktformular)</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>#</th>
						<th>Bezeichnung</th>
						<th>SEO – Seitentitel</th>
						<th>SEO – Beschreibung</th>
						<th>CSS-Klasse: Hintergrund</th>
						<th>CSS-Klasse: Hervorhebung</th>
						<th>E-Mail (Empfänger)</th>
						<th>E-Mail (BCC)</th>
						<th>Subline (Kontaktformular)</th>
						<th>Auswahl</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php while ($rub_row = mysqli_fetch_assoc($rub_sql)) { ?>
						<tr>
							<td scope="row"><?php echo $rub_row['rub_id']; ?></td>
							<td><?php echo $rub_row['rub_name']; ?></td>
							<td><?php echo $rub_row['rub_title']; ?></td>
							<td><?php echo $rub_row['rub_description']; ?></td>
							<td><?php echo $rub_row['rub_css_bg']; ?></td>
							<td><?php echo $rub_row['rub_css_txt']; ?></td>
							<td><?php echo $rub_row['rub_email_zu']; ?></td>
							<td><?php echo $rub_row['rub_email_kopie']; ?></td>
							<td><?php echo $rub_row['rub_form_subline']; ?></td>

							<td>
								<form action="" class="form-inline" method="post" enctype="multipart/form-data">
									<input type="hidden" name="rub_id" value="<?php echo $rub_row['rub_id']; ?>">
									<button type="submit" name="aktivieren" class="btn btn-default btn-sm">aktivieren</button>
								</form>
							</td>
							<td>
								<a href="edit_category.php?rub_id=<?php echo $rub_row['rub_id']; ?>" title="bearbeiten" class="btn btn-info btn-sm">bearbeiten</a>
							</td>
							<td>
								<form action="" method="post" enctype="multipart/form-data">
									<input type="hidden" name="rub_id" value="<?php echo $rub_row['rub_id']; ?>">
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
<?php } ?>

<?php
	include('footer.php');
?>
