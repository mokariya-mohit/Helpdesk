<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ChatMessageAttachment extends Entity
{
    protected array $_accessible = [
        'message_id' => true,
        'file_path' => true,
        'file_name' => true,
        'file_size' => true,
        'file_type' => true,
        'created' => true,
        'chat_message' => true,
    ];
}
