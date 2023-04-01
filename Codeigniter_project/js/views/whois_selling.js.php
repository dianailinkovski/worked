<script type="text/javascript" src="js/jqwidgets/jqx-all.js"></script>
<script type="text/javascript" src="js/jquery.tablehighlighter.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pack.js"></script>
<script type="text/javascript">
/*For Google Charts*/
var repChartContainer = null;
var chartdata = null;
var myView = null;
var chart = null;
var options = null;
var globalData = <?php echo json_encode($gData) ?>;
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
/*******************/
//toggle columns
function hideShowSeries(el,inc){
	myView = new google.visualization.DataView(chartdata);
	var len = myView.getNumberOfColumns();
	var ck = $(el);
	var val = $('.chkbox').index(ck);
	$('input[name="series[]"]:eq('+val+')').attr('checked', !$('input[name="series[]"]:eq('+val+')').attr('checked'));

	var SetColumns = new Array();
	SetColumns[0] = 0;
	var count = 1;
	var checkedLen = $('.chkbox input[type=checkbox]:checked').length;
	var tempColors = new Array();
	if(checkedLen > 0){
		$('.chkbox input[type=checkbox]').each(function(){
			if($(this).attr('checked')){
				var val = parseInt($(this).val());
				for(var se = val+1;se < len ; se += inc){
					SetColumns[count] = se;
					count++;
					tempColors.push(globalData.googleDataColors[se-1]);
				}
			}
		});

		options.colors = tempColors;
		myView.setColumns(SetColumns);
		chart.draw(myView, options);
	}else{
		$('input[name="series[]"]:eq('+val+')').attr('checked',true);
	}
}
</script>