<?php

namespace TreeMap\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vocab\Entity\GuidUniversalTopicArgumentType;
use Application\Entity\Mapper;

/**
 * Class UniversalTopicRuleValue2
 * @package TreeMap\Entity
 */
abstract class UniversalTopicRuleValue extends Mapper
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(name="string_value", type="string")
     */
    public $stringValue;

    /**
     * @ORM\Column(name="date_value", type="datetime")
     */
    public $dateValue;

    /**
     * @ORM\Column(name="number_value", type="float")
     */
    public $numberValue;

    /**
     * @ORM\Column(name="is_second_value", type="boolean")
     */
    public $isSecondValue;

    /**
     * @ORM\ManyToOne(targetEntity="Vocab\Entity\GuidUniversalTopicArgumentType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    public $type;

    /**
     * @var array
     *
     * @ORM\Column(name="json_value", type="json_array")
     */
    public $jsonValue;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param any $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        switch ($this->type->getName()) {
            case GuidUniversalTopicArgumentType::TYPE_NUMBER : {
                $this->setNumberValue($value);
                break;
            }
            case GuidUniversalTopicArgumentType::TYPE_DATE : {
                $this->setDateValue(new \DateTime($value));
                break;
            }
            case GuidUniversalTopicArgumentType::TYPE_TEXT : {
                $this->setStringValue($value);
                break;
            }
            case GuidUniversalTopicArgumentType::TYPE_JSON : {
                $this->setJsonValue($value);
                break;
            }
        }

        return $this;
    }

    /**
     * Get value
     */
    public function getValue()
    {
        switch ($this->type->getName()) {
            case GuidUniversalTopicArgumentType::TYPE_NUMBER :
                return $this->getNumberValue();
            case GuidUniversalTopicArgumentType::TYPE_DATE :
                return $this->getDateValue()->format('Y-m-d');
            case GuidUniversalTopicArgumentType::TYPE_TEXT :
                return $this->getStringValue();
            case GuidUniversalTopicArgumentType::TYPE_JSON :
                return $this->getJsonValue();
        }
    }

    /**
     * Set stringValue
     *
     * @param string $stringValue
     *
     * @return $this
     */
    public function setStringValue($stringValue)
    {
        $this->stringValue = $stringValue;

        return $this;
    }

    /**
     * Get stringValue
     *
     * @return string
     */
    public function getStringValue()
    {
        return $this->stringValue;
    }

    /**
     * Set dateValue
     *
     * @param \DateTime $dateValue
     *
     * @return $this
     */
    public function setDateValue($dateValue)
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    /**
     * Get dateValue
     *
     * @return \DateTime
     */
    public function getDateValue()
    {
        return $this->dateValue;
    }

    /**
     * Set numberValue
     *
     * @param float $numberValue
     *
     * @return $this
     */
    public function setNumberValue($numberValue)
    {
        $this->numberValue = $numberValue;

        return $this;
    }

    /**
     * Get numberValue
     *
     * @return float
     */
    public function getNumberValue()
    {
        return $this->numberValue;
    }

    /**
     * Set isSecondValue
     *
     * @param boolean $isSecondValue
     *
     * @return $this
     */
    public function setIsSecondValue($isSecondValue)
    {
        $this->isSecondValue = $isSecondValue;

        return $this;
    }

    /**
     * Get isSecondValue
     *
     * @return boolean
     */
    public function getIsSecondValue()
    {
        return $this->isSecondValue;
    }

    /**
     * Set type
     *
     * @param \Vocab\Entity\GuidUniversalTopicArgumentType $type
     *
     * @return $this
     */
    public function setType(GuidUniversalTopicArgumentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Vocab\Entity\GuidUniversalTopicArgumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set jsonValue
     *
     * @param array $jsonValue
     *
     * @return $this
     */
    public function setJsonValue($jsonValue)
    {
        $this->jsonValue = $jsonValue;

        return $this;
    }

    /**
     * Get jsonValue
     *
     * @return array
     */
    public function getJsonValue()
    {
        return $this->jsonValue;
    }

    /**
     * Set rule
     *
     * @param any $rule
     *
     * @return \MediaCompany\Entity\UniversalTopicRuleValue|\MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRule
     */
    abstract public function setRule($rule = null);

    /**
     * Get rule
     *
     * @return \MediaCompany\Entity\UniversalTopicRuleValue|\MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRule
     */
    abstract public function getRule();
}