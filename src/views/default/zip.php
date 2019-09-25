<?php
/**
 * @var $this \yii\web\View
 * @var $model \kriss\logReader\models\ZipLogForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->log->name . ' zip';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->log->name, 'url' => ['history', 'slug' => $model->log->slug]];
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin();

echo $form->field($model, 'start')->input('date');
echo $form->field($model, 'end')->input('date');
echo $form->field($model, 'deleteAfterZip')->dropDownList([0 => 'keep', 1 => 'delete']);

echo Html::submitInput('Submit', ['class' => 'btn btn-primary']);

$form->end();
