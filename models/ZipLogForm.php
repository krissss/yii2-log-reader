<?php

namespace kriss\logReader\models;

use kriss\logReader\Log;
use yii\base\Model;
use ZipArchive;

class ZipLogForm extends Model
{
    /**
     * @var Log
     */
    public $log;

    public $start;

    public $end;

    public $deleteAfterZip = 0;

    public function rules()
    {
        return [
            [['start', 'end'], 'string'],
            [['deleteAfterZip'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'start' => 'Start Date',
            'end' => 'End Date',
            'deleteAfterZip' => 'Is Delete After Zip',
        ];
    }

    public function init()
    {
        parent::init();
        $this->start = date('Y-m-01');
        $this->end = date('Y-m-d');
    }

    public function zip()
    {
        $log = $this->log;
        $startStamp = date('Ymd', strtotime($this->start));
        $endStamp = date('Ymd', strtotime($this->end));
        $logs = [];
        foreach (glob(Log::extractFileName($log->alias, '*')) as $fileName) {
            $logEnd = Log::extractFileStamp($log->alias, $fileName);
            // 被自动切割的log文件可能为：jd.log.20181109.1
            $arr = explode('.', $logEnd);
            if ($arr) {
                $logEnd = $arr[0];
            }
            $stamp = date('Ymd', strtotime($logEnd));
            if ($stamp >= $startStamp && $stamp <= $endStamp) {
                $log = new Log($log->name, $log->alias, Log::extractFileStamp($log->alias, $fileName));
                if (!$log->isZip) {
                    $logs[] = $log;
                }
            }
        }
        $current = date('YmdHis');
        $fileName = Log::extractFileName($log->alias, "{$startStamp}-{$endStamp}-{$current}.zip");
        $zip = new ZipArchive();
        if ($zip->open($fileName, ZipArchive::CREATE) !== true) {
            $this->addError('log', 'cannot open zipFile, do you have permission?');
            return false;
        }
        foreach ($logs as $log) {
            $zip->addFile($log->fileName, basename($log->fileName));
        }
        $zip->close();

        // 删除已打包的文件
        if ($this->deleteAfterZip) {
            foreach ($logs as $log) {
                unlink($log->fileName);
            }
        }

        return true;
    }
}
