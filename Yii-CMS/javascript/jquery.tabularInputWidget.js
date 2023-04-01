(function( $ ){
	
	  var nestedWidgetOptions = {};
		
	  function updateOrder(obj, id) {
		  if ($(obj).data('settings').orderAttribute) {
			$('#'+id+' > .tabularColumn > .tabularPortlet input').filter(function(){
				if ($(this).parents('.tabularInputWidget:first').attr('id') != id)
					return false;
				else
					return $(this).attr("name").match('^.+\\[.+\\]\\[.+\\]\\['+$(obj).data('settings').orderAttribute+'\\]$');
			}).each(function(i){
				$(this).val(i+1);
			});
		  }
	  }
	  
	  function makePortlet(id, all) {
		if (typeof all == 'undefined')
			var last = ':last';
		else
			var last = '';

		$('#'+id+' > .tabularColumn > .tabularPortlet'+last)
			.addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
			.children('.tabularPortlet-header')
			.addClass('ui-widget-header ui-corner-all')
			.prepend('<span class="ui-icon ui-icon-minusthick" style="cursor: pointer;"></span>');

		$('#'+id+' > .tabularColumn > .tabularPortlet'+last+' > .tabularPortlet-header > .ui-icon').click(function() {
			$(this).toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
			$(this).parents('.tabularPortlet:first').children('.tabularPortlet-content').toggle();
		});
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
			        	'contracted': true
			        }, options));
			        
			        $(this).data('itemsCount', $(this).data('settings').initialItemsCount);
	            }

		        if ($('#'+id+' > .tabularColumn > .tabularPortlet').length == 0)
		        	$('#'+id+' > .tabularExpand').hide();

				$('#'+id+' > .tabularAdd > .tabularRowAdd').bind('click', {obj: this, id: id}, function(event) {
					$(event.data.obj).tabularInputWidget('addItem');
				});

				$('#'+id+' > .tabularExpand > .tabularRowExpand').bind('click', {obj: this, id: id}, function(event) {
					$(event.data.obj).tabularInputWidget('expandContractAll');
				});

				$('#'+id+' > .tabularColumn > .tabularPortlet > .tabularPortlet-header > .tabularRowDelete').bind('click', {obj: this}, function(event){
					$(event.data.obj).tabularInputWidget('deleteItem', $(this).parent().parent().attr('id').split('_')[1]);
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
						var parent = ui.item.parent().parent();
						updateOrder(parent, parent.attr('id'));
					}
		
					$('#'+id+' > .tabularColumn').sortable(sortable);
				}
				
				updateOrder(this, id);
				makePortlet(id, true);
				
				if ($(this).data('settings').contracted)
					$(this).tabularInputWidget('expandContractAll', true);
	        });
	    },
	    deleteItem: function(itemId) { 
	    	return this.each(function(){
	    		var id = $(this).attr('id');
	    		var proceed = true;
	    		
				if ($(this).data('settings').beforeDeleteItem) {
					var result = $(this).data('settings').beforeDeleteItem(id, itemId);
					if (result === false)
						proceed = false;
	    		}
				if (proceed) {
		        	$('#'+id+'_'+itemId).remove();
		        	updateOrder(this, id);
		        	
					if ($(this).data('settings').afterDeleteItem)
						$(this).data('settings').afterDeleteItem(id, itemId);
				}
	        });
	    },
	    addItem: function(layout) { 
	    	return this.each(function(){
				var id = $(this).attr('id');
				var itemsCount = $(this).data('itemsCount');

				if (layout === undefined) {
					if ($('#'+id+' > .tabularAdd > select').length != 0) {
						var layout = $('#'+id+' > .tabularAdd > select').val();
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
					// modifying empty layout to convert idPh to proper id
					var emptyLayout = $(this).data('settings').emptyLayout[layout];
					var re = new RegExp(id.replace(/n\d+/g, 'idPh'),"g");
					emptyLayout = emptyLayout.replace(re, id);
					emptyLayout = emptyLayout.replace(/idPh/g, 'n'+itemsCount);
					$('#'+id+' > .tabularColumn').append(emptyLayout);
	
				    $('#'+id+' > .tabularColumn > .tabularPortlet:last > .tabularPortlet-header > .tabularRowDelete').bind('click', {id: 'n'+itemsCount, obj: this}, function(event){
				    	$(event.data.obj).tabularInputWidget('deleteItem', event.data.id);
				    });
				    
				    $(this).data('itemsCount', (itemsCount+1));
				    makePortlet(id);
				    updateOrder(this, id);
	
				    var nestedWidgets = $(this).data('settings').nestedWidgets;
				    if (nestedWidgets) {
				    	for (var key in nestedWidgets) {
				    		if (typeof nestedWidgets[key] != 'object')
				    			nestedWidgets[key] = [nestedWidgets[key]];
				    		for (var i = 0; i < nestedWidgets[key].length; i++) {
				    			// convert given id to target proper element, and converting also to get proper key for nestedWidgetOptions with idPh in it (those without idPh are for portlets already present in DOM).
				    			var keyWithId = nestedWidgets[key][i].replace(/\{widgetId\}/g, id);
				    			$('#'+keyWithId.replace(/\{idPh\}/g, 'n'+itemsCount)).tabularInputWidget(nestedWidgetOptions[keyWithId.replace(/n\d+/g, 'idPh').replace(/\{idPh\}/g, 'idPh')]);
				    		}
				    	}
				    }
				    if ($('#'+id+' > .tabularExpand').is(':hidden'))
				    	$('#'+id+' > .tabularExpand').show();
				    
					if ($(this).data('settings').afterAddItem)
						$(this).data('settings').afterAddItem(id, 'n'+itemsCount);
				}
		    });
	    },
		expandContractAll: function(first) {
			return this.each(function(){
				var id = $(this).attr('id');
				
				if ($('#'+id+' > .tabularColumn > .tabularPortlet > .tabularPortlet-header > .ui-icon').hasClass('ui-icon-minusthick')) {
					$('#'+id+' > .tabularColumn > .tabularPortlet > .tabularPortlet-header > .ui-icon').each(function(){
						if (first && $(this).parent().hasClass('nohide')) {
							$(this).parent().removeClass('nohide');
						} else if (first && $(this).parents('.tabularPortlet:first') // if it has children portlets that aren't hidden (sub-widgets with errors)
									.find('.tabularPortlet-header:gt(0) > .ui-icon')
									.hasClass('ui-icon-minusthick')) {
						} else {
							$(this).removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
							$(this).parents('.tabularPortlet:first').children('.tabularPortlet-content').hide();
						}
					});
				} else {
					$('#'+id+' > .tabularColumn > .tabularPortlet > .tabularPortlet-header > .ui-icon').each(function(){
						$(this).addClass('ui-icon-minusthick').removeClass('ui-icon-plusthick');
						$(this).parents('.tabularPortlet:first').children('.tabularPortlet-content').show();
					});
				}
			});
		},
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