<h2>Login</h2>
<?php
echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));
echo $this->Form->input('email', array('label' => 'Email'));
echo $this->Form->input('password', array('label' => 'Password'));
?>
<div class="d-flex">
    <?php 
        echo $this->Form->end('Login');
        echo '<div class="mt-2">'.$this->Html->link('Register', array('controller' => 'users', 'action' => 'register'), array('class' => 'btn btn-primary ml-2 text-white p-2 mt-1')).'</div>'; 
    ?>
</div>


