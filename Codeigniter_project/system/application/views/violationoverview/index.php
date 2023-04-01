<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violation Dashboard</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">Violation Dashboard</a>
        </div>
        
        <?php if ($this->success_msg != ''): ?>
        	<div class="alert alert-success" role="alert">
        		<?php echo $this->success_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if ($this->error_msg != ''): ?>
        	<div class="alert alert-danger" role="alert">
        		<?php echo $this->error_msg; ?>
        	</div>
        <?php endif; ?>
    
        <section id="repricingOverview" class="clear"><?= $this->load->view('components/overview', '', TRUE) ?></section>
    </div>
</div> 

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>