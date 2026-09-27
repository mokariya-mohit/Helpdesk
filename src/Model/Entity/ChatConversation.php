<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChatConversation Entity
 *
 * @property int $id
 * @property int $created_by
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $creator
 * @property \App\Model\Entity\ChatConversationUser[] $conversation_users
 * @property \App\Model\Entity\ChatMessage[] $messages
 */
class ChatConversation extends Entity
{
    protected array $_accessible = [
        'created_by' => true,
        'type' => true,
        'title' => true,
        'description' => true,
        'icon' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'creator' => true,
        'conversation_users' => true,
        'messages' => true,
    ];
}
