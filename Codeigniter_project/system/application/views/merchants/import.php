<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchant Data Export</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility
            <i class="fa fa-angle-right"></i> <a href="/merchants">Merchant Info</a>
            <i class="fa fa-angle-right"></i> Import Merchant Data
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
        
        <h3>
            Import Merchant Data
        </h3>
        
        <p style="width: 60%;">
            Import your edited merchant CSV data file that you downloaded beforehand.
        </p>
        
        <p>
            <a class="btn btn-success" href="/merchants/import_merchant_data">Import Merchant Data</a>
        </p>
        
        <hr />
        
        <h3>
            Import Merchant Contact Data
        </h3>
        
        <p style="width: 60%;">
            Import your edited merchant contact CSV data file that you downloaded beforehand.
        </p>       
        
        <p>
            <a class="btn btn-success" href="/merchants/import_merchant_contact_data">Import Merchant Contact Data</a>
        </p>
                    
    </div>
</div>


