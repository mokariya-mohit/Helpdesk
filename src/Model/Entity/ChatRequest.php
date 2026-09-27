<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChatRequest Entity
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property string|null $initial_message
 * @property string $status
 * @property \Cake\I18n\DateTime|null $accepted_at
 * @property \Cake\I18n\DateTime|null $rejected_at
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $sender
 * @property \App\Model\Entity\User $receiver
 */
class ChatRequest extends Entity
{
    protected array $_accessible = [
        'sender_id' => true,
        'receiver_id' => true,
        'initial_message' => true,
        'status' => true,
        'accepted_at' => true,
        'rejected_at' => true,
        'created' => true,
        'modified' => true,
        'sender' => true,
        'receiver' => true,
    ];
}
