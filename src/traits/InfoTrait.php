<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\traits;

/**
 * @property $name;
 * @property $icon;
 * @property $image;
 *
 * Class InfoTrait
 * @package skeeks\cms\backend
 */
trait InfoTrait
{
    /**
     * @var string
     */
    protected $_name = '';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }


    /**
     * @var string
     */
    protected $_icon = '';

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * @param $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }


    /**
     * @var string
     */
    protected $_image = '';

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->_image = $image;
        return $this;
    }
}