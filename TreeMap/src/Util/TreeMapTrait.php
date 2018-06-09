<?php
namespace TreeMap\Util;

use Application\Util\ColorUtilsTrait;
use TreeMap\Entity\CompositionItem;

trait TreeMapTrait
{
    use ColorUtilsTrait;

    /**
     * Рекурсивно создаёт многоуровневый массив(дерево) для тримапа.
     *
     * @param CompositionItem[] $compositions Массив из "топиков" для тримапа.
     * @param null $parentId Идентификатор "родителя". Используется в рекурсии.
     *
     * @return array
     */
    public function getCompositionsTreeRecursive(array $compositions, $parentId = null)
    {
        /**
         * @var $list CompositionItem[]
         */
        $list = array_filter($compositions, function (CompositionItem $composition) use ($parentId) {
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
            $itemArr = $composition->getArrayCopy();
            $itemArr['internalId'] = $itemArr['id'];
            $itemArr['id'] = $itemArr['topicId'];
            $itemArr['realValue'] = $itemArr['value'];
            $itemArr['color'] = $this->convertIntToRGB($itemArr['color']);
            if ($composition->getIsUniversalTopic()) {
                $itemArr['expressions'] = $this->getTreeMapUniversalTopicsGroups($composition);
            } else {
                $itemArr['items'] = $this->getCompositionsTreeRecursive($compositions, $composition->getId());
                $itemArr['lexicons'] = $this->getLexicons($itemArr['internalId']);
            }

            $items[] = $itemArr;
        }

        return $items;
    }

    abstract protected function getTreeMapUniversalTopicsGroups(CompositionItem $universalTopic, $parentGroup = null);
}