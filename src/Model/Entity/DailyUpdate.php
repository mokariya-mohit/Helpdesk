<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DailyUpdate Entity
 *
 * @property int $id
 * @property int|null $project_id
 * @property \Cake\I18n\Date $update_date
 * @property string|null $client_name
 * @property string|null $project_name
 * @property string|null $tl_name
 * @property string|null $done_tasks
 * @property string|null $progress_tasks
 * @property string|null $remaining_tasks
 * @property string|null $queries
 * @property string|null $notes
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Project $project
 */
class DailyUpdate extends Entity
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
        'update_date' => true,
        'client_name' => true,
        'project_name' => true,
        'tl_name' => true,
        'done_tasks' => true,
        'progress_tasks' => true,
        'remaining_tasks' => true,
        'queries' => true,
        'notes' => true,
        'created' => true,
        'modified' => true,
        'project' => true,
    ];
}
