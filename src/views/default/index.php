<?php
/**
 * @var \yii\web\View $this
 * @var \yii\data\ArrayDataProvider $dataProvider
 * @var integer $defaultTailLine
 */

use yii\grid\GridView;
use yii\helpers\Html;
use kriss\logReader\Log;

$this->title = 'Logs';
$this->params['breadcrumbs'][] = 'Logs';
?>
    <div class="log-reader-index">
        <?= GridView::widget([
            'layout' => '{items}',
            'tableOptions' => ['class' => 'table'],
            'options' => ['class' => 'grid-view table-responsive'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function (Log $log) {
                        return Html::tag('h5', join("\n", [
                            Html::encode($log->name),
                            '<br/>',
                            Html::tag('small', Html::encode($log->fileName)),
                        ]));
                    },
                ], [
                    'attribute' => 'counts',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'sort-ordinal'],
                    'value' => function (Log $log) {
                        return $this->render('_counts', ['log' => $log]);
                    },
                ], [
                    'attribute' => 'size',
                    'format' => 'shortSize',
                    'headerOptions' => ['class' => 'sort-ordinal'],
                ], [
                    'attribute' => 'updatedAt',
                    'format' => 'relativeTime',
                    'headerOptions' => ['class' => 'sort-numerical'],
                ], [
                    'class' => '\yii\grid\ActionColumn',
                    'template' => '{history} {view} {tail} {archive} {delete} {download}',
                    'urlCreator' => function ($action, Log $log) {
                        return [$action, 'slug' => $log->slug];
                    },
                    'buttons' => [
                        'history' => function ($url) {
                            return Html::a('History', $url, [
                                'class' => 'btn btn-xs btn-default',
                            ]);
                        },
                        'view' => function ($url, Log $log) {
                            return !$log->isExist ? '' : Html::a('View', $url, [
                                'class' => 'btn btn-xs btn-primary',
                                'target' => '_blank',
                            ]);
                        },
                        'tail' => function ($url, Log $log) use ($defaultTailLine) {
                            return !$log->isExist ? '' : Html::a('Tail', $url + ['line' => $defaultTailLine], [
                                'class' => 'btn btn-xs btn-primary',
                                'target' => '_blank',
                            ]);
                        },
                        'archive' => function ($url, Log $log) {
                            return !$log->isExist ? '' : Html::a('Archive', $url, [
                                'class' => 'btn btn-xs btn-success',
                                'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
                            ]);
                        },
                        'delete' => function ($url, Log $log) {
                            return !$log->isExist ? '' : Html::a('Delete', array_merge($url, ['since' => $log->updatedAt]), [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => ['method' => 'post', 'data-a' => 'aa', 'confirm' => 'Are you sure?'],
                            ]);
                        },
                        'download' => function ($url, Log $log) {
                            return !$log->isExist ? '' : Html::a('Download', $url, [
                                'class' => 'btn btn-xs btn-default',
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
<?php
$this->registerCss(<<<CSS

.log-reader-index .table tbody td {
   vertical-align: middle;
}

CSS
);
