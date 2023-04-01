<div id="repChartContainer"></div>
<?php /* Switched to using javascript files instead of this function
<!--<html>
	<head></head>
	<body>
		<div id="repChartContainer"></div>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			var data    = null;
			var myView  = null;
			var chart   = null;
			var options = null;
			var globalData =   <?//=json_encode($gData)?>;
			google.load("visualization", "1", {packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
			function drawChart(){
				if(globalData.type != 'scatter')
					data = google.visualization.arrayToDataTable(globalData.data);

				options = {
					title: '',
					chartArea: {width:'850',height:'225',left:'75',top:'20'},
					pointSize:2,
					width:'950',
					height:'300',
					legend: {position: 'none'}
				};

				if(globalData.y_title != undefined){
					options.vAxis = {title:globalData.y_title,titleTextStyle:{color:'black',fontStyle:'none',fontWeight:'bold',italic:false},minValue:0};
					if(globalData.type=='column'){
						if(globalData.maxValue != undefined)
							options.vAxis.maxValue = parseInt(globalData.maxValue)+10;
					}
					else if(globalData.type=='line'){
						options.vAxis.format = '$0.00';
						if(globalData.maxValue != undefined)
							options.vAxis.maxValue = parseFloat(globalData.maxValue)+10;
					}
				}

				if(globalData.x_title != undefined)
					options.hAxis = {title:globalData.x_title,titleTextStyle:{color:'black',fontStyle:'none',fontWeight:'bold',italic:false}};

				if(globalData.googleDataColors != undefined)
					options.colors = globalData.googleDataColors;

				if(globalData.type=='pie'){
					options.legend.position =  'right';
					options.chartArea.left  =  '300';
					options.chartArea.top   =  '30';

					if(globalData.width != undefined){
						options.chartArea.width = globalData.width;
						options.chartArea.left  = '15';
						options.width = globalData.width;
						options.backgroundColor = '#F1F2F2';
						options.pieSliceText    = 'value';
						options.legend.position =  'none';
					}
					if(globalData.height != undefined){
						options.chartArea.height = parseInt(globalData.height)-50;
						options.height = globalData.height ;
					}
					chart = new google.visualization.PieChart(document.getElementById('repChartContainer'));
				}
				else if(globalData.type=='column'){
					chart = new google.visualization.ColumnChart(document.getElementById('repChartContainer'));
				}
				else if(globalData.type=='scatter'){
					options.vAxis.format = '$0.00';

					if(globalData.maxValue != undefined)
						options.vAxis.maxValue = parseFloat(globalData.maxValue)+10;

					var data = new google.visualization.DataTable();
					for(var ind = 0 ;ind < globalData.googleData.length;ind++){
						if(ind == 0){
							for(var dataInd=0;dataInd < globalData.googleData[ind].length;dataInd++){
								if(dataInd == 0)
									data.addColumn('datetime', 'Date');
								else
									data.addColumn('number', globalData.googleData[ind][dataInd]);
							}
						}
						else{
							for(var temp=0;temp<globalData.googleData[ind].length;temp++){
								var str = globalData.googleData[ind][temp];
								if(temp == 0 )
									str = eval(str);
								globalData.googleData[ind][temp] = str;
							}
							data.addRow(globalData.googleData[ind]);
						}
					}
					chart = new google.visualization.ScatterChart(document.getElementById('repChartContainer'));
				}
				else{
					chart = new google.visualization.LineChart(document.getElementById('repChartContainer'));
				}

				google.visualization.events.addListener(chart, 'ready', generateGoogleChart);
				chart.draw(data, options);
			}

			function generateGoogleChart(){}
		</script>
	</body>
</html>-->
 */