<?php

class DecisionPath {
    /** @var BinaryNode[] $Path */
    public $Path;
    /** @var BinaryNode $Terminal */
    public $Terminal;

    /**
     * @param BinaryNode[] $path
     * @param BinaryNode $terminal
     */
    public function __construct($path, $terminal = null) {
        $this->Path = $path;
        $this->Terminal = $terminal;
    }

    /**
     * @return string
     */
    public function __toString() {
        $str = implode(' -> ', $this->Path);
        if ($this->Terminal) {
            $str .= ' => ' . $this->Terminal;
        }

        return $str;
    }
}