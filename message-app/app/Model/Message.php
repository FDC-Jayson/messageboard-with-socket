<?php

class Message extends AppModel {
    public $validate = array(
        'from_user_id' => array(
            'rule' => 'notBlank',
            'message' => 'From User ID is required.'
        ),
        'to_user_id' => array(
            'rule' => 'notBlank',
            'message' => 'To User ID is required.'
        ),
        'message' => array(
            'rule' => 'notBlank',
            'message' => 'Message content is required.'
        )
    );

    public $belongsTo = array(
        'FromUser' => array(
            'className' => 'User',
            'foreignKey' => 'from_user_id'
        ),
        'ToUser' => array(
            'className' => 'User',
            'foreignKey' => 'to_user_id'
        )
    );

    public $hasMany = array(
        'DeletedMessage' => array(
            'className' => 'DeletedMessage',
            'foreignKey' => 'message_id',
            // Other association options if needed
        )
    );
}
