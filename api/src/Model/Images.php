<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;

final class Images extends AbstractModel {
    
    const UPLOADS_DIR = UPLOADS_DIR;

    public function addImageAs64( array &$errors, array &$result ) : bool {

        foreach($result['results'] as $i => $challenge) {
            $dir_filename = str_replace( '\\' , DIRECTORY_SEPARATOR, $challenge['filename']);

            $b64image = base64_encode(file_get_contents(UPLOADS_PATH . DIRECTORY_SEPARATOR . $dir_filename));  
    
            $result['results'][$i]['base64'] = $b64image;
        }
    
        return true;
    
    }
    
    private function createDateCodedPath() : string {
        /** @var string $date */
        $date = date( 'Y.m.d', time() );
        /** @var array $code */
        $code = explode( '.', $date );
        /** @var string $path */
        $path = implode( DIRECTORY_SEPARATOR, $code );
        
        return $path;
    }
    
    private function createFolder( string $dir ) : bool {
        if ( file_exists( $dir ) === FALSE ) {
            
            return (bool) mkdir( $dir, 0777, TRUE );
        }

        return TRUE;
    }    


    private function insertIntoDatabase( array &$errors, array &$result ) : bool {
        $filename = $result[ 'filename' ];

        /** @var string $query */
        $query = 'INSERT INTO images ( filename ) VALUES ( :filename );';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':filename', $filename );
        $statement->execute();

        $result[ 'id' ] = $this->Database->lastInsertId();

        return $result[ 'id' ] !== '0' && $statement->rowCount() > 0;
    } 

    public function getImagePath( array &$errors, array &$result, ?int $image_id ) : bool {
        /** @var bool $validate_image_id */
        $validate_image_id = $this->validateImageId( $errors, $image_id );

        if ( $validate_image_id ) {
            /** @var string $query */
            $query = 'SELECT filename FROM images WHERE id = :id';

            /** @var \PDOStatement statement */
            $statement = $this->Database->prepare( $query );
            $statement->bindValue( ':id', $image_id );
            $statement->execute();

            $sql_result = $statement->fetch(); 
            
            if ( !count( $sql_result ) > 0 ) {
                return FALSE;
            }

            $parsed_sql_result = $this->parseResult( $sql_result[ 'filename' ]);
            $result = [ ...$result, ...$parsed_sql_result ];
            

            return TRUE;

        } else {

            return FALSE;
        }
    }

    private function parseResult( string $result_string ) : string {

        return $result = str_replace( '\\', DIRECTORY_SEPARATOR, $result_string );
    }

    private function sanitizeImageExt( string $image_type ) : ?string {
        switch( $image_type ) {
            case IMAGETYPE_JPEG:
                return '.jpeg';
            case IMAGETYPE_PNG:
                return '.png';
            default:
                return NULL;
        }
    }

    public function uploadImage( array &$errors, array &$result ) : bool {
        $validate_image_file = $this->validateImageFile( $errors );


        if ( $validate_image_file ) {
            /** @var string $temp_image_id */
            $temp_image_id = $_FILES[ 'image' ][ 'name' ];
            /** @var string $temp_image_file */
            $temp_image_file = $_FILES[ 'image' ][ 'tmp_name' ];
            /** @var string $temp_image_type */
            $temp_image_type = $_FILES[ 'image' ][ 'type' ];
            /** @var array $temp_image_parts */
            $temp_image_parts = @explode( '.', $temp_image_id );
            /** @var string $temp_image_ext */
            $temp_image_ext = '.' . $temp_image_parts[ count( $temp_image_parts ) - 1 ];

            list ( $image_width, $image_height, $image_type ) = getimagesize( $temp_image_file );

            /** @var ?string $image_ext */
            $image_ext = $this->sanitizeImageExt( $image_type );
            /** @var string $date_coded_path */
            $date_coded_path = $this->createDateCodedPath();
            /** @var string $target_image_name */
            $target_image_name = str_replace( $temp_image_ext, '', $temp_image_id ) . "-" . time();


            /** @var string $target_image_path */
            $target_image_path = $date_coded_path . DIRECTORY_SEPARATOR . $target_image_name . $image_ext;

            // wenn tagesordner noch nicht existiert, dann einen neuen (mkdir)
            if ( $this->createFolder( UPLOADS_PATH . DIRECTORY_SEPARATOR . $date_coded_path ) === FALSE ) {
                $errors[ 'image' ][] = 'Can\'t create directory for file uploads.';

                return FALSE;
            }

            /** @var string $current_path */
            $current_path = $temp_image_file;
            /** @var string $target_path */
            $target_path = UPLOADS_PATH . DIRECTORY_SEPARATOR . $target_image_path;

            move_uploaded_file( $current_path, $target_path );

            $result[ 'filename' ] = $target_image_path;

            if ( $this->insertIntoDatabase( $errors, $result ) === FALSE ) {
                $errors[ 'image' ][] = 'Failed to insert into Database';

                return FALSE;
            }



            return isset( $result[ 'id' ] );
        }
        else {

            return FALSE;
        }
    }

    private function validateImageFile( array &$errors ) : bool {

        // check if image is appended in form data
        if ( isset( $_FILES[ 'image' ] ) === FALSE ) {

            $errors[ 'image' ][] = 'Please upload an image.';
        }
        // check if maximum filesize is smaller then the actual file size
        elseif( isset( $_FILES[ 'image' ][ 'error' ] ) && $_FILES[ 'image' ][ 'error' ] === 1  ) {

            $errors[ 'image' ][] = 'Maximum file size is ' . str_replace( 'M', 'MB', ini_get( 'upload_max_filesize' ) );
        }
        // validate image
        else {
            list( $image_width, $image_height, $image_type ) = getimagesize( $_FILES[ 'image' ][ 'tmp_name' ] );
            
            // check if the image is a valid image type
            if ( in_array( $image_type, [ IMAGETYPE_JPEG, IMAGETYPE_PNG ] ) === FALSE ) {
                $errors[ 'image' ][] = 'Please upload an image with a valid type.';
            }
            // check if the image has a minimum height from 400px
            if ( $image_height < 400 ) {
                $errors[ 'image' ][] = 'Please upload an image with an minimum height of 400px.';
            }
            // check if the image has a minimum width from 400px
            if ( $image_width < 400 ) {
                $errors[ 'image' ][] = 'Please upload an image with an minimum width of 400px.';
            }
        }

        return isset( $errors[ 'image' ] ) === FALSE || count( $errors[ 'image' ] ) === 0;
    }

    private function validateImageId( array &$errors, ?string $image_id ) : bool {
        if ( is_null( $image_id ) || empty( $image_id ) ) {
            $errors['image_id'][] = 'Bitte gib eine gültige Image-ID an.';
        }

        return isset($errors[ 'image_id' ]) === FALSE || count($errors[ 'image_id' ]) === 0;
    }

}