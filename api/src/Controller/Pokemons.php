<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Pokemons as PokemonModel;
use WBD5204\Authorize;

final class Pokemons extends AbstractController {

    public ?PokemonModel $PokemonModel = NULL;

    public function __construct( ) {
        $this->PokemonModel = new PokemonModel();
    }

    public function getPokemons() {
        $errors = [];
        $results = [];

        if ($this->isMethod( self::METHOD_GET ) 
        && Authorize::authorizeToken( $errors, $results )
        && $this->PokemonModel->get( $errors, $results )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $results['results'], 'jwt' => Authorize::createToken( $results['user_id'] )] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }

    }
}