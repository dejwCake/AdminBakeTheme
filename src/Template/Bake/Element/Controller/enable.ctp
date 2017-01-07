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
%>

    /**
     * Enable method
     *
     * @param string|null $id <%= $singularHumanName %> id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function enable($id = null)
    {
        $this->request->allowMethod(['post']);
        $<%= $singularName %> = $this-><%= $currentModelName %>->get($id);

        $<%= $singularName %>->changeEnableStatus();
        if ($this-><%= $currentModelName; %>->save($<%= $singularName %>)) {
            $this->Flash->success(__('The <%= strtolower($singularHumanName) %> status has been changed.'));
        } else {
            $this->Flash->error(__('The <%= strtolower($singularHumanName) %> status could not be changed. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
