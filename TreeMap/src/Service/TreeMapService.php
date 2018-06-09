<?php
namespace TreeMap\Service;

use MediaCompany\Entity\CreativeTaskComposition;
use MediaCompany\Entity\MediaCompanyTargetComposition;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicGroup;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRule;
use MediaCompany\Entity\MediaCompanyTargetCompositionUniversalTopicRuleValue;
use MediaCompany\Entity\UniversalTopicRule;
use MediaCompany\Entity\CreativeTaskCompositionUniversalTopicRuleValue;
use TreeMap\Model\CompositionItemKey;
use TreeMap\Model\LexiconKey;
use TreeMap\Model\UniversalTopicsGroupKey;
use TreeMap\Model\UniversalTopicsRuleKey;
use Application\Util\ColorUtilsTrait;
use MediaCompany\Entity\UniversalTopicGroup;
use Vocab\Entity\GuidCategoryType;
use Vocab\Entity\GuidLexiconSentiment;
use Vocab\Entity\GuidUniversalTopicArgument;
use Vocab\Entity\GuidUniversalTopicArgumentType;
use Vocab\Entity\GuidUniversalTopicLogicalExpression;
use Vocab\Entity\GuidUniversalTopicLogicalOperator;

abstract class TreeMapService
{
    use ColorUtilsTrait;

    /**
     * Класс для работы с лексиконами.
     */
    private $lexiconClass;

    /**
     * Класс для работы с группами универсальной темы.
     */
    private $universalTopicGroupClass;

    /**
     * Класс для работы с правиланми универсальной темы.
     */
    private $universalTopicRuleClass;

    /**
     * Класс для работы со значенями правил универсальной темы.
     */
    private $universalTopicRuleValueClass;

    /**
     * Класс для работы с составом тримапа.
     */
    private $compositionClass;

    private $entityManager;

    public function __construct(
        $entityManager,
        $lexiconClass,
        $universalTopicGroupClass,
        $universalTopicRuleClass,
        $universalTopicRuleValueClass,
        $compositionClass
    ) {
        $this->entityManager = $entityManager;

        $this->lexiconClass = $lexiconClass;
        $this->universalTopicGroupClass = $universalTopicGroupClass;
        $this->universalTopicRuleClass = $universalTopicRuleClass;
        $this->universalTopicRuleValueClass = $universalTopicRuleValueClass;
        $this->compositionClass = $compositionClass;
    }

    /**
     * Возвращает подготовленный древовидный массив состава творческогог задания.
     *
     * @param CompositionItemKey $compositionItemKey Объект ключей состава творческого задания.
     * @param array $compositions Массив состава творческого задания.
     * @param null $parentId Идентициактор родителя.
     *
     * @return array
     */
    public function getCompositionsTreeRecursive(CompositionItemKey $compositionItemKey, array $compositions, $parentId = null)
    {
        $list = array_filter($compositions, function ($composition) use ($parentId) {
            if (!$parentId && !$composition->getParent()) {
                return true;
            }

            if (!$composition->getParent()) {
                return false;
            }

            return $composition->getParent()->getId() == $parentId;
        });

        $items = [];
        foreach ($list as $composition) {
            $item = [];
            $compositionArr = $composition->getArrayCopy();

            if ($compositionItemKey->getInternalIdKey()) {
                $item['internalId'] = $compositionArr[$compositionItemKey->getInternalIdKey()];
            }

            if ($compositionItemKey->getNameKey()) {
                $item['name'] = $compositionArr[$compositionItemKey->getNameKey()];
            }

            if ($compositionItemKey->getIdKey()) {
                $item['id'] = $compositionArr[$compositionItemKey->getIdKey()];
            }

            if ($compositionItemKey->getRealValueKey()) {
                $item['realValue'] = $compositionArr[$compositionItemKey->getRealValueKey()];
            }

            if ($compositionItemKey->getColorKey()) {
                $item['color'] = $this->convertIntToRGB($compositionArr[$compositionItemKey->getColorKey()]);
            }

            if ($compositionItemKey->getTopicIdKey()) {
                $item['topicId'] = $compositionArr[$compositionItemKey->getTopicIdKey()];
            }

            if ($compositionItemKey->getIsChangeNameKey()) {
                $item['isChangeName'] = $compositionArr[$compositionItemKey->getIsChangeNameKey()];
            }

            if ($compositionItemKey->getValueKey()) {
                $item['value'] = $compositionArr[$compositionItemKey->getValueKey()];
            }

            if ($compositionItemKey->getFontColorKey()) {
                $item['fontColor'] = $compositionArr[$compositionItemKey->getFontColorKey()];
            }

            if ($compositionItemKey->getIsUniversalTopicKey()) {
                $item['isUniversalTopic'] = $compositionArr[$compositionItemKey->getIsUniversalTopicKey()];
            }

            if ($compositionItemKey->getActiveKey()) {
                $item['active'] = $compositionArr[$compositionItemKey->getActiveKey()];
            }

            if ($compositionItemKey->getIsChangeOriginalValueKey()) {
                $item['isChangeOriginalValue'] = $compositionArr[$compositionItemKey->getIsChangeOriginalValueKey()];
            }

            if ($compositionItemKey->getIsMustNotRemovedKey()) {
                $item['isMustNotRemoved'] = $compositionArr[$compositionItemKey->getIsMustNotRemovedKey()];
            }

            if ($composition->getIsUniversalTopic()) {
                if ($compositionItemKey->getExpressionKey()) {
                    $compositionItemKey->getExpressionKey()->setCompositionKey($composition->getId());
                    $item['expressions'] = $this->getUniversalTopicsGroups(
                        $compositionItemKey->getExpressionKey()
                    );
                }
            } else {
                $item['items'] = $this->getCompositionsTreeRecursive(
                    $compositionItemKey,
                    $compositions,
                    $composition->getId()
                );

                if ($compositionItemKey->getLexiconKey()) {
                    $item['lexicons'] = $this->getLexicons(
                        $compositionItemKey->getLexiconKey(),
                        $composition->getId()
                    );
                }
            }

            $items[] = $item;
            unset($item);
        }

        return $items;
    }

    /**
     * Возвращает подготовленный древовидный массив лексиконов.
     *
     * @param LexiconKey $lexiconKey Объект ключей лексикона.
     * @param number $compositionId Идентификатор топика
     *
     * @return array
     */
    private function getLexicons(LexiconKey $lexiconKey, $compositionId)
    {
        $lexiconList = $this->entityManager
            ->getRepository($this->lexiconClass)
            ->getList(['composition' => $compositionId], null);

        $list = [];
        foreach ($lexiconList as $lexiconItem) {
            $lexicon = [];
            $lexiconArr = $lexiconItem->getArrayCopy();
            $lexiconArr['sentiment'] = $lexiconItem->getSentiment()->getArrayCopy();

            if ($lexiconKey->getIdKey()) {
                $lexicon['id'] = $lexiconArr[$lexiconKey->getIdKey()];
            }

            if ($lexiconKey->getNameKey()) {
                $lexicon['name'] = $lexiconArr[$lexiconKey->getNameKey()];
            }

            if ($lexiconKey->getRealValueKey()) {
                $lexicon['realValue'] = $lexiconArr[$lexiconKey->getRealValueKey()];
            }

            if ($lexiconKey->getValueKey()) {
                $lexicon['value'] = $lexiconArr[$lexiconKey->getValueKey()];
            }

            if ($lexiconKey->getColorKey()) {
                $lexicon['color'] = $this->convertIntToRGB($lexiconArr['sentiment'][$lexiconKey->getColorKey()]);
            }

            if ($lexiconKey->getFontColorKey()) {
                $lexicon['fontColor'] = $lexiconArr['sentiment'][$lexiconKey->getFontColorKey()];
            }

            if ($lexiconKey->getSentimentIdKey()) {
                $lexicon['sentimentId'] = $lexiconArr['sentiment'][$lexiconKey->getSentimentIdKey()];
            }

            if ($lexiconKey->getSentimentKey()) {
                $lexicon['sentiment'] = $lexiconArr['sentiment'][$lexiconKey->getSentimentKey()];
            }

            if ($lexiconKey->getIsChangeOriginalValueKey()) {
                $item['isChangeOriginalValue'] = $lexiconArr[$lexiconKey->getIsChangeOriginalValueKey()];
            }

            $list[] = $lexicon;
            unset($lexicon);
        }

        return $list;
    }

    /**
     * Возвращает подготовленный древовидный массив групп с правилами универсальной темы.
     *
     * @param UniversalTopicsGroupKey $universalTopicsGroupKey Объект ключей группы универсальной темы..
     * @param null $parentGroup Родительская группа.
     *
     * @return array
     */
    private function getUniversalTopicsGroups(
        UniversalTopicsGroupKey $universalTopicsGroupKey,
        $parentGroup = null
    ) {
        $result = [];

        $filter = [
            'parent' => $parentGroup,
            'isActive' => true
        ];

        if ($universalTopicsGroupKey->getCompositionKey()) {
            $filter['composition'] = $universalTopicsGroupKey->getCompositionKey();
        }

        if ($universalTopicsGroupKey->getCreativeTaskKey()) {
            $filter['creativeTask'] = $universalTopicsGroupKey->getCreativeTaskKey();
        }

        if ($universalTopicsGroupKey->getMediaCompanyKey()) {
            $filter['mediaCompany'] = $universalTopicsGroupKey->getMediaCompanyKey();
        }

        $groupList = $this->entityManager
            ->getRepository($this->universalTopicGroupClass)
            ->findBy($filter);

        foreach ($groupList as $groupItem) {
            $groupArr = $groupItem->getArrayCopy();
            $groupArr['logical_operator'] = $groupItem->getLogicalOperator()->getArrayCopy();

            if ($universalTopicsGroupKey->getIdKey()) {
                $group['id'] = $groupArr[$universalTopicsGroupKey->getIdKey()];
            }

            if ($universalTopicsGroupKey->getActiveKey()) {
                $group['isActive'] = $groupArr[$universalTopicsGroupKey->getActiveKey()];
            }

            if ($universalTopicsGroupKey->getLogicalOperatorKey()) {
                $group['logicalOperator'] =
                    $groupArr['logical_operator'][$universalTopicsGroupKey->getLogicalOperatorKey()];
            }


            if ($universalTopicsGroupKey->getLogicalOperatorIdKey()) {
                $group['logicalOperatorId'] =
                    $groupArr['logical_operator'][$universalTopicsGroupKey->getLogicalOperatorIdKey()];
            }

            $group['groups'] = $this->getUniversalTopicsGroups(
                $universalTopicsGroupKey,
                $groupItem->getId()
            );

            if ($universalTopicsGroupKey->getRuleKey()) {
                $group['rules'] = $this->getRuleListUniversalTopic($universalTopicsGroupKey->getRuleKey(), $groupItem);
            }

            if (is_null($parentGroup)) {
                $result = $group;
            } else {
                $result[] = $group;
            }
            unset($group);
        }

        return $result;
    }

    /**
     * Возвращает подготовленный древовидный массив правил универсальной темы.
     *
     * @param UniversalTopicsRuleKey $universalTopicsRuleKey Объект ключей правил универсального топика.
     * @param UniversalTopicGroup | MediaCompanyTargetCompositionUniversalTopicGroup $group Объекты группы.
     *
     * @return array
     */
    private function getRuleListUniversalTopic(UniversalTopicsRuleKey $universalTopicsRuleKey, $group)
    {
        $result = [];
        $ruleList = $this->entityManager
            ->getRepository($this->universalTopicRuleClass)
            ->findBy([
            'group' => $group->getId(),
            'isActive' => true
        ]);

        foreach ($ruleList as $ruleItem) {
            $rule = [];
            $ruleArr = $ruleItem->getArrayCopy();
            $ruleArr['argument'] = $ruleItem->getArgument()->getArrayCopy();
            $ruleArr['categoryType'] = $ruleItem->getCategoryType()->getArrayCopy();
            $ruleArr['logicalExpression'] = $ruleItem->getLogicalExpression()->getArrayCopy();
            $valueRuleList = $this->entityManager
                ->getRepository($this->universalTopicRuleValueClass)
                ->findBy([
                'rule' => $ruleItem->getId()
            ]);

            $ruleArr['isHasSecondValue'] = false;
            foreach ($valueRuleList as $value) {
                if ($value->getIsSecondValue()) {
                    $ruleArr['isHasSecondValue'] = true;
                    $ruleArr['secondValue'] = $value->getValue();
                } else {
                    $ruleArr['firstValue'] = $value->getValue();
                }
                $ruleArr['valueType'] = $value->getType()->getName();
            }

            if ($universalTopicsRuleKey->getIdKey()) {
                $rule['id'] = $ruleArr[$universalTopicsRuleKey->getIdKey()];
            }

            if ($universalTopicsRuleKey->getActiveKey()) {
                $rule['isActive'] = $ruleArr[$universalTopicsRuleKey->getActiveKey()];
            }

            if ($universalTopicsRuleKey->getArgumentIdKey()) {
                $rule['argumentId'] = $ruleArr['argument'][$universalTopicsRuleKey->getArgumentIdKey()];
            }

            if ($universalTopicsRuleKey->getArgumentKey()) {
                $rule['argument'] = $ruleArr['argument'][$universalTopicsRuleKey->getArgumentKey()];
            }

            if ($universalTopicsRuleKey->getCategoryTypeKey()) {
                $rule['categoryType'] = $ruleArr['categoryType'][$universalTopicsRuleKey->getCategoryTypeKey()];
            }

            if ($universalTopicsRuleKey->getCategoryTypeIdKey()) {
                $rule['categoryTypeId'] = $ruleArr['categoryType'][$universalTopicsRuleKey->getCategoryTypeIdKey()];
            }

            if ($universalTopicsRuleKey->getLogicalExpressionKey()) {
                $rule['logicalExpression'] = $ruleArr['logicalExpression'][$universalTopicsRuleKey->getLogicalExpressionKey()];
            }

            if ($universalTopicsRuleKey->getLogicalExpressionIdKey()) {
                $rule['logicalExpressionId'] = $ruleArr['logicalExpression'][$universalTopicsRuleKey->getLogicalExpressionIdKey()];
            }

            if ($universalTopicsRuleKey->getIsHasSecondValueKey()) {
                $rule['isHasSecondValue'] = $ruleArr[$universalTopicsRuleKey->getIsHasSecondValueKey()];
            }

            if ($universalTopicsRuleKey->getValueTypeKey()) {
                $rule['valueType'] = $ruleArr[$universalTopicsRuleKey->getValueTypeKey()];
            }

            if ($universalTopicsRuleKey->getFirstValueKey()) {
                $rule['firstValue'] = $ruleArr[$universalTopicsRuleKey->getFirstValueKey()];
            }

            if ($universalTopicsRuleKey->getSecondValueKey()) {
                $rule['secondValue'] = $ruleArr[$universalTopicsRuleKey->getSecondValueKey()];
            }

            $result[] = $rule;
            unset($rule);

        }
        return $result;
    }

    /**
     * Возвращает подготовленный древовидный массив правил глобальной универсальной темы.
     *
     * @param UniversalTopicsGroupKey $utGroupKey Объект ключей группы универсального топика.
     *
     * @return array
     */
    public function getGlobalUniversalTopic(UniversalTopicsGroupKey $utGroupKey)
    {
        $result['expressions'] = $this->getUniversalTopicsGroups($utGroupKey);
        return $result;
    }

    /**
     * Сохраняет состав глобальной универсальной темы.
     *
     * @param UniversalTopicsGroupKey $utGroupKey Объект ключей группы универсального топика.
     * @param array $globalUT Глобальный универсальный топик.
     *
     * @return void
     */
    public function saveGlobalUniversalTopic(UniversalTopicsGroupKey $utGroupKey, $globalUT)
    {
        $this->saveExpressions($utGroupKey, $globalUT['expressions']);
    }

    /**
     * Сохраняет состав тримапа.
     *
     * @param CompositionItemKey $compositionItemKey Объект ключей состава тримапа.
     * @param array $compositions Состав тримапа.
     *
     * @return void
     */
    public function saveCompositions(CompositionItemKey $compositionItemKey, array $compositions)
    {
        $treeMapId = ($compositionItemKey->getCreativeTaskKey()) ?
            $compositionItemKey->getCreativeTaskKey()->getId() :
            $compositionItemKey->getMediaCompanyKey()->getId();

        $this->entityManager
            ->getRepository($this->compositionClass)
            ->deactivateCompositions($treeMapId);

        $this->saveCompositionsRecursive($compositionItemKey, $compositions);

        $this->entityManager
            ->getRepository($this->compositionClass)
            ->removeDeactivatedCompositions($treeMapId);
    }

    /**
     * Рекурсивно сохраняет состав тримапа.
     *
     * @param CompositionItemKey $compositionItemKey Объект ключей состава тримапа.
     * @param array $compositions Состав тримапа.
     * @param CreativeTaskComposition|MediaCompanyTargetComposition|null $parent
     *
     * @return void
     */
    private function saveCompositionsRecursive(
        CompositionItemKey $compositionItemKey,
        array $compositions,
        $parent = null
    ) {
        if (!empty($compositions)) {
            $topicIds = array_column($compositions, 'id');

            $filter['topicId'] = $topicIds;

            if ($compositionItemKey->getCreativeTaskKey()) {
                $filter['creativeTask'] = $compositionItemKey->getCreativeTaskKey()->getId();
            }

            if ($compositionItemKey->getMediaCompanyKey()) {
                $filter['mediaCompany'] = $compositionItemKey->getMediaCompanyKey()->getId();
            }

            $savedCompositions = $this->entityManager
                ->getRepository($this->compositionClass)
                ->getList(
                    $filter,
                    'topicId'
                );

            foreach ($compositions as $item) {

                if (isset($savedCompositions[$item['id']])) {
                    $composition = $savedCompositions[$item['id']];
                } else {
                    $composition = new $this->compositionClass();

                    if ($compositionItemKey->getCreativeTaskKey()) {
                        $composition->setCreativeTask($compositionItemKey->getCreativeTaskKey());
                    }

                    if ($compositionItemKey->getMediaCompanyKey()) {
                        $composition->setMediaCompany($compositionItemKey->getMediaCompanyKey());
                    }

                    $composition->setTopicId($item['id']);
                }

                $composition->setActive('true')
                    ->setName($item['name'])
                    ->setValue($item["realValue"] ? $item["realValue"] : 0)
                    ->setFontColor($item['fontColor'])
                    ->setColor($this->convertColorToInt($item["color"]))
                    ->setParent($parent);

                if ($compositionItemKey->getIsChangeOriginalValueKey()) {
                    $composition->setIsChangeOriginalValue($item[$compositionItemKey->getIsChangeOriginalValueKey()]);
                }

                if ($compositionItemKey->getIsMustNotRemovedKey()) {
                    $composition->setIsMustNotRemoved($item[$compositionItemKey->getIsMustNotRemovedKey()]);
                }
                if (!$item['isUniversalTopic']) {
                    $composition->setIsUniversalTopic(false);
                } else {
                    $composition->setIsUniversalTopic(true);
                    $utGroupKey = new UniversalTopicsGroupKey();
                    $utGroupKey->setCompositionKey($composition);
                    $this->saveExpressions($utGroupKey, $item['expressions']);
                }

                $this->entityManager->persist($composition);
                $this->entityManager->flush($composition);

                if (!empty($item['items'])) {
                    $this->saveCompositionsRecursive(
                        $compositionItemKey,
                        $item['items'],
                        $composition
                    );
                }

                if (!empty($item['lexicons'])) {

                    $this->entityManager
                        ->getRepository($this->lexiconClass)
                        ->deactivateCompositionsLexicon($composition);

                    $this->saveLexicons(
                        $composition,
                        $item['lexicons']
                    );

                    $this->entityManager
                        ->getRepository($this->lexiconClass)
                        ->removeDeactivatedCompositionsLexicon($composition);
                }
            }
        }
    }

    /**
     * Сохранение лексиконов для категории.
     *
     * @param CreativeTaskComposition|MediaCompanyTargetComposition $composition Состав тримапа.
     * @param array $lexicons Массив с лексиконами.
     *
     * @return void
     */
    private function saveLexicons($composition, array $lexicons)
    {
        $savedLexicons = $this->entityManager
            ->getRepository($this->lexiconClass)
            ->getList([
                'composition' => $composition->getId()
            ],
            'lexiconId'
            );

        foreach ($lexicons as $item) {
            $sentimentId = $this->entityManager
                ->getRepository(GuidLexiconSentiment::class)
                ->findOneById($item['sentimentId']);
            if (isset($savedLexicons[$item['id']])) {
                $lexicon = $savedLexicons[$item['id']];
            } else {
                $lexicon = new $this->lexiconClass();
            }

            $lexicon->setComposition($composition)
                ->setActive('true')
                ->setLexiconId($item['id'])
                ->setName($item['name'])
                ->setSentiment($sentimentId)
                ->setValue($item["realValue"] ? $item["realValue"] : 0);
            $this->entityManager->persist($lexicon);
        }
        $this->entityManager->flush();
    }

    /**
     * Сохранение логических выражений для универсальной темы.
     *
     * @param UniversalTopicsGroupKey $utGroupKey Объект ключей группы универсальной темы.
     * @param array $expressions Массив групп с правилами.
     */
    private function saveExpressions(UniversalTopicsGroupKey $utGroupKey, array $expressions)
    {
        $parentGroup = null;

        if (!empty($expressions['rules'])) {
            $parentGroup = $this->saveUniversalTopicGroup(
                $utGroupKey,
                $expressions
            );
        }

        foreach ($expressions['groups'] as $groupRules) {
            $this->saveUniversalTopicGroup(
                $utGroupKey,
                $groupRules,
                $parentGroup
            );
        }
    }

    /**
     * Сохранение группы с правилами универсальной темы.
     *
     * @param UniversalTopicsGroupKey $utGroupKey Объект ключей группы универсальной темы.
     * @param array $groupArr Массив с правилами.
     * @param Integer | null $parentGroup Родитель группы.
     *
     * @return UniversalTopicGroup | MediaCompanyTargetCompositionUniversalTopicGroup
     */
    private function saveUniversalTopicGroup(
        UniversalTopicsGroupKey $utGroupKey,
        array $groupArr,
        $parentGroup = null)
    {
        if (is_null($groupArr['id'])) {
            $newGroup = new $this->universalTopicGroupClass();
        } else {
            $newGroup = $this->entityManager
                ->getRepository($this->universalTopicGroupClass)
                ->findOneById($groupArr['id']);
        }
        if ($groupArr['isActive']) {
            $logicalOperator = $this->entityManager
                ->getRepository(GuidUniversalTopicLogicalOperator::class)
                ->findOneById($groupArr['logicalOperatorId']);

            $newGroup->setLogicalOperator($logicalOperator)
                ->setIsActive($groupArr['isActive'])
                ->setParent($parentGroup);

            if ($utGroupKey->getCompositionKey()) {
                $newGroup->setComposition($utGroupKey->getCompositionKey());
            }

            if ($utGroupKey->getMediaCompanyKey()) {
                $newGroup->setMediaCompany($utGroupKey->getMediaCompanyKey());
            }

            if ($utGroupKey->getCreativeTaskKey()) {
                $newGroup->setCreativeTask($utGroupKey->getCreativeTaskKey());
            }

            $this->entityManager->persist($newGroup);
            $this->entityManager->flush($newGroup);

            foreach ($groupArr['rules'] as $rule) {
                $this->saveUniversalTopicRule($newGroup, $rule);
            }

        } else {
            $this->entityManager
                ->getRepository($this->universalTopicGroupClass)
                ->delete($newGroup->getId());
        }
        return $newGroup;
    }

    /**
     * Сохранение правила внутри группы универсальной темы.
     *
     * @param UniversalTopicGroup|MediaCompanyTargetCompositionUniversalTopicGroup $group Группа.
     * @param array $rule Массив с правилами.
     *
     * @return UniversalTopicRule|MediaCompanyTargetCompositionUniversalTopicRule
     */
    private function saveUniversalTopicRule($group, array $rule)
    {
        if (is_null($rule['id'])) {
            $newRule = new $this->universalTopicRuleClass();
        } else {
            $newRule = $this->entityManager
                ->getRepository($this->universalTopicRuleClass)
                ->findOneById($rule['id']);
        }

        $newRule->setGroup($group);
        $newRule->setIsActive($rule['isActive']);

        $categoryType = $this->entityManager
            ->getRepository(GuidCategoryType::class)
            ->findOneById($rule['categoryTypeId']);
        $newRule->setCategoryType($categoryType);

        $argument = $this->entityManager
            ->getRepository(GuidUniversalTopicArgument::class)
            ->findOneById($rule['argumentId']);
        $newRule->setArgument($argument);

        $logicalExpression = $this->entityManager
            ->getRepository(GuidUniversalTopicLogicalExpression::class)
            ->findOneById($rule['logicalExpressionId']);
        $newRule->setLogicalExpression($logicalExpression);

        $this->entityManager->persist($newRule);
        $this->entityManager->flush($newRule);

        $this->saveUniversalTopicRuleValue(
            $newRule,
            $rule['firstValue'],
            $rule['valueType'],
            false,
            $rule['id']
        );

        if ($rule['secondValue']) {
            $this->saveUniversalTopicRuleValue(
                $newRule,
                $rule['secondValue'],
                $rule['valueType'],
                true,
                $rule['id']
            );
        }

        return $newRule;
    }

    /**
     * Сохранение значения для правила универсальной темы.
     *
     * @param UniversalTopicRule|MediaCompanyTargetCompositionUniversalTopicRule $rule Правило универсальной темы.
     * @param any $value Значение.
     * @param number $valueTypeId Тип значения.
     * @param bool $isSecondValue Является вторым значением.
     * @param null $ruleId id переданного правила.
     *
     * @return CreativeTaskUniversalTopicRuleValue|MediaCompanyTargetCompositionUniversalTopicRuleValue|null|
     */
    private function saveUniversalTopicRuleValue(
        $rule,
        $value,
        $valueTypeId,
        $isSecondValue = false,
        $ruleId = null)
    {
        $type = $this->entityManager
            ->getRepository(GuidUniversalTopicArgumentType::class)
            ->findOneBy(['name' => $valueTypeId]);
        if (is_null($ruleId)) {
            $valueRule =  new $this->universalTopicRuleValueClass();
        } else {
            $valueRule =  $this->entityManager
                ->getRepository($this->universalTopicRuleValueClass)
                ->findOneBy(['rule' => $ruleId]);
        }

        $valueRule
            ->setType($type)
            ->setRule($rule)
            ->setValue($value)
            ->setIsSecondValue($isSecondValue);

        $this->entityManager->persist($valueRule);
        $this->entityManager->flush($valueRule);
        return $valueRule;
    }
}