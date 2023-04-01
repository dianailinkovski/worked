(function ()
{
    'use strict';

    angular
        .module('app.mail')
        .controller('MailController', MailController);

    /** @ngInject */
    function MailController($document, $mdDialog, $mdSidenav, Inbox)
    {
        var vm = this;

        // Data
        vm.accounts = {
            'creapond'    : 'johndoe@creapond.com',
            'withinpixels': 'johndoe@withinpixels.com'
        };
        vm.checked = [];
        vm.colors = ['blue-bg', 'blue-grey-bg', 'orange-bg', 'pink-bg', 'purple-bg'];
        vm.selectedAccount = 'creapond';
        vm.selectedMail = {};
        vm.toggleSidenav = toggleSidenav;

        vm.responsiveReadPane = false;
        vm.scrollPos = 0;
        vm.scrollEl = angular.element('#content > md-content');

        vm.inbox = Inbox.data;
        vm.selectedMail = vm.inbox[0];

        // Methods
        vm.checkAll = checkAll;
        vm.closeReadPane = closeReadPane;
        vm.composeDialog = composeDialog;
        vm.isChecked = isChecked;
        vm.replyDialog = replyDialog;
        vm.selectMail = selectMail;
        vm.toggleStarred = toggleStarred;
        vm.toggleCheck = toggleCheck;

        //////////

        /**
         * Select mail
         *
         * @param mail
         */
        function selectMail(mail)
        {
            vm.selectedMail = mail;
            vm.responsiveReadPane = true;

            // Store the current scrollPos
            vm.scrollPos = vm.scrollEl.scrollTop();

            // Scroll to the top
            vm.scrollEl.scrollTop(96);
        }

        /**
         * Close read pane
         */
        function closeReadPane()
        {
            if ( vm.responsiveReadPane )
            {
                vm.responsiveReadPane = false;
                vm.scrollEl.scrollTop(vm.scrollPos);
            }
        }

        /**
         * Toggle starred
         *
         * @param mail
         * @param event
         */
        function toggleStarred(mail, event)
        {
            event.stopPropagation();
            mail.starred = !mail.starred;
        }

        /**
         * Toggle checked status of the mail
         *
         * @param mail
         * @param event
         */
        function toggleCheck(mail, event)
        {
            if ( event )
            {
                event.stopPropagation();
            }

            var idx = vm.checked.indexOf(mail);

            if ( idx > -1 )
            {
                vm.checked.splice(idx, 1);
            }
            else
            {
                vm.checked.push(mail);
            }
        }

        /**
         * Return checked status of the mail
         *
         * @param mail
         * @returns {boolean}
         */
        function isChecked(mail)
        {
            return vm.checked.indexOf(mail) > -1;
        }

        /**
         * Check all
         */
        function checkAll()
        {
            if ( vm.allChecked )
            {
                vm.checked = [];
                vm.allChecked = false;
            }
            else
            {
                angular.forEach(vm.inbox, function (mail)
                {
                    if ( !isChecked(mail) )
                    {
                        toggleCheck(mail);
                    }
                });

                vm.allChecked = true;
            }
        }

        /**
         * Open compose dialog
         *
         * @param ev
         */
        function composeDialog(ev)
        {
            $mdDialog.show({
                controller         : 'ComposeDialogController',
                controllerAs       : 'vm',
                locals             : {
                    selectedMail: undefined
                },
                templateUrl        : 'app/main/apps/mail/dialogs/compose/compose-dialog.html',
                parent             : angular.element($document.body),
                targetEvent        : ev,
                clickOutsideToClose: true
            });
        }

        /**
         * Open reply dialog
         *
         * @param ev
         */
        function replyDialog(ev)
        {
            $mdDialog.show({
                controller         : 'ComposeDialogController',
                controllerAs       : 'vm',
                locals             : {
                    selectedMail: vm.selectedMail
                },
                templateUrl        : 'app/main/apps/mail/dialogs/compose/compose-dialog.html',
                parent             : angular.element($document.body),
                targetEvent        : ev,
                clickOutsideToClose: true
            });
        }

        /**
         * Toggle sidenav
         *
         * @param sidenavId
         */
        function toggleSidenav(sidenavId)
        {
            $mdSidenav(sidenavId).toggle();
        }
    }
})();