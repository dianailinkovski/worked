<div class="panel panel-default">
    <div class="panel-heading">
        Notifications Sent - Last 30 Days
    </div>
    <div class="panel-body">
        <div id="notifications-sent-chart">
            <?php if (empty($data_points)): ?>
                <p>
                    No notifications have been sent.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
        
<?php if (!empty($data_points)): ?>
    <script type="text/javascript">
        
      //google.setOnLoadCallback(drawNotificationChart, true);

      drawNotificationChart();
    
      function drawNotificationChart() 
      {
        var notif_data = google.visualization.arrayToDataTable([
          ['Date', 'Notifications'],
          <?php foreach ($data_points as $key => $value): ?>
              ['<?php echo $key ; ?>', <?php echo $value; ?>],
          <?php endforeach; ?>
        ]);
    
        var notif_options = {
          title: '',
          //legend: { position: 'bottom' },
          legend: false,
          hAxis: {
            title: 'Date',
            showTextEvery: 5, 
            slantedText: false, 
          },
          vAxis: {
            title: 'Notifications'
          },
          series: {
        	  0:{color: '#81B72A', visibleInLegend: false},
          }
        };
    
        var notif_chart = new google.visualization.AreaChart(document.getElementById('notifications-sent-chart'));
    
        notif_chart.draw(notif_data, notif_options);
      }
      
    </script>
<?php endif; ?>    