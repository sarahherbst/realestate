<?php
	// Chart #1
	$ch1_sql = sql_select_between('all', 'page_tracking', '', '', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
	if ( mysqli_num_rows($ch1_sql) == true ) {
		// Anzahl aller Page-Tracks
		$ch1_count = mysqli_num_rows($ch1_sql);
		// Anzahl Page-Tracks 
		$page1 			= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'startseite', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage1 	= mysqli_num_rows($page1);
		$page2 			= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'ansprechpartner', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage2 	= mysqli_num_rows($page2);
		$page3 			= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'impressum', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage3 	= mysqli_num_rows($page3);
		$page4 			= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'datenschutz', 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage4 	= mysqli_num_rows($page4);

		$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$i = 5;
		while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
			${'page'.$i} 		= sql_select_between('pag_id', 'page_tracking', 'pag_praefix', 'rub_'.$count_rub_row['rub_id'], 'pag_date', array($dbStartdate, $dbEnddate), '', '' );
			${'countPage'.$i} 	= mysqli_num_rows(${'page'.$i});
			$i++;
		}

		$ch1_array = [];
		array_push($ch1_array, array('page' => 'Startseite', 'clicks' => $countPage1));
		array_push($ch1_array, array('page' => 'Ansprechpartner', 'clicks' => $countPage2));
		array_push($ch1_array, array('page' => 'Impressum', 'clicks' => $countPage3));
		array_push($ch1_array, array('page' => 'Datenschutz', 'clicks' => $countPage4));

		$rub_name_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$iCount = 5;
		while ($rub_name_row = mysqli_fetch_assoc($rub_name_sql)) {
			array_push($ch1_array, array('page' => $rub_name_row['rub_name'], 'clicks' => ${'countPage'.$iCount}));
			$iCount++;
		}

		//counting the length of the array
		$countArrayLength = count($ch1_array);
?>
	<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['corechart']});
		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart1);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart1() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'page');
			data.addColumn('number', 'clicks');
			data.addRows([
				<?php
					for($i=0;$i<$countArrayLength;$i++){
						echo "['" . $ch1_array[$i]['page'] . "'," . $ch1_array[$i]['clicks'] . "],";
					}
				?>
			]);

			// Set chart options
			var options = {
				legend: 'label',
				chartArea: {
					left: '6%',
					top: '6%',
					height: '94%',
					width: '94%',
				},
				fontSize: '16'
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.PieChart(document.getElementById('ch1Div'));
			chart.draw(data, options);
		}

		window.addEventListener('resize', function() {
			drawChart1();
		});
	</script>
<?php } ?>