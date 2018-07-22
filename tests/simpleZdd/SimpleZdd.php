<?php
use PHPUnit\FrameWork\TestCase;

class SimpleZddTest extends TestCase {
    protected static function getMethod($methodName) {
        $class = new ReflectionClass('SimpleZdd');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    public function testIsFullyConnected() {
        $method = self::getMethod('IsFullyCalculated');

        $g = new Graph(4);
        $p = new Edge(0, 1, 'p');
        $q = new Edge(1, 3, 'q');
        $r = new Edge(1, 2, 'r');
        $s = new Edge(0, 2, 's');
        $t = new Edge(2, 3, 't');
        $g->Edges = [$p, $q, $r, $s, $t];

        $simpleZdd = new SimpleZdd($g);
        $this->assertTrue($method->invokeArgs($simpleZdd, [2, [$s, $r, $t], []]));
        $this->assertTrue($method->invokeArgs($simpleZdd, [2, [$s, $r, $p], [$t]]));
        $this->assertTrue($method->invokeArgs($simpleZdd, [1, [$r], [$p, $q]]));
        $this->assertFalse($method->invokeArgs($simpleZdd, [2, [$s, $r], []]));
        $this->assertFalse($method->invokeArgs($simpleZdd, [1, [], []]));
    }

    public function testCheckPath() {
        $method = self::getMethod('CheckPath');

        $g = new Graph(4);
        $p = new Edge(0, 1, 'p');
        $q = new Edge(1, 3, 'q');
        $r = new Edge(1, 2, 'r');
        $s = new Edge(0, 2, 's');
        $t = new Edge(2, 3, 't');
        $g->Edges = [$p, $q, $r, $s, $t];

        $simpleZdd = new SimpleZdd($g);

        $this->assertNull($method->invokeArgs($simpleZdd, [[$s, $q], [], [0, 1, 0, 1], $q]));
        $this->assertNull($method->invokeArgs($simpleZdd, [[$p], [], [0, 0, 2, 3], $p]));
        $this->assertNull($method->invokeArgs($simpleZdd, [[$p, $t], [$s], [0, 0, 1, 1], $t]));

        $this->assertFalse($method->invokeArgs($simpleZdd, [[$p, $s], []]));
        $this->assertFalse($method->invokeArgs($simpleZdd, [[$s, $r, $t], [$p]]));
        $this->assertFalse($method->invokeArgs($simpleZdd, [[$r], [$p, $q]]));
        $this->assertFalse($method->invokeArgs($simpleZdd, [[$p, $t], [$s, $r, $q]]));

        $this->assertTrue($method->invokeArgs($simpleZdd, [[$p, $q], [$r, $s, $t]]));
        $this->assertTrue($method->invokeArgs($simpleZdd, [[$s, $t], [$p, $q, $r]]));
        $this->assertTrue($method->invokeArgs($simpleZdd, [[$s, $r, $q], [$p, $t]]));
        $this->assertTrue($method->invokeArgs($simpleZdd, [[$p, $r, $t], [$s, $q]]));
    }
}