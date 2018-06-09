<?php
namespace TreeMap\Service;

use Doctrine\ORM\EntityManager;
use MediaCompany\Entity\MediaCompanyTargetComposition;
use MediaCompany\Entity\MediaCompanyTargetCompositionLexicon;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRule;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicGroup;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRuleValue;

class MediaCompaniesTargetTreeMapService extends TreeMapService
{
    public function __construct(
        EntityManager $entityManager
    ) {
        parent::__construct(
            $entityManager,
            MediaCompanyTargetCompositionLexicon::class,
            MediaCompanyTargetCompositionUniversalTopicGroup::class,
            MediaCompanyTargetCompositionUniversalTopicRule::class,
            MediaCompanyTargetCompositionUniversalTopicRuleValue::class,
            MediaCompanyTargetComposition::class
        );
    }
}