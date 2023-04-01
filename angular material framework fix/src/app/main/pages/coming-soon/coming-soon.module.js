(function ()
{
    'use strict';

    angular
        .module('app.pages.coming-soon', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_coming-soon', {
            url  : '/pages/coming-soon',
            views: {
                'main@'                        : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_coming-soon': {
                    templateUrl: 'app/main/pages/coming-soon/coming-soon.html',
                    controller : 'ComingSoonController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/coming-soon');

    }

})();