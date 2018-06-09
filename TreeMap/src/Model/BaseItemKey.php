<?php
namespace TreeMap\Model;

use TreeMap\Model\Mapper;

abstract class BaseItemKey extends Mapper
{
    protected $idKey;
    protected $nameKey;
    protected $realValueKey;
    protected $colorKey;
    protected $isChangeNameKey;
    protected $valueKey;
    protected $fontColorKey;
    protected $activeKey;
    protected $isChangeOriginalValueKey;
}