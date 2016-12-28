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

/**
 *
 * @property string[] $visibleRpAttributes
 * @property string[] $rpAttributes
 *
 * Class RpViewWidget
 *
 * @package skeeks\cms\rpViewWidget
 */
class RpViewWidget extends WidgetRenderable
{
    /**
     * @var CmsContentElement|CmsTree
     */
    public $model = null;

    /**
     * @var array
     */
    public $visible_properties      = [];

    /**
     * @var int
     */
    public $visible_only_has_values = true;


    public function init()
    {
        parent::init();
        self::registerTranslations();
    }

    static public $isRegisteredTranslations = false;

    static public function registerTranslations()
    {
        if (self::$isRegisteredTranslations === false)
        {
            \Yii::$app->i18n->translations['skeeks/cms-rp-view'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@skeeks/cms/rpViewWidget/messages',
                'fileMap' => [
                    'skeeks/cms-rp-view' => 'main.php',
                ],
                //'on missingTranslation' => \Yii::$app->i18n->missingTranslationHandler
            ];
            self::$isRegisteredTranslations = true;
        }
    }
    /*
    public static function t($category, $message, $params = [], $language = null)
    {
        self::registerTranslations();
        return \Yii::t('skeeks/shop/' . $category, $message, $params, $language);
    }*/


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
            'visible_properties'                => \Yii::t('skeeks/cms-rp-view', 'Visible properties'),
            'visible_only_has_values'           => \Yii::t('skeeks/cms-rp-view', 'Show only properties whose values are set'),
        ]);
    }
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(),
        [
            'visible_properties'                => \Yii::t('skeeks/cms-rp-view', 'If not selected, no property, showing all'),
        ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
        [
            [['visible_properties'], 'safe'],
            [['visible_only_has_values'], 'boolean'],
        ]);
    }

    /**
     * Admin edit widget data
     *
     * @return array
     */
    public function getCallableData()
    {
        $result = parent::getCallableData();

        $data = $this->model->toArray();
        $data['class'] = $this->model->className();
        $result['object'] = $data;
        return $result;
    }

    /**
     * @param ActiveForm $form
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderConfigForm(ActiveForm $form)
    {
        /* @var $controller \skeeks\cms\controllers\AdminCmsSiteController */
        $controller = \Yii::$app->controller;
        $object = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'object');

        $model = \Yii::createObject($object);

        if (!$model)
        {
            return \Yii::t('skeeks/cms-rp-view', 'Not specified model in the widget settings');
        }

        echo $form->field($this, 'visible_properties')->widget(
            Chosen::class,
            [
                'items'     => $model->relatedPropertiesModel->attributeLabels(),
                'multiple'  => true
            ]
        );

        echo $form->field($this, 'visible_only_has_values')->radioList(\Yii::$app->formatter->booleanFormat);
    }

    /**
     * @return string
     */
    protected function _run()
    {
        if (!$this->model)
        {
            return \Yii::t('skeeks/cms-rp-view', 'Not specified model in the widget settings');
        }

        return parent::_run();
    }

    /**
     * Smart visible code => value
     *
     * @return array
     */
    public function getRpAttributes()
    {
        $result = [];
        $rpm = $this->model->relatedPropertiesModel;

        if ($attributes = $this->visibleRpAttributes)
        {
            foreach ($attributes as $attribute)
            {
                if (!$this->visible_only_has_values)
                {
                    $result[] = $attribute;
                    continue;
                }

                $value = $rpm->getSmartAttribute($attribute);
                if ($value)
                {
                    $result[$attribute] = $value;
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Only visible codes
     *
     * @return array
     */
    public function getVisibleRpAttributes()
    {
        if ($this->visible_properties)
        {
            return (array) $this->visible_properties;
        } else
        {
            $attributes = $this->model->relatedPropertiesModel->toArray();
            if ($attributes)
            {
                return array_keys($attributes);
            } else
            {
                return [];
            }
        }
    }
}