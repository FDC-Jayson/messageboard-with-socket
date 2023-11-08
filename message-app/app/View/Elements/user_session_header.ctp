<!-- app/View/Elements/user_session_header.ctp -->

<?php 
    $loguser = isset($loguser) ? $loguser : null;
?>

<div class="ml-auto d-flex align-items-center">
    <label class="m-0 text-white text-monospace" style="font-size: 14px">
        <?php
            if (!empty($loguser)) {
                // Create a link to the user's profile details
                echo $this->Html->link('View Profile', array('controller' => 'userProfiles', 'action' => 'details'), array('class' => 'text-info'));
                echo ' | Welcome, ' . h($loguser['email']);
            }
        ?>
    </label>
    <div class="m-0 d-flex align-items-center">
        <?php if (!empty($loguser)) {
            echo $this->Form->postLink(
                // Button content and link text
                $this->Html->tag('span', 'View Messages', array('class' => 'btn-text')),
                // $this->Html->tag('span', 'View Messages', array('class' => 'btn-text')) .
                // $this->Html->tag('span', '5', array('class' => 'badge badge-pill badge-danger badge-number ml-1')),
                
                // URL to the action
                array('controller' => 'messages', 'action' => 'index'),

                // Additional options for the link
                array(
                    'escape' => false, // Allow HTML tags in the link content
                    'class' => 'btn btn-primary ml-2', // Button and link styles
                )
            );
            // echo $this->Html->link('Edit Profile', array('controller' => 'userProfiles', 'action' => 'edit'), array('class' => 'btn btn-primary mx-2'));
            echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'), array('class' => 'btn btn-danger ml-2'));
            echo $this->Html->image($loguser['UserProfile']['image'] != '' ? '/img/profiles/'.$loguser['UserProfile']['image'] : '/img/profiles/no-image.png', array('class' => 'rounded-circle border-sucess ml-2', 'alt' => 'Profile Picture', 'width' => '30', 'height' => '30'));
            
        } ?>
    </div>
</div>
