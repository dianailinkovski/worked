(function ()
{
    'use strict';

    angular
        .module('app.navigation')
        .controller('NavigationController', NavigationController);

    /** @ngInject */
    function NavigationController($scope,$mdDialog)
    {
        var vm = this;

        // Data
        vm.msScrollOptions = {
            suppressScrollX: true
        };

        // Methods
        $scope.onCreateSubject = function(){
            vm.showAdvanced(null);
        }

        vm.showAdvanced = function (ev)
        {
            $mdDialog.show({
                controller         : NavigationController,
                templateUrl        : 'dialog1.html',
                parent             : angular.element(document.body),
                targetEvent        : ev,
                clickOutsideToClose: true
            })
                .then(function (answer)
                {
                    vm.alert = 'You said the information was "' + answer + '".';
                }, function ()
                {
                    vm.alert = 'You cancelled the dialog.';
                });
        };
        //////////
    }



})();


