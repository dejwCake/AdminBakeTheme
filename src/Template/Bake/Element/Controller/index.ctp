    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
<% $belongsTo = $this->Bake->aliasExtractor($modelObj, 'BelongsTo'); %>
<% if ($belongsTo): %>
        $this->paginate = [
            'contain' => [<%= $this->Bake->stringifyList($belongsTo, ['indent' => false]) %>]
        ];
<% endif; %>
<% if ($sorting): %>
        $this->paginate += [
            'order' => [
                '<%= $pluralName %>.sort' => 'asc'
            ]
        ];
<% endif; %>
        $<%= $pluralName %> = $this->paginate($this-><%= $currentModelName %>);

        $this->set(compact('<%= $pluralName %>'));
        $this->set('_serialize', ['<%= $pluralName %>']);
    }
