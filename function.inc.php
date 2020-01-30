<?php
require('connection.inc.php');

//MYSQL SIMPLE SELECT
/*
** Die sql_select() - function selectiert je nach Bedarf werte aus der Datenbank.
** OPTIONEN: 
** $selectors 	- Mögliche Werte: 'all' oder die gewünschten Spaltennamen einer Tabelle
** $table 		- Gibt den Namen der Tabelle an
** $orderby 	- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 		- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_select($selectors, $table, $orderby, $order) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }
	if ($order == '') { 
		$sql = "SELECT $selectors FROM $table"; 
	} else {
		$sql = "SELECT $selectors FROM $table ORDER BY $orderby $order";
	}

	$res = mysqli_query($db, $sql) or die(mysqli_error($db));

	return $res;
}

//MYSQL EXTEND SELECT -> WHERE
/*
** Die sql_select_between() - function arbeitet wie die sql_select() - function, mit where clause
** OPTIONEN: 
** $selectors 		- Mögliche Werte: 'all' oder die gewünschten Spaltennamen einer Tabelle
** $table 			- Gibt den Namen der Tabelle an
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $orderby 		- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 			- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_select_between($selectors, $table, $whereSelectors, $whereValues, $betweenSelector, $betweenValues, $orderby, $order) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }

	$sql = "SELECT $selectors FROM $table WHERE ";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= "$whereSelectors[0] LIKE '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $key => $val) {
			if (!$i == 0) { $sql .= " AND $val LIKE '" . $whereValues[$i] . "'"; }
			$i++;
		}
		$sql .= " AND ";
	} elseif ($whereSelectors !== '') {
		$sql .= "$whereSelectors LIKE '" . $whereValues . "' AND ";
	}

	$sql .= "$betweenSelector BETWEEN '" . $betweenValues[0] . "' AND '" . $betweenValues[1] . "'";

	if ($orderby != '') {
		$sql .= " ORDER BY $orderby $order";
	}
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}


//MYSQL EXTEND SELECT -> WHERE
/*
** Die sql_select_where() - function arbeitet wie die sql_select() - function, mit where clause
** OPTIONEN: 
** $selectors 		- Mögliche Werte: 'all' oder die gewünschten Spaltennamen einer Tabelle
** $table 			- Gibt den Namen der Tabelle an
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $orderby 		- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 			- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_select_where($selectors, $table, $whereSelectors, $whereValues, $orderby, $order) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }

	$sql = "SELECT $selectors FROM $table WHERE ";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= "$whereSelectors[0] LIKE '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $key => $val) {
			if (!$i == 0) { $sql .= " AND $val LIKE '" . $whereValues[$i] . "'"; }
			$i++;
		}
	} else {
		$sql .= "$whereSelectors LIKE '" . $whereValues . "'";
	}

	if ($orderby != '') {
		$sql .= " ORDER BY $orderby $order";
	}
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

function sql_select_where_limit($selectors, $table, $whereSelectors, $whereValues, $orderby, $order, $limit) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }

	$sql = "SELECT $selectors FROM $table WHERE ";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= "$whereSelectors[0] LIKE '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $key => $val) {
			if (!$i == 0) { $sql .= " AND $val LIKE '" . $whereValues[$i] . "'"; }
			$i++;
		}
	} else {
		$sql .= "$whereSelectors LIKE '" . $whereValues . "'";
	}

	if ($orderby != '') {
		$sql .= " ORDER BY $orderby $order";
	}

	if ($limit != '') {
		$sql .= " LIMIT $limit";
	}
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL EXTEND SELECT -> WHERE
/*
** Die sql_select_where_not_in() - function arbeitet wie die sql_select() - function, mit where clause
** OPTIONEN: 
** $selectors 		- Mögliche Werte: 'all' oder die gewünschten Spaltennamen einer Tabelle
** $table 			- Gibt den Namen der Tabelle an
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $orderby 		- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 			- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_select_where_not_in($selectors, $table, $whereSelectors, $whereValues, $orderby, $order) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }

	$sql = "SELECT $selectors FROM $table WHERE ";

	$sql .= "$whereSelectors NOT IN ('" . $whereValues . "')";

	if ($orderby != '') {
		$sql .= " ORDER BY $orderby $order";
	}
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL EXTEND SELECT -> WHERE
/*
** Die sql_select_testimonial_where() - function arbeitet wie die sql_select() - function, mit where clause
** OPTIONEN: 
** $selectors 		- Mögliche Werte: 'all' oder die gewünschten Spaltennamen einer Tabelle
** $table 			- Gibt den Namen der Tabelle an
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $orderby 		- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 			- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_select_testimonial_where($selectors, $table, $whereSelectors, $whereValues, $orderby, $order) {
	global $db;

	if ($selectors = 'all') { $selectors = '*'; }

	$sql = "SELECT $selectors FROM $table WHERE ";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= "$whereSelectors[0] = '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $key => $val) {
			if (!$i == 0) { $sql .= " AND $val = '" . $whereValues[$i] . "'"; }
			$i++;
		}
	} else {
		$sql .= "$whereSelectors = '" . $whereValues . "'";
		
	}
	$sql .= " AND txt_schluessel = 'testimonial'";

	if ($orderby != '') {
		$sql .= " ORDER BY $orderby $order";
	}
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL DELETE -> WHERE
/*
** Die sql_delete() - function arbeitet wie die sql_select() - function, mit where clause
** OPTIONEN: 
** $table 			- Gibt den Namen der Tabelle an
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $orderby 		- Gibt den Spaltennamen nachdem sortiert werden soll an, ist der Wert '' wird der ORDER BY Befehl ignoriert
** $order 			- Mögliche Werte: 'ASC', 'DESC'
*/
function sql_delete($table, $whereSelectors, $whereValues) {
	global $db;

	$sql = "DELETE FROM $table WHERE ";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= "$whereSelectors[0] = '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $key => $val) {
			if (!$i == 0) { $sql .= " AND $val = '" . $whereValues[$i] . "'"; }
			$i++;
		}
	} else {
		$sql .= "$whereSelectors = '" . $whereValues . "'";
	}

	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL INSERT INTO
/*
** Die sql_insert() - function erstellt einen INSERT INTO mysql-Befehl
** $table 		- Gibt den Namen der Tabelle an
** $selectors 	- Gibt die Spaltennamen als array an. !Ein Einzelner Wert wird als String angegeben
** $values 		- Gibt die Spalteninhalte als array an. !Ein Einzelner Wert wird als String angegeben
*/
function sql_insert($table, $selectors, $values) {
	global $db;

	$sql = "INSERT INTO $table (";

	if (is_array($selectors) & is_array($values)) {
		foreach($selectors as $key => $val) {
			$sql .= "$val, ";
		}

		$sql = substr_replace($sql, '', -2);
		$sql .= ", new_time, new_date, chg_time, chg_date) VALUES (";

		foreach($values as $key => $val) {
			$sql .= "'$val', ";
		}

		$sql = substr_replace($sql, '', -2);
		$sql .= ", curtime(), curdate(), curtime(), curdate())";
	} else {
		$sql .= "$selectors, new_time, new_date, chg_time, chg_date) VALUES ('$values', curtime(), curdate(), curtime(), curdate())";
	}

	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL UPDATE
/*
** Die sql_update() - function erstellt einen INSERT INTO mysql-Befehl
** $table 		- Gibt den Namen der Tabelle an
** $selectors 	- Gibt die Spaltennamen als array an. !Ein Einzelner Wert wird als String angegeben
** $values 		- Gibt die Spalteninhalte als array an. !Ein Einzelner Wert wird als String angegeben
** $whereSelectors 	- Gibt die gewünschten Spaltennamen als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
** $whereValues 	- Gibt die Inhalte nach denen selectiert werden soll als array an. !Eine Einzelne Spalte kann per string eingetragen werden.
*/
function sql_update($table, $selectors, $values, $whereSelectors, $whereValues) {
	global $db;

	$sql = "UPDATE $table SET ";

	if (is_array($selectors) & is_array($values)) {
		$sql .= "$selectors[0] = '" . $values[0] . "'";
		$i = 0;
		foreach($selectors as $key => $val) {
			if (!$i == 0) { 
				$sql .= ", $val = '" . $values[$i] . "'"; 
			}
			$i++;
		}
	} else {
		$sql .= "$selectors  = '" . $values . "'";
	}
	$sql .= ", chg_time = curtime(), chg_date = curdate()";

	if (is_array($whereSelectors) & is_array($whereValues)) {
		$sql .= " WHERE $whereSelectors[0] = '" . $whereValues[0] . "'";
		$i = 0;
		foreach($whereSelectors as $wkey => $whereVal) {
			if (!$i == 0) { $sql .= " AND $whereVal = '" . $whereValues[$i] . "'"; }
			$i++;
		}
	} else {
		$sql .= " WHERE $whereSelectors = '" . $whereValues . "'";
	}

	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	return $res;
}

//MYSQL OUTPUT
/*
** Die sql_output() - function gibt nach bedarf entsprechende Werte aus der Datenbank aus
** $source 		- Gibt die Quelle des querys an
** $type 		- Bestimmt die Art der Ausgabe
** $selectors 	- Legt die Auszugebenen Spalten fest. !per '{custom} ...' kann $selectors übergangen werden
** Beispiel: echo sql_output($result, 'table', array('hid', 'hersteller', 'strasse', 'hausnummer', 'plz', 'ort', 'tel', 'email'));
*/
function sql_output($source, $type, $selectors) {
	$output = '';
	while($row = mysqli_fetch_object($source)) {
		switch ($type) {
			case 'table':
				$output .= '<tr>';
				$i=0;
				foreach($selectors as $key => $val) {
					if ($i==0) {
						if ($val!=str_replace('{custom}','',$val)) { 
							$val = str_replace('{custom}','',$val);
							$output .= '<th scope="row">'.$val.'</th>'; 
						} else { 
							$output .= '<th scope="row">'.$row->$val.'</th>'; 
						}
						$i++;
					} else {
						if ($val!=str_replace('{custom}','',$val)) { 
							$val = str_replace('{custom}','',$val);
							$output .= '<td>'.$val.'</td>'; 
						}
						else { 
							$output .= '<td>'.$row->$val.'</td>'; 
						}
					}
				}
				$output .= '</tr>';
				break;
			case 'select': 
				if (is_array($selectors)) {
					foreach ($selectors as $key => $val) {
						if ($val!=str_replace('{custom}','',$val)) { 
							$val = str_replace('{custom}','',$val);
							$output .= '<option>'.$val.'</option>'; 
						} else { 
							$output .= '<option>'.$row->$val.'<option>'; 
						}
					}
				} else {
					$output .= '<option>'.$row->$selectors.'</option>'; 
				}
		}
	}

	return $output;
}


// CHECKBOX ÜBERPRÜFUNG
/* Funktion zur Überprüfung von checked Radio-Buttons 
*/
function check_art($value) {
    if ( isset($_POST['art']) && ($_POST['art'] == $value) ) {
        $check = ' checked';
        return $check;
    } else {
        $check = '';
        return $check;
    }
}
function check_besonderheiten($value) {
    if ( isset($_POST['besonderheiten']) && ($_POST['besonderheiten'] == $value) ) {
        $check = ' checked';
        return $check;
    } else {
        $check = '';
        return $check;
    }
}
function check_energieausweis($value) {
    if ( isset($_POST['energieausweis']) && ($_POST['energieausweis'] == $value) ) {
        $check = ' checked';
        return $check;
    } else {
        $check = '';
        return $check;
    }
}


// JAHREINGABE ÜBERPRÜFUNG
function jahr_kontrollieren($jahr) {
	if (preg_match('(19|20)[0-9]{2}', $jahr)) {
		$bol=1;
	} else {
		$bol=0;
	}
}


// GEBURTSDATUM ÜBERPRÜFUNG
/* Alter berechnen
*/
function fc_alter($date) {
	$dateOfBirth 	= $date;
	$today 			= date("Y-m-d");
	$diff 			= date_diff(date_create($dateOfBirth), date_create($today));

	return $diff->format('%y');
}


// DATUMFORMAT ÜBERPRÜFUNG
/* 
** Als Wert bitte das Datum eingeben, dabei das Format beachten: (JAHR-MM-TT).
** Als Rückgabewert wird übergeben, ob ein VERGANGENES Datum existiert hat (true/false).
** Array fängt mit 0 an! Also: 0=Jahr, 1=Monat und 2=Tag 
*/
function datum_kontrollieren($datum) {
	$temp = explode('-', $datum);
	// Die Anzahl der Tage des Monats wird ermittelt.
	switch($temp[1]) {
		case '1':   $ml = 31;
					break;
		case '2':   //Es wird überprüft, ob das Jahr ein Schaltjahr ist.
					if ( $temp[0] % 4 == 0 ) {
						$ml = 29;
					}
					//Kein Schaltjahr.
					else {
						$ml = 28;
					}
					break;
		case '3':   $ml = 31;
					break;
		case '4':   $ml = 30;
					break;
		case '5':   $ml = 31;
					break;
		case '6':   $ml = 30;
					break;
		case '7':   $ml = 31;
					break;
		case '8':   $ml = 31;
					break;
		case '9':   $ml = 30;
					break;
		case '10':  $ml = 31;
					break;
		case '11':  $ml = 30;
					break;
		case '12':  $ml = 31;
					break;
	}

	if ( $temp[0] == date('Y') ) {
		if ( $temp[1] > 0  && $temp[1] <= date('m') ) {
			if ( $temp[2] > 0 && ( $temp[1] < date('m') || $temp[2] <= date('d') ) ) {
				$bol = 1;
			} else {
				$bol = 0;
			}
		} else {
			$bol = 0;
		}
	} elseif ( $temp[0] >= 0 && $temp[0] < date('Y') ) {
		if ( $temp[1] > 0 && $temp[1] <= 12 ) {
				if ( $temp[2] > 0 && $temp[2] <= $ml ) {
					$bol = 1;
				} else {
					$bol = 0;
				}
		} else {
			$bol = 0;
		}
	} else {
		$bol = 0;
	}
	return $bol;
}

// IMAGEUPLOAD
/*
** Größenanpassung von Motiven
*/
function imgthumb($imgfile, $location, $filenameOnly = true) {
	//Dateiname erzeugen
	$filename 		= basename($imgfile);

	//Fügt den Pfad zur Datei dem Dateinamen hinzu
	//Aus ordner/img/bild1.jpg wird dann ordner_bilder_bild1.jpg
	if (!$filenameOnly) {
		$replace 	= array('/','\\','.');
		$filename 	= str_replace($replace,'_',dirname($imgfile)).'_'.$filename;
	}

	//Schreibarbeit sparen
	$folder 		= $location;

	//Speicherordner vorhanden
	if ( !is_dir($folder) ) {
		return false;
	}

	//Wenn Datei schon vorhanden, kein Thumbnail erstellen
	if ( file_exists($folder.$filename) ) {
		return $folder.$filename;
	}

	//Ausgansdatei vorhanden? Wenn nicht, false zurückgeben
	if ( !file_exists($imgfile) ) {
		return false;
	}

	//Infos über das Bild
	$endung = strrchr($imgfile,'.');

	list($width, $height) = getimagesize($imgfile);
	$imgratio = $width / $height;

	//Ist das Bild höher als breit?
	if ($imgratio > 1) {
		$newwidth 	= 480;
		$newheight 	= 480 / $imgratio;
	} else {
		$newheight 	= 480;
		$newwidth 	= 480 * $imgratio;
	}

	//Bild erstellen
	//Achtung: imagecreatetruecolor funktioniert nur bei bestimmten GD Versionen
	//Falls ein Fehler auftritt, imagecreate nutzen
	if ( function_exists('imagecreatetruecolor') ) {
	  $thumb 		= imagecreatetruecolor($newwidth,$newheight); 
	} else {
		$thumb 		= imagecreate($newwidth,$newheight);
	}

	if ($endung == '.jpg') {
		imageJPEG($thumb,$folder.'temp.jpg');
		$thumb 		= imagecreatefromjpeg($folder.'temp.jpg');
		$source 	= imagecreatefromjpeg($imgfile);
	} elseif ($endung == '.gif') {
		imageGIF($thumb,$folder.'temp.gif');
		$thumb 		= imagecreatefromgif($folder.'temp.gif');
		$source 	= imagecreatefromgif($imgfile);
	} elseif ($endung == '.png') {
		imagePNG($thumb,$folder.'temp.png');
		$thumb 		= imagecreatefrompng($folder.'temp.png');
		$source 	= imagecreatefrompng($imgfile);
	}

	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	//Bild speichern
	if ($endung == '.png') {
		imagepng($thumb,$folder.$filename);
	} elseif($endung == '.gif') {
		imagegif($thumb,$folder.$filename);
	} else {
		imagejpeg($thumb,$folder.$filename,90);
	}

	//Speicherplatz wieder freigeben
	ImageDestroy($thumb);
	ImageDestroy($source);

	//Pfad zu dem Bild zurückgeben
	return $folder.$filename;
}

function imgresize($imgfile, $location, $filenameOnly = true) {
	//Dateiname erzeugen
	$filename = basename($imgfile);

	//Fügt den Pfad zur Datei dem Dateinamen hinzu
	//Aus ordner/img/bild1.jpg wird dann ordner_bilder_bild1.jpg
	if (!$filenameOnly) {
		$replace = array('/','\\','.');
		$filename = str_replace($replace,'_',dirname($imgfile)).'_'.$filename;
	}

	//Schreibarbeit sparen
	$folder = $location;

	//Speicherordner vorhanden
	if ( !is_dir($folder) ) {
		return false;
	}

	//Wenn Datei schon vorhanden, kein Thumbnail erstellen
	if ( file_exists($folder.$filename) ) {
		return $folder.$filename;
	}

	//Ausgansdatei vorhanden? Wenn nicht, false zurückgeben
	if ( !file_exists($imgfile) ) {
		return false;
	}

	//Infos über das Bild
	$endung = strrchr($imgfile,'.');

	list($width, $height) = getimagesize($imgfile);
	$imgratio=$width/$height;

	//Ist das Bild höher als breit?
	if ($imgratio > 1) {
		$newwidth = 1920;
		$newheight = 1920 / $imgratio;
	} else {
		$newheight = 1920;
		$newwidth = 1920 * $imgratio;
	}

	//Bild erstellen
	//Achtung: imagecreatetruecolor funktioniert nur bei bestimmten GD Versionen
	//Falls ein Fehler auftritt, imagecreate nutzen
	if ( function_exists('imagecreatetruecolor') ) {
		$thumb = imagecreatetruecolor($newwidth,$newheight); 
	} else {
		$thumb = imagecreate($newwidth,$newheight);
	}

	if ($endung == '.jpg') {
		imageJPEG($thumb,$folder.'temp.jpg');
		$thumb = imagecreatefromjpeg($folder.'temp.jpg');
		$source = imagecreatefromjpeg($imgfile);
	} elseif ($endung == '.gif') {
		imageGIF($thumb,$folder.'temp.gif');
		$thumb = imagecreatefromgif($folder.'temp.gif');
		$source = imagecreatefromgif($imgfile);
	}

	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	//Bild speichern
	if ($endung == '.png') {
		imagepng($thumb,$folder.$filename);
	} elseif($endung == '.gif') {
		imagegif($thumb,$folder.$filename);
	} else {
		imagejpeg($thumb,$folder.$filename,90);
	}

	//Speicherplatz wieder freigeben
	ImageDestroy($thumb);
	ImageDestroy($source);

	//Pfad zu dem Bild zurückgeben
	return $folder.$filename;
}

// VALIDE ZEICHENKETTE
/*
** Zeichenkette prüfen und ggf. umwandeln
*/
function validString($validString) {
	$validString = strtolower($validString);
	$replace = array(
		'/ä/' => 'ae',
		'/ü/' => 'ue',
		'/ö/' => 'oe',
		'/ß/' => 'ss',
		'/&/' => '-',
		'/\040/' => '-',
		'/---/' => '-',
		'/--/' => '-',
		'/[^a-z0-9\.\-]/' => ''
	);

	$validString = preg_replace(array_keys($replace), array_values($replace), $validString);
	// Check valid
	if ( preg_match('/^[a-z1-9\.\-]+\.[a-z1-9\.\-]+/', $validString) ) {
		return $validString;
	} else {
		return $validString;
	}
}