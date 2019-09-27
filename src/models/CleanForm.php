<?php

namespace kriss\logReader\models;

use kriss\logReader\Log;
use yii\base\Model;

class CleanForm extends Model
{
    /**
     * @var Log
     */
    public $log;

    public $start;

    public $end;

    public function rules()
    {
        return [
            [['start', 'end'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'start' => 'Start Date',
            'end' => 'End Date',
        ];
    }

    public function init()
    {
        parent::init();
        $this->start = date('Y-m-01', strtotime('-1 month'));
        $this->end = date('Y-m-01');
    }

    public function attributeHints()
    {
        return [
            'start' => 'contain this',
            'end' => 'not contain this',
        ];
    }

    public function clean()
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
            if ($stamp >= $startStamp && $stamp < $endStamp) {
                $log = new Log($log->name, $log->alias, Log::extractFileStamp($log->alias, $fileName));
                if (!$log->isZip) {
                    $logs[] = $log;
                }
            }
        }

        foreach ($logs as $log) {
            unlink($log->fileName);
        }

        return true;
    }
}
