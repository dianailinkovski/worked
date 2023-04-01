<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Categories</strong>
        </h3>
    </div>
    
    <div class="panel-body">

        <?=$this->load->view('category/_breadcrumbs', '', TRUE);?>
        
        	<div id="jsPage" >
        	<?=$this->load->view('category/_status_msg', '', TRUE);?>
        
            <form name="PageForm" id="PageForm" method="post" action="<?php echo $BASE_URL;?>category/show/catId=<?php echo @$CATEGORY['id'];?>" > 
            <input type="hidden" name="catId" value="<?php echo @$CATEGORY['id'];?>" />
            <input type="hidden" name="do" value="save" />
        
            <table cellpadding="0" cellspacing="0" class="formTable" >
              <tr>
                <td colspan="3" align="center">
        			<?php if ($CATEGORY['id']):?> Edit Category
        			<?php else: ?> Add New Category
        			<?php endif; ?>       
                </td>
              </tr>
        		<?php if (empty($CATEGORY['id'])):?> 
        			<tr>
        			<td>Parent Category:</td>
        			<td>
        			<select name="parentid">
        			<option value='-1' <?php if ($CATEGORY['parentid']=='-1'):?>selected<?php endif; ?>>ROOT</option>
        			<?php echo $MENU;?>
        			</select>
        			</td>
        			<td>&nbsp;</td>
        		  </tr>
        		<?php endif; ?>       
            	<tr>
                <td>Category Name:</td>
                <td><input maxlength="60" type="text" name="name" class="input_def" value="<?php echo $CATEGORY['name'];?>"></td>
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
        
        <?php if ($CATEGORIES):?>
        	<table cellpadding="3" cellspacing="0" width="39%">
        		<tr>
        		  <th width="75%">Name</th>
        		  <th width="25%" colspan="4">Action</th>
        		</tr>
        
        		<?php $i=1;?>
        		<?php foreach ($CATEGORIES as $row):?>
        		<tr style="background:<?php echo $i%2 ? '#E6E6E6':'#FFFFFF'; $i++?>;" class="tableRow">
                  <td align='left'>
                   <?php echo $row['catName'];?> <!--(<?php //if(empty($row['marketplaceCount'])) echo "0"; else echo $row['marketplaceCount']; ?>) -->
                  </td>
                  <td align="center">
                  	<a href='<?php echo $BASE_URL;?>category/show/catId=<?php echo $row['catId'];?>'>Edit</a>
                  </td>
                  <td align="center">
                  	<a href='<?php echo $BASE_URL;?>category/move/srcCatId=<?php echo $row['catId'];?>'>Move</a>
                  </td>
                  <td align="center">
                  	<a href='<?php echo $BASE_URL;?>category/crawlers/catId=<?php echo $row['catId'];?>'>Crawlers</a>
                  </td>
                  <td align="center">
                  <?php if (!$row['haveSubCategories']):?>
                    <a id="deleteLink" onclick="return confirm('Are you sure?')" href='<?php echo $BASE_URL;?>category/delete/catId=<?php echo $row['catId'];?>'>Delete</a>&nbsp;
                  <?php endif; ?>
                  </td>
            </tr>
        		<?php endforeach; ?>
        </table>
        <?php else:?>
        No subcategories
        <?php endif; ?>
    </div>        
</div>