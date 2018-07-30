<?php
abstract class FrontierPath {
    /** @var Graph $Graph */
    public $Graph;

    /** @var int[][] $Frontiers */
    protected $Frontiers;

    /** @var BinaryNode $TerminalTrue */
    protected $TerminalTrue;
    /** @var BinaryNode $TerminalFalse */
    protected $TerminalFalse;

    /** @var FZddNode[][] $NodeLevels */
    protected $NodeLevels;

    /**
     * Check if given vertex connects to edge that will be considered in next edges
     * @param int $vertex
     * @param int $presentEdgeIndex
     * @return bool
     */
    private function VertexIsUsedInFuture($vertex, $presentEdgeIndex) {
        for ($e = $presentEdgeIndex + 1; $e < count($this->Graph->Edges); $e++) {
            if ($this->Graph->Edges[$e]->Connects($vertex)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate frontiers set for all edges
     */
    protected function GenerateFrontiers() {
        $this->Frontiers = [[]];

        for ($e = 0; $e < count($this->Graph->Edges); $e++) {
            $this->Frontiers[$e + 1] = $this->Frontiers[$e];
            $currentFrontier = &$this->Frontiers[$e + 1];
            $edge = $this->Graph->Edges[$e];
            foreach ($edge->GetVertices() as $v) {
                if (!in_array($v, $currentFrontier)) {
                    $currentFrontier[] = $v;
                }

                if (!$this->VertexIsUsedInFuture($v, $e)) {
                    $vertexIndexInCurrentFrontier = array_search($v, $currentFrontier);
                    array_splice($currentFrontier, $vertexIndexInCurrentFrontier, 1);
                }
            }
        }
    }
    
    /**
     * Find equivalent node, return null if the node does not exist
     * @param FZddNode $node
     * @param int $index
     * @param int[] $frontierVertices
     * @return FZddNode|null
     */
    protected function FindEquivalentNode($node, $index, $frontierVertices) {
        if (!isset($this->NodeLevels[$index])) {
            return null;
        }

        foreach ($this->NodeLevels[$index] as $sameLevelNode) {
            if ($sameLevelNode->IsEquivalent($node, $frontierVertices)) {
                return $sameLevelNode;
            }
        }

        return null;
    }

    /**
     * Update degrees and components on given node if specified edge vertices are chosen
     * @param FZddNode $node
     * @param int[] $edgeVertices
     */
    protected function UpdateAfterAddEdge($node, $edgeVertices) {
        foreach ($edgeVertices as $v) {
            $node->Degrees[$v]++;
        }
        $node->CombineComponents($edgeVertices);
    }
}