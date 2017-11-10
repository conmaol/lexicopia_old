<?php

class AddNewEntry {

    public static function addEntry($fieldData) {
        define("LEXICOPIA_PATH", "../lexicopia/" . $fieldData["lang"] . "/");
        $id = "qqq-" . str_replace(" ", "_", $fieldData["target"]) . "-" . time();
        file_put_contents(LEXICOPIA_PATH . "lexemes/" . $id . ".xml", self::getEntryXml($fieldData, $id));
        //updateTargetJSONFile($lang, $_POST, $id);
        $targetIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/target-index.json"), true);
        array_push($targetIndexJSON["target_index"], self::getTargetEntry($fieldData["target"], $fieldData["en"], $id));
        file_put_contents(LEXICOPIA_PATH . "cache/target-index.json", json_encode($targetIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
        //updateEnglishJSONFile($lang, $_POST, $id);
        $found = false;
        $englishIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/english-index.json"), true);
        foreach ($englishIndexJSON["english_index"] as $key => $entry) {
            if ($entry["en"] == $fieldData["en"]) {      //existing English entry found so add new Gaelic form
                array_push($englishIndexJSON["english_index"][$key]["gds"], array("id" => "{$id}", "form" => "{$fieldData["target"]}"));
                $found = true;
            }
        }
        if (!$found) {    //entry not found so add a new one
            array_push($englishIndexJSON["english_index"], self::getEnglishEntry($fieldData["target"], $fieldData["en"], $id));
        }
        file_put_contents(LEXICOPIA_PATH . "cache/english-index.json", json_encode($englishIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    private static function getEntryXml($fields, $id) {
        $timestamp = time();
        $xml = <<<XML
    <lexeme id="{$id}" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../lexeme.xsd">
      <form>
        <orth>{$fields["target"]}</orth>
      </form>
      <trans>{$fields["en"]}</trans>
      <note>Related forms: {$fields["related"]}</note>
      <note>Note: {$fields["notes"]}</note>
      <note>Contributed by user {$fields["userEmail"]} at {$timestamp}.</note>
    </lexeme>
XML;
        return $xml;
    }

    private static function getTargetEntry($target, $en, $id)
    {
        $entry = array("word" => $target, "id" => "{$id}", "en" => $en);
        return $entry;
    }

    private static function getEnglishEntry($target, $en, $id)
    {
        $entry = array("en" => $en, "gds" => array(array("id" => "{$id}", "form" => "{$target}")));
        return $entry;
    }

}



/*
        function updateTargetJSONFile($lang, $fields, $id) {
            $found = false;
            $targetFile = file_get_contents("../lexicopia/" . $lang . "/cache/target-index.json");
            $targetJson = json_decode($targetFile);
            $targetJSONArray = json_decode($targetFile, true); // WHAT?
            array_push($targetJSONArray["target_index"], getTargetEntry($fields["gd"], $fields["en"], $id));
            $targetJson = $targetJSONArray;
            file_put_contents("../lexicopia/" . $lang . "/cache/target-index.json", json_encode($targetJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
            return $id;
        }
        */

/*
function updateEnglishJSONFile($lang, $fields, $id) {
    $found = false;
    $englishFile = file_get_contents("../lexicopia/" . $lang . "/cache/english-index.json");
    $englishJson = json_decode($englishFile, true);
    foreach ($englishJson["english_index"] as $key => $entry) {
        if ($entry["en"] == $fields["en"]) {      //existing English entry found so add new Gaelic form
            array_push($englishJson["english_index"][$key]["gds"], array("id" => "{$id}", "form" => "{$fields["gd"]}"));
            $found = true;
        }
    }
    if (!$found) {    //entry not found so add a new one
        array_push($englishJson["english_index"], getEnglishEntry($fields["gd"], $fields["en"], $id));
    }
    file_put_contents("../lexicopia/" . $lang . "/cache/english-index.json", json_encode($englishJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}
*/

?>
