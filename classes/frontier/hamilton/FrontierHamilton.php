<?php

class FrontierHamilton extends FrontierPath {
    /**
     * @param Graph $graph
     * @param int $startVertex
     */
    public function __construct($graph) {
        $this->Graph = $graph;

        $this->TerminalFalse = new BinaryNode(false, true);
        $this->TerminalTrue = new BinaryNode(true, true);

        $this->NodeLevels = [];

        $this->GenerateFrontiers();
    }

    /**
     * @return FHTree
     */
    public function GenerateTree() {
        $rootNode = FrontierNode::CreateRootNode($this->Graph);
        $tree = new FrontierTree($rootNode);
        $this->Traverse($tree);
        
        return $tree;
    }

    /**
     * @param FrontierTree $tree
     * @param FrontierNode $node
     * @param int $index
     * @return FrontierNode
     */
    private function Traverse($tree, $node = null, $index = 0) {
        if (!$node && $tree->RootNode) {
            $this->Traverse($tree, $tree->RootNode);
        } else {
            foreach ([false, true] as $useThisEdge) {
                echo "Current: {$index} ({$node->Value}) => " . ($useThisEdge ? '1' : '0') . "\r";
                $terminalStatus = $this->CheckTerminal($node, $useThisEdge, $index);
                
                if (is_null($terminalStatus) && $index + 1 < count($this->Graph->Edges)) {
                    $childNode = $node->ForkChild($this->Graph->Edges[$index + 1]);
                    $node->SetChild($useThisEdge ? 1 : 0, $this->Traverse($tree, $childNode, $index + 1));
                } else {
                    $node->SetChild($useThisEdge ? 1 : 0, $terminalStatus ? $this->TerminalTrue : $this->TerminalFalse);
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
     * @param FrontierNode $node
     * @param bool $useThisEdge
     * @param int $edgeIndex
     * @return bool|null
     */
    private function CheckTerminal($node, $useThisEdge, $edgeIndex) {
        if ($edgeIndex >= count($this->Graph->Edges)) {
            return false;
        }

        $edge = $this->Graph->Edges[$edgeIndex];
        $edgeVertices = $edge->GetVertices();

        if ($useThisEdge) {
            $this->UpdateAfterAddEdge($node, $edgeVertices);
        }

        for ($v = 0; $v < $this->Graph->Vertices; $v++) {
            if ($node->Degrees[$v] != 2) {
                return in_array($v, $this->Frontiers[$edgeIndex + 1]) ? null : false;
            }

            if ($v > 0 && $node->Components[$v] != $node->Components[$v - 1]) {
                return in_array($v, $this->Frontiers[$edgeIndex + 1]) || in_array($v - 1, $this->Frontiers[$edgeIndex + 1]) ?
                        null : false;
            }
        }

        return true;
    }
}