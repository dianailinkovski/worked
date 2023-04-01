(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.simple.left-sidenav', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_simple_left-sidenav', {
            url  : '/ui/layouts/simple/left-sidenav',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/simple/left-sidenav/left-sidenav.html',
                    controller : 'SimpleLeftSidenavController as vm'
                }
            }
        });
    }

})();