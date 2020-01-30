<?php
	// Chart #4
	$ch4_sql = sql_select_between('all', 'menu_tracking', '', '', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
	if ( mysqli_num_rows($ch4_sql) == true ) {
		// Anzahl aller Page-Tracks
		$ch4_count = mysqli_num_rows($ch4_sql);
		// Anzahl Page-Tracks 
		$page1 			= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'startseite', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage1 	= mysqli_num_rows($page1);
		$page2 			= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'ansprechpartner', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage2 	= mysqli_num_rows($page2);
		$page3 			= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'impressum', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage3 	= mysqli_num_rows($page3);
		$page4 			= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'datenschutz', 'men_date', array($dbStartdate, $dbEnddate), '', '' );
		$countPage4 	= mysqli_num_rows($page4);

		$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$i = 5;
		while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
			${'page'.$i} 		= sql_select_between('men_id', 'menu_tracking', 'men_praefix', 'rub_'.$count_rub_row['rub_id'], 'men_date', array($dbStartdate, $dbEnddate), '', '' );
			${'countPage'.$i} 	= mysqli_num_rows(${'page'.$i});
			$i++;
		}

		$ch4_array = [];
		array_push($ch4_array, array('page' => 'Startseite', 'clicks' => $countPage1));
		array_push($ch4_array, array('page' => 'Ansprechpartner', 'clicks' => $countPage2));
		array_push($ch4_array, array('page' => 'Impressum', 'clicks' => $countPage3));
		array_push($ch4_array, array('page' => 'Datenschutz', 'clicks' => $countPage4));

		$rub_name_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$iCount = 5;
		while ($rub_name_row = mysqli_fetch_assoc($rub_name_sql)) {
			array_push($ch4_array, array('page' => $rub_name_row['rub_name'], 'clicks' => ${'countPage'.$iCount}));
			$iCount++;
		}

		//counting the length of the array
		$countArrayLength = count($ch4_array);
?>
	<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['corechart']});
		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart4);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart4() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'page');
			data.addColumn('number', 'clicks');
			data.addRows([
				<?php
					for($i=0;$i<$countArrayLength;$i++){
						echo "['" . $ch4_array[$i]['page'] . "'," . $ch4_array[$i]['clicks'] . "],";
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
			var chart = new google.visualization.PieChart(document.getElementById('ch4Div'));
			chart.draw(data, options);
		}

		window.addEventListener('resize', function() {
			drawChart4();
		});
	</script>
<?php } ?>