<?php

namespace WBD5204;

use WBD5204\Model as AbstractModel;

final class Quizes extends AbstractModel {
    
    public function write( array $content, int $id ) : bool {

        // Wie läuft das mit der id?

        if (getInputData() === FALSE) {
            return FALSE;
        }

        /** @var array $data */
        $data = getInputData( $id );

        /** @var \PDOStatement $query */
        $query = 'INSERT INTO quizes (pokemon, question, title, description) VALUES (:pokemon, :question, :title, :description) WHERE :id = id';
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':id',           $id );
        $statement->bindValue( ':pokemon',      $data['pokemon'] );
        $statement->bindValue( ':question',     $data['question'] );
        $statement->bindValue( ':title',        $data['title'] );
        $statement->bindValue( ':description',  $data['description'] );
        $statement->execute();

        /** @var array $results */
        $results = $statement->fetch();

        return count($results) > 0;
    } 

    public function delete( array $content, int $id ) : bool {
        

        // DELETE FROM (DB)
    }

    private function getInputData( array &$errors, int $id ) : array|bool {
        //get input
        /** @var ?string $input_username */
        $input_pokemon = filter_input( INPUT_POST, 'pokemon');
        /** @var ?string $input_username */
        $input_question = filter_input( INPUT_POST, 'question');
        /** @var ?string $input_username */
        $input_title = filter_input( INPUT_POST, 'title');
        /** @var ?string $input_username */
        $input_description = filter_input( INPUT_POST, 'description');

        //validate Input
        /** @var bool $validate_pokemon */
        $validate_pokemon = ( empty($input_pokemon) === FALSE ) && ( is_null($input_pokemon === FALSE) );
        /** @var bool $validate_question */
        $validate_question = ( empty($input_question) === FALSE ) && ( is_null($input_question === FALSE) );
        /** @var bool $validate_title */
        $validate_title = $this->validateTitle($errors, $input_title);
        /** @var bool $validate_description */
        $validate_description = $this->validateDescription($errors, $input_title);

        if ($validate_pokemon && $validate_question && $validate_title && $validateDescription === TRUE) {
            /** @var array $inputs */
            $inputs = [
                'pokemon' => $input_pokemon,
                'question' => $input_question,
                'title' => $input_title,
                'description' => $input_description
            ];

            return $inputs;

        } else {

            return FALSE;

        }
    }


    private function validateTitle( array &$errors, ?string $title ) : bool {
        if (is_null($title) || is_empty($title)) {
            $errors['title'][] = 'Bitte gib einen Titel für die Quest ein.';
        }
        if (strlen($title) > 40) {
            $errors['title'][] = 'Der Titel sollte maxmimal 40 Zeichen lang sein.';
        }
        if (strlen($title) < 5) {
            $errors['title'][] = 'Der Titel sollte mindestens 5 Zeichen lang sein.';
        }

        return isset($errors[ 'title' ]) === FALSE || count($errors[ 'title' ]) === 0;
    }


    private function validateDescription( array &$errors, ?string $description ) : bool {
        if (is_null($description) || is_empty($description)) {
            $errors['description'][] = 'Bitte gib eine Beschreibung die Quests ein.';
        }
        if (strlen($description) > 140) {
            $errors['description'][] = 'Die Beschreibung sollte maxmimal 140 Zeichen lang sein.';
        }
        if (strlen($description) < 20) {
            $errors['description'][] = 'Die Beschreibung sollte mindestens 20 Zeichen lang sein.';
        }

        return isset($errors[ 'description' ]) === FALSE || count($errors[ 'description' ]) === 0;
    }

    
    private function getQuizData( int $id ) : array {
        /** @var string $query */
        $query = 'SELECT pokemon, question, title, description FROM quizes WHERE :id = id';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( 'id', $id );
        $statement->execute();

        /** @var array $results */
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}