<?php
$day = $date["mday"];
$month = $date["mon"];
$year = $date["year"];
$monthName = Yii::t('common', $date["month"]);
	
$thisMonth = getDate(mktime(0, 0, 0, $month, 1, $year));
$nextMonth = getDate(mktime(0, 0, 0, $month + 1, 1, $year));
	
$nextMonthTimestamp = mktime(0, 0, 0, $month + 1, 1, $year);
$nextYearTimestamp = mktime(0, 0, 0, $month, 1, $year + 1);
$prevMonthTimestamp = mktime(0, 0, 0, $month - 1, 1, $year);
$prevYearTimestamp = mktime(0, 0, 0, $month, 1, $year - 1);

$firstWeekDay = $thisMonth["wday"];
$daysInThisMonth = round(($nextMonth[0] - $thisMonth[0]) / (60 * 60 * 24));

$dateIsCurrentMonth = $month == date('m') && $year == date('Y');
$extraCells = 7 - (($firstWeekDay + $daysInThisMonth) % 7);
?>

<div id="<?php echo 'eventsCalendarWidget-'.$this->id; ?>" class="eventsCalendarWidget">

<table class="calendar-table">
	
<tr>
	<td colspan="7" class="calendar-month">
		<a href="javascript:;" title="<?php echo Yii::t('eventModule.common', 'Année précédente'); ?>"><span class="glyphicon glyphicon-fast-backward"></span></a>&nbsp;
		<a href="javascript:;" title="<?php echo Yii::t('eventModule.common', 'Mois précédent'); ?>"><span class="glyphicon glyphicon-step-backward"></span></a>&nbsp;
		<span><?php echo $monthName.' '.$year; ?></span>&nbsp;
		<a href="javascript:;" title="<?php echo Yii::t('eventModule.common', 'Mois suivant'); ?>"><span class="glyphicon glyphicon-step-forward"></span></a>&nbsp;
		<a href="javascript:;" title="<?php echo Yii::t('eventModule.common', 'Année suivante'); ?>"><span class="glyphicon glyphicon-fast-forward"></span></a>
	</td>
</tr>

<tr><th>Di</th><th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th></tr>
<tr>
	
<?php if ($firstWeekDay > 0): ?>
	<td colspan="<?php echo $firstWeekDay; ?>" class="calendar-day">&nbsp;</td>
<?php endif;

$weekDay = $firstWeekDay; 
for ($i = 1; $i <= $daysInThisMonth; $i++):
	$weekDay %= 7;

	if ($weekDay == 0): ?>
</tr>
<tr>

	<?php endif;

	$popoverContent = '';
	if (isset($events[$i])):
		
		$sectionIds = array();
		foreach ($events[$i] as $event):
			if (!in_array($event->section_id, $sectionIds)):
				$sectionIds[] = $event->section_id;
			endif;
		endforeach;
		
		$dateContent = '<span class="popover-trigger event-type-'.implode("-", $sectionIds).'">'.$i.'</span>';
		$popoverContent = '<div class="cal-events-wrapper" style="display:none;">';
		
		$sectionAliases = array();
		foreach ($events[$i] as $event):
			if ($event->date_end >= date('Y-m-d H:i:s')):
				$dateContentUrl = Yii::app()->controller->createUrl('/event/default/detail', array('section_id'=>$event->section_id, 'n'=>$event->title_url));
			else:
				$dateContentUrl = Yii::app()->controller->createUrl('/event/default/detail', array('section_id'=>$event->section_id, 'archives'=>'archives', 'n'=>$event->title_url));
			endif;
			$popoverContent .= CHtml::link(CHtml::encode($event->title), $dateContentUrl, array('class'=>'event-section-id-'.$event->section_id));
		endforeach;
		
		$popoverContent .= "</div>";
		
		$dateContent .= $popoverContent;
	else:
		$dateContent = $i;
	endif;
	
	?>

	<td align="center" class="calendar-date">
	<?php if ($dateIsCurrentMonth && $day == $i): ?>
		<strong><?php echo $dateContent; ?></strong>
	<?php else: 
			echo $dateContent;
		endif; ?>
	</td>
	
	<?php
	$weekDay++;
endfor; 

if ($extraCells < 7): ?>
	<td colspan="<?php echo $extraCells; ?>">&nbsp;</td>
<?php endif; ?>
	
</tr>
</table>

<div class="spinner"></div>

</div>

<?php Yii::app()->clientScript->registerScript('eventsCalendarWidgetBindings'.$this->id, "
	
	function eventsCalendarWidgetBindPopover".$this->id."() {
		$('#eventsCalendarWidget-".$this->id." .calendar-table .popover-trigger').popover({
			html:true, 
			placement:'right', 
			trigger:'click',
			content:function(){
				return $(this).next('#eventsCalendarWidget-".$this->id." .cal-events-wrapper').html();
			}
		});
	}
	
	function eventsCalendarWidgetHideSpinner".$this->id."() {
		$('#eventsCalendarWidget-".$this->id." .spinner').hide();
	}
	
	function eventsCalendarWidgetDisplaySpinner".$this->id."() {
		$('#eventsCalendarWidget-".$this->id." .spinner').show();
	}
	
	var eventsCalendarWidgetYear".$this->id." = ".$year.";
	var eventsCalendarWidgetMonth".$this->id." = ".$month.";
	var eventsCalendarWidgetDay".$this->id." = ".$day.";
	var processingCalendar".$this->id." = false;

	$('#eventsCalendarWidget-".$this->id."').on('click', '.calendar-month a:eq(0)', function(){
		if (!processingCalendar".$this->id.") {
			eventsCalendarWidgetDisplaySpinner".$this->id."();
			var date = (eventsCalendarWidgetYear".$this->id."-1)+'-'+eventsCalendarWidgetMonth".$this->id."+'-'+eventsCalendarWidgetDay".$this->id.";
			
			processingCalendar".$this->id." = true;
			$.post('".$_SERVER['REQUEST_URI']."', {eventsCalendarWidgetDate:date".($this->sectionId !== null ? ', eventsCalendarWidgetSectionId:'.$this->sectionId : '')."}, function(data) {
				$('#eventsCalendarWidget-".$this->id."').html($('#events-calendar-widget-render #eventsCalendarWidget-".$this->id."', $('<div>' + data + '</div>')).html());
				eventsCalendarWidgetYear".$this->id." -= 1;
				processingCalendar".$this->id." = false;
				eventsCalendarWidgetBindPopover".$this->id."();
				eventsCalendarWidgetHideSpinner".$this->id."();
			});
		}
	});
	$('#eventsCalendarWidget-".$this->id."').on('click', '.calendar-month a:eq(1)', function(){
		if (!processingCalendar".$this->id.") {
			eventsCalendarWidgetDisplaySpinner".$this->id."();
			if (eventsCalendarWidgetMonth".$this->id." - 1 == 0) {
				var date = (eventsCalendarWidgetYear".$this->id."-1)+'-12-'+eventsCalendarWidgetDay".$this->id.";
			} else {
				var date = eventsCalendarWidgetYear".$this->id."+'-'+(eventsCalendarWidgetMonth".$this->id."-1)+'-'+eventsCalendarWidgetDay".$this->id.";
			}
			
			processingCalendar".$this->id." = true;
			$.post('".$_SERVER['REQUEST_URI']."', {eventsCalendarWidgetDate:date".($this->sectionId !== null ? ', eventsCalendarWidgetSectionId:'.$this->sectionId : '')."}, function(data) {
				$('#eventsCalendarWidget-".$this->id."').html($('#events-calendar-widget-render #eventsCalendarWidget-".$this->id."', $('<div>' + data + '</div>')).html());
				
				if (eventsCalendarWidgetMonth".$this->id." - 1 == 0) {
					eventsCalendarWidgetYear".$this->id." -= 1;
					eventsCalendarWidgetMonth".$this->id." = 12;
				} else {
					eventsCalendarWidgetMonth".$this->id." -= 1;
				}
				processingCalendar".$this->id." = false;
				eventsCalendarWidgetBindPopover".$this->id."();
				eventsCalendarWidgetHideSpinner".$this->id."();
			});
		}
	});
	$('#eventsCalendarWidget-".$this->id."').on('click', '.calendar-month a:eq(2)', function(){
		if (!processingCalendar".$this->id.") {
			eventsCalendarWidgetDisplaySpinner".$this->id."();
			if (eventsCalendarWidgetMonth".$this->id." + 1 == 13) {
				var date = (eventsCalendarWidgetYear".$this->id."+1)+'-01-'+eventsCalendarWidgetDay".$this->id.";
			} else {
				var date = eventsCalendarWidgetYear".$this->id."+'-'+(eventsCalendarWidgetMonth".$this->id."+1)+'-'+eventsCalendarWidgetDay".$this->id.";
			}
			
			processingCalendar".$this->id." = true;
			$.post('".$_SERVER['REQUEST_URI']."', {eventsCalendarWidgetDate:date".($this->sectionId !== null ? ', eventsCalendarWidgetSectionId:'.$this->sectionId : '')."}, function(data) {
				$('#eventsCalendarWidget-".$this->id."').html($('#events-calendar-widget-render #eventsCalendarWidget-".$this->id."', $('<div>' + data + '</div>')).html());
				
				if (eventsCalendarWidgetMonth".$this->id." + 1 == 13) {
					eventsCalendarWidgetYear".$this->id." += 1;
					eventsCalendarWidgetMonth".$this->id." = 1;
				} else {
					eventsCalendarWidgetMonth".$this->id." += 1;
				}
				processingCalendar".$this->id." = false;
				eventsCalendarWidgetBindPopover".$this->id."();
				eventsCalendarWidgetHideSpinner".$this->id."();
			});
		}
	});
	$('#eventsCalendarWidget-".$this->id."').on('click', '.calendar-month a:eq(3)', function(){
		if (!processingCalendar".$this->id.") {
			eventsCalendarWidgetDisplaySpinner".$this->id."();
			var date = (eventsCalendarWidgetYear".$this->id."+1)+'-'+eventsCalendarWidgetMonth".$this->id."+'-'+eventsCalendarWidgetDay".$this->id.";
	
			processingCalendar".$this->id." = true;
			$.post('".$_SERVER['REQUEST_URI']."', {eventsCalendarWidgetDate:date".($this->sectionId !== null ? ', eventsCalendarWidgetSectionId:'.$this->sectionId : '')."}, function(data) {
				$('#eventsCalendarWidget-".$this->id."').html($('#events-calendar-widget-render #eventsCalendarWidget-".$this->id."', $('<div>' + data + '</div>')).html());
				eventsCalendarWidgetYear".$this->id." += 1;
				processingCalendar".$this->id." = false;
				eventsCalendarWidgetBindPopover".$this->id."();
				eventsCalendarWidgetHideSpinner".$this->id."();
			});
		}
	});
	
	eventsCalendarWidgetBindPopover".$this->id."();
", CClientScript::POS_READY); ?>