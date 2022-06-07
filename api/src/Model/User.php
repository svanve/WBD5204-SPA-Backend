<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;
use WBD5204\Session as Session;

final class User extends AbstractModel {


    public function comparePasswords( array $credentials, string $user_input ) : bool { 
        /** @var string $hashed_salt */
        $hashed_salt = $credentials['salt'];
        /** @var strign $hashed_password */
        $hashed_password = $credentials['password'];

        return $hashed_password === $this->createHashedPassword($user_input, $hashed_salt);
    }

        
    public function createHashedPassword( string $password, string $salt) : string {
        return $hashed_password = hash( 'sha512', "{$password}{$salt}");
    }

        
    public function createHashedSalt() : string {
        $rand = rand(1234, 9876);
        $time = time();
        
        return hash( 'sha512', "{$time} - {$rand}");
    }


    public function emailExists( ?string $email ) : bool {
        /** @var string $query */
        $query = 'SELECT email FROM users WHERE email = :email';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( 'email', $email );
        $statement->execute();

        /** @var array $results */
        $results = $statement->fetchAll();

        return count($results) > 0;
    }

    public function getCredentials( string $username ) : array {
        /** @var string $query */
        $query = 'SELECT id, password, salt FROM users WHERE username = :username';
    
        /** @var \PDOStatement $statement  */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue(':username', $username );
        $statement->execute();

        $result = $statement->fetch(); 

        if ( $result === FALSE ) {
            return [];
        }

        return $result;
    }


    public function getImageId( string $user_id ) : string {
        /** @var string $query */
        $query = 'SELECT image_id FROM users WHERE id = :user_id';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':user_id', $user_id );
        $statement->execute();

        $result = $statement->fetch();

        return $result['image_id'];
    }


    private function getLoggedInUser( ?string $param = 'id' ) : int|string {
        $user_id = Session::get('user_id');

        if ( $param === 'id' ) {
            return $user_id;
        } else if ( $param === 'username') {
            return $this->getUsername( $user_id );
        }
    }


    public function getUserId( string $username ) : int {
        /** @var string $query */
        $query = 'SELECT id FROM users WHERE username = :username';

        /** @var \PDOStatement $statement  */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':username', $username );
        $statement->execute();

        $result = $statement->fetch();

        return $result['id'];
    }


    public function getUsername( int $user_id ) : string {
        /** @var string $query */
        $query = 'SELECT username FROM users WHERE id = :id';

        /** @var \PDOStatement $statement  */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':id', $user_id );
        $statement->execute();

        $result = $statement->fetch();

        return $result;
    }


    public function isLoggedIn( array &$errors ) : bool {

        
        if ( !Session::exists('user_id') ) {

            $errors['session'][] = 'Du musst dich zuerst einloggen.';
            return FALSE;
        }

        return TRUE;
    }

    
    public function login( array &$errors = [], array &$result = [] ) : bool {
        /** @var ?string $input_username */
        $input_username = filter_input( INPUT_POST, 'username');
        /** @var ?string $input_password */
        $input_password = filter_input( INPUT_POST, 'password');
        
        /** @var bool $validate_username */
        $validate_username = empty($input_username) === FALSE;
        /** @var bool $validate_password */
        $validate_password = empty($input_password) === FALSE;
        
        if ( $validate_username || $validate_password ) {
            /** @var array $credentials */
            $credentials = $this->getCredentials( $input_username );
            
            if ( empty($credentials) ) {
                $errors['password'][] = 'Der Username oder das Passwort ist falsch.';
                return FALSE;
            }
            
            /** @var bool $compare_passwords */
            $compare_passwords = $this->comparePasswords( $credentials, $input_password );
            
            if( !$compare_passwords ) {
                $errors['password'][] = 'Der Username oder das Passwort ist falsch.';
                return $compare_passwords;
            }

            $result['user_id'] = $credentials['id'];
            
            return $compare_passwords;
        }
        else {
            if( !$validate_username ) {
                $errors['username'][] = 'Bitte gib deinen Usernamen ein.';
            }
            if( !$validate_password ) {
                $errors['password'][] = 'Bitte gib dein Passwort ein.';
            }
            
            return FALSE;
        }
    }


    public function logout( array &$errors = [], array &$success = [] ) : bool {

        if ( !Authorize::authorizeToken( $errors ) ) {
            $success['logout'][] = 'Du wurdest erfolgreich ausgeloggt.';
            
            return TRUE;
        } 
        else {
            $errors['logout'][] = 'Es gab ein Problem beim Ausloggen.';
            
            return FALSE;
        }
    }
    
    public function getProfile( array &$errors, array &$result, int $user_id ) : bool {
        $query = 
        'SELECT 
            u.firstname,
            u.lastname,
            u.username,
            u.email,

            i.filename
             
        FROM users AS u
        
        JOIN images AS i
            ON u.image_id = i.id
        
        WHERE u.id = :id'; 

        $statement = $this->Database->prepare($query);
        $statement->bindValue( ':id', $user_id );
        $statement->execute();

        $result['results'] = $statement->fetchAll();

        return count( $result ) > 0;
    }

    public function register( array &$errors = [], array &$result = [] ) : bool {
        
        /** @var string $input_firstname */
        $input_firstname = filter_input( INPUT_POST, 'firstname');
        /** @var string $input_lastname */
        $input_lastname = filter_input( INPUT_POST, 'lastname');
        /** @var string $input_username */
        $input_username = filter_input( INPUT_POST, 'username');
        /** @var string $input_email */
        $input_email = filter_input( INPUT_POST, 'email');
        /** @var string $input_password */
        $input_password = filter_input( INPUT_POST, 'password');
        /** @var string $input_password_repeat */
        $input_password_repeat = filter_input( INPUT_POST, 'password_repeat');    
        /** @var int $image_id */
        $image_id = $result['id'];

        /** @var bool $validate_firstname */
        $validate_firstname = $this->validateFirstname( $errors, $input_firstname);
        /** @var bool $validate_lastname */
        $validate_lastname = $this->validateLastname( $errors, $input_lastname);
        /** @var bool $validate_username */
        $validate_username = $this->validateUsername( $errors, $input_username);
        /** @var bool $validate_email */
        $validate_email = $this->validateEmail( $errors, $input_email);
        /** @var bool $validate_password */
        $validate_password = $this->validatePassword( $errors, $input_password, $input_password_repeat);

        if( $validate_username && $validate_email && $validate_password ) {
            /** @var string $hashed_salt */
            $hashed_salt = $this->createHashedSalt();
            /** @var string $hashed_password */
            $hashed_password = $this->createHashedPassword( $input_password, $hashed_salt );

            /** @var string $query */
            $query = 'INSERT INTO users (username, email, password, salt, image_id, firstname, lastname) VALUES (:username, :email, :password, :salt, :image_id, :firstname, :lastname)';

            /** @var \PDOStatement $statement */
            $statement = $this->Database->prepare( $query );
            $statement->bindValue(':username', $input_username);
            $statement->bindValue(':email', $input_email);
            $statement->bindValue(':password', $hashed_password);
            $statement->bindValue(':salt', $hashed_salt);
            $statement->bindValue(':image_id', $image_id);
            $statement->bindValue(':firstname', $input_firstname);
            $statement->bindValue(':lastname', $input_lastname);
            $statement->execute();

            return $statement->rowCount() > 0;
        }
        else {
            return FALSE;
        }
    }


    public function usernameExists( ?string $username ) : bool {
        /**  @var string $query */
        $query = 'SELECT username FROM users WHERE :username = username';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':username', $username );
        $statement->execute();

        /** @var array $results */
        $results = $statement->fetchAll();

        return count($results) > 0;
    }


    public function validateEmail( array &$errors, ?string $email) : bool {
        if (empty($email)) {
            $errors['email'][] = 'Bitte gib eine Emailadresse ein.';
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            $errors['email'][] = 'Bitte gib eine gültige Emailadresse ein.';
        }
        if ($this->emailExists($email)) {
            $errors['email'][] = 'Die eingegebene Emailadresse existiert bereits.';
        }

        return isset( $errors['email'] ) === FALSE || count($errors['email']) === 0;
    }

    public function validateFirstname( array &$errors, ?string $firstname ) : bool {
        if (empty($firstname)) {
            $errors['firstname'][] = 'Bitte gib deinen Vornamen ein.';
        }

        return TRUE;
    }
    
    public function validateLastname( array &$errors, ?string $lastname ) : bool {
        if (empty($lastname)) {
            $errors['lastname'][] = 'Bitte gib deinen Nachnamen ein.';
        }

        return TRUE;
    }
                
                
    public function validatePassword( array &$errors, ?string $password, ?string $password_repeat ) : bool {
        if (empty($password)) {
            $errors['password'][] = 'Bitte gib ein Passwort ein.';
        }
        if (strlen($password) < 8 ) {
            $errors['password'][] = 'Dein Passwort sollte mindestens 8 Zeichen lang sein.';
        }
        if (strlen($password) > 64) {
            $errors['password'][] = 'Dein Passwort sollte maximal 64 Zeichen lang sein.';
        }
        if (preg_match('/\s/', $password)) {
            $errors['password'][] = 'Das Passwort sollte keine Leerzeichen enthalten.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors['password'][] = 'Das Passwort sollte mindestens einen Kleinbuchstaben enthalten.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors['password'][] = 'Das Passwort sollte mindestens einen Grossbuchstaben enthalten.';
        }
        if (!preg_match('/[\d]/', $password)) {
            $errors['password'][] = 'Das Passwort sollte mindestens eine Zahl enthalten.';
        }
        if (!preg_match('/\W/', $password)) {
            $errors['password'][] = 'Das Passwort sollte mindestens ein Sonderzeichen enthalten.';
        }
        if (empty($password_repeat)) {
            $errors['password'][] = "Bitte gib das Passwort ebenfalls im Feld 'Passwort wiederholen' ein.";
        }
        if ($password !== $password_repeat) {
            $errors['password'][] = 'Bitte wiederhole das Passwort richtig.';
        }
        
        return ( isset($errors['password']) === FALSE || count($errors['password']) === 0 ) &&
        ( isset($errors['password_repeat']) === FALSE || count($errors['password_repeat']) === 0 );
    }
                
    public function validateUsername( array &$errors, ?string $username ) : bool {
        if (is_null($username) || empty($username)) {
            $errors['username'][] = 'Bitte gib einen Username ein.';
        }
        if (strlen($username) < 4) {
            $errors['username'][] = 'Der Username sollte mindestens 4 Zeichen lang sein.';
        }
        if (strlen($username) > 20) {
            $errors['username'][] = 'Der Username sollte maximal 16 Zeichen lang sein.';
        }
        // TODO! preg_match
        if (!preg_match( '/[a-zA-Z0-9]{3,30}/', $username)) {
            $errors['username'][] = "Der Username sollte nur Zahlen und Buchstaben enthalten.";
        }
        if ($this->usernameExists( $username )) {
            $errors['username'][] = 'Der Username existiert bereits.';
        }

        return isset($errors[ 'username' ]) === FALSE || count($errors[ 'username' ]) === 0;
    }
}