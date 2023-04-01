(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.fullwidth', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_fullwidth', {
            url  : '/ui/layouts/carded/fullwidth',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/fullwidth/fullwidth.html',
                    controller : 'CardedFullwidthController as vm'
                }
            }
        });
    }

})();