<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ChatMessagesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('chat_messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ChatConversations', [
            'foreignKey' => 'conversation_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Sender', [
            'className' => 'Users',
            'foreignKey' => 'sender_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ChatAttachments', [
            'className' => 'ChatMessageAttachments',
            'foreignKey' => 'message_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

        $this->hasMany('ChatReactions', [
            'className' => 'ChatMessageReactions',
            'foreignKey' => 'message_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('conversation_id')
            ->requirePresence('conversation_id', 'create')
            ->notEmptyString('conversation_id');

        $validator
            ->integer('sender_id')
            ->requirePresence('sender_id', 'create')
            ->notEmptyString('sender_id');

        $validator
            ->scalar('message')
            ->allowEmptyString('message')
            ->maxLength('message', 4000);

        $validator
            ->scalar('message_type')
            ->maxLength('message_type', 20);

        $validator
            ->boolean('is_deleted');

        return $validator;
    }
}
