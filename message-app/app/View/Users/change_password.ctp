<!-- app/View/Users/change_credentials.ctp -->
<h2>Change Account Email & Password</h2>
<div class="error-message d-none message"></div>
<?php
    echo $this->Form->create('User', array('id' => 'change-password-form'));
    echo $this->Form->input('old_password', array('type' => 'password', 'label' => 'Old Password'));
    echo $this->Form->input('password', array('type' => 'password', 'label' => 'New Password'));
    echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm Password'));
    echo $this->Form->end('Save Changes');
?>

<?php echo $this->Html->script('/js/change-password.js'); ?>