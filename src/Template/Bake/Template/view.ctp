<%
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema, $skipViewFields) {
        return $schema->columnType($field) !== 'binary' &&!in_array($field, $skipViewFields);
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
$hidden = ['password', 'remember_token'];
%>
<section class="content-header">
  <h1>
    <?php echo __('<%= $singularHumanName %>'); ?>
  </h1>
  <ol class="breadcrumb">
    <li>
    <?= $this->Html->link('<i class="fa fa-dashboard"></i> ' . __('Back'), ['action' => 'index'], ['escape' => false])?>
    </li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <i class="fa fa-info"></i>
                <h3 class="box-title"><?php echo __('Information'); ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
<%
                    if ($groupedFields['string']) :
                        foreach ($groupedFields['string'] as $field) :
                            if (isset($associationFields[$field])) :
                                $details = $associationFields[$field];
%>
                    <dt><?= __('<%= Inflector::humanize($details['property']) %>') ?></dt>
                    <dd>
                        <?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %> : '' ?>
                    </dd>
<%
                            else :
                                if (in_array($field, $translateFields)) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %> ({0})', $supportedLanguages[$defaultLanguage]['title']) ?></dt>
                    <dd>
                        <?= h($<%= $singularVar %>-><%= $field %>) ?>
                    </dd>
                    <?php foreach ($supportedLanguages as $language => $languageSettings): ?>
                        <?php if($languageSettings['locale'] == $defaultLocale) { continue; } ?>
                    <dt><?= __('<%= Inflector::humanize($field) %> ({0})', $languageSettings['title']) ?></dt>
                    <dd>
                        <?= h($<%= $singularVar %>->translation($languageSettings['locale'])-><%= $field %>) ?>
                    </dd>
                    <?php endforeach; ?>
<%
                                elseif (!in_array($field, $hidden)) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
                    <dd>
                        <?= h($<%= $singularVar %>-><%= $field %>) ?>
                    </dd>
<%
                                endif;
                            endif;
                        endforeach;
                    endif;

                    if ($associations['HasOne']) :
                        foreach ($associations['HasOne'] as $alias => $details) :
%>
                    <dt><?= __('<%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>') ?></dt>
                    <dd>
                        <?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?>
                    </dd>
<%
                        endforeach;
                    endif;
                        
                    if ($groupedFields['number']) :
                        foreach ($groupedFields['number'] as $field) :
                            if ($field != $primaryKey[0]) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
                    <dd>
                        <?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?>
                    </dd>
<%
                            endif;
                        endforeach;
                    endif;
                        
                    if ($groupedFields['boolean']) :
                        foreach ($groupedFields['boolean'] as $field) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
                    <dd>
                        <?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?>
                    </dd>
<%
                        endforeach;
                    endif;
                        
                    if ($groupedFields['text']) :
                        foreach ($groupedFields['text'] as $field) :
                            if (in_array($field, $translateFields)) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %> ({0})', $supportedLanguages[$defaultLanguage]['title']) ?></dt>
                    <dd>
                        <?= h($<%= $singularVar %>-><%= $field %>) ?>
                    </dd>
                    <?php foreach ($supportedLanguages as $language => $languageSettings): ?>
                        <?php if($languageSettings['locale'] == $defaultLocale) { continue; } ?>
                    <dt><?= __('<%= Inflector::humanize($field) %> ({0})', $languageSettings['title']) ?></dt>
                    <dd>
                        <?= $this->Text->autoParagraph(h($<%= $singularVar %>->translation($languageSettings['locale'])-><%= $field %>)) ?>
                    </dd>
                    <?php endforeach; ?>
<%
                            elseif (!in_array($field, $hidden)) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
                    <dd>
                        <?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?>
                    </dd>
<%
                            endif;
                        endforeach;
                    endif;

                    if ($groupedFields['date']) :
                        foreach ($groupedFields['date'] as $field) :
                            if (!in_array($field, ['deleted'])) :
%>
                    <dt><?= __('<%= Inflector::humanize($field) %>') ?></dt>
                    <dd>
                        <?= h($<%= $singularVar %>-><%= $field %>) ?>
                    </dd>
<%
                            endif;
                        endforeach;
                    endif;
%>
                </dl>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- ./col -->
</div>
<!-- div -->

<%
    $relations = $associations['HasMany'] + $associations['BelongsToMany'];
    foreach ($relations as $alias => $details):
        $otherSingularVar = Inflector::variable($alias);
        $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
%>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <i class="fa fa-share-alt"></i>
                    <h3 class="box-title"><?= __('Related {0}', ['<%= $otherPluralHumanName %>']) ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">

                <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
                    <table class="table table-hover">
                        <tbody>
                            <tr>
<%
                                foreach ($details['fields'] as $field):
                                    if (in_array($field, ['created', 'modified', 'deleted', 'password', 'remember_token', 'id'])) { continue; }
%>
                                    <th>
                                    <%= Inflector::humanize($field) %>
                                    </th>
<%
                                    endforeach;
%>
                                <th>
                                    <?php echo __('Actions'); ?>
                                </th>
                            </tr>
                            <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
                                <tr>
<%
                                    foreach ($details['fields'] as $field):
                                        if (in_array($field, ['created', 'modified', 'deleted', 'password', 'remember_token', 'id'])) { continue; }
%>
                                    <td>
                                    <?= h($<%= $otherSingularVar %>-><%= $field %>) ?>
                                    </td>
<%
                                    endforeach;
                                    $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}";
%>
                                    <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>], ['class'=>'btn btn-info btn-xs']) ?>
                                    <?= $this->Html->link(__('Edit'), ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>], ['class'=>'btn btn-warning btn-xs']) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $otherPk %>), 'class'=>'btn btn-danger btn-xs']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
<%
    endforeach;
%>
</section>