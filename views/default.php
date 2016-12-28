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
<? if ($attributes = $widget->rpAttributes) :  ?>
    <? foreach ($attributes as $code => $value ) :  ?>
        <p>
            <strong><?= $widget->model->relatedPropertiesModel->getRelatedProperty($code)->name; ?>:</strong>
            <?= $value; ?>
        </p>
    <? endforeach;  ?>
<? endif;  ?>