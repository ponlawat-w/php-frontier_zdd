<?php
require_once(__DIR__ . '/loader.php');

$g = new Graph(4);
$g->AddEdge(0, 1, 'p')->AddEdge(1, 3, 'q')->AddEdge(1, 2, 'r')->AddEdge(0, 2, 's')->AddEdge(2, 3, 't')->AddEdge(0, 3, 'u');

$frontierHamilton = new FrontierHamilton($g);
$tree = $frontierHamilton->GenerateTree();
$count = 0;
echo PHP_EOL;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo '-----' . PHP_EOL;

unset($g, $frontierHamilton, $tree, $count);

readline();

$grid = Grid::Create(4);
$frontierHamilton = new FrontierHamilton($grid);
$tree = $frontierHamilton->GenerateTree();
$count = 0;
echo PHP_EOL;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo '-----' . PHP_EOL;

unset($grid, $frontierHamilton, $tree, $count);

readline();

$grid = Grid::Create(5);
$frontierHamilton = new FrontierHamilton($grid);
$tree = $frontierHamilton->GenerateTree();
$count = 0;
echo PHP_EOL;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;
echo '-----' . PHP_EOL;

readline();

$grid = Grid::Create(6);
$frontierHamilton = new FrontierHamilton($grid);
$tree = $frontierHamilton->GenerateTree();
$count = 0;
echo PHP_EOL;
$tree->PrintPaths($count);
echo "Total: {$count} ways" . PHP_EOL;