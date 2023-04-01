<?php
/**
 * CTreeGridView class file.
 *
 * Used:
 * YiiExt - http://code.google.com/p/yiiext/
 * treeTable - http://plugins.jquery.com/project/treeTable
 * jQuery ui - http://jqueryui.com/
 *
 * @author quantum13
 * @link http://quantum13.ru/
 */


Yii::import('zii.widgets.grid.CGridView');


class CQTreeGridView extends CGridView
{

    /**
     * @var string the base script URL for all treeTable view resources (e.g. javascript, CSS file, images).
     * Defaults to null, meaning using the integrated grid view resources (which are published as assets).
     */
    public $baseTreeTableUrl;

    /**
     * @var string the base script URL for jQuery ui draggable and droppable.
     * Defaults to null, meaning using the integrated grid view resources (which are published as assets).
     */
    public $baseJuiUrl;
    
    public $dragdrop=true;

    public $allowChildrenAttribute;
    
    public $currentModelId;
    
    public $dropFunction;
    
    public $widgetAttribute;
    
    public $pathAttribute;
    
    private $_locationWidget = false;

    /**
     * Initializes the tree grid view.
     */
    public function init()
    {
        parent::init();
        if($this->baseTreeTableUrl===null)
            $this->baseTreeTableUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.QTreeGridView.treeTable'));

        if($this->baseJuiUrl===null)
            $this->baseJuiUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.QTreeGridView.jui'));
        
        if ($this->widgetAttribute !== null)
        	$this->_locationWidget = true;

        //Calc parent id from nesteD set
        if(count($this->dataProvider->data)) {
            $left = $this->dataProvider->data[0]->tree->leftAttribute;
            $right = $this->dataProvider->data[0]->tree->rightAttribute;
            $level = $this->dataProvider->data[0]->tree->levelAttribute;
            $stack = array();
            $currentLevel = 0;
            $previousModel = null;
            $currentModelLevel = null;
            try {
                foreach($this->dataProvider->data as $model) {
                	if ($this->_locationWidget) {
                		$model->{$this->widgetAttribute}['ancestorIsCurrentModel'] = false;
                		
	                	if (!is_null($currentModelLevel)) {
	                		if ($model->$level > $currentModelLevel)
	                			$model->{$this->widgetAttribute}['ancestorIsCurrentModel'] = true;
	                		else
	                			$currentModelLevel = null;
	                	}
	                	if ($this->currentModelId == $model->getPrimaryKey()) {
	                		$currentModelLevel = $model->$level;
	                	}
                	}
                    if($model->$level==1) { //root with level=1
                        $model->parentId = 0;
                        
                        if ($this->_locationWidget) {
                        	if ($this->allowChildrenAttribute !== null)
                        		$model->{$this->widgetAttribute}['parentCanHaveChildren'] = false;

                        	$model->{$this->widgetAttribute}['path'] = '/'.$model->{$this->pathAttribute};
                        }
					    $currentLevel = 1;
                    } else {
                        if($model->$level == $currentLevel) {
                            if(is_null($stack[count($stack)-1])) {
                                throw new Exception('Tree is corrupted');
                            }
                            $model->parentId = $stack[count($stack)-1]->getPrimaryKey();
                            
                            if ($this->_locationWidget) {
		                        if ($this->allowChildrenAttribute !== null)
		                        	$model->{$this->widgetAttribute}['parentCanHaveChildren'] = $stack[count($stack)-1]->{$this->allowChildrenAttribute};
	
						        $model->{$this->widgetAttribute}['path'] = $stack[count($stack)-1]->{$this->widgetAttribute}['path'].'/'.$model->{$this->pathAttribute};
                            }
                        
                        } elseif($model->$level > $currentLevel) {
                            if(is_null($previousModel)) {
                                throw new Exception('Tree is corrupted');
                            }
                            $currentLevel = $model->$level;
                            $model->parentId = $previousModel->getPrimaryKey();
                            
                            if ($this->_locationWidget) {
                            	if ($this->allowChildrenAttribute !== null)
                            		$model->{$this->widgetAttribute}['parentCanHaveChildren'] = $previousModel->{$this->allowChildrenAttribute};
	                        
	                        	$model->{$this->widgetAttribute}['path'] = $previousModel->{$this->widgetAttribute}['path'].'/'.$model->{$this->pathAttribute};
                            }
					        array_push($stack, $previousModel);
                        } elseif($model->$level < $currentLevel) {
                            for($i=0;$i<$currentLevel - $model->$level;$i++) {
                                array_pop($stack);
                            }
                            if(is_null($stack[count($stack)-1])) {
                                throw new Exception('Tree is corrupted');
                            }
                            $currentLevel = $model->$level;
                            $model->parentId = $stack[count($stack)-1]->getPrimaryKey();
                            
                            if ($this->_locationWidget) {
		                        if ($this->allowChildrenAttribute !== null)
		                        	$model->{$this->widgetAttribute}['parentCanHaveChildren'] = $stack[count($stack)-1]->{$this->allowChildrenAttribute};
	
						        $model->{$this->widgetAttribute}['path'] = $stack[count($stack)-1]->{$this->widgetAttribute}['path'].'/'.$model->{$this->pathAttribute};
                            }
                        }
                    }
                    $previousModel = $model;
                }
            }
            catch (Exception $e) {
              Yii::app()->user->setFlash('CQTeeGridView', $e->getMessage());
            }

        }
    }

    /**
     * Registers necessary client scripts.
     */
    public function registerClientScript()
    {
        parent::registerClientScript();

        $cs=Yii::app()->getClientScript();
        $cs->registerScriptFile($this->baseTreeTableUrl.'/javascripts/jquery.treeTable.js',CClientScript::POS_END);
        
        /*
        $cs->registerScriptFile($this->baseJuiUrl.'/jquery.ui.core.min.js',CClientScript::POS_END);
        $cs->registerScriptFile($this->baseJuiUrl.'/jquery.ui.widget.min.js',CClientScript::POS_END);
        $cs->registerScriptFile($this->baseJuiUrl.'/jquery.ui.mouse.min.js',CClientScript::POS_END);
        $cs->registerScriptFile($this->baseJuiUrl.'/jquery.ui.droppable.min.js',CClientScript::POS_END);
        $cs->registerScriptFile($this->baseJuiUrl.'/jquery.ui.draggable.min.js',CClientScript::POS_END);
        */
        
        $cs->registerCssFile($this->baseTreeTableUrl.'/stylesheets/jquery.treeTable.css');

        $cs->registerScript('treeTable', '
            $(document).ready(function()  {
              $("#'.$this->getId().' .items").treeTable();
            });
            ');
        
        if ($this->dragdrop)
        {
	        $cs->registerScript('draganddrop', '
	            $(document).ready(function()  {
	               $("#'.$this->getId().' .items tr.initialized").not(".nodrag").draggable({
	                  helper: "clone",
	                  opacity: .75,
	                  refreshPositions: true, // Performance?
	                  revert: "invalid",
	                  revertDuration: 300,
	                  scroll: true
	                });
	
	                $("#'.$this->getId().' .items tr.initialized, #'.$this->getId().' .items tr.before, #'.$this->getId().' .items tr.after").not(".nodrop").droppable({
	                    accept: ".initialized",
	                    drop: '.($this->dropFunction !== null ? $this->dropFunction : '
	        					function(e, ui) {
	                    		  if (window.location.href.lastIndexOf("/") == window.location.href.length-1)
	                    		    var href = window.location.href.substr(0, window.location.href.length-1);
	                    		  else
	                    			var href = window.location.href;

			                      if($(this).hasClass("initialized")) {
			                        window.location.href = href+"/moveNode/child/to/"+$(this).attr("id")+"/id/"+$(ui.draggable).attr("id");
			                      }
			                      if($(this).hasClass("before")) {
			                        window.location.href = href+"/moveNode/before/to/"+$(this).attr("id").replace("before-", "")+"/id/"+$(ui.draggable).attr("id");
			                      }
			                      if($(this).hasClass("after")) {
			                        window.location.href = href+"/moveNode/after/to/"+$(this).attr("id").replace("after-", "")+"/id/"+$(ui.draggable).attr("id");
			                      }
	        					}
	                    	').',
	                    hoverClass: "accept",
	                    over: function(e, ui) {
	                      // Make the droppable branch expand when a draggable node is moved over it.
	                      if(this.id != $(ui.draggable.parents("tr")[0]).id && !$(this).is(".expanded")) {
	                        $(this).treeTable_expand();
	                      }
	                    },
	                    activate: function(e, ui) {
	                      $(".after").css("display", "table-row");
	                      $(".before").css("display", "table-row");
	                    },
	                    deactivate: function(e, ui) {
	                      $(".after").css("display", "none");
	                      $(".before").css("display", "none");
	                    },
	                  });
	            });
	        ');
        }
    }

    /**
     * Renders the data items for the grid view.
     */
    public function renderItems() {

        if(Yii::app()->user->hasFlash('CQTeeGridView')) {
            print '<div style="background-color:#ffeeee;padding:7px;border:2px solid #cc0000;">'. Yii::app()->user->getFlash("CQTeeGridView") . '</div>';
        }
        parent::renderItems();
    }


    /**
     * Renders a table body row with id and parentId, needed for ActsAsTreeTable
     * jQuery extension.
     * @param integer $row the row number (zero-based).
     */
    public function renderTableRow($row)
    {
        $model=$this->dataProvider->data[$row];
        $parentClass = $model->parentId
                       ?'child-of-'.$model->parentId.' '
                       :'';
        
        if ($this->_locationWidget) 
        {
	        $allowDraggable = $model->{$this->widgetAttribute}['allowDraggable'] ? '' : 'nodrag ';

	        if ($model->{$this->widgetAttribute}['ancestorIsCurrentModel'] == false) {
		        if ($this->allowChildrenAttribute !== null)
		        	$allowDroppable = $model->{$this->allowChildrenAttribute} ? '' : 'nodrop ';
		        else
		        	$allowDroppable = '';
	        }
	        else
	        	$allowDroppable = 'nodrop ';
	
	        if ($this->allowChildrenAttribute !== null) {
	        	$bordersDroppable = $model->{$this->widgetAttribute}['parentCanHaveChildren'] && $model->{$this->widgetAttribute}['ancestorIsCurrentModel'] == false ? '' : 'nodrop ';
	        	$dropRowChild = $model->{$this->widgetAttribute}['parentCanHaveChildren'] ? 'dropRowChild ' : '';
	        } else {
	        	$bordersDroppable = '';
	        	$dropRowChild = '';
	        }

	        $rowPath = ' path="'.$model->{$this->widgetAttribute}['path'].'"';
	        $bordersPath = ' path="'.substr($model->{$this->widgetAttribute}['path'], 0, strrpos($model->{$this->widgetAttribute}['path'], '/')).'"';
	        
	        if ($bordersDroppable == '' && $this->currentModelId == $model->getPrimaryKey())
	        	$bordersDroppable = 'nodrop ';
	        
	        echo '<tr'.$bordersPath.' style="display:none;" class="'.$bordersDroppable.'before" id="before-'.$model->getPrimaryKey().'"><td style="padding:0;"><div style="height:3px;"></div></td></tr>';
	
	        if($this->rowCssClassExpression!==null)
	        {
	            echo '<tr'.$rowPath.' id="'.$model->getPrimaryKey().'" class="'.$allowDraggable.$dropRowChild.$allowDroppable.$parentClass.$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$model)).'">';
	        }
	        else if(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
	            echo '<tr'.$rowPath.' id="'.$model->getPrimaryKey().'" class="'.$allowDraggable.$dropRowChild.$allowDroppable.$parentClass.$this->rowCssClass[$row%$n].'">';
	        else
	            echo '<tr'.$rowPath.' id="'.$model->getPrimaryKey().'" class="'.$allowDraggable.$dropRowChild.$allowDroppable.$parentClass.'">';
	        foreach($this->columns as $column) {
	            $column->renderDataCell($row);
	        }
	
	        echo "</tr>\n";
	
	        echo '<tr'.$bordersPath.' style="display:none;" class="'.$bordersDroppable.'after" id="after-'.$model->getPrimaryKey().'"><td style="padding:0;"><div style="height:3px;"></div></td></tr>';
        }
        else {
	        echo '<tr style="display:none;" class="before" id="before-'.$model->getPrimaryKey().'"><td style="padding:0;"><div style="height:3px;"></div></td></tr>';
	
	        if($this->rowCssClassExpression!==null)
	        {
	            echo '<tr id="'.$model->getPrimaryKey().'" class="'.$parentClass.$this->evaluateExpression($this->rowCssClassExpression,array('row'=>$row,'data'=>$model)).'">';
	        }
	        else if(is_array($this->rowCssClass) && ($n=count($this->rowCssClass))>0)
	            echo '<tr id="'.$model->getPrimaryKey().'" class="'.$parentClass.$this->rowCssClass[$row%$n].'">';
	        else
	            echo '<tr id="'.$model->getPrimaryKey().'" class="'.$parentClass.'">';
	        foreach($this->columns as $column) {
	            $column->renderDataCell($row);
	        }
	
	        echo "</tr>\n";
	        echo '<tr style="display:none;" class="after" id="after-'.$model->getPrimaryKey().'"><td style="padding:0;"><div style="height:3px;"></div></td></tr>';
        }
    }

}
