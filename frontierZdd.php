<?php
require_once(__DIR__ . '/loader.php');

$g = new Graph(4);
$g->AddEdge(0, 1, 'p')->AddEdge(1, 3, 'q')->AddEdge(1, 2, 'r')->AddEdge(0, 2, 's')->AddEdge(2, 3, 't');

$zdd = new FrontierZdd($g);
$startTime = microtime(true);
$tree = $zdd->GenerateTree();
$calcTime = round((microtime(true) - $startTime), 4);
$count = 0;
echo PHP_EOL;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo "Calculation Time: {$calcTime} seconds" . PHP_EOL;
echo '-----' . PHP_EOL;
readline();

// Grid 4x4 vertices (3x3 edges)
$grid = Grid::Create(4);
$gridZdd = new FrontierZdd($grid);
$startTime = microtime(true);
$gridTree = $gridZdd->GenerateTree();
$calcTime = round((microtime(true) - $startTime), 4);
$count = 0;
echo PHP_EOL;
$gridTree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo "Calculation Time: {$calcTime} seconds" . PHP_EOL;
echo '-----' . PHP_EOL;
readline();

unset($g, $zdd, $tree, $grid, $gridZdd, $gridTree, $count);

// Grid 5x5 vertices (4x4 edges)
$grid = Grid::Create(5);
$gridZdd = new FrontierZdd($grid);
$startTime = microtime(true);
$gridTree = $gridZdd->GenerateTree();
$calcTime = round((microtime(true) - $startTime), 4);
$count = 0;
echo PHP_EOL;
$gridTree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo "Calculation Time: {$calcTime} seconds" . PHP_EOL;
echo '-----' . PHP_EOL;
readline();

unset($grid, $gridZdd, $gridTree, $count);
$grid = Grid::Create(6);
$gridZdd = new FrontierZdd($grid);
$startTime = microtime(true);
$gridTree = $gridZdd->GenerateTree();
$calcTime = round((microtime(true) - $startTime), 4);
$count = 0;
echo PHP_EOL;
echo 'Ready!' . PHP_EOL;
readline();
$gridTree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo "Calculation Time: {$calcTime} seconds" . PHP_EOL;
echo '-----' . PHP_EOL;