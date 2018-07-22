<?php

class FullSpanBinaryTree {
    /** @var Graph $Graph */
    private $Graph;

    /** @var int $Origin */
    private $Origin;
    /** @var int $Destination */
    private $Destination;

    /** @var BinaryNode $TerminalFalse */
    private $TerminalFalse;
    /** @var BinaryNode $TerminalTrue */
    private $TerminalTrue;

    /**
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
    }

    /**
     * @return BinaryTree
     */
    public function GenerateTree() {
        if (!count($this->Graph->Edges)) {
            return null;
        }

        $root = new BinaryNode($this->Graph->Edges[0]);

        $tree = new BinaryTree($root);
        $this->Traverse($tree->RootNode, 0);

        return $tree;
    }

    private function Traverse($node, $index, $path = []) {
        $currentEdge = $this->Graph->Edges[$index];

        $pathExcludeThis = $path;
        $pathIncludeThis = array_merge($path, [$currentEdge]);

        if ($index < count($this->Graph->Edges) - 1) {
            $nextEdge = $this->Graph->Edges[$index + 1];

            $falseChild = $this->Traverse(new BinaryNode($nextEdge), $index + 1, $pathExcludeThis);
            $trueChild = $this->Traverse(new BinaryNode($nextEdge), $index + 1, $pathIncludeThis);
        } else {
            $falseChild = $this->ValidatePath($pathExcludeThis) ? $this->TerminalTrue : $this->TerminalFalse;
            $trueChild = $this->ValidatePath($pathIncludeThis) ? $this->TerminalTrue : $this->TerminalFalse;
        }

        $node->SetFalseChild($falseChild);
        $node->SetTrueChild($trueChild);

        return $node;
    }

    private function ValidatePath($path) {
        for ($v = 0; $v < $this->Graph->Vertices; $v++) {
            if (($v == $this->Origin || $v == $this->Destination) && Graph::GetDegree($v, $path) != 1) {
                return false;
            } else if ($v != $this->Origin && $v != $this->Destination) {
                $degree = Graph::GetDegree($v, $path);
                if ($degree != 0 && $degree != 2) {
                    return false;
                }
            }
        }

        return true;
    }
}