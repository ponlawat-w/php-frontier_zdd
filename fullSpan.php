<?php
require_once(__DIR__ . '/loader.php');

$g = new Graph(4);
$g->AddEdge(0, 1, 'p')->AddEdge(1, 3, 'q')->AddEdge(1, 2, 'r')->AddEdge(0, 2, 's')->AddEdge(2, 3, 't');

$fullSpan = new FullSpanBinaryTree($g);
$tree = $fullSpan->GenerateTree();

$paths = $tree->GetPaths();
foreach ($paths as $path) {
    if (!$path->Terminal->Value) {
        continue;
    }
    echo $path . PHP_EOL;
}

echo '-----' . PHP_EOL;

// Grid 3x3
$grid = Grid::Create(3);
$gridFullSpan = new FullSpanBinaryTree($grid);
$gridTree = $gridFullSpan->GenerateTree();
$gridPaths = $gridTree->GetPaths();
foreach ($gridPaths as $path) {
    if (!$path->Terminal->Value) {
        continue;
    }
    echo $path . PHP_EOL;
}