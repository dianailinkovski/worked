<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Saved Reports</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/savedreports">Saved Reports</a>
        </div>
            
				<section id="savedReports" class="clear"><?=$this->load->view('savedreports/_reports_list', '', TRUE)?></section>
				
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce807605e46" data-unique="55ce807605e46" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce807605e46"></script>
<?php endif; ?>    