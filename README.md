Yii2 Log Reader
===============
Yii2 log reader

> this project is extend from [zhuravljov/yii2-logreader](https://github.com/zhuravljov/yii2-logreader), and Add more operation like delete and so on.

Preview
------------
![preview](https://github.com/krissss/yii2-log-reader/raw/master/screenshots/vim-screenshot.jpg)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kriss/yii2-log-reader "*"
```

or add

```
"kriss/yii2-log-reader": "*"
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
            'class' => 'zhuravljov\yii\logreader\Module',
            'aliases' => [
                'Frontend Errors' => '@frontend/runtime/logs/app.log',
                'Backend Errors' => '@backend/runtime/logs/app.log',
                'Console Errors' => '@console/runtime/logs/app.log',
            ],
        ],
    ],
];
```

You can then access Log Reader using the following URL:

```php
http://localhost/path/to/index.php?r=logreader
```

or if you have enabled pretty URLs, you may use the following URL:

```php
http://localhost/path/to/logreader
```