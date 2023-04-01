<!-- start bydate -->

<?php
if (isset($date_from))
{
    if (is_numeric($date_from))
    {
        $date_from = date('Y-m-d', $date_from);
    }
}
if (isset($date_to))
{
    if (is_numeric($date_to))
    {
        $date_to = date('Y-m-d', $date_to);
    }
}
?>

<section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="date_range">
    <div class="leftCol">
        <label>Date Range</label>
    </div>
    <div class="rightCol">
        <div class="inputContainer">
            <input class="start dateInput" value="<?= $time_frame ? 'Start' : $date_from ?>" id="date_from" name="date_from" max="<?= date('Y-m-d'); ?>"/>
            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Start Date" id="date_from_a" width="24" height="24" class="imgIcon" />
        </div>
        <div class="inputContainer">
            <input class="start dateInput" value="<?= $time_frame ? 'Stop' : $date_to ?>" id="date_to" name="date_to" max="<?= date('Y-m-d'); ?>"/>
            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Stop Date" id="date_to_a" width="24" height="24" class="imgIcon" />
        </div>
        <div class="inputContainer inputDivider"><b>or</b></div>
        <div class="inputContainer reports_radio">
            <input type="radio" name="time_frame" id="tf24" value="24" <?= ($time_frame=='24') ? 'checked="checked"' : '' ?>><label for="tf24">Last 24 Hours</label>
            <input type="radio" name="time_frame" id="tf7" value="7" <?= $time_frame == '7' ? 'checked="checked"' : '' ?>><label for="tf7">Last 7 Days</label>
            <input type="radio" name="time_frame" id="tf30" value="30" <?= $time_frame == '30' ? 'checked="checked"' : '' ?>><label for="tf30">Last 30 Days</label>
        </div>
    </div>
</section>
<!-- end bydate -->