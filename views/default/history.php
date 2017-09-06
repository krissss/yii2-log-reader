<?php
/**
 * @var \yii\web\View $this
 * @var string $name
 * @var \yii\data\ArrayDataProvider $dataProvider
 */

use yii\grid\GridView;
use yii\helpers\Html;
use kriss\logReader\Log;

$this->title = $name;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $name;
?>
    <div class="log-reader-history">
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'fileName',
                    'format' => 'raw',
                    'value' => function (Log $log) {
                        return pathinfo($log->fileName, PATHINFO_BASENAME);
                    },
                ], [
                    'attribute' => 'counts',
                    'format' => 'raw',
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
                    'template' => '{view} {delete}',
                    'urlCreator' => function ($action, Log $log) {
                        return [$action, 'slug' => $log->slug, 'stamp' => $log->stamp];
                    },
                    'buttons' => [
                        'view' => function ($url) {
                            return Html::a('View', $url, [
                                'class' => 'btn btn-xs btn-primary',
                                'target' => '_blank',
                            ]);
                        },
                        'delete' => function ($url) {
                            return Html::a('Delete', $url, [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
                            ]);
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
<?php
$this->registerCss(<<<CSS

.log-reader-history .table tbody td {
   vertical-align: middle;
}

CSS
);