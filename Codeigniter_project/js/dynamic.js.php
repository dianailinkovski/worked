<script type="text/javascript">
	var base_url = "<?=base_url()?>";
	var image_url = "<?=frontImageUrl()?>";
	var teamMembers = <?= ! empty($team_members) ? json_encode($team_members) : 'null'?>;
	var emSugSelector = '<?=isset($element_name) ? $element_name : '#autoSelectedItems'?>';
	var $currentContentBtn = null;
	var notifyResource = '<?=isset($show_notify_resource) ? $show_notify_resource : ''?>';
	$(document).ready(function(){

		<?php if ( ! empty($show_notify_resource)): ?>
		$('#violator_notification').on('click', function() {
			violator_notification(notifyResource);
		});

		$('#violator_notification_form').on('submit', function(e) {
			e.preventDefault();
			$('#notify_dialog .btn_save').click();
		});
		<?php endif; ?>

		// Pricing Over Time
	<?php if (isset($optArray)):?>

		<?php if (isset($optArray['byproduct'])):?>
			var autocompleteInput = $(".autoCompleteContainer");
			$(".product_name").autocomplete({
				source: function( request, response ) {
					pEl = $(this.element).next('input[name="products[]"]');
					$.ajax({
						url: base_url+"schedule/get_products_names/"+request.term,
						dataType: "json",
						data: {},
						success: function(items) {
							if(!items || items.length == 0) {
								if(typeof auto_complete_no_result != 'undefined') auto_complete_no_result('product_name');
								return false;
							}
							response($.map( items, function( item ) {
								return {label: item.title, value: item.title, Id: item.id}
							}));
						}
					});
				},
				minLength: 1,
				deferRequestBy: 0,
				appendTo: autocompleteInput,
				select: function( event, ui ) {
					$(pEl).val(ui.item.Id);
					showNextBlock('<?=$optArray['byproduct']['next_block']?>');
					new AddProductButton(autocompleteInput);
				}
			});
		<?php endif;?>

		<?php if (isset($optArray['bycompetition'])):?>
			$("#products").attr('data-report-type', 'competition');
			var autocompleteInput = $(".autoCompleteContainer");
			$(".product_name").autocomplete({
				source: function( request, response ) {
					pEl = $(this.element).next('input[name="products[]"]');
					$.ajax({
						url: base_url+"schedule/get_products_names/"+request.term+"/competition/",
						dataType: "json",
						data: {},
						success: function(items) {
							if(!items || items.length == 0) {
								if(typeof auto_complete_no_result != 'undefined') auto_complete_no_result('product_name');
								return false;
							}
							response($.map( items, function( item ) {
								return {label: item.title, value: item.title, Id: item.id}
							}));
						}
					});
				},
				minLength: 1,
				deferRequestBy: 0,
				appendTo: autocompleteInput,
				select: function( event, ui ) {
					$(pEl).val(ui.item.Id);
					showNextBlock('<?=$optArray['bycompetition']['next_block']?>');
				}
			});
		<?php endif;?>

		<?php if (isset($optArray['bygroup'])):?>
			$('input[name=group_id]').on('change', function() {
				showNextBlock('<?=$optArray['bygroup']['next_block']?>');
			});
		<?php endif;?>

		$('#date_from').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate:'-20y',
			maxDate:'0d',
			onSelect:function(dateText, e){
				showNextBlock('<?=$optArray['bydate']['next_block']?>');
				$('input[name=time_frame]:radio').each(function(){
					$(this).attr('checked',false);
				});
			},
			onClose: function(){
				if(!is_date_range_valid()){
					$(this).val('');
					return false;
				}
				else
				{
					elaborate_keyword();
				}
			}
		});

		$('#date_to').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate:'-20y',
			maxDate:'0d',
			onSelect:function(dateText, e){
				showNextBlock('<?=$optArray['bydate']['next_block']?>');
				$('input[name=time_frame]:radio').each(function(){
					$(this).attr('checked',false);
				});
			},
			onClose: function(dateText, e){
				if(!is_date_range_valid()){
					$(this).val('');
					return false;
				}
				else
				{
					elaborate_keyword();
				}
			}
		});
		$("#date_from_a").click(function(){$('#date_from').click();});
		$("#date_to_a").click(function(){$('#date_to').click();});

		$('.reports_radio input[type=radio]').click(function(){
			var radio_type = ($(this).attr('name'));
				<?php if (isset($optArray['show_comparison'])):?>
				if(radio_type === 'show_comparison'){ // competitor report option
					var $radios = $('input:radio[name=show_comparison]');
					$radios.attr('checked', false);
					r = $(this).attr('id').replace('show_comparison_', '');
					$radios.filter('[value='+r+']').attr('checked', true);
					showNextBlock('<?=$optArray['show_comparison']['next_block'];?>');
				}
				<?php endif;?>
			if(radio_type === 'time_frame'){
				var $radios = $('input:radio[name=time_frame]');
				//$radios.attr('checked', false);
				r = $(this).attr('id').replace(/tf/, '');
				$radios.filter('[value='+r+']').attr('checked', true);
				clearDates();
				showNextBlock('<?=$optArray['bydate']['next_block'];?>');
			}
		});
	<?php endif;?>

		var productsElement = $('#product_container .rightCol');
		var is_competition = productsElement.attr('data-report-type') === 'competition';
		$('#product_container').on('click', '.product-plus', function(){
			if ( ! productsElement.find('.product-minus').length)
				new RemoveProductButton(this, 'insertBefore');
			new Product($(this).parent(), is_competition, 'insertAfter');
		});

		$('#product_container').on('click', '.product-minus', function(){
			var previous = $(this).parent().prev();
			$(this).parent().remove();
			if (productsElement.find('.product-minus').length <= 1)
				$('.product-minus').remove();
			if (productsElement.find('.product-plus').length < 1)
				new AddProductButton(previous);
		});

	});

if ($('#total_merchants_gauge').length){
	var number_of_merchants = <?=isset($number_of_merchants) ? $number_of_merchants : 0?>;
	google.load('visualization', '1', {packages:['gauge']});
	merchantsGauge(number_of_merchants, 115, 115);
}

<?php if ( ! empty($gData)): ?>
	//this will come from a php function that already
	//prepares the data for the necessary chart output
	function getGoogleData(){
		return <?=json_encode($gData);?>;
	}

	/*For Google Charts*/
	var repChartContainer = null;
	var data = null;
	var chart = null;
	var chartdata = null;
	var myView = null;
	var options = null;
	var globalData = getGoogleData();
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

	<?php //if( ! empty($gData['data']['size'])): ?>
	google.setOnLoadCallback(drawGoogleChart);

	var googleData = getGoogleData();
	var marketLookup = <?=json_encode(get_market_lookup())?>;

	<?php //endif;?>
<?php endif; ?>
</script>
