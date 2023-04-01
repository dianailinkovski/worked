(function ()
{
    'use strict';

    angular
        .module('app.file-manager', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.file-manager', {
            url    : '/file-manager',
            views  : {
                'content@app': {
                    templateUrl: 'app/main/apps/file-manager/file-manager.html',
                    controller : 'FileManagerController as vm'
                }
            },
            resolve: {
                Documents: function (apiResolver)
                {
                    return apiResolver.resolve('fileManager.documents@get');
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/apps/file-manager');
    }

})();