
<div class="error-message d-none message"></div>

<h2>User Registration</h2>
<?php 
    echo $this->Form->create('User', [
        'id' => 'registration-form',
        'url' => ['controller' => 'api', 'action' => 'register']
    ]);
    echo $this->Form->input('name', array('label' => 'Name'));
    echo $this->Form->input('email', array('label' => 'Email'));
    echo $this->Form->input('password', array('type' => 'password', 'label' => 'Password'));
    echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm Password'));
    echo $this->Form->end('Register');
?>

<?php echo $this->Html->script('/js/registration.js'); ?>

