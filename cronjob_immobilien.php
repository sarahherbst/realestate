#!/usr/local/bin/php
<?php
	
	// Pfade richtig definieren
	chdir(dirname(__FILE__));

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$error = 0;
	$alert = '';

	$importfolder = 'import';
	$i = 0;

	//FUNKTION 1 => Verzeichnis auslesen und nach ZIPs suchen
	if ($handle = opendir($importfolder)) {
		while (false !== ($file = readdir($handle))) {
			// Zähle Dateien in Verzeichnis
			$files 	= scandir($importfolder);

			foreach($files as $file) {
				$file_extension = pathinfo($file);

				if($file_extension['extension'] == 'zip' OR $file_extension['extension'] == 'ZIP') {

					$i++;

					// Wird Datei noch übertragen?
					$filesize = filesize($source);
					clearstatcache();

					sleep(5);
					$filesize2 = filesize($source);

					if ($filesize == $filesize2) {

						$zip = new ZipArchive;

						if ($zip->open(__DIR__.'/'.$importfolder.'/'.$file) === TRUE) {
							$zip->extractTo(__DIR__.'/'.$importfolder.'/');
							$zip->close();
						} else {
							$error++;
							$alert .= 'ZIP-Datei konnte nicht entpackt werden.<br>';
						}

						if ($error == 0) {
							// ZIP löschen
							$deletezip = unlink(__DIR__ . '/'.$importfolder.'/' .$file);

							if (!$deletezip === true) {
								$error++;
								$alert .= 'ZIP-Datei konnte nicht gelöscht werden.<br>';
							}
						}
					} else {
						$error++;
						$alert .= 'ZIP-Datei wurde noch nicht vollständig übertragen. Später nochmal versuchen.<br>';
					}

					// wenn ZIP-Datei vorhanden, lese XML-Datei aus
					if($i > 0 && $error == 0) {
						$files 	= scandir($importfolder);

						foreach($files as $file) {
							$file_extension = pathinfo($file);

							if($file_extension['extension'] == 'xml' OR $file_extension['extension'] == 'XML') {
								$i++;

								// evtl. Apostrophe ersetzen
								$filename 		= __DIR__.'/'.$importfolder.'/'.$file;

								echo $filename.'<br>';

								$file_contents 	= file_get_contents($filename);
								$file_contents 	= str_replace("'", '‘', $file_contents);
								file_put_contents($filename, $file_contents);

								$xml = simplexml_load_file(__DIR__ . '/'.$importfolder.'/' .$file);

								if($xml === false) {

									$error++;
									$alert .= 'XML-Datei konnte nicht gelesen werden.<br>';

									$movexml = rename(__DIR__ . '/'.$importfolder.'/' .$file, __DIR__ . '/error/'.$file);

								} elseif($error < 1) {

									$immobilienliste = $xml -> anbieter -> immobilie;

									// Einzelne Immobilien durchgehen
									foreach ($immobilienliste as $immobilie) {

										$aktion = $immobilie -> verwaltung_techn -> aktion -> attributes();
										$aktion = $aktion[aktionart];

										if ($aktion == 'DELETE') {

											// Objektstatus auf 8 setzen
											$objektnr_extern = $immobilie -> verwaltung_techn -> objektnr_extern;

											$sql_update 	= sql_update('objekte', 'obj_status', '8', 'obj_objektnr_extern', $objektnr_extern);

											if (!$sql_update === true) {
												$error++;
												$alert .= 'Das Objekt konnte nicht deaktiviert werden.<br>';
											}

										} else {

											// Anbieter
											$anbieternr 	= $xml -> anbieter -> anbieternr;
											$firma 			= $xml -> anbieter -> firma;
											$openimmo_anid 	= $xml -> anbieter -> openimmo_anid;

											// Immobilie allgemein
											$nutzungsart 	= $immobilie -> objektkategorie -> nutzungsart -> attributes();
											if ($nutzungsart[WOHNEN] == 'true') {$nutzungsart = 'Wohnen';}
											else if ($nutzungsart[GEWERBE] == 'true') {$nutzungsart = 'Gewerbe';}
											else if ($nutzungsart[ANLAGE] == 'true') {$nutzungsart = 'Anlage';}
											else if ($nutzungsart[WAZ] == 'true') {$nutzungsart = 'WAZ';}

											$vermarktungsart = $immobilie -> objektkategorie -> vermarktungsart -> attributes();
											if ($vermarktungsart[KAUF] == 'true') {$vermarktungsart = 'Kauf';}
											else if ($vermarktungsart[MIETE_PACHT] == 'true') {$vermarktungsart = 'Miete/Pacht';}
											else if ($vermarktungsart[ERBPACHT] == 'true') {$vermarktungsart = 'Erbpacht';}
											else if ($vermarktungsart[LEASING] == 'true') {$vermarktungsart = 'Leasing';}

											$objektart = $immobilie -> objektkategorie -> objektart;
											foreach($objektart -> children() as $typ => $value) {
												if($typ == 'zimmer') {$art = 'Zimmer';}
												if($typ == 'wohnung') {$art = 'Wohnung';}
												if($typ == 'haus') {$art = 'Haus';}
												if($typ == 'grundstueck') {$art = 'Grundstück';}
												if($typ == 'buero_praxen') {$art = 'Büro/Praxis';}
												if($typ == 'einzelhandel') {$art = 'Einzelhandel';}
												if($typ == 'gastgewerbe') {$art = 'Gastgewerbe';}
												if($typ == 'hallen_lager_prod') {$art = 'Hallen/Lager/Produktion';}
												if($typ == 'land_und_forstwirtschaft') {$art = 'Land- und Forstwirtschaft';}
												if($typ == 'sonstige') {$art = 'Sonstige';}
												if($typ == 'freizeitimmobilie_gewerblich') {$art = 'Freizeitimmobilie (gewerblich)';}
												if($typ == 'zinshaus_renditeobjekt') {$art = 'Zinshaus/Renditeobjekt';}
												if($typ == 'parken') {$typ = '';}

												if(!$typ == '') {
													$unterart = $immobilie -> objektkategorie -> objektart -> $typ -> attributes();
												}

												if(!$unterart == '') {
													// Großschreibung anpassen
													$unterart = strtolower($unterart);
													$unterart = ucfirst($unterart);

													// Umlaute anpassen
													$unterart = str_replace('ae', 'ä', $unterart);
													$unterart = str_replace('oe', 'ö', $unterart);
													$unterart = str_replace('ue', 'ü', $unterart);
													$unterart = str_replace('_', ' ', $unterart);
												}

												if($immobilie -> objektkategorie -> objektart -> parken) {
													$unterart_parken = $immobilie -> objektkategorie -> objektart -> parken -> attributes();
													if(!$unterart_parken == '') {
														// Großschreibung anpassen
														$unterart_parken = strtolower($unterart_parken);
														$unterart_parken = ucfirst($unterart_parken);
													}
												}
											}

											// Adresse
											$plz						= $immobilie -> geo -> plz;
											$ort 						= $immobilie -> geo -> ort;
											$strasse 					= $immobilie -> geo -> strasse;
											$hausnummer 				= $immobilie -> geo -> hausnummer;
											$etage 						= $immobilie -> geo -> etage;

											$land 						= $immobilie -> geo -> land -> attributes();
											$land 						= $land[iso_land];
											if ($land == '') {$land = 'DEU';}

											$reg_zusatz = $immobilie -> geo -> regionaler_zusatz;

											// Kontaktperson
											$kontaktperson_nachname 		= $immobilie -> kontaktperson -> name;
											$kontaktperson_vorname 			= $immobilie -> kontaktperson -> vorname;
											$kontaktperson_anrede 			= $immobilie -> kontaktperson -> anrede;
											$kontaktperson_firma 			= $immobilie -> kontaktperson -> firma;
											$kontaktperson_strasse 			= $immobilie -> kontaktperson -> strasse;
											$kontaktperson_hausnummer 		= $immobilie -> kontaktperson -> hausnummer;
											$kontaktperson_plz 				= $immobilie -> kontaktperson -> plz;
											$kontaktperson_ort 				= $immobilie -> kontaktperson -> ort;
											$kontaktperson_url 				= $immobilie -> kontaktperson -> url;

											$kontaktperson_email_zentrale 	= $immobilie -> kontaktperson -> email_zentrale;
											$kontaktperson_email_direkt 	= $immobilie -> kontaktperson -> email_direkt;
											$kontaktperson_tel_zentrale 	= $immobilie -> kontaktperson -> tel_zentrale;
											$kontaktperson_tel_durchw 		= $immobilie -> kontaktperson -> tel_durchw;
											$kontaktperson_tel_fax 			= $immobilie -> kontaktperson -> tel_fax;

											// Preise
											$kaufpreis 						= $immobilie -> preise -> kaufpreis;
											if(!$kaufpreis == '') {
												$kaufpreis 					= number_format(floatval($kaufpreis),2,',','');
											}

											$provisionspflichtig = $immobilie -> preise -> provisionspflichtig;
											if ($provisionspflichtig == 'true') {
												$provisionspflichtig = '1';
											} else {
												$provisionspflichtig = '0';
											}

											$innen_courtage 			= $immobilie -> preise -> innen_courtage;
											$aussen_courtage 			= $immobilie -> preise -> aussen_courtage;

											$waehrung 					= $immobilie -> preise -> waehrung -> attributes();
											$waehrung 					= $waehrung[iso_waehrung];

											$x_fache 					= $immobilie -> preise -> x_fache;
											if(!$x_fache == '') {
												$x_fache 				= number_format(floatval($x_fache),1,',','');
											}

											$nettorendite_soll 			= $immobilie -> preise -> nettorendite_soll;
											$nettorendite_ist 			= $immobilie -> preise -> nettorendite_ist;

											$preis_zeiteinheit 			= $immobilie -> preise -> preis_zeiteinheit;
											if(!$preis_zeiteinheit == '') {
												// Großschreibung anpassen
												$preis_zeiteinheit = strtolower($preis_zeiteinheit);
												$preis_zeiteinheit = ucfirst($preis_zeiteinheit);
											}

											$kaltmiete 					= $immobilie -> preise -> kaltmiete;
											$nettokaltmiete 			= $immobilie -> preise -> nettokaltmiete;
											if(!$nettokaltmiete == '') {
												$nettokaltmiete 		= number_format(floatval($nettokaltmiete),2,',','');
											}

											$nebenkosten 				= $immobilie -> preise -> nebenkosten;
											if(!$nebenkosten == '') {
												$nebenkosten 			= number_format(floatval($nebenkosten),2,',','');
											}

											$warmmiete 					= $immobilie -> preise -> warmmiete;
											if(!$warmmiete == '') {
												$warmmiete 				= number_format(floatval($warmmiete),2,',','');
											}

											$hausgeld 					= $immobilie -> preise -> hausgeld;
											if(!$hausgeld == '') {
												$hausgeld 				= number_format(floatval($hausgeld),2,',','');
											}

											$stp_sonstige 				= $immobilie -> preise -> stp_sonstige[stellplatzmiete];
											if(!$stp_sonstige == '') {
												$stp_sonstige 			= number_format(floatval($stp_sonstige),2,',','');
											}

											$stellplatzmiete 			= $immobilie -> preise -> stellplatzmiete;

											$kaution 					= $immobilie -> preise -> kaution;
											if(!$kaution == '') {
												$kaution 				= number_format(floatval($kaution),2,',','');
											}

											$mietpreis_pro_qm 			= $immobilie -> preise -> mietpreis_pro_qm;
											if (!$mietpreis_pro_qm == '') {
												$mietpreis_pro_qm = 'Q';
											} else {
												$mietpreis_pro_qm = 'M';
											}

											$mieteinnahmen_ist 			= $immobilie -> preise -> mieteinnahmen_ist;
											if(!$mieteinnahmen_ist == '') {
												$mieteinnahmen_ist 		= number_format(floatval($mieteinnahmen_ist),2,',','');
											}

											$mieteinnahme_soll 			= $immobilie -> preise -> mieteinnahme_soll;
											if(!$mieteinnahme_soll == '') {
												$mieteinnahme_soll 		= number_format(floatval($mieteinnahme_soll),2,',','');
											}

											$erbpacht 					= $immobilie -> preise -> erbpacht;
											if(!$erbpacht == '') {
												$erbpacht 				= number_format(floatval($erbpacht),2,',','');
											}

											// Flächen
											$wohnflaeche 				= $immobilie -> flaechen -> wohnflaeche;
											if(!$wohnflaeche == '') {
												$wohnflaeche 			= number_format(floatval($wohnflaeche),2,',','');
											}

											$nutzflaeche 				= $immobilie -> flaechen -> nutzflaeche;
											if(!$nutzflaeche == '') {
												$nutzflaeche 			= number_format(floatval($nutzflaeche),2,',','');
											}

											$grundstuecksflaeche 		= $immobilie -> flaechen -> grundstuecksflaeche;
											if(!$grundstuecksflaeche == '') {
												$grundstuecksflaeche 	= number_format(floatval($grundstuecksflaeche),2,',','');
											}

											$vermietbare_flaeche		= $immobilie -> flaechen -> vermietbare_flaeche;
											if(!$vermietbare_flaeche == '') {
												$vermietbare_flaeche 	= number_format(floatval($vermietbare_flaeche),2,',','');
											}

											$bueroflaeche 				= $immobilie -> flaechen -> bueroflaeche;
											if(!$bueroflaeche == '') {
												$bueroflaeche 			= number_format(floatval($bueroflaeche),2,',','');
											}

											$gesamtflaeche 				= $immobilie -> flaechen -> gesamtflaeche;
											if(!$gesamtflaeche == '') {
												$gesamtflaeche 			= number_format(floatval($gesamtflaeche),2,',','');
											}

											$verkaufsflaeche 			= $immobilie -> flaechen -> verkaufsflaeche;
											if(!$verkaufsflaeche == '') {
												$verkaufsflaeche 		= number_format(floatval($verkaufsflaeche),2,',','');
											}

											$ladenflaeche 				= $immobilie -> flaechen -> ladenflaeche;
											if(!$ladenflaeche == '') {
												$ladenflaeche 			= number_format(floatval($ladenflaeche),2,',','');
											}

											$gastroflaeche 				= $immobilie -> flaechen -> gastroflaeche;
											if(!$gastroflaeche == '') {
												$gastroflaeche 			= number_format(floatval($gastroflaeche),2,',','');
											}

											$sonstflaeche 				= $immobilie -> flaechen -> sonstflaeche;
											if(!$sonstflaeche == '') {
												$sonstflaeche 			= number_format(floatval($sonstflaeche),2,',','');
											}

											$fensterfront 				= $immobilie -> flaechen -> fensterfront;
											if(!$fensterfront == '') {
												$fensterfront 			= number_format(floatval($fensterfront),2,',','');
											}

											$grz 						= $immobilie -> flaechen -> grz;
											if(!$grz == '') {
												$grz 					= number_format(floatval($grz),2,',','');
											}

											$gfz 						= $immobilie -> flaechen -> gfz;
											if(!$gfz == '') {
												$gfz 					= number_format(floatval($gfz),2,',','');
											}

											$teilbar_ab 				= $immobilie -> flaechen -> teilbar_ab;
											if(!$teilbar_ab == '') {
												$teilbar_ab 			= number_format(floatval($teilbar_ab),2,',','');
											}

											$anzahl_stellplaetze 		= $immobilie -> flaechen -> anzahl_stellplaetze;
											if(!$anzahl_stellplaetze == '') {
												$anzahl_stellplaetze 	= number_format(floatval($anzahl_stellplaetze),0,',','');
											}

											$plaetze_gastraum 			= $immobilie -> flaechen -> plaetze_gastraum;
											if(!$plaetze_gastraum == '') {
												$plaetze_gastraum 		= number_format(floatval($plaetze_gastraum),0,',','');
											}

											$anzahl_betten 				= $immobilie -> flaechen -> anzahl_betten;
											if(!$anzahl_betten == '') {
												$anzahl_betten 			= number_format(floatval($anzahl_betten),2,',','');
											}

											$anzahl_balkon_terrassen 	= $immobilie -> flaechen -> anzahl_balkon_terrassen;
											if (!$anzahl_balkon_terrassen == '1' && !$anzahl_balkon_terrassen == '0') {
												$anzahl_balkon_terrassen = '9';
											}

											$anzahl_zimmer 				= $immobilie -> flaechen -> anzahl_zimmer;

											$anzahl_schlafzimmer 		= $immobilie -> flaechen -> anzahl_schlafzimmer;

											$anzahl_badezimmer 			= $immobilie -> flaechen -> anzahl_badezimmer;

											$einliegerwohnung 			= $immobilie -> flaechen -> einliegerwohnung;
											if ($einliegerwohnung == 'true') {$einliegerwohnung = '1';}
											else if ($einliegerwohnung == 'false') {$einliegerwohnung = '0';}
											else if ($einliegerwohnung == '') {$einliegerwohnung = '9';}


											$heizung 					= $immobilie -> ausstattung -> heizungsart;
											if ($heizung[ETAGE] == '1') {$heizung = 'Etagenheizung';}
											else if ($heizung[OFEN] == '1') {$heizung = 'Ofen';}
											else if ($heizung[ZENTRAL] == '1') {$heizung = 'Zentralheizung';}
											else {$heizung = '';}


											$boden 						= $immobilie -> ausstattung -> boden;
											if(!$boden == '') {
												// Großschreibung anpassen
												$boden = strtolower($boden);
												$boden = ucfirst($boden);
											}

											$gartennutzung 				= $immobilie -> ausstattung -> gartennutzung;
											if ($gartennutzung == 'true') {$gartennutzung = '1';}
											else if ($gartennutzung == 'false') {$gartennutzung = '0';}
											else {$gartennutzung = '9';}

											$kueche 					= $immobilie -> ausstattung -> kueche;
											if ($kueche[EBK] == '1' || $kueche[OFFEN] == '1') {$kueche = '1';}
											else {$kueche = '9';}

											$moebiliert 				= $immobilie -> ausstattung -> moebliert;
											if ($moebiliert[moeb] == 'TEIL') {$moebiliert = 'teilweise möbiliert';}
											else if ($moebiliert[moeb] == 'VOLL') {$moebiliert = 'vollständig möbiliert';}
											else {$moebiliert = '';}

											$stellplatzart 				= $immobilie -> ausstattung -> stellplatzart;
											if(!$stellplatzart == '') {
												// Großschreibung anpassen
												$stellplatzart = strtolower($stellplatzart);
												$stellplatzart = ucfirst($stellplatzart);
											}

											$kantine_cafeteria 			= $immobilie -> ausstattung -> kantine_cafeteria;
											if ($kantine_cafeteria == 'true') {$kantine_cafeteria = '1';}
											else if ($kantine_cafeteria == 'false') {$kantine_cafeteria = '0';}
											else {$kantine_cafeteria = '9';}

											$dv_verkablung 			= $immobilie -> ausstattung -> dv_verkablung;
											if ($dv_verkablung == 'true') {$dv_verkablung = '1';}
											else if ($dv_verkablung == 'false') {$dv_verkablung = '0';}
											else {$dv_verkablung = '9';}

											$rampe 						= $immobilie -> ausstattung -> rampe;
											if ($rampe == 'true') {$rampe = '1';}
											else if ($rampe == 'false') {$rampe = '0';}
											else {$rampe = '9';}

											$gastterrasse 				= $immobilie -> ausstattung -> gastterrasse;
											if ($gastterrasse == 'true') {$gastterrasse = '1';}
											else if ($gastterrasse == 'false') {$gastterrasse = '0';}
											else {$gastterrasse = '9';}

											$stromanschlusswert 		= $immobilie -> ausstattung -> stromanschlusswert;
											if(!$stromanschlusswert == '') {
												$stromanschlusswert 	= number_format(floatval($stromanschlusswert),0,',','');
											}

											// Zustand Angaben
											$baujahr 					= $immobilie -> zustand_angaben -> baujahr;

											if(isset($immobilie -> user_defined_anyfield -> zustand_klartext)) {
												$zustand = $immobilie -> user_defined_anyfield -> zustand_klartext;
											}

											if(isset($immobilie -> zustand_angaben -> alter)) {

												$alter 		= $immobilie -> zustand_angaben -> alter -> attributes();
												$alter 		= $alter[alter_attr];

												if(!$alter == '') {
													// Großschreibung anpassen
													$alter = strtolower($alter);
													$alter = ucfirst($alter);
												}
											}

											$bebaubar_nach 				= $immobilie -> zustand_angaben -> bebaubar_nach;
											if ($bebaubar_nach[zustand_art] == 'B_PLAN') {$bebaubar_nach = 'Plan';}
											else if ($bebaubar_nach[zustand_art] == '34_NACHBARSCHAFT') {$bebaubar_nach = 'Nachbarschaft';}
											else if ($bebaubar_nach[zustand_art] == '35_AUSSENGEBIET') {$bebaubar_nach = 'Außengebiet';}
											else {$bebaubar_nach = '';}

											$erschliessung 				= $immobilie -> zustand_angaben -> erschliessung;
											if(!$erschliessung == '') {
												// Großschreibung anpassen
												$erschliessung = strtolower($erschliessung);
												$erschliessung = ucfirst($erschliessung);
											}

											// Ernergiepass
											$epart 						= $immobilie -> zustand_angaben -> energiepass -> epart;
											if (!$epart == '') {
												if ($epart == 'BEDARF') {$epart = 'Bedarfsausweis';}
												else if ($epart == 'VERBRAUCH') {$epart = 'Verbrauchsausweis';}
											}

											$endenergiebedarf 			= $immobilie -> zustand_angaben -> energiepass -> endenergiebedarf;
											if (!$endenergiebedarf == '') {
												$endenergiebedarf 		= number_format(floatval($endenergiebedarf),2,',','');
											}

											$heizwert 					= $immobilie -> zustand_angaben -> energiepass -> heizwert;

											$gueltig_bis 				= $immobilie -> zustand_angaben -> energiepass -> gueltig_bis;
											$energieverbrauchkennwert 	= $immobilie -> zustand_angaben -> energiepass -> energieverbrauchkennwert;
											if (!$energieverbrauchkennwert == '') {
												$energieverbrauchkennwert = number_format(floatval($energieverbrauchkennwert),2,',','');
											}

											$mitwarmwasser 				= $immobilie -> zustand_angaben -> energiepass -> mitwarmwasser;
											$primaerenergietraeger 		= $immobilie -> zustand_angaben -> energiepass -> primaerenergietraeger;
											$wertklasse 				= $immobilie -> zustand_angaben -> energiepass -> wertklasse;
											$energie_baujahr			= $immobilie -> zustand_angaben -> energiepass -> baujahr;
											$ausstelldatum 				= $immobilie -> zustand_angaben -> energiepass -> ausstelldatum;
											$gebaeudeart 				= $immobilie -> zustand_angaben -> energiepass -> gebaeudeart;

											// Freitexte
											$titel 						= $immobilie -> freitexte -> objekttitel;
											$dreizeiler 				= $immobilie -> freitexte -> dreizeiler;

											$lage 						= $immobilie -> freitexte -> lage;
											$lage 						= nl2br($lage);

											$ausstattung 				= $immobilie -> freitexte -> ausstatt_beschr;
											$ausstattung 				= nl2br($ausstattung);

											$beschreibung 				= $immobilie -> freitexte -> objektbeschreibung;
											$beschreibung 				= nl2br($beschreibung);

											$sonstige_angaben 			= $immobilie -> freitexte -> sonstige_angaben;
											$sonstige_angaben 			= nl2br($sonstige_angaben);

											// Verwaltungstechnische Daten
											$adresse_freigeben 			= $immobilie -> verwaltung_objekt -> adresse_freigeben;
											if($adresse_freigeben == 'true') {$adresse_freigeben = '1';} else {$adresse_freigeben = '0';}

											$haustiere 					= $immobilie -> verwaltung_objekt -> haustiere;
											if ($haustiere == 'true') {$haustiere = '1';}
											else if ($haustiere == 'false') {$haustiere = '0';}
											else {$haustiere = '9';}

											$wbs_sozialwohnung 			= $immobilie -> verwaltung_objekt -> wbs_sozialwohnung;
											if ($wbs_sozialwohnung == 'true') {$wbs_sozialwohnung = '1';}
											else if ($wbs_sozialwohnung == 'false') {$wbs_sozialwohnung = '0';}
											else {$wbs_sozialwohnung = '9';}

											$adr_boersen_frei 			= $immobilie -> verwaltung_objekt -> user_defined_anyfield -> immonet_daten -> boersen_rechte -> adr_boersen_frei;
											if($adr_boersen_frei == 'true') {$adr_boersen_frei = '1';} else {$adr_boersen_frei = '0';}

											$verfuegbar_ab 				= $immobilie -> verwaltung_objekt -> verfuegbar_ab;
											$vermietet 					= $immobilie -> verwaltung_objekt -> vermietet;
											$objektnr_extern 			= $immobilie -> verwaltung_techn -> objektnr_extern;
											$objektnr_intern 			= $immobilie -> verwaltung_techn -> objektnr_intern;
											$openimmo_obid 				= $immobilie -> verwaltung_techn -> openimmo_obid;
											$stand_vom 					= $immobilie -> verwaltung_techn -> stand_vom;

											$nichtraucher 				= $immobilie -> verwaltung_objekt -> nichtraucher;
											if ($nichtraucher == 'true') {$nichtraucher = '1';}
											else if ($nichtraucher == 'false') {$nichtraucher = '0';}
											else {$nichtraucher = '9';}

											$geschlecht 				= $immobilie -> verwaltung_objekt -> geschlecht;
											if ($nichtraucher == 'NUR_MANN') {$nichtraucher = 'nur männlich';}
											else if ($nichtraucher == 'NUR_FRAU') {$nichtraucher = 'nur weiblich';}
											else {$nichtraucher = '9';}

											$max_personen 				= $immobilie -> verwaltung_objekt -> max_personen;
											if(!$max_personen == '') {
												$max_personen 			= number_format(floatval($max_personen),0,',','');
											}

											$laufzeit 					= $immobilie -> verwaltung_objekt -> laufzeit;
											if(!$laufzeit == '') {
												$laufzeit 				= number_format(floatval($laufzeit),0,',','');
											}


											// Daten der Kontaktperson updaten / einpflegen
											$kontakt_sql 	= sql_select_where('obj_kt_id', 'objekte_kontakt', array('obj_kt_vorname', 'obj_kt_nachname'), array($kontaktperson_vorname, $kontaktperson_nachname), '', '');
											$kontakt_count	= mysqli_num_rows($kontakt_sql);

											if($kontakt_count >= 1) {
												$kontakt_row 	= mysqli_fetch_assoc($kontakt_sql);
												$sql_update = sql_update('objekte_kontakt', array('obj_kt_anrede', 'obj_kt_firma', 'obj_kt_strasse', 'obj_kt_hausnummer', 'obj_kt_plz', 'obj_kt_ort', 'obj_kt_url', 'obj_kt_email_zentrale', 'obj_kt_email_direkt', 'obj_kt_tel_zentrale', 'obj_kt_tel_durchw', 'obj_kt_tel_fax'), array($kontaktperson_anrede, $kontaktperson_firma, $kontaktperson_strasse, $kontaktperson_hausnummer, $kontaktperson_plz, $kontaktperson_ort, $kontaktperson_url, $kontaktperson_email_zentrale, $kontaktperson_email_direkt, $kontaktperson_tel_zentrale, $kontaktperson_tel_durchw, $kontaktperson_tel_fax), 'obj_kt_id', $kontakt_row['obj_kt_id']);

												if(!$sql_update === true) {
													$error++;
													$alert .= 'Daten der Kontaktperson konnten nicht aktualisiert werden.<br>';
												}
											} else {
												$sql_insert = sql_insert('objekte_kontakt', array('obj_kt_vorname', 'obj_kt_nachname', 'obj_kt_anrede', 'obj_kt_firma', 'obj_kt_strasse', 'obj_kt_hausnummer', 'obj_kt_plz', 'obj_kt_ort', 'obj_kt_url', 'obj_kt_email_zentrale', 'obj_kt_email_direkt', 'obj_kt_tel_zentrale', 'obj_kt_tel_durchw', 'obj_kt_tel_fax'), array($kontaktperson_vorname, $kontaktperson_nachname, $kontaktperson_anrede, $kontaktperson_firma, $kontaktperson_strasse, $kontaktperson_hausnummer, $kontaktperson_plz, $kontaktperson_ort, $kontaktperson_url, $kontaktperson_email_zentrale, $kontaktperson_email_direkt, $kontaktperson_tel_zentrale, $kontaktperson_tel_durchw, $kontaktperson_tel_fax));

												if(!$sql_insert === true) {
													$error++;
													$alert .= 'Daten der Kontaktperson konnten nicht eingepflegt werden.<br>';
												}
											}

											$kontakt_sql 	= sql_select_where('obj_kt_id', 'objekte_kontakt', array('obj_kt_vorname', 'obj_kt_nachname'), array($kontaktperson_vorname, $kontaktperson_nachname), '', '');
											$kontakt_count	= mysqli_num_rows($kontakt_sql);
											$kontakt_row 	= mysqli_fetch_assoc($kontakt_sql);
											$kontakt_id 	= $kontakt_row['obj_kt_id'];


											// Objektdaten einpflegen
											if ($error == 0) {
												if ($aktion == 'CHANGE') {
													$sql_change_select = sql_select_where('obj_objektnr_extern', 'objekte', 'obj_objektnr_extern', $objektnr_extern, '', '');
													if ($sql_change_select == true && mysqli_num_rows($sql_change_select) >= 1) {
														$sql_update = sql_update('objekte', array('obj_anbieternr', 'obj_firma', 'obj_openimmo_anid', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_art', 'obj_unterart', 'obj_unterart_parken', 'obj_plz', 'obj_ort', 'obj_strasse', 'obj_hausnummer', 'obj_etage', 'obj_land', 'obj_reg_zusatz', 'obj_kontaktperson', 'obj_kaufpreis', 'obj_provisionspflichtig', 'obj_innen_courtage', 'obj_aussen_courtage', 'obj_waehrung', 'obj_x_fache', 'obj_nettorendite_soll', 'obj_nettorendite_ist', 'obj_preis_zeiteinheit', 'obj_kaltmiete', 'obj_nettokaltmiete', 'obj_nebenkosten', 'obj_warmmiete', 'obj_hausgeld', 'obj_stp_sonstige', 'obj_stellplatzmiete', 'obj_kaution', 'obj_mietpreis_pro_qm', 'obj_mieteinnahmen_ist', 'obj_mieteinnahme_soll', 'obj_erbpacht', 'obj_wohnflaeche', 'obj_grundstuecksflaeche', 'obj_vermietbare_flaeche', 'obj_nutzflaeche', 'obj_bueroflaeche', 'obj_gesamtflaeche', 'obj_verkaufsflaeche', 'obj_ladenflaeche', 'obj_gastroflaeche', 'obj_sonstflaeche', 'obj_einliegerwohnung', 'obj_plaetze_gastraum', 'obj_fensterfront', 'obj_teilbar_ab', 'obj_grz', 'obj_gfz', 'obj_anzahl_stellplaetze', 'obj_anzahl_betten', 'obj_anzahl_balkon_terrassen', 'obj_anzahl_zimmer', 'obj_anzahl_schlafzimmer', 'obj_anzahl_badezimmer', 'obj_befeuerung', 'obj_heizung', 'obj_fahrstuhl', 'obj_gartennutzung', 'obj_kueche', 'obj_moebiliert', 'obj_boden', 'obj_kantine_cafeteria', 'obj_dv_verkablung', 'obj_rampe', 'obj_gastterrasse', 'obj_stromanschlusswert', 'obj_stellplatzart', 'obj_baujahr', 'obj_zustand', 'obj_alter', 'obj_epart', 'obj_endenergiebedarf', 'obj_gueltig_bis', 'obj_energieverbrauchkennwert', 'obj_primaerenergietraeger', 'obj_heizwert', 'obj_mitwarmwasser', 'obj_wertklasse', 'obj_energie_baujahr', 'obj_ausstelldatum', 'obj_gebaeudeart', 'obj_bebaubar_nach', 'obj_erschliessung', 'obj_titel', 'obj_dreizeiler', 'obj_lage', 'obj_ausstattung', 'obj_beschreibung', 'obj_sonstige_angaben', 'obj_adresse_freigeben', 'obj_haustiere', 'obj_wbs_sozialwohnung', 'obj_adr_boersen_frei', 'obj_verfuegbar_ab', 'obj_objektnr_extern', 'obj_objektnr_intern', 'obj_openimmo_obid', 'obj_stand_vom', 'obj_vermietet', 'obj_nichtraucher', 'obj_geschlecht', 'obj_max_personen', 'obj_laufzeit', 'obj_status'), array($anbieternr, $firma, $openimmo_anid, $nutzungsart, $vermarktungsart, $art, $unterart, $unterart_parken, $plz, $ort, $strasse, $hausnummer, $etage, $land, $reg_zusatz, $kontakt_id, $kaufpreis, $provisionspflichtig, $innen_courtage, $aussen_courtage, $waehrung, $x_fache, $nettorendite_soll, $nettorendite_ist, $preis_zeiteinheit, $kaltmiete, $nettokaltmiete, $nebenkosten, $warmmiete, $hausgeld, $stp_sonstige, $stellplatzmiete, $kaution, $mietpreis_pro_qm, $mieteinnahmen_ist, $mieteinnahme_soll, $erbpacht, $wohnflaeche, $grundstuecksflaeche, $vermietbare_flaeche, $nutzflaeche, $bueroflaeche, $gesamtflaeche, $verkaufsflaeche, $ladenflaeche, $gastroflaeche, $sonstflaeche, $einliegerwohnung, $plaetze_gastraum, $fensterfront, $teilbar_ab, $grz, $gfz, $anzahl_stellplaetze, $anzahl_betten, $anzahl_balkon_terrassen, $anzahl_zimmer, $anzahl_schlafzimmer, $anzahl_badezimmer, $befeuerung, $heizung, $fahrstuhl, $gartennutzung, $kueche, $moebiliert, $boden, $kantine_cafeteria, $dv_verkablung, $rampe, $gastterrasse, $stromanschlusswert, $stellplatzart, $baujahr, $zustand, $alter, $epart, $endenergiebedarf, $gueltig_bis, $energieverbrauchkennwert, $primaerenergietraeger, $heizwert, $mitwarmwasser, $wertklasse, $energie_baujahr, $ausstelldatum, $gebaeudeart, $bebaubar_nach, $erschliessung, $titel, $dreizeiler, $lage, $ausstattung, $beschreibung, $sonstige_angaben, $adresse_freigeben, $haustiere, $wbs_sozialwohnung, $adr_boersen_frei, $verfuegbar_ab, $objektnr_extern, $objektnr_intern, $openimmo_obid, $stand_vom, $vermietet, $nichtraucher, $geschlecht, $max_personen, $laufzeit, '1'), 'obj_objektnr_extern', $objektnr_extern);

														if(!$sql_update === true) {
																$error++;
																$alert .= 'Eintrag der Objektdaten in die Datenbank konnte nicht geupdatet werden.';
														}
													} else {
														$sql_insert = sql_insert('objekte', array('obj_anbieternr', 'obj_firma', 'obj_openimmo_anid', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_art', 'obj_unterart', 'obj_unterart_parken', 'obj_plz', 'obj_ort', 'obj_strasse', 'obj_hausnummer', 'obj_etage', 'obj_land', 'obj_reg_zusatz', 'obj_kontaktperson', 'obj_kaufpreis', 'obj_provisionspflichtig', 'obj_innen_courtage', 'obj_aussen_courtage', 'obj_waehrung', 'obj_x_fache', 'obj_nettorendite_soll', 'obj_nettorendite_ist', 'obj_preis_zeiteinheit', 'obj_kaltmiete', 'obj_nettokaltmiete', 'obj_nebenkosten', 'obj_warmmiete', 'obj_hausgeld', 'obj_stp_sonstige', 'obj_stellplatzmiete', 'obj_kaution', 'obj_mietpreis_pro_qm', 'obj_mieteinnahmen_ist', 'obj_mieteinnahme_soll', 'obj_erbpacht', 'obj_wohnflaeche', 'obj_grundstuecksflaeche', 'obj_vermietbare_flaeche', 'obj_nutzflaeche', 'obj_bueroflaeche', 'obj_gesamtflaeche', 'obj_verkaufsflaeche', 'obj_ladenflaeche', 'obj_gastroflaeche', 'obj_sonstflaeche', 'obj_einliegerwohnung', 'obj_plaetze_gastraum', 'obj_fensterfront', 'obj_teilbar_ab', 'obj_grz', 'obj_gfz', 'obj_anzahl_stellplaetze', 'obj_anzahl_betten', 'obj_anzahl_balkon_terrassen', 'obj_anzahl_zimmer', 'obj_anzahl_schlafzimmer', 'obj_anzahl_badezimmer', 'obj_befeuerung', 'obj_heizung', 'obj_fahrstuhl', 'obj_gartennutzung', 'obj_kueche', 'obj_moebiliert', 'obj_boden', 'obj_kantine_cafeteria', 'obj_dv_verkablung', 'obj_rampe', 'obj_gastterrasse', 'obj_stromanschlusswert', 'obj_stellplatzart', 'obj_baujahr', 'obj_zustand', 'obj_alter', 'obj_epart', 'obj_endenergiebedarf', 'obj_gueltig_bis', 'obj_energieverbrauchkennwert', 'obj_primaerenergietraeger', 'obj_heizwert', 'obj_mitwarmwasser', 'obj_wertklasse', 'obj_energie_baujahr', 'obj_ausstelldatum', 'obj_gebaeudeart', 'obj_bebaubar_nach', 'obj_erschliessung', 'obj_titel', 'obj_dreizeiler', 'obj_lage', 'obj_ausstattung', 'obj_beschreibung', 'obj_sonstige_angaben', 'obj_adresse_freigeben', 'obj_haustiere', 'obj_wbs_sozialwohnung', 'obj_adr_boersen_frei', 'obj_verfuegbar_ab', 'obj_objektnr_extern', 'obj_objektnr_intern', 'obj_openimmo_obid', 'obj_stand_vom', 'obj_vermietet', 'obj_nichtraucher', 'obj_geschlecht', 'obj_max_personen', 'obj_laufzeit', 'obj_status'), array($anbieternr, $firma, $openimmo_anid, $nutzungsart, $vermarktungsart, $art, $unterart, $unterart_parken, $plz, $ort, $strasse, $hausnummer, $etage, $land, $reg_zusatz, $kontakt_id, $kaufpreis, $provisionspflichtig, $innen_courtage, $aussen_courtage, $waehrung, $x_fache, $nettorendite_soll, $nettorendite_ist, $preis_zeiteinheit, $kaltmiete, $nettokaltmiete, $nebenkosten, $warmmiete, $hausgeld, $stp_sonstige, $stellplatzmiete, $kaution, $mietpreis_pro_qm, $mieteinnahmen_ist, $mieteinnahme_soll, $erbpacht, $wohnflaeche, $grundstuecksflaeche, $vermietbare_flaeche, $nutzflaeche, $bueroflaeche, $gesamtflaeche, $verkaufsflaeche, $ladenflaeche, $gastroflaeche, $sonstflaeche, $einliegerwohnung, $plaetze_gastraum, $fensterfront, $teilbar_ab, $grz, $gfz, $anzahl_stellplaetze, $anzahl_betten, $anzahl_balkon_terrassen, $anzahl_zimmer, $anzahl_schlafzimmer, $anzahl_badezimmer, $befeuerung, $heizung, $fahrstuhl, $gartennutzung, $kueche, $moebiliert, $boden, $kantine_cafeteria, $dv_verkablung, $rampe, $gastterrasse, $stromanschlusswert, $stellplatzart, $baujahr, $zustand, $alter, $epart, $endenergiebedarf, $gueltig_bis, $energieverbrauchkennwert, $primaerenergietraeger, $heizwert, $mitwarmwasser, $wertklasse, $energie_baujahr, $ausstelldatum, $gebaeudeart, $bebaubar_nach, $erschliessung, $titel, $dreizeiler, $lage, $ausstattung, $beschreibung, $sonstige_angaben, $adresse_freigeben, $haustiere, $wbs_sozialwohnung, $adr_boersen_frei, $verfuegbar_ab, $objektnr_extern, $objektnr_intern, $openimmo_obid, $stand_vom, $vermietet, $nichtraucher, $geschlecht, $max_personen, $laufzeit, '1'));

														if(!$sql_insert === true) {
															$error++;
															$alert .= 'Eintrag der Objektdaten in die Datenbank konnte nicht vorgenommen werden.';
														}
													}
												} else if ($aktion !== 'DELETE' || $aktion !== 'CHANGE') {
													$sql_insert = sql_insert('objekte', array('obj_anbieternr', 'obj_firma', 'obj_openimmo_anid', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_art', 'obj_unterart', 'obj_unterart_parken', 'obj_plz', 'obj_ort', 'obj_strasse', 'obj_hausnummer', 'obj_etage', 'obj_land', 'obj_reg_zusatz', 'obj_kontaktperson', 'obj_kaufpreis', 'obj_provisionspflichtig', 'obj_innen_courtage', 'obj_aussen_courtage', 'obj_waehrung', 'obj_x_fache', 'obj_nettorendite_soll', 'obj_nettorendite_ist', 'obj_preis_zeiteinheit', 'obj_kaltmiete', 'obj_nettokaltmiete', 'obj_nebenkosten', 'obj_warmmiete', 'obj_hausgeld', 'obj_stp_sonstige', 'obj_stellplatzmiete', 'obj_kaution', 'obj_mietpreis_pro_qm', 'obj_mieteinnahmen_ist', 'obj_mieteinnahme_soll', 'obj_erbpacht', 'obj_wohnflaeche', 'obj_grundstuecksflaeche', 'obj_vermietbare_flaeche', 'obj_nutzflaeche', 'obj_bueroflaeche', 'obj_gesamtflaeche', 'obj_verkaufsflaeche', 'obj_ladenflaeche', 'obj_gastroflaeche', 'obj_sonstflaeche', 'obj_einliegerwohnung', 'obj_plaetze_gastraum', 'obj_fensterfront', 'obj_teilbar_ab', 'obj_grz', 'obj_gfz', 'obj_anzahl_stellplaetze', 'obj_anzahl_betten', 'obj_anzahl_balkon_terrassen', 'obj_anzahl_zimmer', 'obj_anzahl_schlafzimmer', 'obj_anzahl_badezimmer', 'obj_befeuerung', 'obj_heizung', 'obj_fahrstuhl', 'obj_gartennutzung', 'obj_kueche', 'obj_moebiliert', 'obj_boden', 'obj_kantine_cafeteria', 'obj_dv_verkablung', 'obj_rampe', 'obj_gastterrasse', 'obj_stromanschlusswert', 'obj_stellplatzart', 'obj_baujahr', 'obj_zustand', 'obj_alter', 'obj_epart', 'obj_endenergiebedarf', 'obj_gueltig_bis', 'obj_energieverbrauchkennwert', 'obj_primaerenergietraeger', 'obj_heizwert', 'obj_mitwarmwasser', 'obj_wertklasse', 'obj_energie_baujahr', 'obj_ausstelldatum', 'obj_gebaeudeart', 'obj_bebaubar_nach', 'obj_erschliessung', 'obj_titel', 'obj_dreizeiler', 'obj_lage', 'obj_ausstattung', 'obj_beschreibung', 'obj_sonstige_angaben', 'obj_adresse_freigeben', 'obj_haustiere', 'obj_wbs_sozialwohnung', 'obj_adr_boersen_frei', 'obj_verfuegbar_ab', 'obj_objektnr_extern', 'obj_objektnr_intern', 'obj_openimmo_obid', 'obj_stand_vom', 'obj_vermietet', 'obj_nichtraucher', 'obj_geschlecht', 'obj_max_personen', 'obj_laufzeit', 'obj_status'), array($anbieternr, $firma, $openimmo_anid, $nutzungsart, $vermarktungsart, $art, $unterart, $unterart_parken, $plz, $ort, $strasse, $hausnummer, $etage, $land, $reg_zusatz, $kontakt_id, $kaufpreis, $provisionspflichtig, $innen_courtage, $aussen_courtage, $waehrung, $x_fache, $nettorendite_soll, $nettorendite_ist, $preis_zeiteinheit, $kaltmiete, $nettokaltmiete, $nebenkosten, $warmmiete, $hausgeld, $stp_sonstige, $stellplatzmiete, $kaution, $mietpreis_pro_qm, $mieteinnahmen_ist, $mieteinnahme_soll, $erbpacht, $wohnflaeche, $grundstuecksflaeche, $vermietbare_flaeche, $nutzflaeche, $bueroflaeche, $gesamtflaeche, $verkaufsflaeche, $ladenflaeche, $gastroflaeche, $sonstflaeche, $einliegerwohnung, $plaetze_gastraum, $fensterfront, $teilbar_ab, $grz, $gfz, $anzahl_stellplaetze, $anzahl_betten, $anzahl_balkon_terrassen, $anzahl_zimmer, $anzahl_schlafzimmer, $anzahl_badezimmer, $befeuerung, $heizung, $fahrstuhl, $gartennutzung, $kueche, $moebiliert, $boden, $kantine_cafeteria, $dv_verkablung, $rampe, $gastterrasse, $stromanschlusswert, $stellplatzart, $baujahr, $zustand, $alter, $epart, $endenergiebedarf, $gueltig_bis, $energieverbrauchkennwert, $primaerenergietraeger, $heizwert, $mitwarmwasser, $wertklasse, $energie_baujahr, $ausstelldatum, $gebaeudeart, $bebaubar_nach, $erschliessung, $titel, $dreizeiler, $lage, $ausstattung, $beschreibung, $sonstige_angaben, $adresse_freigeben, $haustiere, $wbs_sozialwohnung, $adr_boersen_frei, $verfuegbar_ab, $objektnr_extern, $objektnr_intern, $openimmo_obid, $stand_vom, $vermietet, $nichtraucher, $geschlecht, $max_personen, $laufzeit, '1'));

													if(!$sql_insert === true) {
														$error++;
														$alert .= 'Eintrag der Objektdaten in die Datenbank konnte nicht vorgenommen werden.';
													}
												}
											}

											if($error == 0) {
												$objekt_sql 	= sql_select_where('obj_id', 'objekte', array('obj_art', 'obj_strasse', 'obj_hausnummer', 'obj_plz'), array($art, $strasse, $hausnummer, $plz), '', '');
												$objekt_row 	= mysqli_fetch_assoc($objekt_sql);
												$objekt_id 		= $objekt_row['obj_id'];

												// Bilddaten in images eintragen
												foreach ($immobilie -> anhaenge -> anhang as $anhang) {
													$img_gruppe 		= $anhang[gruppe];
													$img_anhangtitel 	= $anhang -> anhangtitel;
													$img_pfad 			= $anhang -> daten -> pfad;

													$sql_insert = sql_insert('images', array('img_institut', 'img_schluessel', 'img_item_id', 'img_bild', 'img_titel', 'img_beschreibung'), array('1', 'objekt', $objekt_id, 'img/objekte/'.$img_pfad, $img_gruppe, $img_anhangtitel));

													if (!$sql_insert === true) {
														$error++;
														$alert .= 'Bilddaten für '.$img_pfad.' konnten nicht in die Datenbank eingetragen werden.<br>';
													}
												}
											}

											if($error == 0) {
												$files 	= scandir($importfolder.'/');

												foreach($files as $file) {

													$file_extension = pathinfo($file);
													$allowed_extensions = array('jpg', 'jpeg', 'JPG', 'JPEG', 'png', 'PNG', 'gif', 'GIF');

													if (in_array($file_extension['extension'], $allowed_extensions)) {
														rename(__DIR__ . '/'.$importfolder.'/' .$file, __DIR__ . '/img/objekte/'.$file);
													}
												}
											}
										}
									}

									if($error == 0) {
										// XML löschen
										$deletexml = unlink($filename);

										if ($deletexml !== true) {
											$error++;
											$alert .= 'XML-Datei konnte nicht gelöscht werden.<br>';
										}
									}
								}
							}

							if ($i == 0) {
								$error++;
								$alert .= 'Es ist keine XML-Datei vorhanden.<br>';
							}
						}
					}
				}
			}
		}

		closedir($handle);
	}

	if($i >= 1) {

		require('phpmailer/PHPMailerAutoload.php');

		// Template abrufen
		$message 			= 'Es gibt eine ZIP-Datei.<br><br>Fehler: '.$error.'<br><br>Alert:'.$alert;

		//Setup PHPMailer
		$mail 				= new PHPMailer;
		$mail->setLanguage('de', 'phpmailer/language/');
		$mail->CharSet 		='UTF-8';
		//$mail->SMTPDebug 	= 2; 					// Enable verbose debug output
		$mail->isSMTP();						// Set mailer to use SMTP
		$mail->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
		$mail->SMTPOptions 	= array(
			'ssl' => array(
				'verify_peer' 		=> false,
				'verify_peer_name' 	=> false,
				'allow_self_signed' => true
			)
		);
		$mail->SMTPAuth 	= true; 			// Enable SMTP authentication
		$mail->Username 	= $smtp_user; 		// SMTP username
		$mail->Password 	= $smtp_passwort; 	// SMTP password
		$mail->SMTPSecure 	= 'ssl'; 			// Enable TLS encryption, `ssl` also accepted
		$mail->Port 		= $smtp_port; 		// TCP port to connect to
		//$mail->isHTML(true);					// Set email format to html

		//Absender
		$mail->SetFrom('Immobilien Center Ettlingen');
		$mail->Sender 		= ('info@immobiliencenter-ettlingen.de');
		$mail->addReplyTo('Sarah Herbst', 'herbst@ffemedia.de');

		//Empfänger
		$mail->addAddress('herbst@ffemedia.de', 'Sarah Herbst');

		//Betreff
		$mail->Subject 		= 'Cronjob';

		$mail->MsgHTML($message);
		$mail->AltBody 		= 'Um diese Nachricht zu sehen, nutzen Sie bitte eine html-kompatible E-Mail-Anwendung!';

		//E-Mail versenden
		$mail->Send();

	}

?>