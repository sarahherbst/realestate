<?php 
	$ch2_sql = sql_select_between('all', 'item_tracking', array('NOT ite_position', 'NOT ite_position'), array('Formular', 'E-Mail'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
	if (mysqli_num_rows($ch2_sql) == true) {
		// Anzahl aller Item-Tracks
		$ch2_array 		= [];
		
		// Ansprechpartner
		$item1 			= sql_select_between('ite_id', 'item_tracking', array('ite_praefix', 'NOT ite_position', 'NOT ite_position', 'NOT ite_name'), array('ansprechpartner', 'Formular', 'E-Mail', 'Artikel'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
		$countItem1 	= mysqli_num_rows($item1);
		array_push($ch2_array, array('name' => 'Ansprechpartner', 'clicks' => $countItem1));

		// Rubriken
		$count_rub_sql = sql_select_where('rub_id, rub_name', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
		$i = 2;
		while ($count_rub_row = mysqli_fetch_assoc($count_rub_sql)) {
			${'item'.$i} 		= sql_select_between('ite_id', 'item_tracking', array('ite_praefix', 'NOT ite_position', 'NOT ite_position', 'NOT ite_name'), array('rub_'.$count_rub_row['rub_id'], 'Formular', 'E-Mail', 'Artikel'), 'ite_date', array($dbStartdate, $dbEnddate), '', '' );
			${'countItem'.$i} 	= mysqli_num_rows(${'item'.$i});
			array_push($ch2_array, array('name' => $count_rub_row['rub_name'], 'clicks' => ${'countItem'.$i}));
			$i++;
		}

	$countArrayLength = count($ch2_array);
?>
	<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart2);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart2() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'name');
			data.addColumn('number', 'clicks');
			data.addRows([
				<?php
					for($i=0;$i<$countArrayLength;$i++){
						echo "['" . $ch2_array[$i]['name'] . "'," . $ch2_array[$i]['clicks'] . "],";
					}
				?>
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
			var chart = new google.visualization.PieChart(document.getElementById('ch2Div'));
			chart.draw(data, options);
		}
		window.addEventListener('resize', function() {
			drawChart2();
		});
	</script>
<?php } ?>