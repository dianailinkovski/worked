<script type="text/javascript" src="js/jqwidgets/jqx-all.js"></script>
<script type="text/javascript" src="js/jquery.tablehighlighter.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pack.js"></script>
<script type="text/javascript">

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

google.load('visualization', '1', {packages:['gauge']});

<?php if (isset($gData) && count($gData) > 0): ?>
var data    = null;
var myView  = null;
var chart   = null;
var options = null;
var globalData =   <?=json_encode($gData) ?>;

$('#repChartContainer').show();

<?php endif; ?>



function merchantsGauge(num, w, h) {
	google.setOnLoadCallback(function(){ drawMerchantsGauge(num, w, h) });
}

function drawMerchantsGauge(num, w, h) {
	// set the max and min
	var max = 500;
	var min = 0;
	if (isNaN(num) || num < 0)
		num = 0;
	else if (num > max)
		max = num;

	// calculate the animation so that it
	// lasts the same amount of time for any number
	var meter_duration = 1500; // 3 second animation
	var meter_period = 80;
	var meter_step = Math.ceil(num * meter_period / meter_duration);

	// set the gauges look
	var gf = Math.floor(max * .55);
	var gt = Math.floor(max * .75);
	var yt = Math.floor(max * .90);

	var options = {
	  width: w, height: h,
	  min: min, max: max,
	  redFrom: yt, redTo: max,
	  yellowFrom: gt, yellowTo: yt,
	  greenFrom: gf, greenTo: gt,
	  minorTicks: 5,
	  animation: {
		  duration: 1500, easing: 'out'
	  }
	};

	// create the gauge
	var data = google.visualization.arrayToDataTable([
		['Label', 'Total Merchants'],
		['Merchants', 0]
	]);
	var gauge = new google.visualization.Gauge(
		document.getElementById('total_merchants_gauge')
	);
	gauge.draw(data, options);

	// animate
	var meter = 0;
	var decel = 0;
	var meter_interval = setInterval(function() {
		if (meter >= num)
			clearInterval(meter_interval);
		data.setValue(0, 1, meter);
		gauge.draw(data, options);
		if (decel == 1) {
			if (meter > (num * .75)) {
				meter_step = Math.ceil(meter_step * .25);
				decel++;
			}
		}
		else if (decel == 0) {
			if (meter > (num * .5)) {
				meter_step = Math.ceil(meter_step * .50);
				decel++;
			}
		}
		meter = Math.min(meter + meter_step, num);
	}, meter_period);
}

merchantsGauge(<?=$number_of_merchants?>, 115, 115);
</script>