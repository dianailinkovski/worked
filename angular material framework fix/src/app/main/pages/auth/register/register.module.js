(function ()
{
    'use strict';

    angular
        .module('app.pages.auth.register', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_auth_register', {
            url  : '/pages/auth/register',
            views: {
                'main@'                          : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_auth_register': {
                    templateUrl: 'app/main/pages/auth/register/register.html',
                    controller : 'RegisterController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/auth/register');
    }

})();
