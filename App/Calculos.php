<?php

namespace App;

class Calculos{
    protected $somas = [];
    protected $porcentagens = [];
    protected $medias = [];
    protected $modas = [];
    protected $medianas = [];
    
    public function addSoma(int $colunaIndex, string $label): self {
        $this->somas[] = [
            'coluna' => $colunaIndex,
            'tipo' => 'soma',
            'label' => $label
        ];
        return $this;
    }  


    public function addPorcentagem(int $colunaIndex, string $valorAlvo, string $label): self {
        $this->porcentagens[] = [
            'coluna' => $colunaIndex,
            'valor' => $valorAlvo,
            'label' => $label
        ];
        return $this;
    }

    public function addMedia(int $colunaIndex, string $label): self {
        $this->medias[] = [
            'coluna' => $colunaIndex,
            'tipo' => 'media',
            'label' => $label
        ];
        return $this;
    }

    public function addMediana(int $colunaIndex, string $label): self {
        $this->medianas[] = [
            'coluna' => $colunaIndex,
            'tipo' => 'mediana',
            'label' => $label
        ];
        return $this;
    }

    public function addModa(int $colunaIndex, string $label): self {
        $this->modas[] = [
            'coluna' => $colunaIndex,
            'tipo' => 'moda',
            'label' => $label
        ];
        return $this;
    }
}