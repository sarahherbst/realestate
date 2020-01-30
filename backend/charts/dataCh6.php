<?php
	// Chart #6
	$ch6_sql = mysqli_query($db, "SELECT *, COUNT(*) AS counter FROM item_tracking WHERE (ite_position = 'Formular') AND ite_date BETWEEN '$dbStartdate' AND '$dbEnddate'") or die(mysqli_error($db));
	if ( mysqli_num_rows($ch6_sql) == true ) {
		// Anzahl aller Page-Tracks
		$ch6_array = [];

		$ch6_tracks_sql 	= mysqli_query($db, "SELECT *, COUNT(*) AS counter FROM item_tracking WHERE (ite_position = 'Formular') AND ite_date BETWEEN '$dbStartdate' AND '$dbEnddate' GROUP BY ite_name ORDER BY counter DESC") or die(mysqli_error($db));
		while( $row = mysqli_fetch_assoc($ch6_tracks_sql) ) {  
			array_push($ch6_array, array("ansprechpartner" => $row['ite_name'], "clicks" => $row['counter']));
		}

		//counting the length of the array
		$countArrayLength = count($ch6_array);
?>
	<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart6);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart6() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'ansprechpartner');
			data.addColumn('number', 'clicks');
			data.addRows([
				<?php
					for($i=0;$i<$countArrayLength;$i++){
						echo "['" . $ch6_array[$i]['ansprechpartner'] . "'," . $ch6_array[$i]['clicks'] . "],";
					}
				?>
			]);
			var view = new google.visualization.DataView(data);
			view.setColumns([
				0, 
				1,
				{
					calc: "stringify",
					sourceColumn: 1,
					type: "string",
					role: "annotation"
				}
			]);


			// Set chart options
			var options = {
				chartArea: {
					left: '30%',
					top: '6%',
					height: '100%',
					width: '70%',
				},
				fontSize: '16'
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.BarChart(document.getElementById('ch6Div'));
			chart.draw(view, options);
		}
		window.addEventListener('resize', function() {
			drawChart6();
		});
	</script>
<?php } ?>