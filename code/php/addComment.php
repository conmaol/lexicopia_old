<?php

$lang = "gd";
$LEXICOPIA_PATH = "../../" . $lang . "/";

$id = $_POST["id"];
$comment = $_POST["comment"] . " [" . $_POST["userEmail"] . ": " . time() . "].";
$fileName = $LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
$lexeme = new SimpleXMLElement($fileName, 0, true);
$lexeme->addChild("note", $comment);

file_put_contents($fileName, $lexeme->asXML());

?>

