(function() {

    'use strict';

    angular
        .module('studyAgain')
        .controller('HomeController', HomeController);

    function HomeController($auth, $http, $rootScope, $stateParams) {

        var vm = this;

        vm.loading = true;
        vm.ask = $stateParams.src === ('ask');
        vm.questionDeleted = $stateParams.src === ('delete-question');

        $http.get('/api/questions').then(function (response) {
            vm.questions = response.data;
            vm.loading = false;
        });
    }

})();