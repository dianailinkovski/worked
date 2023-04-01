(function ()
{
    'use strict';

    angular
        .module('app.calendar', [
            'ui.calendar'
        ])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.calendar', {
            url  : '/calendar',
            views: {
                'content@app': {
                    templateUrl: 'app/main/apps/calendar/calendar.html',
                    controller : 'CalendarController as vm'
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/apps/calendar');
    }

})();