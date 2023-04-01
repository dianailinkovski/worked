(function ()
{
    'use strict';

    angular
        .module('app.pages.auth.lock', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_auth_lock', {
            url  : '/pages/auth/lock',
            views: {
                'main@'                      : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_auth_lock': {
                    templateUrl: 'app/main/pages/auth/lock/lock.html',
                    controller : 'LockController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/auth/lock');
    }

})();