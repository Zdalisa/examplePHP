<?php
namespace TreeMap\Service;

use Doctrine\ORM\EntityManager;
use MediaCompany\Entity\CreativeTaskComposition;
use MediaCompany\Entity\CreativeTaskCompositionLexicon;
use MediaCompany\Entity\UniversalTopicRule;
use MediaCompany\Entity\CreativeTaskCompositionUniversalTopicRuleValue;
use MediaCompany\Entity\UniversalTopicGroup;

class CreativeTaskTreeMapService extends TreeMapService
{
    public function __construct(
        EntityManager $entityManager
    ) {
        parent::__construct(
            $entityManager,
            CreativeTaskCompositionLexicon::class,
            UniversalTopicGroup::class,
            UniversalTopicRule::class,
            CreativeTaskCompositionUniversalTopicRuleValue::class,
            CreativeTaskComposition::class
        );
    }
}