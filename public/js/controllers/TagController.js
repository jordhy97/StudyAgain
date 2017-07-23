(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('TagController', TagController);

    function TagController($http, $state, $stateParams) {
        var vm = this;

        vm.loading = true;
        vm.query = $stateParams.q;

        vm.link = 'api/tags?';
        if ($stateParams.q) {
            vm.link += ('q=' + $stateParams.q + '&');
        }
        vm.link += ('page=' + $stateParams.page);

        $http.get(vm.link).then(function (response) {
            vm.currentPage = response.data.current_page;
            vm.lastPage = response.data.last_page;

            vm.tags = response.data.data;

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
        });

        vm.search = function() {
            $state.go('tags', {q : vm.query});
        }
    }
})();