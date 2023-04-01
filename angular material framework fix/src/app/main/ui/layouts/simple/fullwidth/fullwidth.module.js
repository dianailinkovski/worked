(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.simple.fullwidth', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_simple_fullwidth', {
            url  : '/ui/layouts/simple/fullwidth',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/simple/fullwidth/fullwidth.html',
                    controller : 'SimpleFullwidthController as vm'
                }
            }
        });
    }

})();