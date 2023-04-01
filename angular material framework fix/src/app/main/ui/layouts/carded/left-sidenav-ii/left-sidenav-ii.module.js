(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.left-sidenav-ii', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_left-sidenav-ii', {
            url  : '/ui/layouts/carded/left-sidenav-ii',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/left-sidenav-ii/left-sidenav-ii.html',
                    controller : 'CardedLeftSidenavIIController as vm'
                }
            }
        });
    }

})();