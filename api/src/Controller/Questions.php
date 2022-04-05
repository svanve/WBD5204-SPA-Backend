<?php

namespace WBD5204\Controller;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Questions as QuestionModel;

final class Questions extends AbstractController {

    public ?QuestionModel $UserModel = NULL;

    public function __construct( ) {
        $this->QuestionModel = new QuestionModel();
    }

    public function getQuestions() {
        $errors = [];
        $results = [];

        if ($this->isMethod( self::METHOD_GET ) 
        // && Authorize::authorizeToken( $errors, $result )
        && $this->QuestionModel->get( $errors, $results )) {
            $this->responseCode(200);
            $this->printJSON( ['success' => true, 'result' => $results, /* 'jwt' => Authorize::createToken( $result['user_id'] ) */] );
        } else {
            $this->responseCode(400);
            $this->printJSON( ['errors' => $errors] );
        }

    }
}