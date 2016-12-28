<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 08.12.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\rpViewWidget\RpViewWidget */
?>
<? if ($fields = $widget->model->relatedPropertiesModel->toArray() ) :  ?>
    <? foreach ($fields as $field => $value ) :  ?>
         <? if (in_array($field, (array) $widget->visible_properties)) : ?>

           <? if (
                ($widget->visible_only_has_values && $widget->model->relatedPropertiesModel->getAttribute($field))
                ||
                !$widget->visible_only_has_values
            ) : ?>
                <p>
                    <strong><?= $widget->model->relatedPropertiesModel->getRelatedProperty($field)->name; ?>:</strong>
                    <?= $widget->model->relatedPropertiesModel->getSmartAttribute($field); ?>
                </p>
           <? endif; ?>
        <? endif; ?>
    <? endforeach;  ?>

<? endif;  ?>