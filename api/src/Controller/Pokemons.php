<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Pokemons as PokemonModel;

final class Pokemons extends AbstractController {

    public ?PokemonModel $PokemonModel = NULL;

    public function __construct( ) {
        $this->PokemonModel = new PokemonModel();
    }

    public function getPokemons() {
        $errors = [];
        $results = [];

        if ($this->isMethod( self::METHOD_GET ) 
        && $this->PokemonModel->get( $errors, $results )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $results] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }

    }
}