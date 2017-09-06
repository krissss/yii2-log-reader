Yii2 Log Reader
===============
Yii2 log reader

> this project is extend from [zhuravljov/yii2-logreader](https://github.com/zhuravljov/yii2-logreader), and Add more operation like `delete` `download` and so on.

> from 2.0. `history` can load file that Yii2 FileTarget rotated.

Preview
------------
![preview](https://github.com/krissss/yii2-log-reader/raw/master/screenshots/preview.png)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kriss/yii2-log-reader "^2.0"
```

or add

```
"kriss/yii2-log-reader": "^2.0"
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
            'class' => 'kriss\logreader\Module',
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
http://localhost/path/to/index.php?r=log-reader
```

or if you have enabled pretty URLs, you may use the following URL:

```php
http://localhost/path/to/log-reader
```

Advanced Usage
-----

you can config module params `extraBehaviors` to add behaviors, like user login filter:

```php
return [
    'bootstrap' => ['log-reader'],
    'modules' => [
        'log-reader' => [
            'class' => 'kriss\logreader\Module',
            'extraBehaviors' => [
                'login-filter' => 'xxx/action/UserLoginFilter'
            ],
            'aliases' => [
                'Frontend Errors' => '@frontend/runtime/logs/app.log',
                'Backend Errors' => '@backend/runtime/logs/app.log',
                'Console Errors' => '@console/runtime/logs/app.log',
            ],
        ],
    ],
];
```