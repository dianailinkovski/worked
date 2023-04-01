(function ()
{
    'use strict';

    angular
        .module('app.dashboard-analytics', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider)
    {
        $stateProvider.state('app.dashboard-analytics', {
            url    : '/dashboard-analytics',
            views  : {
                'content@app': {
                    templateUrl: 'app/main/apps/dashboards/analytics/dashboard-analytics.html',
                    controller : 'DashboardAnalyticsController as vm'
                }
            },
            resolve: {
                DashboardData: function (apiResolver)
                {
                    return apiResolver.resolve('dashboard.analytics@get');
                }
            }
        });
    }

})();