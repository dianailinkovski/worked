(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.right-sidenav-ii', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_right-sidenav-ii', {
            url  : '/ui/layouts/carded/right-sidenav-ii',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/right-sidenav-ii/right-sidenav-ii.html',
                    controller : 'CardedRightSidenavIIController as vm'
                }
            }
        });
    }

})();