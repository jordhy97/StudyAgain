(function() {

    'use strict';

    angular
        .module('studyAgain')
        .controller('TaggedQuestionsController', TaggedQuestionsController);

    function TaggedQuestionsController($http, $state, $stateParams) {

        var vm = this;

        vm.tagName = $stateParams.tagName;
        vm.loading = true;

        $http.get('/api/questions/tagged/' + vm.tagName + '?page=' + $stateParams.page).then(function (response) {
            vm.currentPage = response.data.current_page;
            vm.lastPage = response.data.last_page;
            if (vm.currentPage > vm.lastPage) {
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
    }

})();