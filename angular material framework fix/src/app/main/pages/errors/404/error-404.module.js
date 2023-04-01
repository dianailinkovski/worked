(function ()
{
    'use strict';

    angular
        .module('app.pages.error-404', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_errors_error-404', {
            url  : '/pages/errors/error-404',
            views: {
                'main@'                             : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_errors_error-404': {
                    templateUrl: 'app/main/pages/errors/404/error-404.html',
                    controller : 'Error404Controller as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/errors/404');

    }

})();