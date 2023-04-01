(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.blank', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_blank', {
            url  : '/ui/layouts/blank',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/blank/blank.html',
                    controller : 'BlankController as vm'
                }
            }
        });
    }

})();