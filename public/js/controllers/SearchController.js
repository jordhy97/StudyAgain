(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('SearchController', SearchController);

    function SearchController($http, $stateParams, $rootScope) {
        var vm = this;

        vm.loading = true;
        vm.query = $stateParams.q;
        $rootScope.$broadcast('updateQuery', vm.query);

        $http.get('/api/questions?search=' + $stateParams.q + '&page=' + $stateParams.page).then(function (response) {
            vm.total = response.data.total;

            if (vm.total > 0) {

                vm.currentPage = response.data.current_page;
                vm.lastPage = response.data.last_page;
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
            }
            vm.loading = false;
        });
    }
})();