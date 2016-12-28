Widget view model relited properties SkeekS CMS
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-rp-view-widget "*"
```

or add

```
"skeeks/cms-rp-view-widget": "*"
```

Example
----------

```php

<? $widget = \skeeks\cms\rpViewWidget\RpViewWidget::beginWidget('product-properties', [
    'model' => $model,
    //'visible_properties' => ['color', 'material'],
    //'visible_only_has_values' => true,
    //'viewFile' => '@app/views/your-file',
]); ?>
    <? //$widget->viewFile = '';?>
<? \skeeks\cms\rpViewWidget\RpViewWidget::end(); ?>

```

View file
----------
```php

<?php
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\rpViewWidget\RpViewWidget */
?>
<? if ($attributes = $widget->rpAttributes) :  ?>
    <? foreach ($attributes as $code ) :  ?>
        <p>
            <strong><?= $widget->model->relatedPropertiesModel->getRelatedProperty($code)->name; ?>:</strong>
            <?= $widget->model->relatedPropertiesModel->getSmartAttribute($code); ?>
        </p>
    <? endforeach;  ?>
<? endif;  ?>

```

##Links
* [Web site](http://cms.skeeks.com)
* [Author](http://skeeks.com)

___

> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](http://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](http://skeeks.com) | [cms.skeeks.com](http://cms.skeeks.com)


