<?php

class Grid {
    /**
     * @param int $size
     * @return Graph
     */
    public static function Create($size) {
        $vertices = $size * $size;
        $g = new Graph($vertices);

        for ($v = 0; $v < $vertices; $v++) {
            $col = $v % $size;

            if ($col > 0) {
                $g->AddEdge($v - 1, $v);
            }

            if ($v >= $size) {
                $g->AddEdge($v - $size, $v);
            }
        }

        return $g;
    }
}