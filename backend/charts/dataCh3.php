<?php
	// Chart #3
	$ch3_sql = sql_select_between('all', 'item_tracking', 'ite_name', '%Conversion%', 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
	if ( mysqli_num_rows($ch3_sql) == true ) {
		// Item-Tracks
		
		// Conversionarten rausfinden
		$con_art_sql = mysqli_query($db, "SELECT *FROM item_tracking WHERE ite_name LIKE '%Conversion%' GROUP BY ite_name");
		$con_art_num = mysqli_num_rows($con_art_sql);
		$con_art_i 	 = 1;
		$row = '["Rubrik",';
		while( $con_art_row = mysqli_fetch_array($con_art_sql) ) {
			$row .= '"'.$con_art_row['ite_name'].'"';
			if ($con_art_i < $con_art_num) {
				$row .= ', ';
			}
			$con_art_i++;
		}
		$row .= ' ],';

		// Rubriken abfragen
		$ch3_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$rub_i = 0;
		while ($ch3_rub_row = mysqli_fetch_assoc($ch3_rub_sql)) {
			// Rubrikname
			$row .= '["'.$ch3_rub_row['rub_name'].'", ';
			$rub_id = $ch3_rub_row['rub_id'];

			// Anzahl Conversionarten
			$con_art_sql = mysqli_query($db, "SELECT * FROM item_tracking WHERE ite_name LIKE '%Conversion%' GROUP BY ite_name");
			$con_art_num = mysqli_num_rows($con_art_sql);
			$con_art_i 	 = 1;
			while( $con_art_row = mysqli_fetch_array($con_art_sql) ) {
				// Conversion-Name
				$conversion_name = $con_art_row['ite_name'];

				// Klick-Anzahl
				$con_klick_sql = mysqli_query($db, "SELECT COUNT(*) AS klicks FROM item_tracking WHERE ite_praefix = 'rub_".$rub_id."' AND ite_name = '".$conversion_name."' ");
				$con_klick_row = mysqli_fetch_assoc($con_klick_sql);
				$row .= $con_klick_row['klicks'];
				if ($con_art_i < $con_art_num) {
					$row .= ', ';
				}
				$con_art_i++;
			}

			$row .= ' ],';
			$rub_i++;
		}
?>

	<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['bar']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart3);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart3() {
			// Create the data table.
			var data = google.visualization.arrayToDataTable([
				<?php echo $row; ?>
			]);

			// Set chart options
			var options = {
				chartArea: {
					left: '6%',
					top: '6%',
					height: '94%',
					width: '94%',
				},
				fontSize: '16'
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.charts.Bar(document.getElementById('ch3Div'));
			chart.draw(data, options);
		}
		window.addEventListener('resize', function() {
			drawChart3();
		});
	</script>
<?php } ?>