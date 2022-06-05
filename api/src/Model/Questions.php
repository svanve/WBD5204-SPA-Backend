<?php

namespace WBD5204\Model;

use WBD5204\Model as AbstractModel;

final class Questions extends AbstractModel {

    // @GET
    public function get( array &$errors, array &$results ) {
        $query = 'SELECT * FROM questions';

        $statement = $this->Database->prepare($query);
        $statement->execute();

        $results['results'] = $statement->fetchAll();

        return count( $results['results'] ) > 0;
    }
}