<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\TemplateTask;
use Bake\Utility\Model\AssociationFilter;
use Cake\ORM\Table;

/**
 * AdminTemplate shell task.
 */
class AdminTemplateTask extends TemplateTask
{

    protected $skipFormFields = ['created_by', 'entity_id', 'entity_class', 'slug'];
    protected $skipIndexFields = ['created_by', 'entity_id', 'entity_class'];
    protected $skipViewFields = [];

    /**
     * Execution method always used for tasks
     *
     * @param string|null $name The name of the controller to bake view templates for.
     * @param string|null $template The template to bake with.
     * @param string|null $action The action to bake with.
     * @return mixed
     */
    public function main($name = null, $template = null, $action = null)
    {
        if (empty($this->param('prefix'))) {
            $this->params['prefix'] = '/Admin';
        }


        //TODO remove translation associations from $associations

        parent::main($name, $template, $action);
    }

    /**
     * Loads Controller and sets variables for the template
     * Available template variables:
     *
     * - 'modelObject'
     * - 'modelClass'
     * - 'primaryKey'
     * - 'displayField'
     * - 'singularVar'
     * - 'pluralVar'
     * - 'singularHumanName'
     * - 'pluralHumanName'
     * - 'fields'
     * - 'keyFields'
     * - 'schema'
     *
     * @return array Returns variables to be made available to a view template
     */
    protected function _loadController()
    {
        $controller = parent::_loadController();
        $controller['translateFields'] = [];
        if($controller['modelObject']->behaviors()->has('Translate')) {
            $controller['translateFields'] = $controller['modelObject']->behaviors()->get('Translate')->config('fields');
        }

        $controller['skipFormFields'] = [];
        $controller['skipIndexFields'] = [];
        $controller['skipViewFields'] = [];
        foreach ($controller['fields'] as $field) {
            if(in_array($field, $this->skipFormFields)) {
                $controller['skipFormFields'][] = $field;
            }
            if(in_array($field, $this->skipIndexFields)) {
                $controller['skipIndexFields'][] = $field;
            }
            if(in_array($field, $this->skipViewFields)) {
                $controller['skipViewFields'][] = $field;
            }
        }

        return $controller;
    }

    /**
     * Get filtered associations
     * To be mocked...
     *
     * @param \Cake\ORM\Table $model Table
     * @return array associations
     */
    protected function _filteredAssociations(Table $model)
    {
        $associations = parent::_filteredAssociations($model);
        $associations = collection($associations)->map(function($relationAssociations) {
            return collection($relationAssociations)->reject(function($association) {
                return ($association['property'] == '_i18n' || strpos($association['property'], '_translation') !== false);
            })->toArray();
        })->toArray();
        $associations = collection($associations)->filter(function($relationAssociations) {
            return count($relationAssociations);
        })->toArray();

        return $associations;
    }
}
