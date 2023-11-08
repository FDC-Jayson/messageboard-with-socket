
<div ng-app="messageBoard" ng-controller="mainCtrl" ng-init="userId=<?php echo $userData['id'] ?>">

    <div ng-view></div>

</div>

<?php echo $this->Html->script('/js/socket.io.min'); ?>
<?php echo $this->Html->script('/js/angular.min'); ?>
<?php echo $this->Html->script('/js/angular-route'); ?>
<?php echo $this->Html->script('/js/messages'); ?>