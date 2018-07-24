<?php

class FZddNode extends BinaryNode {
    /** @var int[] $Degrees */
    public $Degrees;
    /** @var int[] $Components */
    public $Components;

    /**
     * Clone method
     * @return FZddNode
     */
    public function __clone() {
        $node = new FZddNode($this->Value, $this->IsTerminal);
        $node->Degrees = $this->Degrees;
        $node->Components = $this->Components;
        return $node;
    }

    /**
     * Fork child with new value but keep degrees and components
     * @param mixed $childVale
     * @return FZddNode
     */
    public function ForkChild($childValue) {
        $node = new FZddNode($childValue, $this->IsTerminal);
        $node->Degrees = $this->Degrees;
        $node->Components = $this->Components;
        return $node;
    }

    /**
     * Combine vertices components of given edge vertices
     * @param int[] $edgeVertices
     */
    public function CombineComponents($edgeVertices) {
        $minComponent = min($this->Components[$edgeVertices[0]], $this->Components[$edgeVertices[1]]);
        $maxComponent = max($this->Components[$edgeVertices[0]], $this->Components[$edgeVertices[1]]);
        foreach ($this->Components as &$component) {
            if ($component == $maxComponent) {
                $component = $minComponent;
            }
        }
        unset($component);
    }

    /**
     * Check if current node is equivalent to given node on specified frontier vertices
     * @param FZddNode $node
     * @param int[] $frontierVertices
     * @return bool
     */
    public function IsEquivalent($node, &$frontierVertices) {
        foreach ($frontierVertices as $vertex) {
            if ($this->Degrees[$vertex] != $node->Degrees[$vertex] || $this->Components[$vertex] != $node->Components[$vertex]) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Create root node from graph (based on first edge)
     * @param Graph $graph
     * @return FZddNode
     */
    public static function CreateRootNode($graph) {
        if (!count($graph->Edges)) {
            throw new Exception('Graph conatins no edges!');
        }
        $rootNode = new FZddNode($graph->Edges[0]);
        $rootNode->Degrees = [];
        $rootNode->Components = [];

        for ($v = 0; $v < $graph->Vertices; $v++) {
            $rootNode->Degrees[$v] = 0;
            $rootNode->Components[$v] = $v;
        }

        return $rootNode;
    }
}