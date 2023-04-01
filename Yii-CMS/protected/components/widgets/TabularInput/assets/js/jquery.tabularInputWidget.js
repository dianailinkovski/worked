(function( $ ){
	
	  // Holds options for nested widgets (created dynamically by parent widget)
	  var nestedWidgetOptions = {};

	  function updateOrder(obj, id) {
		  if ($(obj).data('settings').orderAttribute) {
			$('#'+id+' .tabularItem input').data('filterId', id).filter(filterNested).filter(function(){
				return $(this).attr("name").match('^.+\\[.+\\]\\[.+\\]\\['+$(obj).data('settings').orderAttribute+'\\]$');
			}).each(function(i){
				$(this).val(i+1);
			});
		  }
	  }
	  
	  function makeItem(obj, id, all) {
		if (typeof all == 'undefined')
			var last = ':last';
		else
			var last = '*';

		$('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).filter(last).find('.tabularRowCollapse').data('filterId', id).filter(filterNested).click({obj: obj, id: id}, function(event) {
			if ($(this).hasClass('tabularRowCollapseOn')) {
				$(this).html($(event.data.obj).data('settings').collapseButtonOff);
				$(this).removeClass('tabularRowCollapseOn');
				$(this).addClass('tabularRowCollapseOff');
				$(this).closest('.tabularItem').find('.tabularItem-content').data('filterId', event.data.id).filter(filterNested).hide();
			} else {
				$(this).html($(event.data.obj).data('settings').collapseButtonOn);
				$(this).removeClass('tabularRowCollapseOff');
				$(this).addClass('tabularRowCollapseOn');
				$(this).closest('.tabularItem').find('.tabularItem-content').data('filterId', event.data.id).filter(filterNested).show();
			}
		});
	  }

	  // Target the closest parent widget (used in filters)
	  function filterNested() {
		  return $(this).closest('.tabularInputWidget').attr('id') == $(this).data('filterId');
	  }

	  var methods = {
	    init: function(options) { 
	        if (typeof options.emptyLayout == 'undefined') {
	        	alert('Mandatory parameters are missing');
	        	return;
	        }
	        return this.each(function() {
	        	var id = $(this).attr('id');

		        if (!$(this).data('settings')) {
		        	$(this).data('settings', $.extend({
			        	'initialItemsCount': 0,
			        	'initiallyCollapsed': true
			        }, options));
			        
			        $(this).data('itemsCount', $(this).data('settings').initialItemsCount);
	            }

		        if ($('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).length == 0)
		        	$('#'+id+' .tabularRowCollapseAll').data('filterId', id).filter(filterNested).hide();

				$('#'+id+' .tabularRowAdd').data('filterId', id).filter(filterNested).bind('click', {obj: this, id: id}, function(event) {
					$(event.data.obj).tabularInputWidget('addItem');
				});

				$('#'+id+' .tabularRowCollapseAll').data('filterId', id).filter(filterNested).bind('click', {obj: this, id: id}, function(event) {
					$(event.data.obj).tabularInputWidget('expandContractAll');
				});

				$('#'+id+' .tabularRowDelete').data('filterId', id).filter(filterNested).bind('click', {obj: this}, function(event){
					$(event.data.obj).tabularInputWidget('deleteItem', $(this).closest('.tabularItem').attr('id').split('_')[1]);
				});
				
				if ($(this).data('settings').orderAttribute) {
					var sortable = $.extend({
						distance: 20,
			        }, $(this).data('settings').sortable);
					
					if ($(this).data('settings').sortable !== undefined && $(this).data('settings').sortable.update !== undefined)
						var sortableUpdate = $(this).data('settings').sortable.update;
					else
						var sortableUpdate = function(){};
		
					sortable.update = function(event, ui) { 
						sortableUpdate(event, ui);
						var parent = ui.item.closest('.tabularInputWidget');
						updateOrder(parent, parent.attr('id'));
					}
					$('#'+id+' .tabularColumn').data('filterId', id).filter(filterNested).sortable(sortable);
				}
				updateOrder(this, id);
				makeItem(this, id, true);
				
				if ($(this).data('settings').initiallyCollapsed)
					$(this).tabularInputWidget('expandContractAll', true);
				
				if ($(this).data('settings').afterInit)
					$(this).data('settings').afterInit(id);
	        });
	    },
	    deleteItem: function(itemId) { 
	    	return this.each(function(){
	    		var id = $(this).attr('id');
	    		var proceed = true;
	    		
	    		if ((typeof $(this).data('settings').deleteConfirmDialog == 'undefined' || !$(this).data('settings').deleteConfirmDialog) || ($(this).data('settings').deleteConfirmDialog && confirm($(this).data('settings').deleteConfirmDialog))){
					if ($(this).data('settings').beforeDeleteItem) {
						var result = $(this).data('settings').beforeDeleteItem(id, itemId);
						if (result === false)
							proceed = false;
		    		}
					if (proceed) {
			        	$('#'+id+'_'+itemId).remove();
			        	
				        if ($('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).length == 0)
				        	$('#'+id+' .tabularRowCollapseAll').data('filterId', id).filter(filterNested).hide();
			        	
			        	updateOrder(this, id);
			        	
						if ($(this).data('settings').afterDeleteItem)
							$(this).data('settings').afterDeleteItem(id, itemId);
					}
	    		}
	        });
	    },
	    addItem: function(layout) { 
	    	return this.each(function(){
				var id = $(this).attr('id');
				var itemsCount = $(this).data('itemsCount');

				if (layout === undefined) {
					var layoutSelect = $('#'+id+' .tabularRowLayoutSelect').data('filterId', id).filter(filterNested);
					if (layoutSelect.length != 0) {
						var layout = layoutSelect.val();
					} else {
						var layoutCount = 0;
						for (var key in $(this).data('settings').emptyLayout) {
							if (layoutCount == 0)
								layout = key;
							layoutCount++;
						}
						if ($(this).data('settings').beforeAddItem) {
							var result = $(this).data('settings').beforeAddItem(id);
							if (result)
								layout = result;
						}
					}
	    		}
				if (layout != '')
				{
					// Modifying empty layout to convert itemIdPh to proper id
					var emptyLayout = $(this).data('settings').emptyLayout[layout];
					var re = new RegExp(id.replace(/n\d+/g, 'itemIdPh'),"g");
					emptyLayout = emptyLayout.replace(re, id);
					emptyLayout = emptyLayout.replace(/itemIdPh/g, 'n'+itemsCount);
					$('#'+id+' .tabularColumn').data('filterId', id).filter(filterNested).append(emptyLayout);

					$('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).filter(':last').find('.tabularRowDelete').data('filterId', id).filter(filterNested).bind('click', {id: 'n'+itemsCount, obj: this}, function(event){
						$(event.data.obj).tabularInputWidget('deleteItem', event.data.id);
				    });
				    
				    $(this).data('itemsCount', (itemsCount+1));
				    makeItem(this, id);
				    updateOrder(this, id);
	
				    var nestedWidgets = $(this).data('settings').nestedWidgets;
				    if (nestedWidgets) {
				    	for (var key in nestedWidgets) {
				    		if (typeof nestedWidgets[key] != 'object')
				    			nestedWidgets[key] = [nestedWidgets[key]];
				    		for (var i = 0; i < nestedWidgets[key].length; i++) {
				    			// Convert given id to target proper element, and converting also to get proper key for nestedWidgetOptions with {itemId} in it (those without {itemId} are for items already present in DOM).
				    			var keyWithId = nestedWidgets[key][i].replace(/\{formId\}/g, id);
				    			$('#'+keyWithId.replace(/\{itemId\}/g, 'n'+itemsCount)).tabularInputWidget(nestedWidgetOptions[keyWithId.replace(/n\d+/g, 'itemIdPh').replace(/\{itemId\}/g, 'itemIdPh')]);
				    		}
				    	}
				    }
				    var tabularRowCollapseAll = $('#'+id+' .tabularRowCollapseAll').data('filterId', id).filter(filterNested);
				    if (tabularRowCollapseAll.is(':hidden'))
				    	tabularRowCollapseAll.show();
				    
					if ($(this).data('settings').afterAddItem)
						$(this).data('settings').afterAddItem(id, 'n'+itemsCount);
				}
		    });
	    },
		expandContractAll: function(first) {
			return this.each(function(){
				var id = $(this).attr('id');
				var obj = $(this);
				
				if ($('#'+id+' .tabularItem-content').data('filterId', id).filter(filterNested).is(':visible')) {
					$('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).each(function(){
						if (first && $(this).hasClass('tabularItem-nohide')) {
							$(this).removeClass('tabularItem-nohide');
						} else if (first && $(this).find('.tabularItem').is(':visible')) {  // If it has children items that aren't hidden (sub-widgets with errors)
						} else {
							var tabularRowCollapse = $(this).find('.tabularRowCollapse').data('filterId', id).filter(filterNested);
							tabularRowCollapse.html(obj.data('settings').collapseButtonOff);
							tabularRowCollapse.removeClass('tabularRowCollapseOn');
							tabularRowCollapse.addClass('tabularRowCollapseOff');
							$(this).find('.tabularItem-content').data('filterId', id).filter(filterNested).hide();
						}
					});
				} else {
					$('#'+id+' .tabularItem').data('filterId', id).filter(filterNested).each(function(){
						var tabularRowCollapse = $(this).find('.tabularRowCollapse').data('filterId', id).filter(filterNested);
						tabularRowCollapse.html(obj.data('settings').collapseButtonOn);
						tabularRowCollapse.removeClass('tabularRowCollapseOff');
						tabularRowCollapse.addClass('tabularRowCollapseOn');
						$(this).find('.tabularItem-content').data('filterId', id).filter(filterNested).show();
					});
				}
			});
		},
		// Add to the nested widget options (which is scoped globally to all widgets)
		nestedWidgetOptions: function(options) {
			nestedWidgetOptions[options.id] = options.options;
		}
		
	  };

	  $.fn.tabularInputWidget = function( method ) {
	  
	    if ( methods[method] ) {
	      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	    } else if ( typeof method === 'object' || ! method ) {
	      return methods.init.apply( this, arguments );
	    } else {
	      $.error( 'Method ' +  method + ' does not exist' );
	    }    
	  }

	})( jQuery );