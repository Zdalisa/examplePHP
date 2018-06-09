<?php
namespace TreeMap\Entity;

use Application\Entity\Mapper;
use Doctrine\ORM\Mapping as ORM;

abstract class CompositionItem extends Mapper
{
    /**
     * @ORM\Column(name="id")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(name="topic_id", type="string")
     */
    public $topicId;

    /**
     * @ORM\Column(type="string")
     */
    public $name;

    /**
     * @ORM\Column(name="is_change_name", type="boolean")
     */
    public $isChangeName;

    /**
     * @ORM\Column(name="value", type="float")
     */
    public $value;

    /**
     * @ORM\Column(type="integer")
     */
    public $color;

    /**
     * @ORM\Column(name="font_color", type="integer")
     */
    public $fontColor;

    /**
     * @ORM\Column(name="is_universal_topic", type="boolean")
     */
    public $isUniversalTopic;

    /**
     * @ORM\Column(type="boolean")
     */
    public $active;

    /**
     * @ORM\Column(name="is_change_original_value", type="boolean")
     */
    public $isChangeOriginalValue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isChangeName = false;
        $this->active = true;
        $this->isChangeOriginalValue = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;

        return $this;
    }

    public function getTopicId()
    {
        return $this->topicId;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIsChangeName($isChangeName)
    {
        $this->isChangeName = $isChangeName;

        return $this;
    }

    public function getIsChangeName()
    {
        return $this->isChangeName;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;

        return $this;
    }

    public function getFontColor()
    {
        return $this->fontColor;
    }

    public function setIsUniversalTopic($isUniversalTopic)
    {
        $this->isUniversalTopic = $isUniversalTopic;

        return $this;
    }

    public function getIsUniversalTopic()
    {
        return $this->isUniversalTopic;
    }

    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setIsChangeOriginalValue($isChangeOriginalValue)
    {
        $this->isChangeOriginalValue = $isChangeOriginalValue;

        return $this;
    }

    public function getIsChangeOriginalValue()
    {
        return $this->isChangeOriginalValue;
    }

    /**
     * @param CompositionItem $parent
     *
     * @return CompositionItem
     */
    abstract public function setParent($parent = null);

    /**
     * @return CompositionItem
     */
    abstract public function getParent();

    /**
     * @param $universalTopicsGroup
     *
     * @return CompositionItem
     */
    abstract public function addUniversalTopicsGroup($universalTopicsGroup);

    /**
     * @param $universalTopicsGroup
     *
     * @return CompositionItem
     */
    abstract public function removeUniversalTopicsGroup($universalTopicsGroup);
    abstract public function getUniversalTopicsGroups();
}