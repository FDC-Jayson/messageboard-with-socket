<?php

class DeletedMessage extends AppModel {
    public $validate = array(
        'message_id' => array(
            'rule' => 'notBlank',
            'message' => 'From User ID is required.'
        ),
        'user_id' => array(
            'rule' => 'notBlank',
            'message' => 'To User ID is required.'
        )
    );

    public $belongsTo = array(
        'Message' => array(
            'className' => 'Message',
            'foreignKey' => 'messsage_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );
}
