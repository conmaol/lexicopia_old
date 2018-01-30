<?php


class FormOrth {

    public static function addFormOrth($fieldData, $path) {
        define("LEXICOPIA_PATH", $path . $fieldData["lang"] . "/");
        $id = $fieldData["id"];
        $orth = $fieldData["target"];
        $fileName = LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
        $lexeme = new SimpleXMLElement($fileName, 0, true);
        $lexeme->addChild("form")->addChild("orth", $orth)->addAttribute("resp", $fieldData["userEmail"]);
        $lexeme->addChild("note", "Form [{$orth}] contributed by user {$fieldData["userEmail"]} at " . time());
        file_put_contents($fileName, $lexeme->asXML());

        $targetIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/targetIndex.json"), true);
        array_push($targetIndexJSON["targetIndex"], self::getTargetEntry($orth, $id));
        file_put_contents(LEXICOPIA_PATH . "cache/targetIndex.json", json_encode($targetIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);

    }

    private static function getTargetEntry($target, $id) {
        $entry = array("target" => $target, "id" => "{$id}", "en" => "");
        return $entry;
    }


}

?>