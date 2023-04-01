<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Categorize Crawlers</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <p>Assign the category <b><?=$CATEGORY['name'];?></b> to the appropriate crawlers below, then click Save button:</p>
        
        	<?=$this->load->view('category/_status_msg', '', TRUE);?>
        
        <br>
        
        <form method="post" name="crawlerForm" id="crawlerForm" action="<?= $BASE_URL;?>category/crawlers/catId=<?= @$CATEGORY['id'];?>">
            <input type="hidden" name="catId" value="<?= $CATEGORY['id'];?>">
            <input type="hidden" name="do" value="save" />
            <table>
                <tr style="background:silver">
                    <th><input type="checkbox" id="selecctall">Select All</th>
                    <th>Crawler</th>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="Save"></td>
                </tr>
                <? $i = 1;?>
                <? foreach($MARKETPLACES as $crawler):?>
                    <tr style="background:<?php echo $i%2 ? '#E6E6E6':'#FFFFFF'; $i++?>;">
                        <td><input class="checkbox1" type="checkbox" name="crawlerIds[]" value="<?=$crawler['id']?>" <?= (in_array($crawler['id'],$CAT_MRKTS))?'checked':'';?>></td>
                        <td><?=$crawler['display_name']?></td>
                    </tr>
                <? endforeach;?>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="Save"></td>
                </tr>
            </table>
        </form>
        
    </div>
</div>            

