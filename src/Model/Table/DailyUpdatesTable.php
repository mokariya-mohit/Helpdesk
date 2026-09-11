<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DailyUpdates Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 *
 * @method \App\Model\Entity\DailyUpdate newEmptyEntity()
 * @method \App\Model\Entity\DailyUpdate newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\DailyUpdate> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DailyUpdate get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\DailyUpdate findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\DailyUpdate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\DailyUpdate> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DailyUpdate|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\DailyUpdate saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\DailyUpdate>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyUpdate>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyUpdate>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyUpdate> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyUpdate>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyUpdate>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyUpdate>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyUpdate> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DailyUpdatesTable extends Table
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

        $this->setTable('daily_updates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
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
            ->date('update_date')
            ->requirePresence('update_date', 'create')
            ->notEmptyDate('update_date');

        $validator
            ->scalar('client_name')
            ->maxLength('client_name', 100)
            ->allowEmptyString('client_name');

        $validator
            ->scalar('project_name')
            ->maxLength('project_name', 150)
            ->allowEmptyString('project_name');

        $validator
            ->scalar('tl_name')
            ->maxLength('tl_name', 100)
            ->allowEmptyString('tl_name');

        $validator
            ->scalar('done_tasks')
            ->allowEmptyString('done_tasks');

        $validator
            ->scalar('progress_tasks')
            ->allowEmptyString('progress_tasks');

        $validator
            ->scalar('remaining_tasks')
            ->allowEmptyString('remaining_tasks');

        $validator
            ->scalar('queries')
            ->allowEmptyString('queries');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

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
        $rules->add($rules->isUnique(['user_id', 'update_date'], ['allowMultipleNulls' => true]), ['errorField' => 'update_date', 'message' => __('A daily update for this date already exists.')]);

        return $rules;
    }
}
