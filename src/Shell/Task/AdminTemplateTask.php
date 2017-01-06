<?php
namespace DejwCake\AdminBakeTheme\Shell\Task;

use Bake\Shell\Task\TemplateTask;

/**
 * AdminTemplate shell task.
 */
class AdminTemplateTask extends TemplateTask
{

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
}
