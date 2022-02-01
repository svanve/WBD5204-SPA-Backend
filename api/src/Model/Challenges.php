<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;
use \PDOStatement;

final class Challenges extends AbstractModel {
    
    private $user_id;

    // Wie läuft das mit der id? woher kommt sie in den constructor?

    public function __construct() {
        $this->user_id = 2;
    }
    
    public function write( array &$errors = [] ) : bool {

        //get input
        /** @var ?string $input_pokemon */
        $input_pokemon = filter_input( INPUT_POST, 'pokemon_id');
        /** @var ?string $input_question */
        $input_question = filter_input( INPUT_POST, 'question_id');
        /** @var ?string $input_title */
        $input_title = filter_input( INPUT_POST, 'title');
        /** @var ?string $input_description */
        $input_description = filter_input( INPUT_POST, 'description');

        /** @var bool $validate_pokemon */
        $validate_pokemon = empty($input_pokemon) === FALSE || is_null($input_pokemon === FALSE);
        /** @var bool $validate_question */
        $validate_question = empty($input_question) === FALSE || is_null($input_question === FALSE);
        /** @var bool $validate_title */
        $validate_title = $this->validateTitle($errors, $input_title);
        /** @var bool $validate_description */
        $validate_description = $this->validateDescription($errors, $input_description);

        var_dump(empty($input_pokemon));

        if ($validate_pokemon && $validate_question && $validate_title && $validate_description === TRUE) {

            /** @var \PDOStatement $query */
            $query = 'INSERT INTO challenges (pokemon_id, question_id, title, description, user_id) VALUES (:pokemon_id, :question_id, :title, :description, :user_id)';
            $statement = $this->Database->prepare( $query );
            $statement->bindValue( ':user_id',      $this->user_id );
            $statement->bindValue( ':pokemon',      $input_pokemon );
            $statement->bindValue( ':question',     $input_question );
            $statement->bindValue( ':title',        $input_title );
            $statement->bindValue( ':description',  $inout_description );
            $statement->execute();

            return $statement->rowCount() > 0;

        } else {
            
            return FALSE;

        }
        
        // Wie kann ich hier einen error abfangen? :: try{}catch(){} um PDOStatement?

    }

    public function getChallengeById( int $challengeId ) : array {
        
        $query = 'SELECT * FROM challenges WHERE id = :id';
        $statement = $this->database->prepare( $query );
        $statement->bindValue( ':id', $challengeId );
        $statement->execute();
        $challengeResults = $statement->fetch(PDO::FETCH_ASSOC);
        // if( count($challengeResults) <= 0 ) throw new Error('No Challenges returned.');
        

        // STEP 1: get question_content, question_level using question_id
        $query = 'SELECT content, question_level FROM questions WHERE id = :id';
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':id', $challengeResults['question_id']);
        $questionResults = $statement->fetch(PDO::FETCH_ASSOC);
        // if( count($questionResults) <= 0 ) throw new Error('No questions returned.');

        
        // STEP 2: get name, image and level of pokemon using pokemon_id
        $query = 'SELECT * FROM pokemons WHERE id = :id';
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':id', $challengeResults['pokemon_id']);
        $pokemonResults = $statement->fetch(PDO::FETCH_ASSOC);
        // if( count($pokemonResults) <= 0 ) throw new Error('No pokemon returned.');
        
        // the higher the pokemons level, the reward (question_level equals the challenges reward)

        // STEP 3: get name of user by user_id
        $query = 'SELECT name, image FROM pokemons WHERE id = :id';
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':id', $this->user_id );

        // STEP 4: return challenge with title, description, content, image, name, question_level (reward), user_id

        return $challengeData = [
            'title' => $challengeResults['title'],
            'description' => $questionResults['description'],
            'content' => $questionResults['content'],
            'image' => $pokemonResults['image'],
            'title' => $pokemonResults['name'],
            'title' => $pokemonResults['title']
        ];
    }

    public function delete( array $challengeId ) : bool {
        
        //$this->userId;
        // DELETE FROM (DB)
    }

    // Wohin gehört diese Funktion?
    private function validateInput( string $validate_pokemon, string $validate_question, string $validate_title, string $validate_description ) : bool {

        
    }


    private function validateTitle( array &$errors, ?string $title ) : bool {
        if (is_null($title) || empty($title)) {
            $errors['title'][] = 'Bitte gib einen Titel für die Challenge ein.';
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
        if (is_null($description) || empty($description)) {
            $errors['description'][] = 'Bitte gib eine Beschreibung für die Challenge ein.';
        }
        if (strlen($description) > 140) {
            $errors['description'][] = 'Die Beschreibung sollte maxmimal 140 Zeichen lang sein.';
        }
        if (strlen($description) < 20) {
            $errors['description'][] = 'Die Beschreibung sollte mindestens 20 Zeichen lang sein.';
        }

        return isset($errors[ 'description' ]) === FALSE || count($errors[ 'description' ]) === 0;
    }

    
    // private function getQuizData( int $id ) : array {
    //     /** @var string $query */
    //     $query = 'SELECT pokemon, question, title, description FROM quizes WHERE :id = id';

    //     /** @var \PDOStatement $statement */
    //     $statement = $this->Database->prepare( $query );
    //     $statement->bindValue( 'id', $id );
    //     $statement->execute();

    //     /** @var array $results */
    //     $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    //     return $results;
    // }
}