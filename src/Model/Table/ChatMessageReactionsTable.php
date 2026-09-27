<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ChatMessageReactionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('chat_message_reactions');
        $this->setDisplayField('reaction');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ChatMessages', [
            'foreignKey' => 'message_id',
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
            ->integer('message_id')
            ->requirePresence('message_id', 'create')
            ->notEmptyString('message_id');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->scalar('reaction')
            ->maxLength('reaction', 32)
            ->requirePresence('reaction', 'create')
            ->notEmptyString('reaction');

        return $validator;
    }
}
