<?php

class FrontierZdd {
    /** @var Graph $Graph */
    public $Graph;

    /** @var int $Origin */
    public $Origin;
    /** @var int $Destination */
    public $Destination;

    /** @var int[][] $Frontiers */
    private $Frontiers;

    /** @var BinaryNode $TerminalTrue */
    private $TerminalTrue;
    /** @var BinaryNode $TerminalFalse */
    private $TerminalFalse;

    /** @var FZddNode[][] $NodeLevels */
    private $NodeLevels;

    /**
     * Constructor
     * @param Graph $graph
     * @param int $origin
     * @param int $destination
     */
    public function __construct($graph, $origin = 0, $destination = 0) {
        $this->Graph = $graph;
        $this->Origin = $origin;
        $this->Destination = $destination ? $destination : $this->Graph->Vertices - 1;

        $this->TerminalFalse = new BinaryNode(false, true);
        $this->TerminalTrue = new BinaryNode(true, true);

        $this->NodeLevels = [];

        $this->GenerateFrontiers();
    }

    /**
     * Generate binary decision tree for all available paths from Origin to Destination
     * @return FZddTree
     * @throws
     */
    public function GenerateTree() {
        $rootNode = FZddNode::CreateRootNode($this->Graph);
        $tree = new FZddTree($rootNode);
        $this->Traverse($tree);

        return $tree;
    }

    /**
     * Generate frontiers set for all edges
     */
    private function GenerateFrontiers() {
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
     * @param int $vertex
     * @return bool
     */
    private function IsOriginDestination($vertex) {
        return ($vertex == $this->Origin || $vertex == $this->Destination);
    }

    /**
     * Traverse given node in tree on specified index
     * @param FZddTree $tree
     * @param FZddNode $node
     * @param int $index
     * @return FZddNode
     */
    private function Traverse($tree, $node = null, $index = 0) {
        if (!$node && $tree->RootNode) {
            // Start traverse from root Node
            $this->Traverse($tree, $tree->RootNode);
        } else {
            foreach ([false, true] as $useThis) {
                echo "Current: {$index} ({$node->Value}) => " . ($useThis ? '1' : '0') . "\r";
                $terminalStatus = $this->CheckTerminal($node, $useThis, $index);
                if (is_null($terminalStatus) && $index + 1 < count($this->Graph->Edges)) {
                    // Path is not complete
                    $childNode = $node->ForkChild($this->Graph->Edges[$index + 1]);

                    $tempNode = clone $childNode;
                    if ($useThis) {
                        $this->UpdateAfterAddEdge($tempNode, $this->Graph->Edges[$index + 1]->GetVertices());
                    }
                    $equivalentNode = $this->FindEquivalentNode($tempNode, $index + 1, $this->Frontiers[$index + 2]);
                    if ($equivalentNode) {
                        // Equivalent node (of frontiers) exists, and must be pointed from current node
                        $node->SetChild($useThis ? 1 : 0, $equivalentNode);
                    } else {
                        // Traverse normally in deeper level
                        $node->SetChild($useThis ? 1 : 0, $this->Traverse($tree, $childNode, $index + 1));
                    }
                } else {
                    // Path is complete whether successfull or not, point current node to corresponding terminal node
                    $node->SetChild($useThis ? 1 : 0, $terminalStatus ? $this->TerminalTrue : $this->TerminalFalse);
                }
            }
        }

        if (!isset($this->NodeLevels[$index])) {
            $this->NodeLevels[$index] = [];
        }
        $this->NodeLevels[$index][] = $node;

        return $node;
    }

    /**
     * Find equivalent node, return null if the node does not exist
     * @param FZddNode $node
     * @param int $index
     * @param int[] $frontierVertices
     * @return FZddNode|null
     */
    private function FindEquivalentNode($node, $index, &$frontierVertices) {
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
    private function UpdateAfterAddEdge($node, $edgeVertices) {
        foreach ($edgeVertices as $v) {
            $node->Degrees[$v]++;
        }
        $node->CombineComponents($edgeVertices);
    }

    /**
     * Check if current node performs successful path or not
     * Returns true when origin vertex completely connects with destination vertex
     * Returns false when it is impossible to create any successful path from this state
     * Returns null when the path is not complete and must be considered for more edges
     * @param FZddNode $node
     * @param bool $useThis
     * @param int $edgeIndex
     * @return bool|null
     */
    private function CheckTerminal($node, $useThis, $edgeIndex) {
        if ($edgeIndex >= count($this->Graph->Edges)) {
            // Just in case
            return false;
        }

        $edge = $this->Graph->Edges[$edgeIndex];
        $edgeVertices = $edge->GetVertices();
        if ($useThis && $node->Components[$edgeVertices[0]] == $node->Components[$edgeVertices[1]]) {
            // Loop occurs
            return false;
        }

        if ($useThis) {
            $this->UpdateAfterAddEdge($node, $edgeVertices);
        }

        foreach ($edgeVertices as $v) {
            $degree = $node->Degrees[$v];
            $completelyConsidered = !in_array($v, $this->Frontiers[$edgeIndex + 1]);
            // If considering edge vertices are not in frontiers set of current edge,
            // it means that the vertex does not connect to any edge that will be considered in the future

            if ($this->IsOriginDestination($v)) {
                if ($degree > 1 || ($completelyConsidered && $degree != 1)) {
                    // Degrees of origin and destination must not exceed 1
                    return false;
                }
            } else {
                if ($degree > 2 || ($completelyConsidered && $degree != 2 && $degree != 0)) {
                    // Degrees of other vertices must not exceed 2
                    return false;
                }
            }
        }

        for ($v = 0; $v < $this->Graph->Vertices; $v++) {
            if ($this->IsOriginDestination($v)) {
                if ($node->Degrees[$v] != 1) {
                    // Path is not finish because degrees of origin and destination is not 1
                    return null;
                }
            } else if ($node->Degrees[$v] != 0 && $node->Degrees[$v] != 2) {
                // Path is not finish when any vertex except origin and destination contains degree 1
                return null;
            }
        }

        // All cases are cleared, path is successful
        return true;
    }
}