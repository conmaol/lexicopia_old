<?php

class Comment {

    public static function addComment($fieldData, $path) {

        define("LEXICOPIA_PATH", $path);
        $id = $fieldData["id"];
        $comment = $fieldData["comment"] . " [" . $fieldData["userEmail"] . ": " . time() . "].";
        $fileName = LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
        $lexeme = new SimpleXMLElement($fileName, 0, true);
        $lexeme->addChild("note", $comment);
        file_put_contents($fileName, $lexeme->asXML());

    }

}

?>

