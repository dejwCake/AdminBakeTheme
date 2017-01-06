<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\ModelTask;

/**
 * AdminModel shell task.
 */
class AdminModelTask extends ModelTask
{

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
        if (!$this->param('no-translation')) {
            //TODO add more
            $translatableFields = ['title', 'text', 'short_text'];
            $behaviors['Translate'] = [
                '\'fields\' => [\''.implode('\',\'', array_intersect($fields, $translatableFields)).'\']',
                '\'translationTable\' => \''.$model->alias().'I18n\'',
                '\'validator\' => \'translated\'',
            ];
        }

        return $behaviors;
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
