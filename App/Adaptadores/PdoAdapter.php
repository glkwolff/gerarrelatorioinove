<?php

namespace App\Adaptadores;

require_once __DIR__ . '/../Contratos/FonteDeDadosInterface.php';

use App\Contratos\FonteDeDadosInterface;
use PDOStatement;

class PdoAdapter implements FonteDeDadosInterface
{
    private $statement;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function getDados(): array
    {
        $body_associativo = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
        $header = [];
        $body = [];

        if (!empty($body_associativo)) {
            $header = array_keys($body_associativo[0]);
            foreach ($body_associativo as $linha) {
                $body[] = array_values($linha);
            }
        }

        return ['header' => $header, 'body' => $body];
    }
}