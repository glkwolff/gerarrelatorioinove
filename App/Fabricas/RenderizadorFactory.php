<?php
// ...
require_once 'Fabricas/RenderizadorFactory.php'; // Só precisa de conhecer a fábrica!

// O cliente não sabe (nem precisa de saber) como um renderizador é criado.
// Ele apenas pede o que quer.
$formato = $_GET['formato'] ?? 'html'; // Exemplo: Tente aceder com ?formato=json

try {
    $renderizador = \App\Fabricas\RenderizadorFactory::criar($formato);
    $output = $gerador->gerar($renderizador);
    echo $output;
} catch (\Exception $e) {
    die("Erro: " . $e->getMessage());
}