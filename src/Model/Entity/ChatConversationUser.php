<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChatConversationUser Entity
 *
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property \Cake\I18n\DateTime $joined_at
 * @property \Cake\I18n\DateTime|null $last_read_at
 *
 * @property \App\Model\Entity\ChatConversation $conversation
 * @property \App\Model\Entity\User $user
 */
class ChatConversationUser extends Entity
{
    protected array $_accessible = [
        'conversation_id' => true,
        'user_id' => true,
        'role' => true,
        'joined_at' => true,
        'last_read_at' => true,
        'cleared_at' => true,
        'typing_at' => true,
        'conversation' => true,
        'user' => true,
    ];
}
