<?php
namespace TreeMap\Model;

use MediaCompany\Entity\CreativeTask;
use MediaCompany\Entity\CreativeTaskComposition;
use MediaCompany\Entity\MediaCompany;
use MediaCompany\Entity\MediaCompanyTargetComposition;
use TreeMap\Model\UniversalTopicsRuleKey;
use TreeMap\Model\Mapper;

class UniversalTopicsGroupKey extends Mapper
{
    protected $idKey;
    protected $activeKey;
    protected $logicalOperatorIdKey;
    protected $logicalOperatorKey;

    /**
     * @var UniversalTopicsRuleKey
     */
    private $ruleKey;

    /**
     * @var CreativeTask
     */
    protected $creativeTaskKey;

    /**
     * @var MediaCompany
     */
    protected $mediaCompanyKey;

    /**
     * @var CreativeTaskComposition|MediaCompanyTargetComposition $universalTopic
     */
    protected $compositionKey;

    public function setRuleKey(UniversalTopicsRuleKey $value)
    {
        $this->ruleKey = $value;

        return $this;
    }

    public function getRuleKey()
    {
        return $this->ruleKey;
    }

    public function setCreativeTaskKey(CreativeTask $value)
    {
        $this->creativeTaskKey = $value;

        return $value;
    }

    public function setMediaCompanyKey(MediaCompany $value)
    {
        $this->mediaCompanyKey = $value;

        return $value;
    }

    /**
     * @return CreativeTaskComposition|MediaCompanyTargetComposition
     */
    public function setCompositionKey($value)
    {
        $this->compositionKey = $value;

        return $value;
    }
}