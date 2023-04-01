(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.simple.right-sidenav', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_simple_right-sidenav', {
            url  : '/ui/layouts/simple/right-sidenav',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/simple/right-sidenav/right-sidenav.html',
                    controller : 'SimpleRightSidenavController as vm'
                }
            }
        });
    }

})();