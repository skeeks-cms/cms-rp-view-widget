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
        <p id="sx-prop-<?php echo $code; ?>">
            <strong><?= $widget->model->relatedPropertiesModel->getRelatedProperty($code)->name; ?>:</strong>
            <?
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    echo "<a href='{$value}' target='_blank'>" . $value . "</a>";
                } else {
                    echo $value;
                }
             ?>
        </p>
    <? endforeach;  ?>
<? endif;  ?>