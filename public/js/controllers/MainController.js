(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('MainController', MainController);

    function MainController($auth, $rootScope, $http) {
        var vm = this;
        vm.currentUser = null;

        $http.get('/api/user').then(function (response) {
            vm.currentUser = response.data;
        });

        vm.isAuthorized = $auth.isAuthenticated();

        $rootScope.$on('userLoggedIn', function () {
            vm.isAuthorized = $auth.isAuthenticated();

            $http.get('/api/user').then(function (response) {
                vm.currentUser = response.data;
            });

        });

        $rootScope.$on('userLoggedOut', function () {
            vm.isAuthorized = $auth.isAuthenticated();
        });
    }
})();