<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\ModelTask;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * AdminModel shell task.
 */
class AdminModelTask extends ModelTask
{

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
        $tableContext['enabled'] = false;
        $tableContext['password'] = false;
        foreach ($tableObject->schema()->columns() as $column) {
            if($column == 'enabled') {
                $tableContext['enabled'] = true;
            }
            if($column == 'password') {
                $tableContext['password'] = true;
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

        //TODO add more
        $translatableFields = ['title', 'text', 'short_text'];
        if (!$this->param('no-translation') && !empty(array_intersect($fields, $translatableFields))) {
            $behaviors['Translate'] = [
                '\'fields\' => [\''.implode('\',\'', array_intersect($fields, $translatableFields)).'\']',
                '\'translationTable\' => \''.$model->alias().'I18n\'',
            ];
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
        $ignoreFields = ['deleted', 'sort', 'enabled'];
        if (in_array($fieldName, $ignoreFields)) {
            return false;
        }

        return parent::fieldValidation($schema, $fieldName, $metaData, $primaryKey);
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
