(function ()
{
    'use strict';

    angular
        .module('fuse')
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider, $urlRouterProvider, $locationProvider)
    {
        $locationProvider.html5Mode(true);

        $urlRouterProvider.otherwise('/dashboard-project');

        $stateProvider
            .state('app', {
                abstract: true,
                views   : {
                    'main@'         : {
                        templateUrl: 'app/core/layouts/default.html'
                    },
                    'toolbar@app': {
                        templateUrl: 'app/toolbar/toolbar.html',
                        controller : 'ToolbarController as vm'
                    },
                    'navigation@app': {
                        templateUrl: 'app/sidenav/navigation/navigation.html',
                        controller : 'NavigationController as vm'
                    },
                    'quickPanel@app': {
                        templateUrl: 'app/sidenav/quick-panel/quick-panel.html',
                        controller : 'QuickPanelController as vm'
                    },
                    'themeOptions'  : {
                        templateUrl: 'app/core/theming/theme-options/theme-options.html',
                        controller : 'ThemeOptionsController as vm'
                    }
                }
            });
    }

})();