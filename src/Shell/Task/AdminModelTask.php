<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\ModelTask;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Cake\Database\Schema\Table as SchemaTable;

/**
 * AdminModel shell task.
 */
class AdminModelTask extends ModelTask
{

    protected $skipAssociations = ['entity_id'];
    protected $skipRules = ['entity_id'];
    protected $skipValidations = ['deleted', 'sort', 'enabled', 'entity_class', 'entity_id', 'slug'];
    //TODO add more
    protected $translatableFields = ['title', 'slug', 'text', 'short_text', 'description', 'keywords', 'perex'];
    protected $translateFields = null;

    /**
     * Get table context for baking a given table.
     *
     * @param \Cake\ORM\Table $tableObject The model name to generate.
     * @param string $table The table name for the model being baked.
     * @param string $name The model name to generate.
     * @return array
     */
    public function getTableContext($tableObject, $table, $name)
    {
        $tableContext = parent::getTableContext($tableObject, $table, $name);

        $associations = $this->getAssociations($tableObject);
        $tableContext['multiRules'] = $this->getMultiRules($tableObject, $associations);
        //set some variables
        $tableContext['enabled'] = false;
        $tableContext['enabledInLocales'] = false;
        $tableContext['password'] = false;
        $tableContext['view'] = false;
        foreach ($tableObject->schema()->columns() as $column) {
            if($column == 'enabled') {
                $tableContext['enabled'] = true;
            }
            if($column == 'enabled_in_locales') {
                $tableContext['enabledInLocales'] = true;
            }
            if($column == 'password') {
                $tableContext['password'] = true;
            }
            if($column == 'view') {
                $tableContext['view'] = true;
            }
        }

        //set skipping associations as entity_id
        $tableContext['skipAssociations'] = [];
        foreach ($tableContext['associations'] as $type => $assocs) {
            foreach ($assocs as $assoc) {
                if(in_array($assoc['foreignKey'], $this->skipAssociations)) {
                    $tableContext['skipAssociations'][] = $assoc['alias'];
                }
            }
        }

        //Adding translate validation
        if(!empty($this->translateFields)) {
            $tableContext['validation']['_translations'] = [
                'valid' => [
                    'rule' => false,
                    'addNestedMany' => '$translationValidator',
                    'requirePresence' => 'false',
                    'allowEmpty' => true,
                ]
            ];

            $tableContext['translationValidation'] = [];
            foreach ($this->translateFields as $translateField) {
                if(!in_array($translateField, $this->skipValidations)) {
                    $tableContext['translationValidation'][$translateField][] =
                        sprintf("->requirePresence('%s', '%s')", $translateField, 'create');
                    $tableContext['translationValidation'][$translateField][] =
                        sprintf("->allowEmpty('%s')", $translateField);
                }
            }
        }
        return $tableContext;
    }

    /**
     * Get the array of associations to generate.
     *
     * @param \Cake\ORM\Table $table The table to get associations for.
     * @return array
     */
    public function getAssociations(Table $table)
    {
        $associations = parent::getAssociations($table);
        $associations = collection($associations)->map(function($relationAssociations) {
            return collection($relationAssociations)->reject(function($association) {
                return (strpos($association['alias'], 'I18n') !== false);
            })->toArray();
        })->toArray();
        $associations = collection($associations)->filter(function($relationAssociations) {
            return count($relationAssociations);
        })->toArray();
        return $associations;
    }

    /**
     * Get behaviors
     *
     * @param \Cake\ORM\Table $model The model to generate behaviors for.
     * @return array Behaviors
     */
    public function getBehaviors($model)
    {
        $behaviors = parent::getBehaviors($model);

        $fields = $model->schema()->columns();
        if (in_array('deleted', $fields)) {
            $behaviors['Muffin/Trash.Trash'] = [];
        }
        if (in_array('slug', $fields)) {
            $behaviors['DejwCake/Helpers.Sluggable'] = [];
        }
        if (in_array('sort', $fields)) {
            $behaviors['DejwCake/Helpers.Sortable'] = [];
        }

        if (!$this->param('no-translation') && !empty(array_intersect($fields, $this->translatableFields))) {
            $this->translateFields = array_intersect($fields, $this->translatableFields);
            $behaviors['Translate'] = [
                '\'fields\' => [\''.implode('\', \'', $this->translateFields).'\']',
                '\'translationTable\' => \''.$model->alias().'I18n\'',
            ];
        }

        if(isset($behaviors['Tree'])) {
            $behaviors['Tree'] = ['\'recoverOrder\' => [\'left\' => \'ASC\']'];
        }

        return $behaviors;
    }

    /**
     * Find belongsTo relations and add them to the associations list.
     *
     * @param \Cake\ORM\Table $model Database\Table instance of table being generated.
     * @param array $associations Array of in progress associations
     * @return array Associations with belongsTo added in.
     */
    public function findBelongsTo($model, array $associations)
    {
        $associations = parent::findBelongsTo($model, $associations);
        $schema = $model->schema();

        //Add association on field created_by, which is user_id
        foreach ($schema->columns() as $fieldName) {
            if ($fieldName === 'created_by') {
                $tmpModelName = $this->_modelNameFromKey('user_id');
                if (!in_array(Inflector::tableize($tmpModelName), $this->_tables)) {
                    $found = $this->findTableReferencedBy($schema, 'user_id');
                    if ($found) {
                        $tmpModelName = Inflector::camelize($found);
                    }
                }
                $assoc = [
                    'alias' => $tmpModelName,
                    'foreignKey' => $fieldName
                ];

                if ($schema->column($fieldName)['null'] === false) {
                    $assoc['joinType'] = 'INNER';
                }

                if ($this->plugin && empty($assoc['className'])) {
                    $assoc['className'] = $assoc['alias'];
                }
                $associations['belongsTo'][] = $assoc;
            }
        }
        return $associations;
    }

    /**
     * Does individual field validation handling.
     *
     * @param \Cake\Database\Schema\Table $schema The table schema for the current field.
     * @param string $fieldName Name of field to be validated.
     * @param array $metaData metadata for field
     * @param string $primaryKey The primary key field
     * @return array Array of validation for the field.
     */
    public function fieldValidation($schema, $fieldName, array $metaData, $primaryKey)
    {
        if (in_array($fieldName, $this->skipValidations)) {
            return false;
        }

        return parent::fieldValidation($schema, $fieldName, $metaData, $primaryKey);
    }

    /**
     * Generate default validation rules.
     *
     * @param \Cake\ORM\Table $model The model to introspect.
     * @param array $associations The associations list.
     * @return array The validation rules.
     */
    public function getValidation($model, $associations = [])
    {
        $validate =parent::getValidation($model,$associations);



        return $validate;
    }

    /**
     * Generate default rules checker.
     *
     * @param \Cake\ORM\Table $model The model to introspect.
     * @param array $associations The associations for the model.
     * @return array The rules to be applied.
     */
    public function getRules($model, array $associations)
    {
        $rules = parent::getRules($model, $associations);
        foreach ($rules as $key => $rule) {
            if(in_array($key, $this->skipRules)) {
                unset($rules[$key]);
            }
        }

        return $rules;
    }

    /**
     * Generate default multi column rules checker.
     *
     * @param \Cake\ORM\Table $model The model to introspect.
     * @param array $associations The associations for the model.
     * @return array The rules to be applied.
     */
    public function getMultiRules($model, array $associations)
    {
        $multiRules = [];

        $schema = $model->schema();
        foreach ($schema->constraints() as $name) {
            $constraint = $schema->constraint($name);
            if ($constraint['type'] !== SchemaTable::CONSTRAINT_UNIQUE) {
                continue;
            }
            if (count($constraint['columns']) > 1) {
                $multiRules[] = [
                    'name' => 'isUnique',
                    'fields' => $constraint['columns'],
                    'extra' => '[\'allowMultipleNulls\' => false, \'message\' => __(\'This value is not unique\')]',
                ];
            }
        }
        return $multiRules;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('no-translation', [
            'boolean' => true,
            'help' => 'Do not use translation.'
        ]);
        return $parser;
    }
}
