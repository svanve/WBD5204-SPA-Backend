<?php

namespace WBD5204;

abstract class Model {

    protected ?Database $Database = NULL;

    public function __construct() {
        $this->Database = new Database();
    }

    public function getFormData() : array {
        /** @var array $data */
        $data = [];
        // read incoming data, then we have to parse the request into array
        $input = $this->getInput();

        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block)
        {
            if (empty($block))
                continue;

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== FALSE)
            {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }
            // parse all other fields
            else
            {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }

            $data[$matches[1]] = $matches[2];
        }

        return $data;
    }

    public function getInput() : string {

        return file_get_contents('php://input');
    }
}