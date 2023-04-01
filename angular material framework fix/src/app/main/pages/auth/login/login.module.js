(function ()
{
    'use strict';

    angular
        .module('app.pages.auth.login', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_auth_login', {
            url  : '/pages/auth/login',
            views: {
                'main@'                       : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_auth_login': {
                    templateUrl: 'app/main/pages/auth/login/login.html',
                    controller : 'LoginController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/auth/login');
    }

})();