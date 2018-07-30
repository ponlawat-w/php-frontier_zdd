<?php

class Graph {
    /** @var int $Vertices */
    public $Vertices;

    /** @var Edge[] $Edges */
    public $Edges;

    /**
     * @param int $vertices
     */
    public function __construct($vertices) {
        $this->Vertices = $vertices;
        $this->Edges = [];
    }

    /**
     * @param int $v1
     * @param int $v2
     * @return bool
     */
    public function ContainsEdge($v1, $v2) {
        $checkEdge = new Edge($v1, $v2);
        foreach ($this->Edges as $edge) {
            if ($edge->Identical($checkEdge)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $v1
     * @param int $v2
     * @param string $name
     * @return $this
     */
    public function AddEdge($v1, $v2, $name = null) {
        if (!$this->ContainsEdge($v1, $v2)) {
            $newEdge = new Edge($v1, $v2, $name);
            $this->Edges[] = $newEdge;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        $str = "Graph[{$this->Vertices}]" . PHP_EOL;
        $str .= 'Edges: ' . count($this->Edges) . PHP_EOL;
        $str .= '  ' . implode(', ', $this->Edges);

        return $str;
    }

    /**
     * @param int $vertex
     * @param Edge[] $activeEdges
     * @return int
     */
    public static function GetDegree($vertex, $activeEdges) {
        $vertex = (int)$vertex;

        $degree = 0;
        foreach ($activeEdges as $edge) {
            if ($edge->Connects($vertex)) {
                $degree++;
            }
        }

        return $degree;
    }
}