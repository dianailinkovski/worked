<div id="notify_dialog" class="modalWindow dialog">
    <div class="dlg-content">
        <h2>Merchant Violation Notifications</h2>
        <?php if (empty($smtp)): ?>
            <p class="note">You have not configured your mail settings. Violator notifications will not be sent until mail settings are configured.</p>
            <p class="set_mail_settings"><a href="<?php echo site_url('settings/edit_store') ?>">Configure your mail settings</a></p>
        <?php endif; ?>
        <div id="notify_message_fb" style="display:none;"></div>
        <form id="violator_notification_form" action="<?= site_url('myproducts/report_merchant/') ?>" method="post">
            <div class="row_dat">
                <div class="lbel"><label for="notify_active">Active:</label></div>
                <div class="lbl_inpuCnt">
                    <input type="checkbox" id="notify_active" name="active" value="1" title="Send notifications to this seller if it has violations." <?php echo (!empty($violator_notification['active']) AND $violator_notification['active'] == '1') ? 'checked="checked"' : '' ?> />
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_title">Notification Title:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="text" id="notify_title" name="title" value="<?php echo!empty($violator_notification['title']) ? $violator_notification['title'] : '' ?>" maxlength="255" title="The notification's title." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <h3>Notify</h3>
            <div class="row_dat">
                <div class="lbel"><label for="notify_email_to">Seller Email:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="email" id="notify_email_to" name="email_to" value="<?php echo!empty($violator_notification['email_to']) ? $violator_notification['email_to'] : '' ?>" required="required" maxlength="255" title="The email address to whom the violation notification will be sent." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_name_to">Seller Name:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="text" id="notify_name_to" name="name_to" value="<?php echo!empty($violator_notification['name_to']) ? $violator_notification['name_to'] : (!empty($original_name) ? $original_name : '') ?>" maxlength="255" title="The name of the seller." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <h3>Contact Information</h3>
            <div class="row_dat">
                <div class="lbel"><label for="notify_email_from">From:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="email" id="notify_email_from" name="email_from" value="<?php echo!empty($violator_notification['email_from']) ? $violator_notification['email_from'] : $default_email_from ?>" required="required" maxlength="255" title="The reply email." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_name_from">Company:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="text" id="notify_name_from" name="name_from" value="<?php echo!empty($violator_notification['name_from']) ? $violator_notification['email_from'] : getBrandName($this->session->userdata("merchant_store_id")); ?>" required="required" maxlength="255" title="Your company's name." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_phone">Phone #:</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="tel" id="notify_phone" name="phone" value="<?php echo!empty($violator_notification['phone']) ? $violator_notification['phone'] : '' ?>" required="required" maxlength="50" title="Your company's contact info." style="width:100%" />
                </div>
                <div class="clear"></div>
            </div>
            <h3>1st Warning Email</h3>
            <div class="row_dat">
                <div class="lbel"><label for="notify_days_to_warning1">Notify after</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="number" id="notify_days_to_warning1" name="days_to_warning1" value="<?php echo!empty($violator_notification['days_to_warning1']) ? $violator_notification['days_to_warning1'] : 1 ?>" required="required" size="4" maxlength="4" title="The number of days in violation till the first warning is sent." min="0" max="9999" /> day(s) in violation
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_warning1_repetitions">Repeat for</label></div>
                <div class="lbl_inpuCnt">
                    <input type="number" id="notify_warning1_repetitions" name="warning1_repetitions" value="<?php echo!empty($violator_notification['warning1_repetitions']) ? $violator_notification['warning1_repetitions'] : 1 ?>" required="required" size="4" maxlength="4" title="The number of times to give the first warning." min="1" max="9999" /> day(s)
                </div>
                <div class="clear"></div>
            </div>
            <h3>2nd Warning Email</h3>
            <div class="row_dat">
                <div class="lbel"><label for="notify_days_to_warning2">Notify</label></div>
                <div class="lbl_inpuCnt" style="width:300px">
                    <input type="number" id="notify_days_to_warning2" name="days_to_warning2" value="<?php echo!empty($violator_notification['days_to_warning2']) ? $violator_notification['days_to_warning2'] : 1 ?>" required="required" size="4" maxlength="4" title="The number of days in violation after the first warning till the second warning is sent." min="0" max="9999" /> day(s) after end of 1st warning
                </div>
                <div class="clear"></div>
            </div>
            <div class="row_dat">
                <div class="lbel"><label for="notify_warning2_repetitions">Repeat for</label></div>
                <div class="lbl_inpuCnt" >
                    <input type="number" id="notify_warning2_repetitions" name="warning2_repetitions" value="<?php echo!empty($violator_notification['warning2_repetitions']) ? $violator_notification['warning2_repetitions'] : 1 ?>" required="required" size="4" maxlength="4" title="The number of times to give the second warning." min="1" max="9999" /> day(s)
                </div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
</div>