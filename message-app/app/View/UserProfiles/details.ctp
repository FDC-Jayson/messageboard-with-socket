<?php
    function formatDate($date) {
        if ($date === '0000-00-00 00:00:00') {
            return 'NA';
        } else {
            return date("F j, Y", strtotime($date));
        }
    }
?>
<h2>User Profile</h2>
<div class="d-flex mt-4">
    <div>
        <?php
            $imageUrl = !empty($userData['UserProfile']['image']) ?
                '/img/profiles/'.$userData['UserProfile']['image'] :
                '/img/profiles/no-image.png';

            echo $this->Html->image($imageUrl, array(
                'width' => '200',
            ));
        ?>
    </div>
    <div class="d-flex flex-column ml-5">
        <h5><?php echo $userData['UserProfile']['name'] ?></h5>
        <p>Gender: <?php echo $userData['UserProfile']['gender'] ?></p>
        <p>Birthdate: <?php echo formatDate($userData['UserProfile']['birthdate']) ?></p>
        <p>Joined: <?php echo formatDate($userData['created']) ?></p>
        <p>Last Login: <?php echo formatDate($userData['last_login_at']) ?></p>
    </div>
</div>

<div class="mt-3">
    <label>Hubby:</label>
    <p>
        <?php echo $userData['UserProfile']['hubby'] ?>
    </p>
</div>

<div class="action-btn mt-5">
    <?php 
        echo $this->Html->link('Edit Account Profile', 
            array('controller' => 'userProfiles', 'action' => 'edit'), 
            array('class' => 'btn btn-primary')
        );
        echo $this->Html->link('Change Account Password', 
            array('controller' => 'users', 'action' => 'changePassword'), 
            array('class' => 'btn btn-info ml-3')
        );
    ?>
</div>