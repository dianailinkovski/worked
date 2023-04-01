(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.right-sidenav', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_right-sidenav', {
            url  : '/ui/layouts/carded/right-sidenav',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/right-sidenav/right-sidenav.html',
                    controller : 'CardedRightSidenavController as vm'
                }
            }
        });
    }

})();