(function ()
{
    'use strict';

    angular
        .module('app.pages.error-500', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_errors_error-500', {
            url  : '/pages/errors/error-500',
            views: {
                'main@'                             : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_errors_error-500': {
                    templateUrl: 'app/main/pages/errors/500/error-500.html',
                    controller : 'Error500Controller as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/errors/500');

    }

})();