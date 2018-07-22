<?php

/**
 * Class Edge
 */
class Edge {
    /** @var int[] $Vertices */
    private $Vertices;

    /** @var string $Name */
    public $Name;

    /**
     * @param int $v1
     * @param int $v2
     * @param string $name
     */
    public function __construct($v1, $v2, $name = null) {
        if ($v1 == $v2) {
            throw new Exception('Edge must constain different vertices.');
        }

        $this->Vertices = [$v1, $v2];
        $this->Name = $name;
    }

    /**
     * @return int[]
     */
    public function GetVertices() {
        return $this->Vertices;
    }

    /**
     * @param Edge $edge
     * @return bool
     */
    public function Identical($edge) {
        $thisVertices = $this->GetVertices();
        $thatVertices = $edge->GetVertices();

        return ($thisVertices[0] == $thatVertices[0] && $thisVertices[0] == $thatVertices[1])
            || ($thisVertices[0] == $thatVertices[1] && $thisVertices[1] == $thatVertices[0]);
    }

    /**
     * @param int $vertex
     * @return bool
     */
    public function Connects($vertex) {
        return ($this->Vertices[0] === $vertex || $this->Vertices[1] === $vertex);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->Name ? $this->Name : "e({$this->Vertices[0]}, {$this->Vertices[1]})";
    }

    /**
     * @param int $vertex
     * @param Edge[] $edges
     * @return bool
     */
    public static function IsInSet($vertex, &$edges) {
        foreach ($edges as $edge) {
            foreach ($edge->GetVertices() as $v) {
                if ($v == $vertex) {
                    return true;
                }
            }
        }

        return false;
    }
}