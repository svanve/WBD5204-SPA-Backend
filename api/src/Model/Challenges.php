<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;
use WBD5204\Session as Session;
use WBD5204\Model\User as UserModel;

final class Challenges extends AbstractModel {

    public function delete( array $errors, ?string $challenge_id ) : bool {
        /** @var bool $validate_challenge_id */
        $validate_challenge_id = $this->validateChallengeId( $errors, $challenge_id );

        if ( $validate_challenge_id ) {
            /** @var string $query */
            $query = 'DELETE FROM challenges WHERE id = :id';

            /** @var \PDOStatement $statement */
            $statement = $this->Database->prepare($query);
            $statement->bindValue( ':id', $challenge_id );
            $statement->execute();

            return $statement->rowCount() > 0;

        } else {
            return FALSE;
        }
    }
    
    public function getCommunityChallenges( array &$errors, array &$results, string $sort_by ) : bool {
        /** @var string $sanitized_sort_by */
        $sanitized_sort_by = str_replace( ' ', '', (strtolower( $sort_by )) );
        /** @var bool $validate_sort_by */
        $validate_sort_by = $this->validateSortBy( $errors, $sanitized_sort_by );

        if( $validate_sort_by ) {
            
            /** @var string $parse_sort_by */
            $parsed_sort_by = $this->parseSortBy( $sanitized_sort_by );

            $query = 
            'SELECT 
                c.title, 
                c.description, 
                c.id,
                c.question_id,
                
                u.username,
                i2.filename,

                p.name, 
                p.level, 
                p.pokedex_no,
                p.svg,
                
                q.question_level, 
                q.content, 
                q.right_answer, 
                q.wrong_answer_1, 
                q.wrong_answer_2, 
                q.wrong_answer_3
    
            FROM challenges AS c
            
            JOIN users AS u
                ON c.author_id = u.id
            
            JOIN images AS i1
                ON c.image_id = i1.id
            
            JOIN images AS i2
                ON u.image_id = i2.id

            JOIN pokemons AS p
                ON c.pokemon_id = p.id
            
            JOIN questions AS q
                ON c.question_id = q.id

            WHERE NOT c.author_id = :user_id

            ORDER BY' . ' ' .$parsed_sort_by . ' ' . 'ASC';

            $statement = $this->Database->prepare( $query );
            $statement->bindValue( ':user_id', $results['user_id'] );
            $statement->execute();

            $results['results'] = $statement->fetchAll();

            return count( $results ) > 0;
        } else {

            return FALSE;
        }
    }
    
    public function getMyChallenges( array &$errors, array &$results, string $sort_by ) : bool {
        /** @var string $sanitized_sort_by */
        $sanitized_sort_by = str_replace( ' ', '', (strtolower( $sort_by )) );
        /** @var bool $validate_sort_by */
        $validate_sort_by = $this->validateSortBy( $errors, $sanitized_sort_by );

        if( $validate_sort_by ) {
            
            /** @var string $parse_sort_by */
            $parsed_sort_by = $this->parseSortBy( $sanitized_sort_by );

            $query = 
            'SELECT 
                c.title, 
                c.description, 
                c.id,
                c.question_id,
                
                u.username,
                i2.filename,

                p.name, 
                p.level, 
                p.pokedex_no,
                p.svg,
                
                q.question_level, 
                q.content, 
                q.right_answer, 
                q.wrong_answer_1, 
                q.wrong_answer_2, 
                q.wrong_answer_3
    
            FROM challenges AS c
            
            JOIN users AS u
                ON c.author_id = u.id

            JOIN images AS i1
                ON c.image_id = i1.id
            
            JOIN images AS i2
                ON u.image_id = i2.id
            
            JOIN pokemons AS p
                ON c.pokemon_id = p.id
            
            JOIN questions AS q
                ON c.question_id = q.id

            WHERE c.author_id = :user_id

            ORDER BY' . ' ' .$parsed_sort_by . ' ' . 'ASC';

            $statement = $this->Database->prepare( $query );
            // $statement->bindValue( 'user_id', Session::get( 'user_id' ) );
            $statement->bindValue( 'user_id', $results['user_id'] );
            $statement->execute();

            $results['results'] = $statement->fetchAll();
            // $results = $statement->fetchAll();

            return count( $results ) > 0;
        } else {
            return FALSE;
        }   
    }

    private function parseSortBy( string $sort_by ) : string {
        if( $sort_by === 'id' ) {
            return $sort_by = 'c.id';
        } 
        if( $sort_by === 'level' ) {
            return $sort_by = 'p.level';
        } 
        if( $sort_by === 'title' ) {
            return $sort_by = 'c.title';
        } 
        if( $sort_by === 'username' ) {
            return $sort_by = 'u.username';
        } 
    }

    public function update( array &$errors, ?string $challenge_id ) : bool {

        /** @var array $form_data */
        $form_data = $this->getFormData();
        /** @var ?string $input_title */
        $input_title = $form_data[ 'title' ] ?? NULL;
        /** @var ?string $input_description */
        $input_description = $form_data[ 'description' ] ?? NULL;
        /** @var ?string $input_pokemon_id */
        $input_pokemon_id = $form_data[ 'pokemon_id' ] ?? NULL;
        /** @var ?string $input_question_id */
        $input_question_id = $form_data[ 'question_id' ] ?? NULL;

        /** @var bool $validate_title */
        $validate_title = $this->validateTitle( $errors, $input_title );
        /** @var bool $validate_description */
        $validate_description = $this->validateDescription( $errors, $input_description );
        /** @var bool $validate_pokemon_id */
        $validate_pokemon_id = $this->validatePokemonId( $errors, $input_pokemon_id );
        /** @var bool $validate_question_id */
        $validate_question_id = $this->validateQuestionId( $errors, $input_question_id );

        if ( $validate_title && $validate_description && $validate_pokemon_id && $validate_question_id ) {
            /** @var string $query */
            $query = 'UPDATE challenges SET title = :title, description = :description, pokemon_id = :pokemon_id, question_id = :question_id WHERE id = :id';
            /** @var \PDOStatement $statement */
            $statement = $this->Database->prepare( $query );
            $statement->bindValue( ':title', $input_title );
            $statement->bindValue( ':description', $input_description );
            $statement->bindValue( ':pokemon_id', $input_pokemon_id );
            $statement->bindValue( ':question_id', $input_question_id );
            $statement->bindValue( ':id', $challenge_id );
            $statement->execute();

            return $statement->rowCount() > 0;
        } else {
            return FALSE;
        }
    }

    private function validateChallengeId( array &$errors, ?string $challenge_id ) : bool {
        if ( is_null($challenge_id) || empty( $challenge_id ) ) {
            $errors['challenge_id'][] = 'Bitte gib eine gültige Challenge-ID an.';
        }

        return isset($errors[ 'challenge_id' ]) === FALSE || count($errors[ 'challenge_id' ]) === 0;
    }

    private function validateDescription( array &$errors, ?string $description ) : bool {
        if (is_null($description) || empty($description)) {
            $errors['description'][] = 'Bitte gib eine Beschreibung für die Challenge ein.';
        }
        if (strlen($description) > 88) {
            $errors['description'][] = 'Die Beschreibung sollte maxmimal 88 Zeichen lang sein.';
        }
        if (strlen($description) < 20) {
            $errors['description'][] = 'Die Beschreibung sollte mindestens 20 Zeichen lang sein.';
        }

        return isset($errors[ 'description' ]) === FALSE || count($errors[ 'description' ]) === 0;
    }

    private function validatePokemonId( array &$errors, ?string $pokemon_id ) : bool {
        if ( is_null($pokemon_id) || empty($pokemon_id) ) {
            $errors['pokemon_id'][] = 'Bitte gib eine Pokemon-ID an';
        }

        return isset($errors[ 'pokemon_id' ]) === FALSE || count($errors[ 'pokemon_id' ]) === 0;
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

    private function validateQuestionId( array &$errors, ?string $question_id ) : bool {
        if ( is_null($question_id) || empty($question_id) ) {
            $errors['question_id'][] = 'Bitte gib eine Question-ID an.';
        }

        return isset($errors[ 'question_id' ]) === FALSE || count($errors[ 'question_id' ]) === 0;
    }

    private function validateSortBy( array &$errors, string $sanitized_sort_by ) : bool {
        if ( $sanitized_sort_by === 'id' || $sanitized_sort_by === 'title' || $sanitized_sort_by === 'level' || $sanitized_sort_by === 'username') {
            return TRUE;
        } else {
            $errors['sort_by'][] = 'Gib entweder "title", "id", "level" oder "username" an.';
            return FALSE;
        }
    }

    private function validateUserId( array &$errors, ?int $user_id ) : bool {
        if ( is_null($user_id) || empty($user_id)) {
            $errors['user_id'][] = 'Du musst eingeloggt sein, um eine Challenge zu erstellen.';
        } 

        return isset($errors['user_id']) === FALSE || count($errors['user_id']) === 0;
    }

    public function write( string $user_id, array &$errors = [] ) : bool {

        var_dump($_POST);

        //get input
        /** @var ?string $input_pokemon_id */
        $input_pokemon_id = filter_input( INPUT_POST, 'pokemon_id');
        /** @var ?string $input_question_id */
        $input_question_id = filter_input( INPUT_POST, 'question_id');
        /** @var ?string $input_title */
        $input_title = filter_input( INPUT_POST, 'title');
        /** @var ?string $input_description */
        $input_description = filter_input( INPUT_POST, 'description');

        // var_dump($input_pokemon_id);
        // var_dump($input_question_id);

        /** @var bool $validate_user_id */
        $validate_user_id = $this->validateUserId( $errors, $user_id );
        /** @var bool $validate_pokemon */
        $validate_pokemon_id = empty($input_pokemon_id) === FALSE || is_null($input_pokemon_id === FALSE);
        /** @var bool $validate_question */
        $validate_question_id = empty($input_question_id) === FALSE || is_null($input_question_id === FALSE);
        /** @var bool $validate_title */
        $validate_title = $this->validateTitle($errors, $input_title);
        /** @var bool $validate_description */
        $validate_description = $this->validateDescription($errors, $input_description);

        // image_id input
        $user = new UserModel();
        $image_id = $user->getImageId( $user_id );

        if ($validate_user_id && $validate_pokemon_id && $validate_question_id && $validate_title && $validate_description === TRUE) {
            
            /** @var \PDOStatement $query */
            $query = 
                'INSERT INTO challenges (pokemon_id, question_id, title, description, author_id, image_id) 
                VALUES (:pokemon_id, :question_id, :title, :description, :author_id, :image_id)';

            $statement = $this->Database->prepare( $query );
            $statement->bindValue( ':author_id',    $user_id );
            $statement->bindValue( ':pokemon_id',   $input_pokemon_id );
            $statement->bindValue( ':question_id',  $input_question_id );
            $statement->bindValue( ':title',        $input_title );
            $statement->bindValue( ':description',  $input_description );
            $statement->bindValue( ':image_id',  $image_id );
            $statement->execute();

            return $statement->rowCount() > 0;

        } else {
            
            return FALSE;

        }

    }
}