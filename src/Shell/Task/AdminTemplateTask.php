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

    protected $skipFormFields = ['created_by', 'entity_id', 'entity_class', 'slug', 'lft', 'rght', 'sort'];
    protected $skipIndexFields = ['created_by', 'entity_id', 'entity_class', 'sort'];
    protected $skipViewFields = ['lft', 'rght'];

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

        $controller['treeSort'] = false;
        if($controller['modelObject']->behaviors()->has('Tree')) {
            $controller['treeSort'] = true;
        }
        $controller['sort'] = false;
        if(in_array('sort', $controller['fields'])) {
            $controller['sort'] = true;
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

    /**
     * Get a list of actions that can / should have view templates baked for them.
     *
     * @return array Array of action names that should be baked
     */
    protected function _methodsToBake()
    {
        $methods = parent::_methodsToBake();

        $vars = $this->_loadController();
        if($vars['modelObject']->behaviors()->has('Tree')) {
            $methods[] = 'treeSort';
        }
        if(in_array('sort', $vars['fields'])) {
            $methods[] = 'sort';
        }

        return $methods;
    }
}
