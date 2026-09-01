<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * WorkLog Entity
 *
 * @property int $id
 * @property int|null $project_id
 * @property \Cake\I18n\Date $log_date
 * @property string|null $content
 * @property int|null $task_count
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Project $project
 */
class WorkLog extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'project_id' => true,
        'client_id' => true,
        'log_date' => true,
        'content' => true,
        'task_count' => true,
        'created' => true,
        'modified' => true,
        'project' => true,
        'client' => true,
    ];
}
