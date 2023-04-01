// TODO: tie it to a form so it can be used multiple forms on one page

(function ( $ ) {
	$.fn.formtabnav = function( options ) {
		
		var settings = $.extend({
			numberOfSteps: $(".nav-tabs").children("li").length,
			buttonNext: $("#btn-next"),
			buttonPrev: $("#btn-prev"),
			buttonSave: $("#btn-save"),
			form: $(".nav-tabs").parent(),
			validationRules: null,
		}, options );

		var error=false; // must use global scope because of the timeout issue
		var activeFormSettingsAllAttributes; // active form settings attributes will later be transformed and we'll need to know the original value;

		$(document).ready(function(){
			settings.activeFormSettings = settings.form.data('settings');
			
			activeFormSettingsAllAttributes = settings.activeFormSettings.attributes;
			
			if (!settings.activeFormSettings.validateOnSubmit)
			{
				settings.buttonSave.click(function(e)
				{
					e.preventDefault();

					var activeTab = $('.nav-tabs li.active a');
					var activeStep = activeTab.data("step");
					
					error = false;
					validate(activeTab, activeStep);

					if (!error)
						settings.form.submit();
				});
			}
		});

		settings.buttonNext.click(function(e)
		{
			e.preventDefault();
			changeTab($('.nav-tabs li.active').next().children('a').data('step'));
		});

		settings.buttonPrev.click(function(e)
		{
			e.preventDefault();
			changeTab($('.nav-tabs li.active').prev().children('a').data('step'));
		});
		
		// What happens in this function is before the switch of the tab
		function changeTab(targetStep)
		{
			var targetTab = $('.nav-tabs li a[data-step='+targetStep+']');
			var activeTab = $('.nav-tabs li.active a');
			var targetStep = targetTab.data("step");
			var activeStep = activeTab.data("step");
			error = false;
			
			// User is clicking forwards among tabs
			if (activeStep < targetStep)
			{
				// Avoid skipping steps (unless active tab has already been validated)
				if (targetStep-activeStep > 1 && !targetTab.hasClass('validated')){
					error = true;
				}
				
				validate(activeTab, activeStep);
			}
			
			// Must use timeout because yiiactiveform uses 200 ms timeout before calling validate callback for client validation
			setTimeout(function () { 
				if (!error) {
					// Add .validated class to previous step and actualshow to not repeat the "on" event
					activeTab.addClass('validated').data('actualshow', true);
					targetTab.tab('show');
				}
			}, 210);
		}

		function validate(activeTab, activeStep)
		{
			// Other rules...
			if (!error && settings.validationRules !== null){
				error = settings.validationRules(activeStep);
			}
			
			// Ajax validation of fields using yiiactiveform
			if (!error) 
			{
				settings.activeFormSettings.submitting = true;
				var attributesToValidate = []; // only the attributes in the previous tab will be validated
				$(activeTab.attr('href')).find(':input').each(function(){
					attributesToValidate.push($(this).attr('id')); 
				});
						
				// modifying active form settings to only contain the attributes currently visible
				var currentAttributes = [];
				$.each(activeFormSettingsAllAttributes, function () {
					if ($.inArray(this.inputID, attributesToValidate) != -1) {
						currentAttributes.push(this);
					}
				});
				settings.activeFormSettings.attributes = currentAttributes;

				$.ajaxSetup({async: false}); // must unfortunately do this because we can't change it in the yii plugin
				$.fn.yiiactiveform.validate(settings.form, function(messages) {
					if(!$.isEmptyObject(messages)) {
						$.each(settings.activeFormSettings.attributes, function () {
							error = $.fn.yiiactiveform.updateInput(this, messages, settings.form) || error;
						});
			        }
					settings.activeFormSettings.submitting = false;
				});
				$.ajaxSetup({async: true});
				
				settings.activeFormSettings.attributes = activeFormSettingsAllAttributes; // setting it back for validateOnSubmit
			}
		}
		
		// Before tab show, we need this for clicking on tab event which can't be handled in this plugin
		$('a[data-toggle="tab"]').on('show.bs.tab', function (e)
		{
			//actualshow means we bypass this and just show the tab
			if (!$(e.relatedTarget).data('actualshow')) {
				//alert('b');
				changeTab($(e.target).data('step'));
				e.preventDefault();
				return false;
			} else {
				//alert('c');
				$(e.relatedTarget).data('actualshow', false);
			}
		});
		
		
		// After tab show
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e)
		{
			var activeTab = e.target;
			var previousTab = e.relatedTarget;

			if ($(activeTab).data("step") < settings.numberOfSteps) {
				settings.buttonNext.show();
			} else {
				settings.buttonNext.hide();
			}
			
			if ($(activeTab).data("step") > 1) {
				settings.buttonPrev.show();
			} else {
				settings.buttonPrev.hide();
			}
			
			if ($(activeTab).data("step") == settings.numberOfSteps) {
				settings.buttonSave.show();
			} else {
				settings.buttonSave.hide();
			}
		});
	};
}( jQuery ));