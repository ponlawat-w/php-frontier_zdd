<?php
require_once(__DIR__ . '/loader.php');

$g = new Graph(4);
$g->AddEdge(0, 1, 'p')->AddEdge(1, 3, 'q')->AddEdge(1, 2, 'r')->AddEdge(0, 2, 's')->AddEdge(2, 3, 't');

$zdd = new SimpleZdd($g);
$tree = $zdd->GenerateTree();

$count = 0;
$paths = $tree->GetPaths();
foreach ($paths as $path) {
    if (!$path->Terminal || !$path->Terminal->Value) {
        continue;
    }
    echo $path . PHP_EOL;
    $count++;
}
echo "Total: {$count} ways" . PHP_EOL;

echo '-----' . PHP_EOL;
readline();

// Grid 4x4
$grid = Grid::Create(4);
$gridZdd = new SimpleZdd($grid);
$gridTree = $gridZdd->GenerateTree();

$count = 0;
$gridPaths = $gridTree->GetPaths();
foreach ($gridPaths as $path) {
    if (!$path->Terminal || !$path->Terminal->Value) {
        continue;
    }
    echo $path . PHP_EOL;
    $count++;
}
echo "Total: {$count} ways" . PHP_EOL;