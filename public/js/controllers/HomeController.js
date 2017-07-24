(function() {

    'use strict';

    angular
        .module('studyAgain')
        .controller('HomeController', HomeController);

    function HomeController($http, $state, $stateParams, $rootScope) {

        var vm = this;

        vm.ask = $stateParams.src === ('ask');
        vm.questionDeleted = $stateParams.src === ('delete-question');

        vm.loadData = function() {
            vm.loading = true;
            vm.questions = null;
            vm.pages = null;

            $http.get('/api/questions?page=' + $stateParams.page).then(function (response) {
                vm.currentPage = response.data.current_page;
                vm.lastPage = response.data.last_page;
                if (vm.currentPage > vm.lastPage && vm.lastPage !== 0) {
                    $state.go('404Error', {});
                }
                else {
                    vm.questions = response.data.data;

                    var start = vm.currentPage - (vm.lastPage - vm.currentPage < 2 ? (4 - (vm.lastPage - vm.currentPage)) : 2);
                    if (start < 0) {
                        start = 1;
                    }
                    vm.pages = [];
                    for (var i = start; i < start + 5; i++) {
                        if (i > vm.lastPage) {
                            break;
                        }
                        vm.pages.push(i);
                    }

                    vm.loading = false;
                }
            });
        };

        vm.loadData();

        $rootScope.$on('questionDeleted', function () {
            vm.loadData();
        });
    }

})();