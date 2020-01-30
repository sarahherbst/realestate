<?php

	// PDF Angaben
	$institut_logo 		= '<img src="/img/logo-positiv.jpg" width="auto" height="50" style="width: auto; height: 50px;" titel="Institutslogo" alt="Institutslogo">';

	$titel 				= $sitetitel;
	$subtitel 			= 'Immobilienbewertung';

	// Dokument-Name
	$pdf_name = $im_nachname.'-'.$im_vorname.'_'.$subtitel.'.pdf';

	// Erzeugung eures PDF Dokuments
	// TCPDF Library laden
	require_once('tcpdf/tcpdf.php');

	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {
		//Page header
		public function Header() {
			$pdf_header = '';

			$this->SetTopMargin(5);
			$this->SetRightMargin(0);
			$this->SetLeftMargin(0);
			$this->SetFont('dejavusans', '', 10);
			$this->writeHTML($pdf_header, true, false, true, false, '');
		}

		// Page footer
		public function Footer() {
			global $microsite_url;
			global $institut;
			global $institut_url;

			$pdf_footer .= '<table cellspacing="0" cellpadding="15" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400; color: #ffffff; background-color: #0066B3;">';
				$pdf_footer .= '<thead></thead>';
				$pdf_footer .= '<tbody>';
					$pdf_footer .= '<tr style="vertical-align:bottom">';
						$pdf_footer .= '<td style="width: 15%">';
							$pdf_footer .= '<img src="/img/simon-eglau.jpg" width="auto" height="100" style="width: auto; height: 100px;" title="Simon Eglau" alt="Simon Eglau">';
						$pdf_footer .= '</td>';

						$pdf_footer .= '<td style="width: 45%">';
							$pdf_footer .= '<span style="font-size: 9;"><span style="font-size: 10;">Direktor Immobilien</span><br><span style="font-weight: bold; font-size: 12">Simon Eglau</span><br><br><span>Telefon: 07243 94 74 6666</span><br><span>E-Mail: info@immobiliencenter-ettlingen.de</span></span>';
						$pdf_footer .= '</td>';

						$pdf_footer .= '<td style="width: 40%; text-align:right; vertical-align:bottom">';
							$pdf_footer .= '<span style="font-size:9;"><br><span style="font-weight: bold;">'.$institut.'</span><br>Wilhelmstraße 3-7<br>76275 Ettlingen<br>www.immobiliencenter-ettlingen.de</span>';
						$pdf_footer .= '</td>';

					$pdf_footer .= '</tr>';
				$pdf_footer .= '</tbody>';
				$pdf_footer .= '<tfoot></tfoot>';
			$pdf_footer .= '</table>';

			$this->SetRightMargin(0);
			$this->SetLeftMargin(0);
			$this->SetY(-35);
			$this->SetFont('dejavusans', '', 10);
			$this->writeHTML($pdf_footer, true, false, true, false, '');
		}
	}

	// Erstellung des PDF Dokuments
	/*$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);*/
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// Dokumenteninformationen
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($institut);
	$pdf->SetTitle($titel);
	$pdf->SetSubject($sitetitel);

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// Header und Footer Informationen
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// Auswahl des Font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// Auswahl der MArgins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// Automatisches Autobreak der Seiten
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// Image Scale
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// ---------------------------------------------------------

	// Schriftart
	$pdf->SetFont('dejavusans', '', 9);

	// Tickets erstellen
	// Inhalt des PDFs als HTML-Code
	$html = '';

	/*Institutangaben*/
		$html .= '<table cellspacing="0" cellpadding="15" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400;">';
			$html .= '<tbody>';
				$html .= '<tr style="text-align:right">';
					$html .= '<td>'.$institut_logo.'</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table cellspacing="0" cellpadding="20">';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

		/* Objektangaben */
		$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400;">';
			$html .= '<tbody>';
				$html .= '<tr style="vertical-align: top;">';
					$html .= '<td style="width: 100%">';
						$html .= '<span style="font-size: 15; font-weight: lighter; color: #0066B3; text-transform: uppercase;">';
							$html .= $subtitel;
						$html .= '</span>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table cellspacing="0" cellpadding="5">';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

		/* Objektangaben */
		$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400; font-size:10;">';
			$html .= '<tbody>';
				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>Immobilientyp:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_immotyp.'</span>';
					$html .= '</td>';
				$html .= '</tr>';
				if(!$im_haustyp == '') {
					$html .= '<tr>';
						$html .= '<td style="width: 30%">';
							$html .= '<span>Immobilienkategorie:</span>';
						$html .= '</td>';
						$html .= '<td style="width: 70%">';
							$html .= '<span>'.$im_haustyp.'<br></span>';
						$html .= '</td>';
					$html .= '</tr>';
				}
				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>Wohnfläche:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_wohnflaeche.' m²</span>';
					$html .= '</td>';
				$html .= '</tr>';
				if($im_grundstuecksflaeche !== '') {
					$html .= '<tr>';
						$html .= '<td style="width: 30%">';
							$html .= '<span>Grundstücksfläche:</span>';
						$html .= '</td>';
						$html .= '<td style="width: 70%">';
							$html .= '<span>'.$im_grundstuecksflaeche.' m²</span>';
						$html .= '</td>';
					$html .= '</tr>';
				}
				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>Baujahr:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_baujahr.'</span>';
					$html .= '</td>';
				$html .= '</tr>';
				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>Adresse der Immobilie:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_strasse.'<br>'.$im_plz.' '.$im_ort.'</span>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table cellspacing="0" cellpadding="10">';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

		$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400; font-size:10;">';
			$html .= '<tbody>';
				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span style="font-size: 12; color: #0066B3">Ihre Kontaktangaben<br></span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
					$html .= '</td>';
				$html .= '</tr>';

				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>Name:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_vorname.' '.$im_nachname.'</span>';
					$html .= '</td>';
				$html .= '</tr>';

				$html .= '<tr>';
					$html .= '<td style="width: 30%">';
						$html .= '<span>E-Mail-Adresse:</span>';
					$html .= '</td>';
					$html .= '<td style="width: 70%">';
						$html .= '<span>'.$im_email.'</span>';
					$html .= '</td>';
				$html .= '</tr>';

				if(!$im_tel == '') {
					$html .= '<tr>';
						$html .= '<td style="width: 30%">';
							$html .= '<span>Grundstücksfläche:</span>';
						$html .= '</td>';
						$html .= '<td style="width: 70%">';
							$html .= '<span>'.$im_tel.'</span>';
						$html .= '</td>';
					$html .= '</tr>';
				}
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table cellspacing="0" cellpadding="15">';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

		if($im_immotyp == 'Wohnung') {
			$immobilie = 'Ihre Wohnung';
		} else {
			$immobilie = 'Ihr Haus';
		}

		/* Objektangaben */
		$html .= '<table cellspacing="0" cellpadding="15" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400; background-color: #dddddd">';
			$html .= '<tbody>';
				$html .= '<tr style="vertical-align: top;">';
					$html .= '<td style="width: 100%">';
						$html .= '<span style="font-size: 12; font-weight: lighter;">';
							$html .= 'Nach Auswertung unserer Datenbank ist für '.$immobilie.' ein Preis von <br>bis zu <span style="color:#0066B3; font-weight:bold;">'.$max_preis.' €</span> möglich.';
						$html .= '</span>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table cellspacing="0" cellpadding="15">';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

		/* Aufführungsangaben */
		$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji; font-weight: 400; font-size: 10">';
			$html .= '<tbody>';
				$html .= '<tr>';
					$html .= '<td style="width: 40%">';
						$html .= '<p><span style="font-size: 12; font-weight: lighter; color: #0066b3;">Service</span></p>';
						$html .= 'Gerne helfen wir Ihnen dabei, den optimalen Preis für Ihr Objekt zu erzielen. Vertrauen Sie unserer Erfahrung, Kompetenz und profitieren Sie von unserer regionalen Verwurzelung. Diskret und zuverlässig sind wir der Partner an Ihrer Seite.';
					$html .= '</td>';
					$html .= '<td style="width: 10%">';
					$html .= '</td>';

					$html .= '<td style="width: 40%">';
						$html .= '<p><span style="font-size: 12; font-weight: lighter; color: #0066b3;">Weitere Informationen</span></p>';
						$html .= 'Sie möchten Ihr Objekt mit unserer Hilfe verkaufen? Natürlich erstellen wir für Sie eine detaillierte Bewertung vor Ort. Kontaktieren Sie uns einfach!';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';

		/* SPACER */
		$html .= '<table>';
			$html .= '<tbody><tr><td>&nbsp;</td></tr></tbody>';
		$html .= '</table>';
		/* /SPACER */

	// Neue Seite
	$pdf->AddPage();
	// Fügt den HTML Code in das PDF Dokument ein
	/*$pdf->writeHTML($html, true, false, true, false, '');*/
	$pdf->writeHTML($html, true, 0, true, true);

	// Ausgabe der PDF
	$pdf_location 	= '/files/';
	$pdf_link 		= $pdf_location.$pdf_name;
	$pdf->Output(__DIR__.$pdf_link, 'F');

	

?>