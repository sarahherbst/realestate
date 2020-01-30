<?php
	require_once('connection.inc.php');
	require_once('function.inc.php');
	require_once('data.inc.php');

	//Variablen einlesen
	$men_seite 	= $_POST['men_seite'];
	$men_url 	= $_POST['men_url'];

	//Daten konvertieren
	$rub_name_sql 			= sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
	while ($rub_name_row = mysqli_fetch_assoc($rub_name_sql)) {
		if ($men_seite == $rub_name_row['rub_name']) {
			$men_praefix 	= 'rub_'.$rub_name_row['rub_id'];
			$men_seite 		= $rub_name_row['rub_name'];
		}
	}
	if ($men_seite == 'Startseite') {
		$men_praefix 	= 'startseite';
	} elseif ($men_seite == 'Ansprechpartner') {
		$men_praefix 	= 'ansprechpartner';
	} elseif ($men_seite == 'Impressum') {
		$men_praefix 	= 'impressum';
	} elseif ($men_seite == 'Datenschutz') {
		$men_praefix 	= 'datenschutz';
	}

	$rubriken_sql 	= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
	while ($rubriken_row 	= mysqli_fetch_assoc($rubriken_sql)) {
		if ($men_seite == 'rub_'.$rubriken_row['rub_id']) {
			$men_praefix 	= 'rub_'.$rubriken_row['rub_id'];
		}
	}

	//SeitenTracking in Datenbank eintragen
	$sql_menu_tracking = sql_insert('menu_tracking', array('men_praefix', 'men_seite', 'men_url'), array($men_praefix, $men_seite, $men_url));
	if ($sql_menu_tracking == true) {
	} else {
		$fehler++;
		$array_response =  array('Menu-Track konnte nicht eingetragen werden.');
		echo json_encode($array_response);
	}
?>