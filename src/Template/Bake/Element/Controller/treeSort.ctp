<%
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$compact = ["'" . $pluralName . "'"];
%>

    /**
     * TreeSort method
     *
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function treeSort()
    {
        $<%= $pluralName %> = $this-><%= $currentModelName %>->find('threaded', [
            'parentField' => 'parent_id',
            'order' => ['lft' => 'ASC'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $items = json_decode($this->request->data('ids'));
            $this-><%= $currentModelName %>->setNewTreeSort($items);
            $this->Flash->success(__('The <%= strtolower($singularHumanName) %> reorder has been changed.'));
        }

        $this->set(compact(<%= join(', ', $compact) %>));
        $this->set('_serialize', ['<%= $pluralName %>']);
    }
