<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->columnType($field), ['binary', 'text']);
    });

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
    $fields = $fields->reject(function ($field) {
        return $field === 'lft' || $field === 'rght';
    });
}

$fields = $fields->reject(function ($field) {
    return in_array($field, ['password', 'remember_token', 'deleted']);
});

$fields = $fields->reject(function ($field) use ($skipIndexFields) {
    return in_array($field, $skipIndexFields);
});

if (!empty($indexColumns)) {
    $fields = $fields->take($indexColumns);
}
$booleans = collection($fields)->filter(function($field) use ($schema) {
    $type = $schema->columnType($field);
    return in_array($type, ['boolean']);
})->toArray();
$arraysToText = collection($fields)->filter(function($field) {
    return in_array($field, ['enabled_in_locales']);
})->toArray();
$mapFields = ['view'];
%>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <%= $pluralHumanName %>
        <div class="pull-right"><?= $this->Html->link(__('New'), ['action' => 'add'], ['class' => 'btn btn-success btn-xs']) ?></div>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= __('List of') ?> <%= $pluralHumanName %></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="<%= $pluralVar%>Table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
<%
                        foreach ($fields as $field):
                            if (!in_array($field, ['created', 'modified', 'updated', 'id'])):
%>
                                <th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>
<%
                            elseif (!in_array($field, ['deleted'])):
%>
                                <!--<th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>-->
<%
                            endif;
                        endforeach;
%>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
                            <tr>
<%
                            foreach ($fields as $field) {
                                if (!in_array($field, ['created', 'modified', 'updated', 'id'])) {
                                    $isKey = false;
                                    if (!empty($associations['BelongsTo'])) {
                                        foreach ($associations['BelongsTo'] as $alias => $details) {
                                            if ($field === $details['foreignKey']) {
                                                $isKey = true;
%>
                                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
<%
                                                break;
                                            }
                                        }
                                    }

                                    if ($isKey !== true) {
                                        if (in_array($field, $booleans)) {
                                            $pk = '$' . $singularVar . '->' . $primaryKey[0];
%>
                                <td>
                                    <?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No') ?>
<%
                                            if ($field =='enabled') {
%>
                                    &nbsp;
                                    <?php
                                        if ($<%= $singularVar %>-><%= $field %>) {
                                            echo $this->Form->postLink(__('Disable'), ['action' => 'enable', <%= $pk %>], ['escape' => false, 'confirm' => __('Are you sure you want to disable this entry?'), 'class' => 'btn btn-default btn-xs']);
                                        } else {
                                            echo $this->Form->postLink(__('Enable'), ['action' => 'enable', <%= $pk %>], ['escape' => false, 'confirm' => __('Are you sure you want to enable this entry?'), 'class' => 'btn btn-default btn-xs']);
                                        }
                                    ?>
                                </td>
<%
                                            }
                                        } else if (in_array($field, $arraysToText)) {
%>
                                <td><?= h($<%= $singularVar %>-><%= $field %>_text) ?></td>
<%
                                        } else if (in_array($field, $mapFields)) {
        %>
                                <td><?= h($<%= Inflector::pluralize($field) %>[$<%= $singularVar %>-><%= $field %>]) ?></td>
<%
                                        } else if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
%>
                                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
<%
                                        } else {
%>
                                <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
<%
                                        }
                                    }
                                } else {
                                    if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
%>
                                <!--<td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>-->
<%
                                    } else {
%>
                                <!--<td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>-->
<%
                                    }
                                }
                            }
                            $pk = '$' . $singularVar . '->' . $primaryKey[0];
%>
                                <td class="actions" style="white-space:nowrap">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', <%= $pk %>], ['escape' => false, 'class' => 'btn btn-info btn-xs']) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', <%= $pk %>], ['escape' => false, 'class' => 'btn btn-warning btn-xs']) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', <%= $pk %>], ['escape' => false, 'confirm' => __('Are you sure you want to delete this entry?'), 'class' => 'btn btn-danger btn-xs']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
<%
                            foreach ($fields as $field):
                                if (!in_array($field, ['created', 'modified', 'updated', 'id'])):
%>
                            <th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>
<%
                                else:
%>
                            <!--<th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>-->
<%
                                endif;
                            endforeach;
%>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</section>
<!-- /.content -->

<?php $this->start('css'); ?>
<?php echo $this->Html->css('DejwCake/AdminLTE./plugins/datatables/dataTables.bootstrap.css'); ?>
<?php $this->end(); ?>
<?php $this->start('scriptBottom'); ?>
<?php echo $this->Html->script('DejwCake/AdminLTE./plugins/datatables/jquery.dataTables.min.js'); ?>
<?php echo $this->Html->script('DejwCake/AdminLTE./plugins/datatables/dataTables.bootstrap.min.js'); ?>
    <script>
        $(function () {
            $('#<%= $pluralVar%>Table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true
            });
        });
    </script>
<?php $this->end(); ?>
