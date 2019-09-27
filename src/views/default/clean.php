<?php
/**
 * @var $this \yii\web\View
 * @var $model \kriss\logReader\models\ZipLogForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->log->name . ' clean';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->log->name, 'url' => ['history', 'slug' => $model->log->slug]];
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin();

echo $form->field($model, 'start')->input('date');
echo $form->field($model, 'end')->input('date');

echo Html::submitInput('Submit', ['class' => 'btn btn-primary', 'data-confirm' => 'Confirm Delete?']);

$form->end();
