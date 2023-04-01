(function ()
{
    'use strict';

    angular
        .module('app.calendar')
        .controller('CalendarController', CalendarController);

    /** @ngInject */
    function CalendarController($mdDialog, $document)
    {
        var vm = this;

        // Data
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        vm.events = [
            [
                {
                    id   : 1,
                    title: 'Bloqueado',
                    start: new Date(y, m, d, 0, 0, 0),
                    end  : new Date(y, m, d, 8, 0, 0),
                    editable:false,
                    type:'custom'
                }
            ]
        ];

        vm.calendarUiConfig = {
            calendar: {
                editable     : true,
                eventLimit   : true,
                header       : '',
                dayNames     : ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                viewRender   : function (view)
                {
                    vm.calendarView = view;
                    vm.calendar = vm.calendarView.calendar;
                    vm.currentMonthShort = vm.calendar.getDate().format('MMM');
                },
                columnFormat : {
                    month: 'ddd',
                    week : 'ddd M',
                   day  : ''
                },
                eventClick   : eventDetail,
                selectable   : true,
                selectHelper : true,
                select       : select
            }
        };

        // Methods
        vm.addEvent = addEvent;
        vm.next = next;
        vm.prev = prev;

        //////////

        /**
         * Go to next on current view (week, month etc.)
         */
        function next()
        {
            vm.calendarView.calendar.next();
        }

        /**
         * Go to previous on current view (week, month etc.)
         */
        function prev()
        {
            vm.calendarView.calendar.prev();
        }

        /**
         * Show event detail
         *
         * @param calendarEvent
         * @param e
         */
        function eventDetail(calendarEvent, e)
        {
            //showEventDetailDialog(calendarEvent, e);
        }

        /**
         * Add new event in between selected dates
         *
         * @param start
         * @param end
         * @param e
         */
        function select(start, end, e)
        {
            this.calendar.changeView('agendaDay');
        }

        /**
         * Add event
         *
         * @param e
         */
        function addEvent(e)
        {
            /*

            var start = new Date(),
                end = new Date();

            showEventFormDialog('add', false, start, end, e);*/
        }

        /**
         * Show event detail dialog
         * @param calendarEvent
         * @param e
         */
        function showEventDetailDialog(calendarEvent, e)
        {
            $mdDialog.show({
                controller         : 'EventDetailDialogController',
                controllerAs       : 'vm',
                templateUrl        : 'app/main/apps/calendar/dialogs/event-detail/event-detail-dialog.html',
                parent             : angular.element($document.body),
                targetEvent        : e,
                clickOutsideToClose: true,
                locals             : {
                    calendarEvent      : calendarEvent,
                    showEventFormDialog: showEventFormDialog,
                    event              : e
                }
            });
        }

        /**
         * Show event add/edit form dialog
         *
         * @param type
         * @param calendarEvent
         * @param start
         * @param end
         * @param e
         */
        function showEventFormDialog(type, calendarEvent, start, end, e)
        {
            var dialogData = {
                type         : type,
                calendarEvent: calendarEvent,
                start        : start,
                end          : end
            };

            $mdDialog.show({
                controller         : 'EventFormDialogController',
                controllerAs       : 'vm',
                templateUrl        : 'app/main/apps/calendar/dialogs/event-form/event-form-dialog.html',
                parent             : angular.element($document.body),
                targetEvent        : e,
                clickOutsideToClose: true,
                locals             : {
                    dialogData: dialogData
                }
            }).then(function (response)
            {
                if ( response.type === 'add' )
                {
                    // Add new
                    vm.events[0].push({
                        id   : vm.events[0].length + 20,
                        title: response.calendarEvent.title,
                        start: response.calendarEvent.start,
                        end  : response.calendarEvent.end
                    });
                }
                else
                {
                    for ( var i = 0; i < vm.events[0].length; i++ )
                    {
                        // Update
                        if ( vm.events[0][i].id === response.calendarEvent.id )
                        {
                            vm.events[0][i] = {
                                title: response.calendarEvent.title,
                                start: response.calendarEvent.start,
                                end  : response.calendarEvent.end
                            };

                            break;
                        }
                    }
                }
            });
        }

    }

})();