<?php
	$page = 'statistik';
	require('header.php');

	// Phrase, wenn Passwort zurückgesetzt wurde
	if ($_SESSION['first'] == true) {
		echo $_SESSION['first'];
	}

	if ( isset($_POST['Data']) ) {
		// Datum auslesen
		if ( isset($_POST['startdate']) ) {
			$startdate 		= $_POST['startdate'];
			$dbStartdate 	= $_POST['startdate'];
		} else {
			$dbStartdate 	= '2018-08-01';
		}

		if ( isset($_POST['enddate']) ) {
			$enddate 		= $_POST['enddate'];
			$dbEnddate 		= $_POST['enddate'];
			$dbEnddate 		= date('Y-m-d', strtotime('+1 day'));
		} else {
			$dbEnddate 		= date('Y-m-d', strtotime('+1 day'));
		}

	} else {
		$dbStartdate 		= '2018-08-01';
		$dbEnddate 			= date('Y-m-d', strtotime('+1 day'));
		$startdate 			= '2018-08-01';
		$enddate 			= date('Y-m-d');
	}

	include('charts/dataCh1.php');
	include('charts/dataCh2.php');
	include('charts/dataCh3.php');
	include('charts/dataCh4.php');
	include('charts/dataCh5.php');
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Statistik</li>
</ol>

<div class="row">
	<!-- Datumsbereich -->
	<div class="col-lg-12">
		<form action="" method="post">
			<div class="form-row align-items-center">
				<div class="col-auto">
					<label class="sr-only" for="startdate">Startdatum</label>
					<input type="date" class="form-control" id="startdate" name="startdate" value="<?php echo $startdate; ?>" placeholder="Startdatum">
				</div>
				<div class="col-auto">
					<label>&ndash;</label>
				</div>
				<div class="col-auto">
					<label class="sr-only" for="enddate">Enddatum</label>
					<input type="date" class="form-control" id="enddate" name="enddate" value="<?php echo $enddate; ?>" placeholder="Enddatum">
				</div>
				<div class="col-auto">
					<button type="submit" class="btn btn-primary" name="Data">anzeigen</button>
				</div>
			</div>
		</form>
		<br>
	</div>
</div>

<div class="row">
	<!-- Tortendiagramm Seitenbesuch -->
	<?php
		$ch1_sql = sql_select_between('all', 'page_tracking', '', '', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
		if ( mysqli_num_rows($ch1_sql) == true ) {
	?>
	<div class="col-lg-6 mb-3">
		<div class="card h-100">
			<div class="card-header">
				<i class="fa fa-pie-chart"></i>
				<b>Seitenbesuche</b>
			</div>
			<div class="card-body">
				<div id="ch1Div" class="chart"></div>
				<div class="col-12">
					<table class="table">
						<thead>
							<th scope="col">Seite</th>
							<th scope="col">Besuche</th>
						</thead>
						<tbody>
							<tr>
								<?php $page1 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'startseite', 'pag_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage1 	= mysqli_num_rows($page1); ?>
								<td>Startseite</td>
								<td><?php echo $countPage1; ?></td>
							</tr>
							<tr>
								<?php $page2 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'ansprechpartner', 'pag_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage2 	= mysqli_num_rows($page2); ?>
								<td>Ansprechpartner</td>
								<td><?php echo $countPage2; ?></td>
							</tr>
							<tr>
								<?php $page3 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'impressum', 'pag_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage3 	= mysqli_num_rows($page3); ?>
								<td>Impressum</td>
								<td><?php echo $countPage3; ?></td>
							</tr>
							<tr>
								<?php $page4 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'datenschutz', 'pag_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage4 	= mysqli_num_rows($page4); ?>
								<td>Datenschutz</td>
								<td><?php echo $countPage4; ?></td>
							</tr>
							<?php 
								$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
								$i = 5;
								while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
									${'page'.$i} 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'rub_'.$count_rub_row['rub_id'], 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
									${'countPage'.$i} 	= mysqli_num_rows(${'page'.$i});
									echo '<tr>';
										echo '<td>'.$count_rub_row['rub_name'].'</td>';
										echo '<td>'.${'countPage'.$i}.'</td>';
									echo '</tr>';
									$i++;
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<?php $pages 		= sql_select_between('pag_id', 'page_tracking', '', '', 'pag_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPages 	= mysqli_num_rows($pages); ?>
								<td scope="col"><b>Insgesamt</b></td>
								<td><b><?php echo $countPages; ?></b></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="card-footer small text-muted"></div>
		</div>
	</div>
	<?php } ?>

	<!-- Tortendiagramm Conversions -->
	<?php
		$ch2_sql = sql_select_between('all', 'item_tracking', '', '', 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
		if ( mysqli_num_rows($ch2_sql) == true ) {
	?>
	<div class="col-lg-6 mb-3">
		<div class="card h-100">
			<div class="card-header">
				<i class="fa fa-pie-chart"></i>
				<b>Conversions –</b> Rubriken & Ansprechpartner
			</div>
			<div class="card-body">
				<div id="ch2Div" class="chart"></div>
				<div class="col-12">
					<table class="table">
						<thead>
							<th scope="col">Rubrik</th>
							<th scope="col">Klicks</th>
						</thead>
						<tbody>
							<tr>
								<?php $item1 = sql_select_between('ite_id', 'item_tracking', array('ite_praefix', 'NOT ite_position', 'NOT ite_position', 'NOT ite_position', 'NOT ite_name'), array('ansprechpartner', 'Formular', 'E-Mail', 'Telefon', 'Artikel'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countItem1 = mysqli_num_rows($item1); ?>
								<td>Ansprechpartner</td>
								<td><?php echo $countItem1; ?></td>
							</tr>
							
							<?php 
								$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
								$i = 2;
								while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
									${'item'.$i} 		= sql_select_between('ite_id', 'item_tracking', array('ite_praefix', 'NOT ite_position', 'NOT ite_position', 'NOT ite_position', 'NOT ite_name'), array('rub_'.$count_rub_row['rub_id'], 'Formular', 'E-Mail', 'Telefon', 'Artikel'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
									${'countItem'.$i} 	= mysqli_num_rows(${'item'.$i});
									echo '<tr>';
										echo '<td>'.$count_rub_row['rub_name'].'</td>';
										echo '<td>'.${'countItem'.$i}.'</td>';
									echo '</tr>';
									$i++;
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<?php $items = sql_select_between('all', 'item_tracking', array('NOT ite_position', 'NOT ite_position', 'NOT ite_position', 'NOT ite_praefix', 'NOT ite_praefix', 'NOT ite_praefix', 'NOT ite_name'), array('Formular', 'E-Mail', 'Telefon', 'startseite', 'datenschutz', 'impressum', 'Artikel'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countItems 	= mysqli_num_rows($items); ?>
								<td scope="col"><b>Insgesamt</b></td>
								<td><b><?php echo $countItems; ?></b></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="card-footer small text-muted"></div>
		</div>
	</div>
	<?php } ?>

	<!-- Balkendiagramm Conversionaufschlüsselung -->
	<?php
		$ch3_sql = sql_select_between('all', 'item_tracking', 'ite_name', '%Conversion%', 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
		if ( mysqli_num_rows($ch3_sql) == true ) {
	?>
	<div class="col-lg-12 mb-3">
		<div class="card h-100">
			<div class="card-header">
				<i class="fa fa-pie-chart"></i>
				<b>Conversions –</b> Aufschlüsselung nach Rubrik
			</div>
			<div class="card-body">
				<div id="ch3Div" class="chart"></div>
				<div class="col-12">
					<table class="table">
						<thead>
							<?php
								// Conversionarten rausfinden
								$con_art_sql = mysqli_query($db, "SELECT *FROM item_tracking WHERE ite_name LIKE '%Conversion%' GROUP BY ite_name");
								$con_art_num = mysqli_num_rows($con_art_sql);
								$con_art_i 	 = 1;
								echo '<th scope="col">Rubrik</th>';
								while( $con_art_row = mysqli_fetch_array($con_art_sql) ) {
									echo '<th scope="col">'.$con_art_row['ite_name'].'</th>';
								}
							?>
						</thead>
						<tbody>						
							<?php 
								// Rubriken abfragen
								$ch3_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
								while ($ch3_rub_row = mysqli_fetch_assoc($ch3_rub_sql)) {
									echo '<tr>';
									// Rubrikname
									echo '<td>'.$ch3_rub_row['rub_name'].'</td>';
									$rub_id = $ch3_rub_row['rub_id'];

									// Anzahl Conversionarten
									$con_art_sql = mysqli_query($db, "SELECT * FROM item_tracking WHERE ite_name LIKE '%Conversion%' GROUP BY ite_name");
									while( $con_art_row = mysqli_fetch_array($con_art_sql) ) {
										// Conversion-Name
										$conversion_name = $con_art_row['ite_name'];

										// Klick-Anzahl
										$con_klick_sql = mysqli_query($db, "SELECT COUNT(*) AS klicks FROM item_tracking WHERE ite_praefix = 'rub_".$rub_id."' AND ite_name = '".$conversion_name."' ");
										$con_klick_row = mysqli_fetch_assoc($con_klick_sql);
										echo '<td>'.$con_klick_row['klicks'].'</td>';

									}

									echo '</tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer small text-muted"></div>
		</div>
	</div>
	<?php } ?>

	<!-- Tortendiagramm Seitenzugriffe -->
	<?php
		$ch4_sql = sql_select_between('all', 'menu_tracking', '', '', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
		if ( mysqli_num_rows($ch4_sql) == true ) {
	?>
	<div class="col-lg-12 mb-3">
		<div class="card h-100">
			<div class="card-header">
				<i class="fa fa-pie-chart"></i>
				<b>Seitenzugriffe über das Menü</b>
			</div>
			<div class="card-body">
				<div id="ch4Div" class="chart"></div>
				<div class="col-12">
					<table class="table">
						<thead>
							<th scope="col">Seite</th>
							<th scope="col">Besuche</th>
						</thead>
						<tbody>
							<tr>
								<?php $page1 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'startseite', 'men_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage1 	= mysqli_num_rows($page1); ?>
								<td>Startseite</td>
								<td><?php echo $countPage1; ?></td>
							</tr>
							<tr>
								<?php $page2 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'ansprechpartner', 'men_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage2 	= mysqli_num_rows($page2); ?>
								<td>Ansprechpartner</td>
								<td><?php echo $countPage2; ?></td>
							</tr>
							<tr>
								<?php $page3 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'impressum', 'men_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage3 	= mysqli_num_rows($page3); ?>
								<td>Impressum</td>
								<td><?php echo $countPage3; ?></td>
							</tr>
							<tr>
								<?php $page4 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'datenschutz', 'men_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPage4 	= mysqli_num_rows($page4); ?>
								<td>Datenschutz</td>
								<td><?php echo $countPage4; ?></td>
							</tr>
							<?php 
								$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
								$i = 5;
								while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
									${'page'.$i} 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'rub_'.$count_rub_row['rub_id'], 'men_date', array($dbStartdate, $dbEnddate), '', '' );
									${'countPage'.$i} 	= mysqli_num_rows(${'page'.$i});
									echo '<tr>';
										echo '<td>'.$count_rub_row['rub_name'].'</td>';
										echo '<td>'.${'countPage'.$i}.'</td>';
									echo '</tr>';
									$i++;
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<?php $pages 		= sql_select_between('men_id', 'menu_tracking', '', '', 'men_date', array($dbStartdate, $dbEnddate), '', '' ); ?>
								<?php $countPages 	= mysqli_num_rows($pages); ?>
								<td scope="col"><b>Insgesamt</b></td>
								<td><b><?php echo $countPages; ?></b></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="card-footer small text-muted"></div>
		</div>
	</div>
	<?php } ?>

	<?php if($user_access == 'superadmin') { ?>
		<!-- Balkendiagramm Ansprechpartner -->
		<div class="col-lg-12 mb-3">
			<div class="card h-100">
				<div class="card-header">
					<i class="fa fa-bar-chart"></i>
					<b>Ansprechpartner –</b> Klicks
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12 my-auto">
							<div id="ch5Div" class="chart"></div>
						</div>
						<div class="col-12">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Ansprechpartner</th>
										<th scope="col">Email</th>
										<th scope="col">Telefon</th>
										<th scope="col">Klicks</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Insgesamt</th>
										<?php
											$ch5_sql 	= mysqli_query($db, 'SELECT *, COUNT(*) AS counter, sum(ite_position = "E-Mail") AS email, sum(ite_position = "Telefon") AS telefon FROM item_tracking WHERE (ite_position = "E-Mail" OR ite_position = "Telefon") AND ite_date BETWEEN "'.$dbStartdate.'" AND "'.$dbEnddate.'"') or die(mysqli_error($db));
											$ch5 = mysqli_fetch_assoc($ch5_sql);
										?>
											<th><?php echo $ch5['email']; ?></th>
											<th><?php echo $ch5['telefon']; ?></th>
											<th><?php echo $ch5['counter']; ?></th>
									</tr>
								</tfoot>
								<tbody>
									<?php
										$ch5_klicks_sql = mysqli_query($db, 'SELECT *, COUNT(*) AS counter, sum(ite_position = "E-Mail") AS email, sum(ite_position = "Telefon") AS telefon FROM item_tracking WHERE (ite_position = "E-Mail" OR ite_position = "Telefon") AND ite_date BETWEEN "'.$dbStartdate.'" AND "'.$dbEnddate.'" GROUP BY ite_name ORDER BY counter DESC') or die(mysqli_error($db));

										while( $ch5_klicks = mysqli_fetch_assoc($ch5_klicks_sql) ) {  ?>
											<tr class="text-left">
												<td><?php echo $ch5_klicks['ite_name']; ?></td>
												<td><?php echo $ch5_klicks['email']; ?></td>
												<td><?php echo $ch5_klicks['telefon']; ?></td>
												<td><?php echo $ch5_klicks['counter']; ?></td>
											</tr>
										<?php }	?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="card-footer small text-muted"></div>
			</div>
		</div>

		<!-- Tabelle Terminanforderungen -->
		<div class="col-lg-12 mb-3">
			<div class="card h-100">
				<div class="card-header">
					<i class="fa fa-bar-chart"></i>
					<b>Terminanforderungen</b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<table class="table">
								<thead>
									<tr>
										<th scope="col">Formular</th>
										<th scope="col">Anforderung</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Insgesamt</th>
										<?php
											$ch6_sql 	= mysqli_query($db, 'SELECT *, COUNT(*) AS counter FROM item_tracking WHERE (ite_position = "Formular") AND ite_date BETWEEN "'.$dbStartdate.'" AND "'.$dbEnddate.'"') or die(mysqli_error($db));
											$ch6 = mysqli_fetch_assoc($ch6_sql);
										?>
										<th><?php echo $ch6['counter']; ?></th>
									</tr>
								</tfoot>
								<tbody>
									<?php
										$ch6_klicks_sql = mysqli_query($db, 'SELECT *, COUNT(*) AS counter FROM item_tracking WHERE (ite_position = "Formular") AND ite_date BETWEEN "'.$dbStartdate.'" AND "'.$dbEnddate.'" GROUP BY ite_name ORDER BY counter DESC') or die(mysqli_error($db));

										while( $ch6_klicks = mysqli_fetch_assoc($ch6_klicks_sql) ) {  ?>
											<tr class="text-left">
												<td><?php echo $ch6['ite_rubrik']; ?></td>
												<td><?php echo $ch6_klicks['counter']; ?></td>
											</tr>
										<?php }	?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="card-footer small text-muted"></div>
			</div>
		</div>
	<?php } ?>


</div>

<?php
	require('footer.php');
?>