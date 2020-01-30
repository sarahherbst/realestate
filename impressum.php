<?php
	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$schluessel 	= 'impressum';
	$headline		= 'Impressum';
	$ite_rubrik 	= $schluessel;

	// Artikel auslesen
	$text_sql 		= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_status'), array($institut_id, $schluessel, '1'), '', '' );
	$text_row 		= mysqli_fetch_assoc($text_sql);
	
	include('header.php');
?>

		<!-- Header (Logo, Überschrift, Einleitung) -->
		<section class="bg-blue-gradient pt-4">
			
			<!-- Überschrift & Einleitung-->
			<div class="container py-5" style="margin-top: 7rem;">
				<h1 class="display-4 text-light"><?php echo $headline; ?></h1>
			</div>
		</section>
		
		<!-- Inhalt -->
		<div class="bg-white container p-5 mb-5">
			<?php echo str_replace('\r\n', '', $text_row['txt_beitrag']); ?>
		</div>

<?php
	include('footer.php');
?>