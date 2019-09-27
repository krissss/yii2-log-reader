Yii2 Log Reader
===============
Yii2 log reader

> this project is extend from [zhuravljov/yii2-logreader](https://github.com/zhuravljov/yii2-logreader), and Add more operation like `delete` `download` and so on.

> from 2.0. `history` can load file that Yii2 FileTarget rotated. See [History Usage](#history-usage)

Preview
------------

Index

![preview1](https://github.com/krissss/yii2-log-reader/raw/master/screenshots/preview1.jpg)

History

![preview2](https://github.com/krissss/yii2-log-reader/raw/master/screenshots/preview2.jpg)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kriss/yii2-log-reader "2.*"
```

or add

```
"kriss/yii2-log-reader": "2.*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => ['log-reader'],
    'modules' => [
        'log-reader' => [
            'class' => 'kriss\logReader\Module',
            //'as login_filter' => UserLoginFilter::class, // to use login filter
            'aliases' => [
                'Frontend' => '@frontend/runtime/logs/app.log',
                'Backend' => '@backend/runtime/logs/app.log',
                'Console' => '@console/runtime/logs/app.log',
            ],
        ],
    ],
];
```

You can then access Log Reader using the following URL:

```php
http://localhost/path/to/index.php?r=log-reader
```

or if you have enabled pretty URLs, you may use the following URL:

```php
http://localhost/path/to/log-reader
```

History Usage
-----

For every day log view, you can config yii log like this: 

```php
[
    'class' => 'yii\log\FileTarget',
    'categories' => ['test'],
    'logVars' => [],
    'logFile' => '@runtime/logs/test/test.log.' . date('Ymd'), // important
    'maxLogFiles' => 31,
    'dirMode' => 0777,
    'fileMode' => 0777,
]
```

And config log-reader module `aliases` like:

```php
'test' => '@runtime/logs/test/test.log'
```

Then log with be save filename like `test.log.20190924`. This is log-reader `history` load filename.

So you can view every day log in `history` action.
