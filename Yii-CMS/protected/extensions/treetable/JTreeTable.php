<?php

/**
 * JTreeTable class file.
 *
 * @author jerry2801 <jerry2801@gmail.com>
 *
 * @version alpha 4 (2010-10-20 18:48)
 * @version alpha 3 (2010-8-12 14:05)
 * @version alpha 2 (2010-6-4 13:28)
 * @version alpha 1 (2010-4-30 15:57)
 * @requires treeTable v2.3.0 (http://ludo.cubicphuse.nl/jquery-plugins/treeTable/doc/index.html, http://plugins.jquery.com/project/treeTable)
 *
 * A typical usage of JTreeTable is as follows:
 * <pre>
 * $this->widget('ext.treetable.JTreeTable',array(
 *     'id'=>'treeTable',
 *     'primaryColumn'=>'id',
 *     'parentColumn'=>'parent_id',
 *     'columns'=>array(
 *         'id'=>array(
 *             'label'=>'Id',
 *             'headerHtmlOptions'=>array('width'=>80),
 *             'htmlOptions'=>array('align'=>'center'),
 *         ),
 *         'name'=>'Name',
 *     ),
 *     'items'=>array(
 *         array('id'=>1,'parent_id'=>0,'name'=>'test 1'),
 *         array('id'=>2,'parent_id'=>1,'name'=>'test 1\'s children 1'),
 *     ),
 *     'options'=>array(
 *         'initialState'=>'expanded',
 *     ),
 * ));
 * </pre>
 */

class JTreeTable extends CWidget
{
    public $id;
    public $columns;
    public $items;
    public $primaryColumn;
    public $parentColumn;
    public $dataProvider;
    public $options=array();
    public $rowCssClassExpression;
    private $_rowCount = 0;
    private $_keys=array();

    public function init()
    {
        $this->generateTable();
    }

    public function run()
    {
		$options=CJavaScript::encode($this->options);

        $path=dirname(__FILE__).DIRECTORY_SEPARATOR.'source';
        $baseUrl=Yii::app()->getAssetManager()->publish($path);

        $id=$this->id;

        $js='
        $("#'.$id.'").treeTable('.$options.');
        ';

		$cs = Yii::app()->getClientScript();

        /*$juiBasePath=Yii::getPathOfAlias('zii.vendors.jui');
        $juiBaseUrl=Yii::app()->getAssetManager()->publish($juiBasePath);
        $cs->registerCssFile($juiBaseUrl.'/css/base/jquery-ui.css');
        $cs->registerScriptFile($juiBaseUrl.'/js/jquery-ui.min.js');*/

        $cs->registerCssFile($baseUrl.'/stylesheets/jquery.treeTable.css');
        //$cs->registerScriptFile($baseUrl.'/javascripts/jquery.treeTable.js');
		$cs->registerScriptFile($baseUrl.'/javascripts/jquery.treeTable.custom.js');
		
		$cs->registerScript(__CLASS__.'#'.$id,$js);
    }

    protected function generateTable()
    {
        echo '
        <table id="'.$this->id.'">
	        <thead>
	        	<tr>
	    ';
        foreach($this->columns as $key=>$props)
        {
            $this->_keys[]=$key;

            if(is_array($props))
            {
                $htmlOptions=isset($props['headerHtmlOptions'])?$props['headerHtmlOptions']:array();
                echo CHtml::openTag('th',$htmlOptions).$props['label'].CHtml::closeTag('th')."\n";
            }
            else
            {
                echo '<th>'.$props.'</th>'."\n";
            }
        }
        echo '
        </tr>
	        </thead>
	        <tbody>
	    ';

        $this->generateRow(0);

        echo '
        	</tbody>
        </table>
        ';
    }
    
    // recursive
    protected function generateRow($parent_id)
    {
        foreach($this->dataProvider->getData() as $item)
        {
        	if ($item[$this->parentColumn] == $parent_id) {
        		if ($this->rowCssClassExpression !== null)
        			$classEval = $this->evaluateExpression($this->rowCssClassExpression,array('row'=>$this->_rowCount,'data'=>$item));
        		else
        			$classEval = '';
        			
	            if($parent_id == 0)
	                echo '<tr id="node-'.$item[$this->primaryColumn].'"'.(!is_null($classEval) ? ' class="'.$classEval.'"' : '').'>'."\n";
	            else
	                echo '<tr id="node-'.$item[$this->primaryColumn].'" class="child-of-node-'.$parent_id.(!is_null($classEval) ? ' '.$classEval : '').'">'."\n";
	                
	            foreach($this->_keys as $key)
	            {
	                if(is_array($this->columns[$key]) && isset($this->columns[$key]['htmlOptions']))
	                {
	                    $htmlOptions=$this->columns[$key]['htmlOptions'];
	                }
	                else
	                {
	                    $htmlOptions=array();
	                }
	                echo CHtml::openTag('td',$htmlOptions).$item[$key].CHtml::closeTag('td')."\n";
	            }
	            echo '</tr>'."\n";
	            $this->_rowCount++;
	            
	            $this->generateRow($item[$this->primaryColumn]);
        	}
        }
    }
}