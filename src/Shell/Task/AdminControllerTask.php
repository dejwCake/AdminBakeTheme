<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\ControllerTask;
use Cake\ORM\TableRegistry;

/**
 * AdminController shell task.
 */
class AdminControllerTask extends ControllerTask
{
    public $pathFragment = 'Controller/Admin/';

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('no-translation', [
            'boolean' => true,
            'help' => 'Do not use translation.'
        ]);
        return $parser;
    }

    /**
     * Assembles and writes a Controller file
     *
     * @param string $controllerName Controller name already pluralized and correctly cased.
     * @return string Baked controller
     */
    public function bake($controllerName)
    {
        return parent::bake($controllerName);
    }

    /**
     * Generate the controller code
     *
     * @param string $controllerName The name of the controller.
     * @param array $data The data to turn into code.
     * @return string The generated controller file.
     */
    public function bakeController($controllerName, array $data)
    {
        $data += [
            'translation' => !$this->param('no-translation'),
            'sorting' => false,
            'enabled' => false,
            'enabledInLocales' => false,
            'createdBy' => false,
            'slug' => false,
            'view' => false,
            'collection' => false,
            'skipAssociations' => [],
        ];
        $modelObj = $data['modelObj'];
        $schema = $modelObj->schema();
        $fields = $schema->columns();
        if(!in_array('sort', $data['actions']) && in_array('sort', $fields)) {
            $data['actions'][] = 'sort';
            $data['sorting'] = true;
        }
        if(!in_array('enable', $data['actions']) && in_array('enabled', $fields)) {
            $data['actions'][] = 'enable';
            $data['enabled'] = true;
        }
        if(in_array('enabled_in_locales', $fields)) {
            $data['enabledInLocales'] = true;
        }
        if(in_array('created_by', $fields)) {
            $data['createdBy'] = true;
            $data['skipAssociations'][] = 'created_by';
        }
        if(in_array('slug', $fields)) {
            $data['slug'] = true;
        }
        if(in_array('view', $fields)) {
            $data['view'] = true;
        }
        if (!in_array('treeSort', $data['actions']) &&
            in_array('lft', $fields) && $schema->columnType('lft') === 'integer' &&
            in_array('rght', $fields) && $schema->columnType('rght') === 'integer' &&
            in_array('parent_id', $fields)
        ) {
            $data['actions'][] = 'treeSort';
        }
        if($modelObj->behaviors()->has('Media')) {
            $data['collection'] = true;
        }

        $data['prefix'] = '\\Admin';

        return parent::bakeController($controllerName, $data);
    }
}
