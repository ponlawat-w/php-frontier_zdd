<?php

class FZddTree extends BinaryTree {
    /**
     * @var FZddNode $node
     */
    public $RootNode;

    /**
     * Get node array in specified level
     * @param int $level
     * @return FZddNode[]
     */
    private function GetNodesInLevel($level) {
        $nodes = [];
        $this->RootNode->FindNodesInLevel($level, $nodes);
        return $nodes;
    }

    /**
     * Find node that is equivalent to given node
     * @param FZddNode $node
     * @param int $index
     * @param int[] $frontierVertices
     * @return FZddNode|null
     */
    public function FindEquivalentNode($comparingNode, $index, &$frontierVertices) {
        $nodes = $this->GetNodesInLevel($index);
        foreach ($nodes as $node) {
            if ($node->IsEquivalent($comparingNode, $frontierVertices)) {
                return $node;
            }
        }

        return null;
    }
}