<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ChatRequestsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('chat_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Sender', [
            'className' => 'Users',
            'foreignKey' => 'sender_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Receiver', [
            'className' => 'Users',
            'foreignKey' => 'receiver_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('sender_id')
            ->requirePresence('sender_id', 'create')
            ->notEmptyString('sender_id');

        $validator
            ->integer('receiver_id')
            ->requirePresence('receiver_id', 'create')
            ->notEmptyString('receiver_id');

        $validator
            ->scalar('initial_message')
            ->allowEmptyString('initial_message');

        $validator
            ->scalar('status')
            ->inList('status', ['pending', 'accepted', 'rejected']);

        return $validator;
    }
}
