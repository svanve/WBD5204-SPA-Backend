<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Challenges as ChallengeModel;
use WBD5204\Model\User as UserModel;
use WBD5204\Model\Images as ImagesModel;
use WBD5204\Authorize;

final class Challenges extends AbstractController {

    /** @var ?int $user_id */
    public $user_id = NULL;

    public function __construct() {
        $this->ChallengeModel = new ChallengeModel();
        $this->UserModel = new UserModel();
        $this->ImagesModel = new ImagesModel();

        /** @var array $errors */
        $errors = [];


        if ( $this->UserModel->isLoggedIn($errors) ) {
            $this->user_id = $this->UserModel->getLoggedInUser( 'id' );
        };
    }

    // @DELETE
    public function delete( ?int $challenge_id = NULL ) : void {
        /** @var array $errors */
        $errors = [];
        
        if ($this->isMethod( self::METHOD_DELETE ) 
        && $this->UserModel->isLoggedIn( $errors ) 
        && $this->ChallengeModel->delete( $errors, $challenge_id )) {
        
            $this->responseCode(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }
    
    // @GET
    public function index() : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $results */
        $results = [];

        if ($this->isMethod( self::METHOD_GET ) 
        && $this->UserModel->isLoggedIn( $errors ) 
        && $this->ChallengeModel->getCommunityChallenges( $errors, $results, 'id' )) {
                
            $this->responseCode(200);
            $this->printJSON( [ 'success' => true, 'results' => $results ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }
    
    // @GET 
    public function getById( ?int $challenge_id = NULL ) : void {
        /** @var array $errors */
        $errors = [];
        /** @var array $data */
        $result = [];
        
        if ($this->isMethod( self::METHOD_GET ) && $this->ChallengeModel->getChallengeById( $errors, $result, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result ] );
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

        if ($this->isMethod( self::METHOD_GET ) && $this->UserModel->isLoggedIn( $errors ) && $this->ChallengeModel->getCommunityChallenges( $errors, $result, $sort_by )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result ] );
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

        
        if ($this->isMethod( self::METHOD_GET ) && $this->UserModel->isLoggedIn( $errors ) && $this->ChallengeModel->getMyChallenges( $errors, $result, $this->user_id, $sort_by )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $result ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] ); 
        }
    }
    
    
    // @UPDATE 
    public function update( ?int $challenge_id = NULL) {
        /** @var array $errors */
        $errors = [];

        // TODO: Überprüfen ob challenge id zu user gehört!
        
        if ($this->isMethod( self::METHOD_PUT) && $this->UserModel->isLoggedIn( $errors ) && $this->ChallengeModel->update( $errors, $challenge_id )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( [ 'errors' => $errors ] );
        }
    }
    
    // @POST
    public function write () : void {
        /** @var array $errors */
        $errors = [];
    
        if ($this->isMethod( self::METHOD_POST) 
        && Authorize::authorizeToken( $errors )
        && $this->UserModel->isLoggedIn( $errors ) 
        && $this->ChallengeModel->write( $this->user_id, $errors )) {
            $this->responseCode(201);
            $this->printJSON( ['success' => true, 'jwt' => Authorize::createToken() ] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }
}