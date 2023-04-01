<?php //$this->load->view('components/overview_header', array('report_name' => 'Change Password:'), TRUE); ?>

<div style="padding: 15px;">

    <?php if ($error_msg != ''): ?>
    	<div class="warning-msg">
    		<?php echo $error_msg; ?>
    	</div>
    <?php endif; ?>
    
    <?php if (validation_errors() != ''): ?>	
    	<div class="error-msg">
    		<?php echo validation_errors(); ?>
    	</div>
    <?php endif; ?>

    <h2 id="terms-header">
        TrackStreet - Terms and Conditions
    </h2>
    
    <div id="terms-area">
        <div id="terms" style="padding: 8px;">
            <?php echo $terms['terms']; ?>
        </div>
        <div id="accept-form">
            <form action="/signup/confirm/<?php echo $user_uuid; ?>" method="post">
                <p>
                    <span style="color: red;">Terms Version <?php echo $terms['version']; ?>, updated on <?php echo $terms['date']; ?></span>
                </p>
                <p>
                    <input type="checkbox" name="terms_accept" value="accept" /> I accept
                </p>
                <p>
                    <input class="btn btn-success" type="submit" value="Accept" />
                </p>
            </form>
        </div>
    </div>
    
</div>    