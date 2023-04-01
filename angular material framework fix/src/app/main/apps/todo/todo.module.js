(function ()
{
    'use strict';

    angular
        .module('app.todo', [])
        .config(config);

    /** @ngInject */
    function config($stateProvider, $translatePartialLoaderProvider)
    {
        $stateProvider.state('app.to-do', {
            url    : '/to-do',
            views  : {
                'content@app': {
                    templateUrl: 'app/main/apps/todo/todo.html',
                    controller : 'TodoController as vm'
                }
            },
            resolve: {
                Tasks: function (apiResolver)
                {
                    return apiResolver.resolve('todo.tasks@get');
                },
                Tags : function (apiResolver)
                {
                    return apiResolver.resolve('todo.tags@get');
                }
            }
        });

        $translatePartialLoaderProvider.addPart('app/main/apps/todo');
    }

})();