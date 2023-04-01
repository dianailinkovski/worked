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
            <i class="fa fa-angle-right"></i> Export Merchant Data
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
            Export Merchant Data
        </h3>
        
        <p style="width: 60%;">
            The merchants.csv file contains all of the data kept on your tracked merchants. Edit this file to maintain preferences
            and info on each of your merchants. To get started click on the Export Merchant Data button to download the file for editing.
        </p>
        
        <p>
            CSV file columns:
        </p>
        
        <ul class="merchant-csv-fields">
            <li>
                <b>Merchant ID</b>
            </li>
            <li>
                <b>Merchant Name</b>   
            </li>
            <li>    
                <b>Merchant Marketplace</b>
            </li>
            <li>    
                <b>Email</b>
            </li>
            <li>
                <b>Phone</b>
            </li>
            <li>                
                <b>Fax</b>
            </li>
            <li>                
                <b>Address 1</b>
            </li>
            <li>                
                <b>Address 2</b>
            </li>
            <li>                
                <b>City</b>
            </li>
            <li>                
                <b>State</b>
            </li>
            <li>                
                <b>Zip</b>
            </li>
            <li>                
                <b>Website</b>
            </li>
            <li>                
                <b>Send Violation Notifications</b> - Values: y, n - Notes: y = yes, n = no
            </li>
            <li>                
                <b>Notification Template to Use</b> - Values: 1, 2 - Notes: use 1 for "known seller" template, or use 2 for "unknown seller" template
            </li>
            <li>                
                <b>Send to Primary Contact(s)</b> - Values: y, n - Notes: y = yes, n = no
            </li>
            <li>                
                <b>Send to Account Rep(s)</b> - Values: y, n - Notes: y = yes, n = no
            </li>
            <li>                
                <b>Send to CC Address(es)</b> - Values: y, n - Notes: y = yes, n = no
            </li>
        </ul>
        
        <p>
            <a class="btn btn-success" target="_blank" href="/merchants/export_merchants_file">Download Merchant Data</a>
        </p>
        
        <hr />
        
        <h3>
            Export Merchant Contact Data
        </h3>
        
        <p style="width: 60%;">
            The merchant_contacts.csv file contains data on all of your contacts that you have added and connected to each of your tracked merchants.
            To add a new contact, insert a new row into the file and leave the Contact ID field blank.
        </p>
        
        <p>
            CSV file columns:
        </p>        
        
        <ul class="merchant-csv-fields">
            <li>
                <b>Contact ID</b> - Notes: no need to edit this value as we use it internally to keep track of your contacts. Leave this blank if adding
                a new contact row.
            </li>
            <li>
                <b>Merchant ID</b>
            </li>
            <li>
                <b>Merchant Name</b>    
            </li>
            <li>    
                <b>Merchant Marketplace</b>
            </li>
            <li>
                <b>First Name</b>
            </li>
            <li>
                <b>Last Name</b>
            </li>
            <li>    
                <b>Email</b>
            </li>
            <li>
                <b>Phone Number</b>
            </li>
            <li>
                <b>Contact Type</b> - Values: 1, 2, 3 - Notes: Please use one of the following values to indicate contact type: 1 = Primary Contact, 2 = Account Rep, 3 =  CC Address
            </li>    
        </ul>        
        
        <p>
            <a class="btn btn-success" target="_blank" href="/merchants/export_contacts_file">Download Merchant Contact Data</a>
        </p>
                    
    </div>
</div>


