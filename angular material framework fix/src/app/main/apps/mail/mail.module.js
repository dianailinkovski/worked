(function ()
{
    'use strict';

    angular
        .module('app.mail', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.mail', {
            url    : '/mail',
            views  : {
                'content@app': {
                    templateUrl: 'app/main/apps/mail/mail.html',
                    controller : 'MailController as vm'
                }
            },
            resolve: {
                Inbox: function (apiResolver)
                {
                    return apiResolver.resolve('mail.inbox@get');
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/apps/mail');
    }

})();