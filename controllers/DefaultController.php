<?php

namespace kriss\logReader\controllers;

use kriss\logReader\Module;
use kriss\logReader\Log;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @var Module
     */
    public $module;

    public function actionIndex()
    {
        Url::remember();
        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->module->getLogs(),
                'sort' => [
                    'attributes' => [
                        'name',
                        'size' => ['default' => SORT_DESC],
                        'updatedAt' => ['default' => SORT_DESC],
                    ],
                ],
                'pagination' => ['pageSize' => 0],
            ]),
        ]);
    }

    public function actionView($slug, $stamp = null)
    {
        $log = $this->find($slug, $stamp);
        if ($log->isExist) {
            return Yii::$app->response->sendFile($log->fileName, basename($log->fileName), [
                'mimeType' => 'text/plain',
                'inline' => true
            ]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionArchive($slug)
    {
        if ($this->find($slug, null)->archive(date('YmdHis'))) {
            Yii::$app->session->setFlash('success', 'archive success');
            return $this->redirect(['history', 'slug' => $slug]);
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    public function actionHistory($slug)
    {
        Url::remember();
        $log = $this->find($slug, null);

        return $this->render('history', [
            'name' => $log->name,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $this->module->getHistory($log),
                'sort' => [
                    'attributes' => [
                        'fileName',
                        'size' => ['default' => SORT_DESC],
                        'updatedAt' => ['default' => SORT_DESC],
                    ],
                    'defaultOrder' => ['updatedAt' => SORT_DESC],
                ],
            ]),
        ]);
    }

    public function actionDelete($slug, $stamp = null)
    {
        $log = $this->find($slug, $stamp);
        if (unlink($log->fileName)) {
            Yii::$app->session->setFlash('success', 'delete success');
        } else {
            Yii::$app->session->setFlash('error', 'delete error');
        }
        return $this->redirect(Url::previous());
    }

    public function actionDownload($slug, $stamp = null)
    {
        $log = $this->find($slug, $stamp);
        if ($log->isExist) {
            Yii::$app->response->sendFile($log->fileName)->send();
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }

    /**
     * @param string $slug
     * @param null|string $stamp
     * @return Log
     * @throws NotFoundHttpException
     */
    protected function find($slug, $stamp)
    {
        if ($log = $this->module->findLog($slug, $stamp)) {
            return $log;
        } else {
            throw new NotFoundHttpException('Log not found.');
        }
    }
}
