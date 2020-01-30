<?php
	require_once('connection.inc.php');
	require_once('function.inc.php');
	require_once('data.inc.php');

	//Variablen einlesen
	$ite_name 		= $_POST['ite_name'];
	$ite_rubrik		= $_POST['ite_rubrik'];
	$ite_position	= $_POST['ite_position'];
	$ite_url 		= $_POST['ite_url'];

	//Daten konvertieren
	if ( $ite_url != str_replace($microsite_url,'',$ite_url) ) {
		$ite_url 		= str_replace($microsite_url.'/', '', $ite_url);
	}

	if ( $ite_name != str_replace('Ansprechpartner','',$ite_name) ) {
		$ite_name 		= str_replace('Ansprechpartner ', '', $ite_name);
		$ite_praefix 	= 'ansprechpartner';
	}

	if ($ite_name == 'Impressum') {
		$ite_praefix = 'impressum';
	} elseif ($ite_name == 'Datenschutz') {
		$ite_praefix = 'datenschutz';
	} elseif ($ite_name == 'Ansprechpartner') {
		$ite_praefix = 'ansprechpartner';
	}

	if ( $ite_url != str_replace('mailto:','',$ite_url) ) {
		$teilen 	= explode('mailto:', $ite_url);
		$ite_url 	= $teilen[1];
	}
	if ( $ite_url != str_replace('tel:','',$ite_url) ) {
		$teilen 	= explode('tel:', $ite_url);
		$ite_url 	= $teilen[1];
	}
		
	$rub_name_sql 			= sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
	while ($rub_name_row 	= mysqli_fetch_assoc($rub_name_sql)) {
		if ($ite_rubrik == $rub_name_row['rub_name']) {
			$ite_praefix 	= 'rub_'.$rub_name_row['rub_id'];
			$ite_rubrik 	= $rub_name_row['rub_name'];
		}
	}

	if ($ite_praefix != '' || $ite_rubrik != '') {
		//Tracking in Datenbank eintragen
		$sql_item_tracking = sql_insert('item_tracking', array('ite_institut', 'ite_praefix', 'ite_rubrik', 'ite_position', 'ite_name', 'ite_url'), array($institut_id, $ite_praefix, $ite_rubrik, $ite_position, $ite_name, $ite_url));
		if ($sql_item_tracking == true) {
		} else {
			$fehler++;
			$array_response = array('Item-Track konnte nicht eingetragen werden.');
			echo json_encode($array_response);
		}
	}

?>