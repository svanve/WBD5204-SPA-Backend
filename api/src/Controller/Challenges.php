<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Challenges as ChallengeModel;

final class Challenges extends AbstractController {

    public function __construct() {
        $this->ChallengeModel = new ChallengeModel();
    }

    // @POST
    public function write () : void {
        /** @var array $errors */
        $errors = [];

        if ($this->isMethod( self::METHOD_POST) && $this->ChallengeModel->write( $errors )) {
            $this->responseCode(201);
            $this->printJSON( ['success' => true] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @DELETE
    public function delete( ?string $challenge_id = NULL ) : void {
        /** @var array $errors */
        $errors = [];

        if ($this->isMethod( self::METHOD_DELETE ) && $this->ChallengeModel->deleteChallenge( $errors, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // @GET
    public function index() : void {
        /** @var $errors */
        $errors = [];
        /** @var $results */
        $results = [];

        if ($this->isMethod( self::METHOD_GET ) && $this->ChallengeModel->getAllChallenges( $errors, $results )) {
            $this->responseCode(200);
            $this->printJSON( [ 'success' => true, 'results' => $results ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }

    // @GET 
    public function get( ?string $challenge_id = NULL ) : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $data */
        $result = [];
        

        if ($this->isMethod( self::METHOD_GET ) && $this->ChallengeModel->getChallengeById( $errors, $result, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result ] );
        } else {
            $this->respopnseCode(400);
            $this->printJSON( ['errors' => $errors] ); 
        }
    }

    // @UPDATE
    public function update( ?string $challenge_id = NULL) {
        /** @var array $errors */
        $errors = [];

        if ($this->isMethod( self::METHOD_PUT) && $this->ChallengeModel->update( $errors, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }
}