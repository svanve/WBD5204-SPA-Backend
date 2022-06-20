<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Challenges as ChallengeModel;
use WBD5204\Model\User as UserModel;
use WBD5204\Model\Images as ImagesModel;
use WBD5204\Authorize;
use WBD5204\Session;


final class Challenges extends AbstractController {

    protected ?UserModel $UserModel = NULL;

    public function __construct() {
        $this->ChallengeModel = new ChallengeModel();
        $this->UserModel = new UserModel();
        $this->ImagesModel = new ImagesModel();

        /** @var array $errors */
        $errors = [];

    }

    // @DELETE
    public function delete( ?int $challenge_id = NULL ) : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $result */
        $result = [];
        
        if ($this->isMethod( self::METHOD_DELETE ) 
        && Authorize::authorizeToken( $errors, $result )
        && $this->ChallengeModel->delete( $errors, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken( $result['user_id'] ) ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }
    
    // @GET
    public function getCommunity( ?string $sort_by = 'id' ) : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $data */
        $result = [];

        if ($this->isMethod( self::METHOD_GET ) 
        && Authorize::authorizeToken( $errors, $result )
        && $this->ChallengeModel->getCommunityChallenges( $errors, $result, $sort_by )
        && $this->ImagesModel->addImageAs64( $errors, $result ) ) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result['results'], 'jwt' => Authorize::createToken( $result['user_id'] ) ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] ); 
        }
    }
    
    // @GET
    public function getMine( ?string $sort_by = 'id' ) : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $result */
        $result = [];


        if ($this->isMethod( self::METHOD_GET ) 
        && Authorize::authorizeToken( $errors, $result )
        && $this->ChallengeModel->getMyChallenges( $errors, $result, $sort_by )
        && $this->ImagesModel->addImageAs64( $errors, $result ) ) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result['results'], 'jwt' => Authorize::createToken( $result['user_id'] )] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] ); 
        }
    }
    
    
    // @UPDATE 
    public function update( ?int $challenge_id = NULL) {
        /** @var array $errors */
        $errors = [];

        if ($this->isMethod( self::METHOD_PUT)
        && Authorize::authorizeToken( $errors, $result )
        && $this->ChallengeModel->update( $errors, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken( $result['user_id'] )] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }
    
    // @POST
    public function write () : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $result */
        $result = [];
    
        if ($this->isMethod( self::METHOD_POST) 
        && Authorize::authorizeToken( $errors, $result )
        && $this->ChallengeModel->write( $result['user_id'], $errors )) {
            var_dump($result);
            $this->responseCode(201);
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken( $result['user_id'] )] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }
}