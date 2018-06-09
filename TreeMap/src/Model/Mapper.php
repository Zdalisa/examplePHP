<?php
namespace TreeMap\Model;

abstract class Mapper
{
    const SET = 'set';
    const GET = 'get';

    public function __call($name, $arguments) {
        $callFunc = (strpos($name, $this::SET) !== false) ? $this::SET : $this::GET;
        $varName = lcfirst(str_replace([$this::SET, $this::GET], '', $name));
        if (!property_exists($this, $varName)) {
            throw new \Exception('No var in object');
        }
        switch($callFunc) {
            case $this::SET:
                $this->$varName = array_shift($arguments);
                return $this;
            case $this::GET:
                return $this->$varName;
        }
    }
}