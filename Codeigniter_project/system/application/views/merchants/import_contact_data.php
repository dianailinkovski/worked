<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchants</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <h3>
            Updated Merchants
        </h3>
    
        <?php if (empty($updated_merchants)): ?>
            <p>
                No merchants were updated.
            </p>
        <?php else: ?>
            <table class="table table-bordered table-striped table-success table-responsive" id="merchant-auto-complete">
                <thead>
                    <tr>
                	      <th align="left">Website Name</th>  
                		    <!-- <th align="left">Contact Page</th> -->
                		    <th align="left">Phone</th>
                		    <th align="left">Fax</th>
                		    <th align="left">Address</th>
                		    <th align="left">Address 2</th>
                		    <th align="left">City</th>
                		    <th align="left">State</th>
                		    <th align="left">Zip</th>
                		    <th align="left">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($updated_merchants as $merchant): ?>
                        <tr>
                            <td><?php echo $merchant[0]; ?></td>
                            <!-- <td><?php echo $merchant[1]; ?></td> -->
                            <td><?php echo $merchant[2]; ?></td>
                            <td><?php echo $merchant[3]; ?></td>
                            <td><?php echo $merchant[4]; ?></td>
                            <td><?php echo $merchant[5]; ?></td>
                            <td><?php echo $merchant[6]; ?></td>
                            <td><?php echo $merchant[7]; ?></td>
                            <td><?php echo $merchant[8]; ?></td>
                            <td><?php echo $merchant[9]; ?></td>
                        </tr>                                
                    <?php endforeach; ?>
                </tbody>
            </table>            
        <?php endif; ?>
        
        <hr />
 
        <h3>
            Inserted Merchants
        </h3>
    
        <?php if (empty($inserted_merchants)): ?>
            <p>
                No merchants were created.
            </p>
        <?php else: ?>
            <table class="table table-bordered table-striped table-success table-responsive" id="merchant-auto-complete">
                <thead>
                    <tr>
                	      <th align="left">Website Name</th>  
                		    <!-- <th align="left">Contact Page</th> -->
                		    <th align="left">Phone</th>
                		    <th align="left">Fax</th>
                		    <th align="left">Address</th>
                		    <th align="left">Address 2</th>
                		    <th align="left">City</th>
                		    <th align="left">State</th>
                		    <th align="left">Zip</th>
                		    <th align="left">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inserted_merchants as $merchant): ?>
                        <tr>
                            <td><?php echo $merchant[0]; ?></td>
                            <!-- <td><?php echo $merchant[1]; ?></td> -->
                            <td><?php echo $merchant[2]; ?></td>
                            <td><?php echo $merchant[3]; ?></td>
                            <td><?php echo $merchant[4]; ?></td>
                            <td><?php echo $merchant[5]; ?></td>
                            <td><?php echo $merchant[6]; ?></td>
                            <td><?php echo $merchant[7]; ?></td>
                            <td><?php echo $merchant[8]; ?></td>
                            <td><?php echo $merchant[9]; ?></td>
                        </tr>                                
                    <?php endforeach; ?>
                </tbody>
            </table>            
        <?php endif; ?> 
        
    </div>
</div>            