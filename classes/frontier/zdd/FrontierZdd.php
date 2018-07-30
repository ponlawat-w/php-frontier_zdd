<?php

class FrontierZdd extends FrontierPath {
    /** @var int $Origin */
    public $Origin;
    /** @var int $Destination */
    public $Destination;

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
     * @return FrontierTree
     * @throws
     */
    public function GenerateTree() {
        $rootNode = FrontierNode::CreateRootNode($this->Graph);
        $tree = new FrontierTree($rootNode);
        $this->Traverse($tree);

        return $tree;
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
     * @param FrontierTree $tree
     * @param FrontierNode $node
     * @param int $index
     * @param int $state
     * @return FrontierNode
     */
    private function Traverse($tree, $node = null, $index = 0, $state = 0) {
        if (!$node && $tree->RootNode) {
            // Start traverse from root Node
            $this->Traverse($tree, $tree->RootNode);
        } else {
            foreach ([false, true] as $useThis) {
                if (count($this->Graph->Edges) < 65) {
                    $b = $useThis ? 1 : 0;
                    $b <<= count($this->Graph->Edges) - $index - 1;
                    $thisState = $state | $b;
                    echo '>' . sprintf('%' . count($this->Graph->Edges) . 's', decbin($thisState)) . "\r";
                } else {
                    echo "Current: {$index} ({$node->Value}) => " . ($useThis ? '1' : '0') . "\r";
                }
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
                        $node->SetChild($useThis ? 1 : 0, $this->Traverse($tree, $childNode, $index + 1, $thisState));
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
     * Check if current node performs successful path or not
     * Returns true when origin vertex completely connects with destination vertex
     * Returns false when it is impossible to create any successful path from this state
     * Returns null when the path is not complete and must be considered for more edges
     * @param FrontierNode $node
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

        return $edgeIndex < count($this->Graph->Edges) - 1 ? null : true;

        // for ($v = 0; $v < $this->Graph->Vertices; $v++) {
        //     if ($this->IsOriginDestination($v)) {
        //         if ($node->Degrees[$v] != 1) {
        //             // Path is not finish because degrees of origin and destination is not 1
        //             return null;
        //         }
        //     } else if ($node->Degrees[$v] != 0 && $node->Degrees[$v] != 2) {
        //         // Path is not finish when any vertex except origin and destination contains degree 1
        //         return null;
        //     }
        // }

        // // All cases are cleared, path is successful
        // return true;
    }
}