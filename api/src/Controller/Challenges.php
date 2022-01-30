<?php

namespace WBD5204;

use WBD5204\Controller as AbstractController;
use WBD5204\Model\Quizes as QuizModel;

final class Quizes extends AbstractController {
    public function __construct() {
        $this->QuizModel = new QuizModel();
    }

    public function write () : void {
        /** @var array $errors */
        $errors = [];

        if ($this->QuizModel->write( $errors )) {
            $this->response_code(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->response_code(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    public function delete() : void {
        /** @var array $errors */
        $errors = [];

        if ($this->QuizModel->delete( $errors )) {
            $this->response_code(200);
            $this->printJSON( ['success' => true] );
        } else {
            $this->response_code(400);
            $this->printJSON( ['errors' => $errors] );
        }
    }

    // string $pokemon, string $question, string $titel, string $description
}