<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;

final class Images extends AbstractModel {

    const UPLOADS_DIR = UPLOADS_DIR;

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
            $date_coded_path = $this->createDataCodedPath();
            /** @var string $target_image_name */
            $target_image_name = str_replace( $temp_image_ext, '', $temp_image_id ) . "-" . time();

            /** @var string $target_image_path */
            $target_image_path = $date_coded_path . DIRECTORY_SEPARATOR . $target_image_name . $image_ext;
            /** @var string $target_image_path */
            $target_thumbnail_path = $date_coded_path . DIRECTORY_SEPARATOR . $target_image_name . '-thumbnail' . $image_ext;

            // wenn tagesordner noch nicht existiert, dann einen neuen (mkdir)
            if ( $this->createFolder( UPLOADS_PATH . DIRECTORY_SEPARATOR . $date_coded_path ) === FALSE ) {
                $errors[ 'image' ][] = 'Can\'t create directory for file uploads.';

                return FALSE;
            }

            if ( $this->createImage( $errors, $target_image_path ) ) {
                $result[ 'filename' ] = $target_image_name;
                $result[ 'image' ] = [
                    'src'   =>  UPLOADS_URI . "/{$date_coded_path}/{$target_image_name}{$image_ext}"
                ];
            }

            if ( $this->createThumbnail( $errors, $target_thumbnail_path ) ) {
                $result[ 'thumbnail' ] = [
                    'src'   =>  UPLOADS_URI . "/{$date_coded_path}/{$target_image_name}-thumbnail{$image_ext}"
                ];
            }

            if ( $this->insertIntoDatabase( $errors, $result ) === FALSE ) {
                $errors[ 'image' ][] = 'Failed to insert into Database';

                return FALSE;
            }

            return isset( $result[ 'id' ] ) && isset( $result[ 'image' ] ) && isset( $result[ 'thumbnail' ] );
        }
        else {

            return FALSE;
        }
    }

    private function insertIntoDatabase( array &$errors, array &$result ) : bool {
        $filename = $result[ 'filename' ];
        $image_src = $result[ 'image' ][ 'src' ];
        $thumbnail_src = $result[ 'thumbnail' ][ 'src' ];

        /** @var string $query */
        $query = 'INSERT INTO images ( filename, image_src, thumbnail_src ) VALUES ( :filename, :image_src, :thumbnail_src );';

        /** @var \PDOStatement $statement */
        $statement = $this->Database->prepare( $query );
        $statement->bindValue( ':filename', $filename );
        $statement->bindValue( ':image_src', $image_src );
        $statement->bindValue( ':thumbnail_src', $thumbnail_src );
        $statement->execute();

        $result[ 'id' ] = $this->Database->lastInsertId();

        return $result[ 'id' ] !== '0' && $statement->rowCount() > 0;
    }

    private function createThumbnail( array &$errors, string $thumbnail_path, int $thumbnail_width = 200, int $thumbnail_height = 200 ) : bool {
        list( $image_width, $image_height, $image_type ) = getimagesize( $_FILES[ 'image' ][ 'tmp_name' ] );

        switch( $image_type ) {
            case IMAGETYPE_JPEG:
                $gd_src = imagecreatefromjpeg( $_FILES[ 'image' ][ 'tmp_name' ] );
                break;
            case IMAGETYPE_PNG:
                $gd_src = imagecreatefrompng( $_FILES[ 'image' ][ 'tmp_name' ] );
                break;
            default:
                return FALSE;
        }

        /** @var int|float $image_ratio */
        $image_ratio = $image_width / $image_height;
        /** @var int|float $thumbnail_ratio */
        $thumbnail_ratio = $thumbnail_width / $thumbnail_height;

        if ( $thumbnail_ratio > $image_ratio ) {
            $thumbnail_width = $thumbnail_height / $image_ratio;
        }
        else {
            $thumbnail_height = $thumbnail_width / $image_ratio;
        }

        /** @var \GdImage $gd_thumbnail */
        $gd_thumbnail = imagecreate( $thumbnail_width, $thumbnail_height );

        if ( $this->copyImage( $gd_thumbnail, $gd_src, $thumbnail_width, $thumbnail_height, $image_width, $image_height ) === FALSE ) {
            $errors[ 'image' ][] = 'Can\'t copy thumbnail image from resampled.';
        }
        if ( $this->createPNG( $gd_thumbnail, $thumbnail_path ) === FALSE ) {
            $errors[ 'image' ][] = 'Can\'t create a png from thumbnail image.';
        }

        imagedestroy( $gd_src );
        imagedestroy( $gd_thumbnail );

        return isset( $errors[ 'image' ] ) === FALSE || count( $errors[ 'image' ] ) === 0;
    }

    private function createImage( array &$errors, string $image_path ) : bool {
        list ( $image_width, $image_height, $image_type ) = getimagesize( $_FILES[ 'image' ][ 'tmp_name' ] );

        switch( $image_type ) {
            case IMAGETYPE_JPEG:
                $gd_src = imagecreatefromjpeg( $_FILES[ 'image' ][ 'tmp_name' ] );
                break;
            case IMAGETYPE_PNG:
                $gd_src = imagecreatefrompng( $_FILES[ 'image' ][ 'tmp_name' ] );
                break;
            default:
                return FALSE;
        }

        /** @var \GdImage $gd_image */
        $gd_image = imagecreate( $image_width, $image_height );

        if ( $this->copyImage( $gd_image, $gd_src, $image_width, $image_height, $image_width, $image_height ) === FALSE ) {
            $errors[ 'image' ][] = 'Can\'t copy image from resampled.';
        }
        if ( $this->createPNG( $gd_image, $image_path ) === FALSE ) {
            $errors[ 'image' ][] = 'Can\'t create a png from image.';
        }

        imagedestroy( $gd_src );
        imagedestroy( $gd_image );

        return isset( $errors[ 'image' ] ) === FALSE || count( $errors[ 'image' ] ) === 0;
    }

    private function copyImage( $image, $src, $image_width, $image_height, $src_width, $src_height ) : bool {

        return imagecopyresampled( $image, $src, 0, 0, 0 ,0, $image_width, $image_height, $src_width, $src_height  );
    }

    private function createDataCodedPath() : string {
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

    private function createPNG( $image, string $path ) : bool {

        return imagepng( $image, UPLOADS_PATH . DIRECTORY_SEPARATOR .  $path, 9 );
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

}