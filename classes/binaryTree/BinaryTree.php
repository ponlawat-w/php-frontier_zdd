<?php

class BinaryTree {
    /** @var BinaryNode $RootNode */
    public $RootNode;

    /**
     * @param BinaryNode $root
     */
    public function __construct($root) {
        $this->RootNode = $root;
    }

    /**
     * @return array[]
     */
    public function GetPaths() {
        if (!$this->RootNode) {
            return [];
        }

        $paths = [];
        $this->RootNode->RecursivePath($paths);

        return $paths;
    }

    /**
     * @param int $count
     * @param bool $terminalTrueOnly
     */
    public function PrintPaths(&$count = 0, $terminalTrueOnly = true) {
        if (!$this->RootNode) {
            return;
        }

        $this->RootNode->RecursivePrintPaths($count, $terminalTrueOnly);
    }
}