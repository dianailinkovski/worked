<div class="panel panel-default">
    <div class="panel-heading">
        Product Violations - Last 30 Days
    </div>
    <div class="panel-body">
        <div id="product-violation-chart">
        </div>
    </div>
</div>

<script type="text/javascript">

    drawViolationChart();
    //google.setOnLoadCallback(drawViolationChart, true);
    
    function drawViolationChart() 
    {
        var vio_data = google.visualization.arrayToDataTable([
          ['Day', 'Violations'],
          <?php foreach ($violations as $violation): ?>
              ['<?php echo date('M j', strtotime($violation['select_date'])); ?>', <?php echo $violation['violation_count']; ?>],
          <?php endforeach; ?>
        ]);
    
        var vio_options = {
          title: '',
          //legend: { position: 'bottom' },
          legend: false,
          hAxis: {
            //title: 'Date',
            showTextEvery: 5, 
            slantedText: false,
          },
          vAxis: {
            //title: 'Violations'
          },
          series: {
        	  0:{color: '#81B72A', visibleInLegend: false},
          }
        };
    
        var vio_chart = new google.visualization.AreaChart(document.getElementById('product-violation-chart'));
    
        vio_chart.draw(vio_data, vio_options);
    }
  
</script>