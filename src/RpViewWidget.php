<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.12.2016
 */

namespace skeeks\cms\rpViewWidget;

use skeeks\cms\backend\widgets\ActiveFormBackend;
use skeeks\cms\base\WidgetRenderable;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsSavedFilter;
use skeeks\cms\models\CmsTree;
use skeeks\cms\relatedProperties\models\RelatedPropertyModel;
use skeeks\cms\relatedProperties\PropertyType;
use skeeks\cms\widgets\Select;
use skeeks\yii2\form\fields\BoolField;
use skeeks\yii2\form\fields\SelectField;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
    public $visible_properties = [];
    /**
     * @var array
     */
    public $hidden_properties = [];

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
        if (self::$isRegisteredTranslations === false) {
            \Yii::$app->i18n->translations['skeeks/cms-rp-view'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath'       => '@skeeks/cms/rpViewWidget/messages',
                'fileMap'        => [
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
            'name' => \Yii::t('skeeks/cms-rp-view', 'Related item properties'),
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
            [
                'visible_properties'      => \Yii::t('skeeks/cms-rp-view', 'Visible properties'),
                'hidden_properties'      => \Yii::t('skeeks/cms-rp-view', 'Отключенные свойства'),
                'visible_only_has_values' => \Yii::t('skeeks/cms-rp-view', 'Show only properties whose values are set'),
            ]);
    }
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(),
            [
                'visible_properties' => \Yii::t('skeeks/cms-rp-view', 'Если ничего не выбрано показываются все'),
                'hidden_properties' => \Yii::t('skeeks/cms-rp-view', 'Если свойство отключено, то оно не будет отображаться.'),
            ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['visible_properties'], 'safe'],
                [['hidden_properties'], 'safe'],
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
     * @return ActiveForm
     */
    public function beginConfigForm()
    {
        return ActiveFormBackend::begin();
    }

    /**
     * @return array
     */
    public function getConfigFormFields()
    {
        /* @var $controller \skeeks\cms\controllers\AdminCmsSiteController */
        $controller = \Yii::$app->controller;
        $object = \yii\helpers\ArrayHelper::getValue($controller->callableData, 'object');

        $model = \Yii::createObject($object);
        if (!$model) {
            return [
                //\Yii::t('skeeks/cms-rp-view', 'Not specified model in the widget settings')
            ];
        }

        $model->relatedPropertiesModel->initAllProperties();
        return [
            'visible_properties'      => [
                'class'    => SelectField::class,
                'items'    => $model->relatedPropertiesModel->attributeLabels(),
                'multiple' => true,
            ],
            'hidden_properties'      => [
                'class'    => SelectField::class,
                'items'    => $model->relatedPropertiesModel->attributeLabels(),
                'multiple' => true,
            ],
            'visible_only_has_values' => [
                'class'     => BoolField::class,
                'allowNull' => false,
            ],
        ];
    }

    /**
     * @return string
     */
    protected function _run()
    {
        if (!$this->model) {
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

        if ($attributes = $this->visibleRpAttributes) {
            foreach ($attributes as $attribute) {
                if (!$this->visible_only_has_values) {
                    $result[$attribute] = $rpm->getAttributeAsHtml($attribute);
                    continue;
                }

                $value = $rpm->{$attribute};
                if ($value) {
                    $result[$attribute] = $rpm->getAttributeAsHtml($attribute);
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getValue($attribute)
    {
        return $this->model->relatedPropertiesModel->{$attribute};
    }

    /**
     * @param $attribute
     * @return int
     */
    public function getPropertyId($attribute)
    {
        $rp = $this->model->relatedPropertiesModel->getRelatedProperty($attribute);
        return $rp->id;
    }

    public function getUrl($attribute, $cmsTree = null)
    {
        if ($cmsTree === null) {
            $cmsTree = \Yii::$app->cms->currentTree;
        }

        $rp = $this->model->relatedPropertiesModel->getRelatedProperty($attribute);
        $value = $this->model->relatedPropertiesModel->getAttribute($attribute);

        $savedFilter = null;
        if (in_array($rp->property_type, [PropertyType::CODE_LIST])) {
            $savedFilter = CmsSavedFilter::find()->cmsSite()->andWhere(['cms_tree_id' => $cmsTree->id])->andWhere(['value_content_property_enum_id' => $value])->one();
        } elseif (in_array($rp->property_type, [PropertyType::CODE_ELEMENT])) {
            $savedFilter = CmsSavedFilter::find()->cmsSite()->andWhere(['cms_tree_id' => $cmsTree->id])->andWhere(['value_content_element_id' => $value])->one();
        }

        if ($savedFilter) {
            return Url::to(['/cms/saved-filter/view',
                'model' => $savedFilter
            ]);
        }

        return false;
    }
    /**
     * Only visible codes
     *
     * @return array
     */
    public function getVisibleRpAttributes()
    {
        $result = [];

        if ($this->visible_properties) {
            $result = (array)$this->visible_properties;
        } else {
            $this->model->relatedPropertiesModel->initAllProperties();
            $attributes = $this->model->relatedPropertiesModel->toArray();

            if ($attributes) {
                foreach ($attributes as $code => $value) {
                    if ($value) {
                        $result[$code] = $code;
                    }
                }

            }
        }

        if ($this->hidden_properties && $result) {
            foreach ($this->hidden_properties as $property)
            {
                foreach ($result as $key => $value)
                {
                    if ($value == $property) {
                        unset($result[$key]);
                    }
                }
            }
        }
        return $result;
    }
}