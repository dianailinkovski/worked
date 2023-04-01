<?php

echo link_tag('css/jquery-ui.custom.css');
$violation_emails = 0;
$summeries_emails = 0;
?>
<script type="text/javascript">

  var VIOLATION_EMAILS = 0;
  var SUMMARIES_EMAILS = 0;
	
  function render_violation_email(){
		
    if($("#violation_email_html > div").size() > 0)
      VIOLATION_EMAILS = $("#violation_email_html > div").size();
    else
      VIOLATION_EMAILS = 1;
    $('#violation_email_html').show();
    //$('#violation_add_more').show();
    if($('#violation_email_html').children().size() == 0) {
      var html_template = 
        '<div class="row_dat">'+
        '<div class="lbel" style="width: 155px !important;">Add Email Address </div>'+
        '<div class="lbl_inpuCnt">'+
        '<input type="text" class="account_med" name="violation_email_1" id="violation_email_1" /></div>'+
        '<div class="accordian_open" style="float:left;margin:0px;"><a href="javascript:void(0);" onclick="render_another_violation_emails();" > </a></div>'+
        '<div  class="clear"></div>'+
        '</div>';
      $('#violation_email_html').append(html_template);
    }
  }
	
  function hide_violation_emails(){

    VIOLATION_EMAILS = $("#violation_email_html > div").size();
    $('#violation_email_html').hide();
    //$('#violation_add_more').hide();
    //alert(VIOLATION_EMAILS);
    //$('#violation_email_html').html('');
    //$('#violation_add_more').css('display', 'none');
  }
	
  function render_another_violation_emails(){

    VIOLATION_EMAILS++;
    $('#violation_email_html').show();
    var html_template = 
      '<div class="row_dat" id="email_'+VIOLATION_EMAILS+'">'+
      '<div class="lbel" style="width: 155px !important;">Another Email Address </div>'+
      '<div class="lbl_inpuCnt">'+
      '<input type="text" class="account_med" name="violation_email_' + VIOLATION_EMAILS + '" id="violation_email_' + VIOLATION_EMAILS + '"  /></div>'+
      '<div class="accordian_delete" style="float:left;margin:0px;"><a href="javascript:void(0)" style="cursor:pointer;" onClick="remove_email_violation(\'email_'+VIOLATION_EMAILS+'\')"></a></div>'+
      '<div  class="clear"></div>'+
      '</div>';
    $('#violation_email_html').append(html_template);
  }
	
  function remove_email_violation(id)
  {
    $('#'+id).remove();
    VIOLATION_EMAILS--;
		
  }
	
	
  function render_summaries_email(){
    if($("#summaries_email_html > div").size() > 0)
      SUMMARIES_EMAILS = $("#summaries_email_html > div").size();
    else
      SUMMARIES_EMAILS = 1;

    $('#summaries_email_html').show();
    $('#summaries_add_more').show();
    if($('#summaries_email_html').children().size() == 0) {
      var html_template = 
        '<div class="row_dat">'+
        '<div class="lbel" style="width: 155px !important;">Add Email Address </div>'+
        '<div class="lbl_inpuCnt">'+
        '<input type="text" class="account_med" name="summaries_email_1" id="summaries_email_1" /></div>'+
        '<div class="accordian_open" style="float:left;margin:0px;"><a href="javascript:void(0)" onclick="render_another_summaries_emails();"> </a></div>'+
        '<div  class="clear"></div>'+
        '</div>';
      $('#summaries_email_html').append(html_template);
    }
  }
	
  function hide_summaries_emails(){

    SUMMARIES_EMAILS = $("#summaries_email_html > div").size();
    $('#summaries_email_html').hide();
    $('#summaries_add_more').hide();
    //$('#summaries_email_html').html('');
    //$('#summaries_add_more').css('display', 'none');
  }
	
  function render_another_summaries_emails(){
    SUMMARIES_EMAILS++;
    $('#summaries_email_html').show();
    var html_template = 
      '<div class="row_dat" id="sumarries_'+SUMMARIES_EMAILS+'">'+
      '<div class="lbel" style="width: 155px !important;">Another Email Address </div>'+
      '<div class="lbl_inpuCnt">'+
      '<input type="text" class="account_med" name="summaries_email_' + SUMMARIES_EMAILS + '" id="summaries_email_' + SUMMARIES_EMAILS + '"  /></div>'+
      '<div class="accordian_delete" style="float:left;margin:0px;"><a href="javascript:void(0)" style="cursor:pointer;" onClick="remove_email_summaries(\'sumarries_'+SUMMARIES_EMAILS+'\')"></a></div>'+
      '<div  class="clear"></div>'+
      '</div>';
    $('#summaries_email_html').append(html_template);
  }
	
	
  function remove_email_summaries(id)
  {
    $('#'+id).remove();
    SUMMARIES_EMAILS--;
		
  }
  function has_logo(id){
    if($.trim($('#'+id).val()) == "" ){
      inlineMsg(id,'<strong>Error</strong><br />Please Select a Logo.',2);
      return false;
    }
  }
	
  //////Remove Emails from The Db///////////////////////////////////
  function remove_email_summaries_db(id, html_id, email_to_remove)
  {
    var url = base_url+"account/remove_emails_db";
    var data = "id="+id+"&email="+email_to_remove;
    $.ajax({
      type: 'POST',
      data: data,
      url: url,
      cache: false,
      dataType: 'json',
      success: function(response)
      {
        if(response.message == 'success')
        {
          var retID = html_id.split('_');
          $("#"+html_id).remove();
          if(retID[0] == 'violation')
          {
            VIOLATION_EMAILS--;
          }else
          {
            SUMMARIES_EMAILS--;
          }
        }
      }
    });
  }
  ///////////////////////////////////////////////////////////////////
  $(function() {
    $('#datetime').datepicker({ dateFormat: 'yy-mm-dd',maxDate:'yy-mm-dd' });
  });

  $(document).ready(function(e)
  {

    $( "#member_name" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: base_url+"account/get_merchants_names/"+request.term,
          dataType: "json",
          data: {
          },
          success: function(items) {

            if(!items || items.length == 0) {
              if(typeof auto_complete_no_result != 'undefined')
                auto_complete_no_result();
              return false;
            }
            response($.map( items, function( item ) {
              return {
                label: item.username,
                value: item.username,
                Id: item.Id
              }
            }));
          }
        });
      },
      minLength: 1,
      select: function( event, ui ) {
        var callback_func = '';
        $( "#member_name" ).val(ui.item.value);
        $( "#member_id" ).val(ui.item.Id);	  
      }
    });
    
  });

</script>

<div class="content_dashboard">
  <form action="<?php echo base_url(); ?>account/save_notifications_settings" method="post" id="notifications_form" class="" enctype="multipart/form-data">
    <div class="heading_section  clearfix">
      <div class="head">Notifications</div>
      <div class="accordian_close" id="display_4" onclick="show_divs('content_4', 'display_4',   'accordian_open', 'accordian_close');"><a href="javascript:void(0)"></a></div>
    </div>	
    <div class="disp_content_white" id="content_4" >
      <div class="row_dat" style="float:left; width:100%;">
        <div class="message" id="message_notifications">
        </div>
      </div>
      <div class="row_dat">
        <div class="lbel" style="width: 200px !important;"><b>Turn Price Violation emails?</b> </div>
        <div class="lbl_inpuCnt">
          <input type="radio" name="violation_emails" value="Yes" onclick="render_violation_email()"  <?php if (count($violation_info) > 0) echo 'checked'; ?>/> Yes &nbsp;&nbsp;
          <input type="radio" name="violation_emails" value="No" onclick="hide_violation_emails()" <?php if (count($violation_info) == 0) echo 'checked'; ?>/> No</div>
        <div  class="clear"></div>
      </div>

      <div id="violation_email_html" style="width:100%;">
        <?php
        if (count($violation_info) > 0) {
          if (trim($violation_info[0]->email) != '') {
            $email_list = explode(',', $violation_info[0]->email);
            $violation_emails = 0;
            for ($i = 0; $i < count($email_list) - 1; $i++) {
              $title = '';
              $link = '';
              $violation_emails = ($i + 1);
              if ($i == 0) {
                $title = 'Add Email Address';
                $link = '<div class="accordian_open" style="float:left;margin:0px;"><a href="javascript:void(0);" onclick="render_another_violation_emails();" > </a></div>';
              } else {
                $title = 'Another Email Address';
                $link = '<div class="accordian_delete" style="float:left;margin:0px;"><a onclick="remove_email_summaries_db(\'' . $violation_info[0]->id . '\', \'violation_' . ($i + 1) . '\',\'' . trim($email_list[$i]) . '\')" style="cursor: pointer;" href="javascript:void(0)"></a></div>';
              }
              ?>
              <div class="row_dat" id="violation_<?php echo $i + 1; ?>">
                <div class="lbel" style="width: 155px !important;"><?php echo $title; ?> 
                </div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="account_med" name="violation_email_<?php echo $i + 1; ?>" id="violation_email_<?php echo $i + 1; ?>"   value="<?php echo trim($email_list[$i]); ?>" />
                </div>
                <?php echo $link; ?>
                <div  class="clear"></div>
              </div>
              <?php
            }
          }
        }
        ?>
      </div>
      <div class="row_dat">
        <div class="lbel" style="width: 200px !important;"><b>Send daily summaries? </b></div>
        <div class="lbl_inpuCnt">
          <input type="radio" name="summaries_emails" value="Yes" onclick="render_summaries_email()" <?php if (count($summaries_info) > 0)
          echo 'checked'; ?>/> Yes &nbsp;&nbsp;
          <input type="radio" name="summaries_emails" value="No" onclick="hide_summaries_emails()" <?php if (count($summaries_info) == 0)
          echo 'checked'; ?>/> No</div>
        <div  class="clear"></div>
      </div>

      <div id="summaries_email_html" style="width:100%;">
        <?php
        if (count($summaries_info) > 0) {
          if (trim($summaries_info[0]->email) != '') {
            $email_list = explode(',', $summaries_info[0]->email);
            $summeries_emails = 0;
            for ($i = 0; $i < count($email_list) - 1; $i++) {
              $title = '';
              $summeries_emails = ($i + 1);
              $link = '';
              if ($i == 0) {
                $title = 'Add Email Address';
                $link = '<div class="accordian_open" style="float:left;margin:0px;" style="float:left"><a href="javascript:void(0);" onclick="render_another_summaries_emails();" > </a></div>';
              } else {
                $title = 'Another Email Address';
                $link = '<div class="accordian_delete" style="float:left;margin:0px;"><a onclick="remove_email_summaries_db(\'' . $summaries_info[0]->id . '\', \'sumarries_' . ($i + 1) . '\',\'' . trim($email_list[$i]) . '\')" style="cursor: pointer;" href="javascript:void(0)"></a></div>';
              }
              ?>
              <div class="row_dat" id="sumarries_<?php echo $i + 1; ?>">
                <div class="lbel" style="width: 155px !important;"><?php echo $title; ?></div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="account_med" name="summaries_email_<?php echo $i + 1; ?>" id="summaries_email_<?php echo $i + 1; ?>"   value="<?php echo trim($email_list[$i]); ?>" /></div>
              <?php echo $link; ?>
                <div  class="clear"></div>
              </div>
              <?php
            }
          }
        }
        ?>

      </div>
      <div class="row_dat">
        <input type="button" class="btn_save" value="" name="upload_logo" onclick="save_notifications_info(this.form);" />
        <div  class="clear"></div>
      </div>
    </div>
  </form>
</div>

<div class="content_dashboard" style="border-bottom:none;">
  
    <div class="heading_section  clearfix">
      <div class="head ">Team Management</div>
      <div class="accordian_close"  id="display_5" onclick="show_divs('content_5', 'display_5',   'accordian_open', 'accordian_close');"><a href="javascript:void(0)"></a></div>
    </div>
	
    <div class="content_accordian">
      <div class="disp_content_white" id="content_5" style="padding:0px; border-bottom:none;">
        <div class="row_dat" style="float:left; width:100%;">
          <div class="message" id="member_added_notifications" style="padding-left:25px"></div>        
        </div>
        <div class="row_dat" style="width:100%;float:left;padding-left:36px; padding-bottom:0px;">
          Manage Team: <a href="<?php echo $this->config->item('gsession_base_url'); ?>account/manage_teams" target="_blank" style="color:blue">Click here</a>
        </div>
        
        <br />
        <br />
       
        <div class="row_head" style="width:1172px;">
          <div class="chk_cnt">&nbsp;</div>
          <div class="team_member_name">Name</div>
          <div class="rights">Email</div>
          <div class="action">Rights</div>
          <div class="clear"></div>
        </div>
        
        <div id="team_table" >
          <?php 
              if (count($team_members_info) > 0) {
                foreach($team_members_info as $member) {
           ?>
            <div style="width:1172px;display:block" id="team_1" class="row_d">
              <div class="chk_cnt">&nbsp;</div>
              <div class="team_member_name"><?php echo $member['name']?></div>
              <div class="rights"><?php echo $member['email']?></div>
              <div class="action"><?php echo ($member['rights'] == 1) ? 'View Reports' : 'Owner'?></div>              
              <div class="clear"></div>          
            </div>          
          <?php 
                }
          } else { ?>
            <div style="width:1172px;display:block" class="row_d">
              <div class="chk_cnt">&nbsp;</div>
              <div class="team_member_name">No Record</div>    
              <div class="rights">&nbsp;</div>
              <div class="action">&nbsp;</div>              
              <div class="clear"></div>          
            </div>           
          <?php } ?>
        </div>
      </div>
    </div>

</div>

<script type="text/javascript">
  //refresh_team_table();
  VIOLATION_EMAILS = <?php echo $violation_emails; ?>;
  SUMMARIES_EMAILS = <?php echo $summeries_emails; ?>;
</script>