<?php
require_once(__DIR__ . '/loader.php');

$g = new Graph(4);
$g->AddEdge(0, 1, 'p')->AddEdge(1, 3, 'q')->AddEdge(1, 2, 'r')->AddEdge(0, 2, 's')->AddEdge(2, 3, 't');

$zdd = new FrontierZdd($g);
$tree = $zdd->GenerateTree();
$count = 0;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo '-----' . PHP_EOL;

// Grid 4x4
$grid = Grid::Create(4);
$gridZdd = new FrontierZdd($grid);
$gridTree = $gridZdd->GenerateTree();
$count = 0;
$gridTree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;

unset($g, $zdd, $tree, $grid, $gridZdd, $gridTree, $count);

// // Grid 5x5
// $grid = Grid::Create(5);
// $gridZdd = new FrontierZdd($grid);
// $gridTree = $gridZdd->GenerateTree();
// $count = 0;
// $gridTree->PrintPaths($count);
// echo "Total: {$count} ways" . PHP_EOL;