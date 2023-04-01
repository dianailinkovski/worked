(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.left-sidenav', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_left-sidenav', {
            url  : '/ui/layouts/carded/left-sidenav',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/left-sidenav/left-sidenav.html',
                    controller : 'CardedLeftSidenavController as vm'
                }
            }
        });
    }

})();