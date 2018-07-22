<?php

class SimpleZdd {
    /** @var Graph $Graph */
    public $Graph;

    /** @var int $Origin */
    public $Origin;
    /** @var int $Destination */
    public $Destination;

    /** @var BinaryNode $TerminalFalse */
    private $TerminalFalse;
    /** @var BinaryNode $TerminalTrue */
    private $TerminalTrue;

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
    }

    /**
     * Generate binary decision tree
     * @return BinaryTree
     */
    public function GenerateTree() {
        if (!count($this->Graph->Edges)) {
            return null;
        }

        $rootNode = new BinaryNode($this->Graph->Edges[0]);
        $tree = new BinaryTree($rootNode);
        $this->Traverse($tree->RootNode);

        return $tree;
    }

    /**
     * Generate components for all vertices
     * @return int[]
     */
    private function GenerateDefaultComponents() {
        $components = [];
        for ($v = 0; $v < $this->Graph->Vertices; $v++) {
            $components[$v] = $v;
        }

        return $components;
    }

    /**
     * Recursively traverse on specified node, terminates when unsuccessful path is found
     * @param BinaryNode $node
     * @param int $index
     * @param Edge[] $enabledEdges
     * @param Edge[] $disabledEdges
     * @param int[] $components
     * @return BinaryNode
     */
    private function Traverse($node, $index = 0, $enabledEdges = [], $disabledEdges = [], $components = null) {
        if (!$components) {
            $components = $this->GenerateDefaultComponents();
        }

        $currentEdge = $this->Graph->Edges[$index];

        foreach ([false, true] as $useThisEdge) {
            if ($useThisEdge) {
                $enabledEdges[] = $currentEdge;
            } else {
                $disabledEdges[] = $currentEdge;
            }

            $thisComponents = $components;
            $pathStatus = ($useThisEdge && $this->CausesLoop($thisComponents, $currentEdge)) ?
                false : $this->CheckPath($enabledEdges, $disabledEdges);

            $childNode = null;
            if (is_null($pathStatus)) {
                // Path is not complete yet
                if ($index + 1 < count($this->Graph->Edges)) {
                    // Traverse to next edge
                    $childNode = $this->Traverse(
                        new BinaryNode($this->Graph->Edges[$index + 1]),
                        $index + 1,
                        $enabledEdges,
                        $disabledEdges,
                        $thisComponents
                    );
                } else {
                    // All edges are considered, this should not happen
                    $childNode = $this->TerminalFalse;
                }
            } else if ($pathStatus === true) {
                // Path successfully connects origin and destination
                $childNode = $this->TerminalTrue;
            } else if ($pathStatus === false) {
                // Path is impossible to be succesfully established
                $childNode = $this->TerminalFalse;
            }

            if ($useThisEdge) {
                $node->SetTrueChild($childNode);
            } else {
                $node->SetFalseChild($childNode);
            }
        }

        return $node;
    }

    /**
     * Combine components of vertices on following edge
     * @param int[] $components
     * @param Edge $edge
     */
    private static function CombineComponents(&$components, $edge) {
        $vertices = $edge->GetVertices();
        $minComponentVertex = $components[$vertices[0]] < $components[$vertices[1]] ? 0 : 1;
        $minComponent = $components[$vertices[$minComponentVertex]];
        $maxComponent = $components[$vertices[$minComponentVertex ? 0 : 1]];

        for ($c = 0; $c < count($components); $c++) {
            if ($components[$c] == $maxComponent) {
                $components[$c] = $minComponent;
            }
        }
    }

    /**
     * Check if given edge causes loop to current component values
     * @param int[] $components
     * @param Edge $newEdge
     * @return bool
     */
    private function CausesLoop(&$components, $newEdge) {
        $vertices = $newEdge->GetVertices();
        if ($components[$vertices[0]] == $components[$vertices[1]]) {
            return true;
        }

        self::CombineComponents($components, $newEdge);
        return false;
    }

    /**
     * Check if given enabled edges and disabled edges is a correct path that links origin and destination vertex
     * Returns true when yes, false when current path can impossibly lead to successful path
     *  null when the path is not finished yet
     * @param Edge[] $enabledEdges
     * @param Edge[] $disabledEdges
     * @return bool|null
     */
    private function CheckPath($enabledEdges, $disabledEdges) {
        $isPath = true;
        for ($v = 0; $v < $this->Graph->Vertices; $v++) {
            $degree = Graph::GetDegree($v, $enabledEdges);
            $fullyCalculated = $this->IsFullyCalculated($v, $enabledEdges, $disabledEdges);

            if ($this->IsOriginDestination($v)) {
                if ($degree > 1) {
                    // Degree of origin or destination cannot be greater than 1
                    return false;
                }
                if ($degree != 1) {
                    if ($fullyCalculated) {
                        // For the origin/destination vertex that all edges which the vertex connects has been considered
                        // its degree must be 1
                        return false;
                    }

                    // Origin/destination vertex not having degree 1 means the path is not complete
                    $isPath = false;
                }
            } else {
                if ($degree > 2) {
                    // Vertex on path cannot have degree more than 2
                    return false;
                }
                if ($fullyCalculated && ($degree != 2 && $degree != 0)) {
                    // For any vertex on path that edges around it has been considered
                    // its degree must be 2 or 0
                    return false;
                }
                if (Edge::IsInSet($v, $enabledEdges) && $degree != 2) {
                    if ($fullyCalculated) {
                        // For any vertex on path that edges arount it has been considered and selected
                        // its degree must be 2
                        return false;
                    }

                    // Vertex that connects to selected edge not having degree 2 means the path is not complete
                    $isPath = false;
                }
            }
        }

        return $isPath ? true : null;
    }

    /**
     * Check if all edges that connect to given vertex have been considered
     * @param int $vertex
     * @param Edge[] $enabledEdges
     * @param Edge[] $disabledEdges
     */
    private function IsFullyCalculated($vertex, $enabledEdges, $disabledEdges) {
        $calculatedEdges = array_merge($enabledEdges, $disabledEdges);
        $notCalculatedEdges = array_diff($this->Graph->Edges, $calculatedEdges);
        foreach ($this->Graph->Edges as $edge) {
            if ($edge->Connects($vertex) && Edge::IsInSet($vertex, $notCalculatedEdges)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if given vertex is origin or destination point
     * @param int $vertex
     * @return bool
     */
    private function IsOriginDestination($vertex) {
        return ($vertex == $this->Origin || $vertex == $this->Destination);
    }
}