(function ()
{
    'use strict';

    angular
        .module('fuse')
        .controller('AppController', AppController);

    /** @ngInject */
    function AppController(fuseTheming)
    {
        var vm = this;

        // Data
        vm.themes = fuseTheming.themes;

        //////////
    }
})();