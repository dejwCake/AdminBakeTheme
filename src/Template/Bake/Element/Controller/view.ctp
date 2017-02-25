<%
$allAssociations = array_merge(
    $this->Bake->aliasExtractor($modelObj, 'BelongsTo'),
    $this->Bake->aliasExtractor($modelObj, 'BelongsToMany'),
    $this->Bake->aliasExtractor($modelObj, 'HasOne'),
    $this->Bake->aliasExtractor($modelObj, 'HasMany')
);
$allAssociations = collection($allAssociations)->reject(function($association) {
    return (strpos($association, 'I18n') !== false || strpos($association, '_translation') !== false);
})->toArray();
$compact = ["'" . $singularName . "'"];
%>

    /**
     * View method
     *
     * @param string|null $id <%= $singularHumanName %> id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
<% if($translation): %>
        $<%= $singularName%> = $this-><%= $currentModelName %>->find('translations', [
            'contain' => [<%= $this->Bake->stringifyList($allAssociations, ['indent' => false]) %>]
        ])->where(['<%= $currentModelName %>.id' => $id])->firstOrFail();
<% else: %>
        $<%= $singularName%> = $this-><%= $currentModelName %>->get($id, [
            'contain' => [<%= $this->Bake->stringifyList($allAssociations, ['indent' => false]) %>]
        ]);
<% endif %>

<% if($view): %>
        $views = $this-><%= $currentModelName %>->getViews();
<%
        $compact[] = "'views'";
    endif;
%>
<% if($collection): %>
        $collections = $this-><%= $currentModelName %>->getMediaCollections();
<%
        $compact[] = "'collections'";
    endif;
%>
        $this->set(compact(<%= join(', ', $compact) %>));
        $this->set('_serialize', ['<%= $singularName %>']);
    }
