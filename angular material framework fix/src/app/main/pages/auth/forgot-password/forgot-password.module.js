(function ()
{
    'use strict';

    angular
        .module('app.pages.auth.forgot-password', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_auth_forgot-password', {
            url  : '/pages/auth/forgot-password',
            views: {
                'main@'                                 : {
                    templateUrl: 'app/core/layouts/basic.html'
                },
                'content@app.pages_auth_forgot-password': {
                    templateUrl: 'app/main/pages/auth/forgot-password/forgot-password.html',
                    controller : 'ForgotPasswordController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/auth/forgot-password');
    }

})();