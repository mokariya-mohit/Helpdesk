<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ChatMessageAttachmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('chat_message_attachments');
        $this->setDisplayField('file_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ChatMessages', [
            'foreignKey' => 'message_id',
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
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->requirePresence('file_path', 'create')
            ->notEmptyString('file_path');

        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 255)
            ->requirePresence('file_name', 'create')
            ->notEmptyString('file_name');

        $validator
            ->integer('file_size')
            ->notEmptyString('file_size');

        $validator
            ->scalar('file_type')
            ->maxLength('file_type', 50)
            ->requirePresence('file_type', 'create')
            ->notEmptyString('file_type');

        return $validator;
    }
}
