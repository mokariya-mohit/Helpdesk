<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ChatConversationUsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('chat_conversation_users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ChatConversations', [
            'foreignKey' => 'conversation_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('conversation_id')
            ->requirePresence('conversation_id', 'create')
            ->notEmptyString('conversation_id');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->dateTime('joined_at')
            ->requirePresence('joined_at', 'create')
            ->notEmptyDateTime('joined_at');

        $validator
            ->dateTime('last_read_at')
            ->allowEmptyDateTime('last_read_at');

        return $validator;
    }
}
