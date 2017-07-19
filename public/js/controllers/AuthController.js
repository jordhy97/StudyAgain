(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('AuthController', AuthController);

    function AuthController($auth, $state, $rootScope, $stateParams) {
        var vm = this;

        vm.registered = ($stateParams.src === 'register');
        vm.anonAsk = ($stateParams.src === 'anon_ask');

        vm.login = function() {

            vm.loading = true;

            var credentials = {
                email: vm.email,
                password: vm.password
            };


            $auth.login(credentials).then(function(response) {
                // Redirect user here after a successful log in.
                $state.go('home', {});
                $rootScope.$broadcast('userLoggedIn');
            })
                .catch(function(response) {
                    // Handle errors here, such as displaying a notification
                    // for invalid email and/or password.
                    vm.error = true;
                    vm.email = "";
                    vm.password = "";
                })
                .finally(function() {
                    vm.loading = false;
                });
        };

        vm.logout = function() {
          $auth.logout();
          $rootScope.$broadcast('userLoggedOut');
        };

        vm.register = function() {

            vm.loading = true;

            var user = {
                name: vm.name,
                email: vm.email,
                password: vm.password
            };

            $auth.signup(user)
                .then(function(response) {
                    // Redirect user here to login page or perhaps some other intermediate page
                    // that requires email address verification before any other part of the site
                    // can be accessed.
                    $state.go('login', {src : 'register'});
                })
                .catch(function(response) {
                // Handle errors here, such as displaying a notification
                // for invalid email and/or password.
                    vm.error = true;
                    vm.errorMessage = response.data.error;
                    vm.name = "";
                    vm.email = "";
                    vm.password = "";
                })
                .finally(function() {
                    vm.loading = false;
                });
        };
    }
})();