<?php

class BinaryNode {
    /** @var mixed $Value */
    public $Value;

    /** @var BinaryNode[] $Children */
    public $Children;

    /** @var bool $IsTerminal */
    public $IsTerminal;

    public function __construct($value, $isTerminal = false) {
        $this->Value = $value;
        $this->Children = [null, null];
        $this->IsTerminal = $isTerminal;
    }

    /**
     * @return BinaryNode
     */
    public function GetFalseChild() {
        return $this->Children[0];
    }

    /**
     * @return BinaryNode
     */
    public function GetTrueChild() {
        return $this->Children[1];
    }

    /**
     * @param BinaryNode $binaryNode
     */
    public function SetFalseChild($binaryNode) {
        $this->Children[0] = $binaryNode;
    }

    /**
     * @param BinaryNode $binaryNode
     */
    public function SetTrueChild($binaryNode) {
        $this->Children[1] = $binaryNode;
    }

    /**
     * @param array[] &$paths
     * @param array &$temp
     */
    public function RecursivePath(&$paths, &$temp = []) {
        if (is_null($this->Children[0]) && is_null($this->Children[1])) {
            $path = $this->IsTerminal ? new DecisionPath($temp, $this) : new DecisionPath(array_merge($temp, [$this]));
            $paths[] = $path;
        } else {
            if ($this->Children[0]) {
                $newTemp = $temp;
                $this->Children[0]->RecursivePath($paths, $newTemp);
            }
            if ($this->Children[1]) {
                $newTemp = array_merge($temp, [$this]);
                $this->Children[1]->RecursivePath($paths, $newTemp);
            }
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        if (is_bool($this->Value)) {
            return $this->Value ? 'true' : 'false';
        }

        $str = $this->Value->__toString();
        return is_string($str) ? $str : 'NODE';
    }
}