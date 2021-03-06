<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\controllers;
use skeeks\cms\backend\actions\IBackendModelAction;
use skeeks\cms\backend\actions\IBackendModelMultiAction;
use skeeks\cms\IHasInfo;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Object;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * @property $modelClassName;
 * @property $modelDefaultAction;
 * @property $modelShowAttribute;
 * @property $modelPkAttribute;
 * @property $requestPkParamName;
 * @property $modelShowName;
 * @property $modelPkValue;
 *
 * @property Model|ActiveRecord $model;
 * @property IBackendModelAction[] $modelActions;
 * @property IBackendModelMultiAction[] $modelMultiActions;
 *
 * Class BackendModelControllerTrait
 * @package skeeks\cms\backend
 */
trait TBackendModelController
{
    /**
     * @var string
     */
    protected $_modelClassName = '';

    /**
     * Required to fill!
     * The model class with which the controller operates.
     *
     * @example ActiveRecord::class;
     * @var string
     */
    public function getModelClassName()
    {
        return $this->_modelClassName;
    }

    /**
     * @param $modelClassName
     * @return $this
     */
    public function setModelClassName($modelClassName)
    {
        $this->_modelClassName = $modelClassName;
        return $this;
    }


    /**
     * @var string
     */
    protected $_modelDefaultAction = 'update';

    /**
     * Action for controlling the default model
     * @var string
     */
    public function getModelDefaultAction()
    {
        return $this->_modelDefaultAction;
    }

    /**
     * @param $modelDefaultAction
     * @return $this
     */
    public function setModelDefaultAction($modelDefaultAction)
    {
        $this->_modelDefaultAction = $modelDefaultAction;
        return $this;
    }


    /**
     * @var string
     */
    protected $_modelShowAttribute = 'id';

    /**
     * The attribute of the model to be shown in the bread crumbs, and the title of the page.
     * @var string
     */
    public function getModelShowAttribute()
    {
        return $this->_modelShowAttribute;
    }

    /**
     * @param $modelShowAttribute
     * @return $this
     */
    public function setModelShowAttribute($modelShowAttribute)
    {
        $this->_modelShowAttribute = $modelShowAttribute;
        return $this;
    }



    /**
     * @var string
     */
    protected $_modelPkAttribute = 'id';

    /**
     * PK will be used to find the model
     * @var string
     */
    public function getModelPkAttribute()
    {
        return $this->_modelPkAttribute;
    }

    /**
     * @param $modelPkAttribute
     * @return $this
     */
    public function setModelPkAttribute($modelPkAttribute)
    {
        $this->_modelPkAttribute = $modelPkAttribute;
        return $this;
    }


    /**
     * @var string
     */
    protected $_requestPkParamName = 'pk';

    /**
     * The names of the parameter PK, in the query
     * @var string
     */
    public function getRequestPkParamName()
    {
        return $this->_requestPkParamName;
    }

    /**
     * @param $requestPkParamName
     * @return $this
     */
    public function setRequestPkParamName($requestPkParamName)
    {
        $this->_requestPkParamName = $requestPkParamName;
        return $this;
    }


    /**
     * @return string
     */
    public function getModelShowName()
    {
        if (!$this->model)
        {
            return '';
        }

        return isset($this->model->{$this->modelShowAttribute}) ? $this->model->{$this->modelShowAttribute} : '';
    }

    /**
     * @return mixed|string
     */
    public function getModelPkValue()
    {
        if (!$this->model)
        {
            return '';
        }

        return isset($this->model->{$this->modelPkAttribute}) ? $this->model->{$this->modelPkAttribute} : '';
    }


    /**
     * @var null|Model|ActiveRecord
     */
    protected $_model = null;

    /**
     * @return Model|ActiveRecord
     */
    public function getModel()
    {
        if ($this->_model === null && \Yii::$app instanceof Application)
        {
            $pk             = \Yii::$app->request->get($this->requestPkParamName);

            if ($pk)
            {
                $modelClass     = $this->modelClassName;
                $this->_model   = $modelClass::findOne($pk);
            }
        }

        return $this->_model;
    }

    /**
     * @param Model|ActiveRecord $model
     * @return $this
     */
    public function setModel(Object $model = null)
    {
        $this->_model   = $model;
        return $this;
    }





    /**
     * @var IBackendModelAction[]
     */
    protected $_modelActions    = null;

    /**
     * @return BackendAction[]|IBackendModelAction[]
     */
    public function getModelActions()
    {
        if ($this->_modelActions !== null)
        {
            return $this->_modelActions;
        }

        $actions = $this->actions();

        if ($actions)
        {
            foreach ($actions as $id => $data)
            {
                $action = $this->createAction($id);

                if (isset($action->isVisible) && $action->isVisible)
                {
                    if (method_exists($this->model, 'getIsNewRecord'))
                    {
                        if ($this->model && !$this->model->isNewRecord && $action instanceof IBackendModelAction)
                        {
                            $this->_modelActions[$action->id] = $action;
                        }
                    } else
                    {
                        if ($this->model && $action instanceof IBackendModelAction)
                        {
                            $this->_modelActions[$action->id] = $action;
                        }
                    }


                }
            }
        } else
        {
            $this->_modelActions = [];
        }

        if ($this->_modelActions)
        {
            ArrayHelper::multisort($this->_modelActions, 'priority');
        }

        return $this->_modelActions;
    }


    /**
     * @var IBackendModelMultiAction[]
     */
    protected $_modelMultiActions    = null;

    /**
     * @return array|IBackendModelMultiAction[]
     */
    public function getModelMultiActions()
    {
        if ($this->_modelMultiActions !== null)
        {
            return $this->_modelMultiActions;
        }

        $actions = $this->actions();

        if ($actions)
        {
            foreach ($actions as $id => $data)
            {
                $action = $this->createAction($id);

                if ($action instanceof IBackendModelMultiAction)
                {
                    if ($action->isVisible)
                    {
                        $this->_modelMultiActions[$action->id] = $action;
                    }
                }
            }
        } else
        {
            $this->_modelMultiActions = [];
        }

        if ($this->_modelMultiActions)
        {
            ArrayHelper::multisort($this->_modelMultiActions, 'priority');
        }

        return $this->_modelMultiActions;
    }




    /**
     * @throws InvalidConfigException
     */
    protected function _ensureBackendModelController()
    {
        if (!$this->modelClassName)
        {
            throw new InvalidConfigException(\Yii::t('skeeks/cms', "For {modelname} must specify the model class",['modelname' => static::class]));
        }

        if (!class_exists($this->modelClassName))
        {
            throw new InvalidConfigException("{$this->modelClassName} " . \Yii::t('skeeks/cms','the class is not found, you must specify the existing class model'));
        }
    }




    /**
     * Массив объектов действий доступных для текущего контроллера
     * Используется при построении меню.
     * @see ControllerActions
     * @return AdminAction[]
     */
    public function getActions()
    {
        if ($this->_actions !== null)
        {
            return $this->_actions;
        }

        $actions = $this->actions();

        if ($actions)
        {
            foreach ($actions as $id => $data)
            {
                $action                 = $this->createAction($id);

                if (!$action instanceof IBackendModelAction && !$action instanceof IBackendModelMultiAction)
                {
                    if ($action->isVisible)
                    {
                        $this->_actions[$id]    = $action;
                    }
                }
            }
        } else
        {
            $this->_actions = [];
        }

        //Сортировка по приоритетам
        if ($this->_actions)
        {
            ArrayHelper::multisort($this->_actions, 'priority');

        }

        return $this->_actions;
    }



    /**
     * @return array
     */
    public function getBreadcrumbsData()
    {
        $result = [];

        $baseRoute = $this->module instanceof Application ? "/" . $this->id : ("/" . $this->module->id . "/" . $this->id);

        if ($this->name)
        {
            $result[] = [
                'label' => $this->name,
                'url' => $this->url
            ];
        }

        if ($this->action instanceof IBackendModelAction && $this->model)
        {
            $result[] = [
                'label' => $this->modelShowName,
                'url' => $this->action->url
            ];
        }


        if ($this->action && $this->action instanceof IHasInfo)
        {
             $result[] = [
                 'label' => $this->action->name
             ];
        }

        return $result;
    }

}