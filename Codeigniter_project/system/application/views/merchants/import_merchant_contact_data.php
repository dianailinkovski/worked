<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Import Merchant Contact Data</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility
            <i class="fa fa-angle-right"></i> <a href="/merchants">Merchant Info</a>
            <i class="fa fa-angle-right"></i> <a href="/merchants/import">Merchant Import</a>
            <i class="fa fa-angle-right"></i> Merchant Contact Data
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
        
        <?php if (validation_errors() != ''): ?>    
            <div class="alert alert-danger" role="alert">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($form_error_msgs)): ?>
            <div class="alert alert-danger" role="alert">
            	<?php foreach ($form_error_msgs as $error_msg): ?>
                    <p><?php echo $error_msg; ?></p>
            	<?php endforeach; ?>
            </div>    
        <?php endif; ?>                
        
        <h3>
            Import Merchant Contact Data
        </h3>

        <form action="/merchants/import_merchant_contact_data" method="post" enctype="multipart/form-data">

            <p>
                Use the following field to select and upload your merchant contact data CSV file. With each upload, we will version the data
                so that it can be reverted back to at any time.
            </p>        
            
            <hr />
        
            <p>
                <label for="csv_file"><b>Select CSV file:</b></label><br />
                <input type="file" name="csv_file" id="csv_file" />
            </p>
            
            <hr />
            
            <p>
                <input class="btn btn-success" value="Import Merchant Contact Data" type="submit" name="import_merchant_cotact_data_submit" />
            </p>

        </form>
                    
    </div>
</div>


