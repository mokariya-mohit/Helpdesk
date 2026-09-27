<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ChatMessageReaction extends Entity
{
    protected array $_accessible = [
        'message_id' => true,
        'user_id' => true,
        'reaction' => true,
        'created' => true,
        'chat_message' => true,
        'user' => true,
    ];
}
