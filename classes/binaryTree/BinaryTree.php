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
}