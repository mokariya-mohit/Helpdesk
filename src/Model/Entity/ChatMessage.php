<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChatMessage Entity
 *
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property string $message
 * @property string $message_type
 * @property bool $is_deleted
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\ChatConversation $conversation
 * @property \App\Model\Entity\User $sender
 */
class ChatMessage extends Entity
{
    protected array $_accessible = [
        'conversation_id' => true,
        'sender_id' => true,
        'message' => true,
        'message_type' => true,
        'attachment_path' => true,
        'attachment_name' => true,
        'attachment_size' => true,
        'attachment_type' => true,
        'is_deleted' => true,
        'is_edited' => true,
        'edited_at' => true,
        'created' => true,
        'modified' => true,
        'conversation' => true,
        'sender' => true,
        'chat_attachments' => true,
        'chat_reactions' => true,
    ];
}
