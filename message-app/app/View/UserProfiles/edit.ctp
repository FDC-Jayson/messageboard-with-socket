<div class="container">
    <h2>Edit Profile</h2>
    <div class="error-message d-none message"></div>
    <br />
    <?php echo $this->Form->create(
        'UserProfile', 
        array(
            'type' => 'file',
            'id' => 'edit-profile-form',
        )); ?>
    <div class="d-flex justify-content-center mb-0">
        <div class="form-group d-flex">
            <img 
                id="imagePreview" 
                src="<?php echo '/img/profiles/' . $this->request->data['UserProfile']['image']; ?>" width="120" height="120"
                onerror="this.onerror=null;this.src='/img/profiles/no-image.png'"
            />
            <div class="d-flex align-items-center">
                <input type="file" name="data[UserProfile][imageFile]" class="form-control-file d-none" id="upload-image">
                <button type="button" class="btn btn-info ml-4" id="upload-image-btn">Upload Profile Image</button>
            </div>
        </div>
    </div>
    <?php
        echo $this->Form->hidden('UserProfile.image');
        echo $this->Form->input('email', array(
            'class' => 'form-control', 
            'label' => 'Email'
        ));
        echo $this->Form->input('name', array('class' => 'form-control', 'label' => 'Name'));
        echo $this->Form->input('gender', array(
            'label' => 'Gender',
            'type' => 'select',
            'options' => array(
                'Male' => 'Male',
                'Female' => 'Female'
            ),
            'class' => 'form-control form-check-inline'
        ));        
        echo $this->Form->input('birthdate', array('type' => 'text', 'class' => 'form-control datepicker', 'label' => 'Birthdate', 'id' => 'datepicker'));
        echo $this->Form->input('hubby', array('class' => 'form-control', 'label' => 'Hubby'));
    ?>
    <?php echo $this->Form->end('Save Changes'); ?>
</div>


<?php echo $this->Html->script('/js/edit-user-profile.js'); ?>