<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Category Tree</strong>
        </h3>
    </div>
    
    <div class="panel-body">

        <?=$this->load->view('category/_breadcrumbs', '', TRUE);?>
        
        	<div id="jsPage" >
        		
        	<?=$this->load->view('category/_status_msg', '', TRUE);?>
        	
            <form name="PageForm" id="PageForm" method="post" action="<?php echo $BASE_URL;?>category/move/srcCatId=<?php echo $CATEGORY['id'];?>" > 
            <input type="hidden" name="srcCatId" value="<?php echo $CATEGORY['id'];?>" />
            <input type="hidden" name="do" value="save" />
        
            <table cellpadding="0" cellspacing="0" class="formTable" >
            	<tr>
                <td colspan="3" align="center">
        			<br>
                </td>
              </tr>
            	<tr>
                <td>Move this category:</td>
                <td>
        	        <b><?php echo $CATEGORY['name']?></b>
                </td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td style="vertical-align: top">Destination category:</td>
                <td>
                <select name="dstCatId" size="6" style="height:280px !important;width:300px !important;">
        	        <option value='-1'>ROOT</option>
        			<?php echo $MENU;?>
                </select>
                </td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                  <input type="submit" value="Save">
                </td>
              </tr>
            </table>
            </form>
        	</div id="jsPage" class="popLogin" style="display:block;"><br />
        <br />
        <b>Notes*</b><br />
        This action will move the <b><?php echo $CATEGORY['name']?></b> category (and any subcategories below it)
        into the destination category you select.<br />
        All data that belongs to the <b><?php echo $CATEGORY['name']?></b> category will still belong to it.<br />
        Don't try to move a category deeper into it's own branch!  First move the sub-branches.<br />
        
    </div>
</div>