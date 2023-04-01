(function ()
{
    'use strict';

    angular
        .module('app.pages.profile', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.pages_profile', {
            url    : '/pages/profile',
            views  : {
                'content@app': {
                    templateUrl: 'app/main/pages/profile/profile.html',
                    controller : 'ProfileController as vm'
                }
            },
            resolve: {
                Timeline    : function (apiResolver)
                {
                    return apiResolver.resolve('profile.timeline@get');
                },
                About       : function (apiResolver)
                {
                    return apiResolver.resolve('profile.about@get');
                },
                PhotosVideos: function (apiResolver)
                {
                    return apiResolver.resolve('profile.photosVideos@get');
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/pages/profile');

    }

})();