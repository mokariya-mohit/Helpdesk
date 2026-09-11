<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WorkLogs Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 *
 * @method \App\Model\Entity\WorkLog newEmptyEntity()
 * @method \App\Model\Entity\WorkLog newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\WorkLog> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\WorkLog get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\WorkLog findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\WorkLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\WorkLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\WorkLog|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\WorkLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\WorkLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WorkLog>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WorkLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WorkLog> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WorkLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WorkLog>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\WorkLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\WorkLog> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WorkLogsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('work_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
        ]);
        $this->belongsTo('Clients', [
            'foreignKey' => 'client_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('project_id')
            ->allowEmptyString('project_id');

        $validator
            ->date('log_date')
            ->requirePresence('log_date', 'create')
            ->notEmptyDate('log_date');

        $validator
            ->scalar('content')
            ->maxLength('content', 4294967295)
            ->allowEmptyString('content');

        $validator
            ->integer('task_count')
            ->allowEmptyString('task_count');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['user_id', 'log_date']), ['errorField' => 'log_date', 'message' => __('A work log already exists for this date.')]);

        return $rules;
    }
}
