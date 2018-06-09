<?php
namespace TreeMap\Model;

use MediaCompany\Entity\CreativeTask;
use MediaCompany\Entity\MediaCompany;
use TreeMap\Model\BaseItemKey;
use TreeMap\Model\LexiconKey;
use TreeMap\Model\UniversalTopicsGroupKey;

class CompositionItemKey extends BaseItemKey
{
    protected $internalIdKey;
    protected $topicIdKey;
    protected $isUniversalTopicKey;
    protected $isMustNotRemovedKey;

    /**
     * @var CreativeTask
     */
    protected $creativeTaskKey;

    /**
     * @var MediaCompany
     */
    protected $mediaCompanyKey;

    /**
     * @var LexiconKey
     */
    private $lexiconKey;

    /**
     * @var UniversalTopicsGroupKey
     */
    private $expression;

    public function setLexiconKey(LexiconKey $value)
    {
        $this->lexiconKey = $value;

        return $this;
    }

    public function getLexiconKey()
    {
        return $this->lexiconKey;
    }

    public function setExpressionKey(UniversalTopicsGroupKey $value)
    {
        $this->expression = $value;

        return $this;
    }

    public function getExpressionKey()
    {
        return $this->expression;
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
}