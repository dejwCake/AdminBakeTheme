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
            'enabling' => false,
        ];
        $modelObj = $data['modelObj'];

        if(!in_array('sort', $data['actions']) && in_array('sort', $modelObj->schema()->columns())) {
            $data['actions'][] = 'sort';
            $data['sorting'] = true;
        }
        if(!in_array('enable', $data['actions']) && in_array('enabled', $modelObj->schema()->columns())) {
            $data['actions'][] = 'enable';
            $data['enabling'] = true;
        }

        $data['namespace'] .= '\\Admin';

        return parent::bakeController($controllerName, $data);
    }
}
