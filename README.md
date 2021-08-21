Yii2 Gridviews Widget
=====================
new Bootstrap 4 GridView

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist samuelelonghin/yii2-gridview "*"
```

or add

```
"longhinsamuele/yii2-gridview": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \samuelelonghin\gridview\GridView::widget(['data'=> []]); ?>```