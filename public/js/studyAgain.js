(function() {

    'use strict';

    angular
        .module('studyAgain', ['ui.router', 'satellizer', 'angularMoment'])
        .config(function($stateProvider, $urlRouterProvider, $authProvider) {

            // Satellizer configuration that specifies which API
            // route the JWT should be retrieved from
            $authProvider.loginUrl = '/api/login';
            $authProvider.signupUrl = '/api/register';

            // Redirect to the auth state if any other states
            // are requested other than users
            $urlRouterProvider.otherwise('/home');

            $stateProvider
                .state('home', {
                    url: '/home?src',
                    templateUrl: '../views/homeView.html',
                    controller: 'HomeController as home'

                })
                .state('login', {
                    url: '/login?src',
                    templateUrl: '../views/loginView.html',
                    controller: 'AuthController as auth'
                })
                .state('register', {
                    url:'/register',
                    templateUrl: '../views/registerView.html',
                    controller: 'AuthController as auth'
                })
                .state('ask', {
                    url:'/ask',
                    templateUrl: '../views/askView.html',
                    controller: 'QuestionController as ask'
                })
                .state('questionsDetail', {
                    url:'/questions/{questionId:[0-9]+}',
                    templateUrl: '../views/questionsDetailView.html',
                    controller: 'QuestionController as question'
                })
                .state('questionsEdit', {
                    url:'/questions/{questionId:[0-9]+}/edit',
                    templateUrl: '../views/editQuestionView.html',
                    controller: 'QuestionController as question'
                });
        })
        .run(['$transitions', '$state', '$auth', function ($transitions, $state, $auth) {
            $transitions.onStart({}, function (transition) {
                var toState = transition.to();
                if ($auth.isAuthenticated() && (toState.name === 'register' || toState.name === 'login'))
                {
                    $state.go('home', {});
                } else if (!$auth.isAuthenticated() && toState.name === 'ask') {
                    $state.go('login', {src : 'anon_ask'});
                } else if (!$auth.isAuthenticated() && (toState.name === 'questionsEdit')) {
                    $state.go(transition.from().name, {});
                }
            });
        }])
})();