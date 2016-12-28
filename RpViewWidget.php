<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.12.2016
 */
namespace skeeks\cms\rpViewWidget;

use skeeks\cms\base\WidgetRenderable;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsTree;
use skeeks\widget\chosen\Chosen;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

class ElementRPView extends WidgetRenderable
{
    /**
     * @var CmsContentElement|CmsTree
     */
    public $model = null;

    /**
     * @var array
     */
    public $visible_properties      = [];
    public $visible_only_has_values = 1;

    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => \Yii::t('skeeks/cms-rp-view', 'Related item properties')
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
        [
            'visible_properties'                => 'Отображаемые свойства',
            'visible_only_has_values'           => 'Показывать только свойства у которых заданы значения',
        ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
        [
            [['visible_properties'], 'safe'],
            [['visible_only_has_values'], 'integer'],
        ]);
    }

    public function getCallableData()
    {
        $result = parent::getCallableData();

        $data = $this->model->toArray();
        $data['class'] = $this->model->className();
        $result['object'] = $data;
        return $result;
    }

    public function renderConfigForm(ActiveForm $form)
    {
        /* @var $controller \skeeks\cms\controllers\AdminCmsSiteController */
        $controller = \Yii::$app->controller;
        $object = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'object');

        $model = \Yii::createObject($object);

        if (!$model)
        {
            return 'Не указана модель в параметрах виджета';
        }

        echo $form->field($this, 'visible_properties')->widget(
            Chosen::class,
            [
                'items' => $model->relatedPropertiesModel->attributeLabels(),
                'multiple' => true
            ]
        );

        echo $form->field($this, 'visible_only_has_values')->radioList(\Yii::$app->formatter->booleanFormat);
    }

    protected function _run()
    {
        if (!$this->model)
        {
            return 'Не указана модель в параметрах виджета';
        }

        return parent::_run();
    }
}