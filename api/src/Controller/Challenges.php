<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Challenges as ChallengeModel;

final class Challenges extends AbstractController {

    private $challenge;

    public function __construct() {
        $this->challenge = new ChallengeModel();
    }

    public function write () : void {
        /** @var array $errors */
        $errors = [];

        if ($this->challenge->write( $errors )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    public function delete( int $id ) : void {
        /** @var array $errors */
        $errors = [];

        if ($this->challenge->delete( $errors )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    public function read( int $id ) : void {
        /** @var array $errors */
        $errors = [];

        if ($this->challenge->getChallengeById( $id )) {
            $this->response_code(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->respopnse_code(400);
            $this->printJSON( ['errors' => $errors] ); 
        }
    }

    // string $pokemon, string $question, string $titel, string $description
}