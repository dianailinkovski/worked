(function ()
{
    'use strict';

    angular
        .module('app.pages.timeline')
        .controller('TimelineController', TimelineController);

    /** @ngInject */
    function TimelineController(Timeline)
    {
        var vm = this;

        // Data
        vm.timeline = Timeline.data;

        // Methods

        //////////
    }
})();
