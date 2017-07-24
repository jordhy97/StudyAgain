<!DOCTYPE html>
<html lang="en">
<head>
    <title>Study Again</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>


<body ng-app="studyAgain" ng-controller="MainController as main">
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a href="#" class="navbar-left"><img src="images/logo.png" alt="Study Again logo" style="max-height:50px;"></a>
            <a class="navbar-brand" href="#">Study Again</a>
        </div>

        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li ui-sref-active="active"><a ui-sref="home">Home</a></li>
                <li ui-sref-active="active"><a ui-sref="tags">Tags</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li ui-sref-active="active"><a ui-sref="register" ng-if="!main.isAuthorized"><span class="glyphicon glyphicon-user"></span> Register</a></li>
                <li ui-sref-active="active"><a ui-sref="login" ng-if="!main.isAuthorized"><span class="glyphicon glyphicon-log-in"></span> Log In</a></li>
                <li class="dropdown" ng-if="main.isAuthorized"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">Hello, {{main.currentUser.name}}<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#" ng-controller="AuthController as auth" ng-if="main.isAuthorized" ng-click="auth.logout()"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
                </li>
            </ul>

            <form class="navbar-form navbar-left">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" ng-model="main.query">
                    <div class="input-group-btn">
                        <button class="btn btn-default" ng-click="main.search(main.query)">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>

<div class="container" style="margin-top:100px">
    <ui-view></ui-view>
</div>

</body>

<footer class="footer">
    <div class="container text-left">
        <p>Study Again - Created by Jordhy Fernando</p>
        <p>Contact information: <a href="mailto:jordhy.fernando@gmail.com">jordhy.fernando@gmail.com</a></p>
    </div>
</footer>

<!-- Application Dependencies -->
<script src="js/lib/angular.min.js"></script>
<script src="js/lib/angular-ui-router.min.js"></script>
<script src="js/lib/satellizer.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/angular-moment/0.9.0/angular-moment.min.js"></script>

<!-- Application Scripts -->
<script src="js/studyAgain.js"></script>
<script src="js/controllers/MainController.js"></script>
<script src="js/controllers/HomeController.js"></script>
<script src="js/controllers/AuthController.js"></script>
<script src="js/controllers/QuestionController.js"></script>
<script src="js/controllers/TaggedQuestionsController.js"></script>
<script src="js/controllers/SearchController.js"></script>
<script src="js/controllers/TagController.js"></script>
<script src="js/controllers/AnswerController.js"></script>

</html>
