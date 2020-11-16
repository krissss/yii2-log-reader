<?php

namespace kriss\logReader\controllers;

use kriss\logReader\Log;
use kriss\logReader\models\CleanForm;
use kriss\logReader\models\ZipLogForm;
use kriss\logReader\Module;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * @var Module
     */
    public $module;

    public function actionIndex()
    {
        $this->rememberUrl();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->module->getLogs(),
            'sort' => [
                'attributes' => [
                    'name',
                    'size' => ['default' => SORT_DESC],
                    'updatedAt' => ['default' => SORT_DESC],
                ],
            ],
            'pagination' => ['pageSize' => 0],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'defaultTailLine' => $this->module->defaultTailLine,
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
        $this->rememberUrl();

        $log = $this->find($slug, null);
        $allLogs = $this->module->getHistory($log);

        $fullSize = array_sum(ArrayHelper::getColumn($allLogs, 'size'));

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allLogs,
            'sort' => [
                'attributes' => [
                    'fileName',
                    'size' => ['default' => SORT_DESC],
                    'updatedAt' => ['default' => SORT_DESC],
                ],
                'defaultOrder' => ['updatedAt' => SORT_DESC],
            ],
        ]);

        return $this->render('history', [
            'name' => $log->name,
            'dataProvider' => $dataProvider,
            'fullSize' => $fullSize,
            'defaultTailLine' => $this->module->defaultTailLine,
        ]);
    }

    public function actionZip($slug)
    {
        $log = $this->find($slug, null);
        $model = new ZipLogForm(['log' => $log]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $result = $model->zip();
            if ($result !== false) {
                Yii::$app->session->setFlash('success', 'zip success');
                return $this->redirectPrevious();
            } else {
                Yii::$app->session->setFlash('error', 'zip error: ', implode('<br>', $model->getFirstErrors()));
            }
        }
        return $this->render('zip', [
            'model' => $model,
        ]);
    }

    public function actionClean($slug)
    {
        $log = $this->find($slug, null);
        $model = new CleanForm(['log' => $log]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $result = $model->clean();
            if ($result !== false) {
                Yii::$app->session->setFlash('success', 'clean success');
                return $this->redirectPrevious();
            } else {
                Yii::$app->session->setFlash('error', 'clean error: ', implode('<br>', $model->getFirstErrors()));
            }
        }
        return $this->render('clean', [
            'model' => $model,
        ]);
    }

    public function actionDelete($slug, $stamp = null, $since = null)
    {
        $log = $this->find($slug, $stamp);
        if ($since) {
            if ($log->updatedAt != $since) {
                Yii::$app->session->setFlash('error', 'delete error: file has updated');
                return $this->redirectPrevious();
            }
        }
        if (unlink($log->fileName)) {
            Yii::$app->session->setFlash('success', 'delete success');
        } else {
            Yii::$app->session->setFlash('error', 'delete error');
        }
        return $this->redirectPrevious();
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

    public function actionTail($slug, $line = 100, $start = 0, $stamp = null)
    {
        $log = $this->find($slug, $stamp);
        $this->layout = 'main';

        return $this->render('detail', [
            'log' => $log,
        ]);
        if ($log->isExist) {
            $result = shell_exec("tail -n +{$start} {$line} {$log->fileName}");

            Yii::$app->response->format = Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'text/event-stream');
            return $result;
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

    protected function rememberUrl($url = '')
    {
        Url::remember($url, '__logReaderReturnUrl');
    }

    protected function redirectPrevious()
    {
        return $this->redirect(Url::previous('__logReaderReturnUrl'));
    }
}
