(function ()
{
    'use strict';

    angular
        .module('app.ui.layouts.carded.fullwidth-ii', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.ui_layouts_carded_fullwidth-ii', {
            url  : '/ui/layouts/carded/fullwidth-ii',
            views: {
                'content@app': {
                    templateUrl: 'app/main/ui/layouts/carded/fullwidth-ii/fullwidth-ii.html',
                    controller : 'CardedFullwidthIIController as vm'
                }
            }
        });
    }

})();