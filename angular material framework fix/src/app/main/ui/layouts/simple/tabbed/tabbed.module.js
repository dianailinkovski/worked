(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.simple.tabbed', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_simple_tabbed', {
            url  : '/ui/layouts/simple/tabbed',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/simple/tabbed/tabbed.html',
                    controller : 'SimpleTabbedController as vm'
                }
            }
        });
    }

})();