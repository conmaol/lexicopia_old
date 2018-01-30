<?php

class NewEntry {

    public static function addEntry($fieldData,$path) {
        define("LEXICOPIA_PATH", $path . $fieldData["lang"] . "/");
        $id = "qqq-" . str_replace(" ", "_", $fieldData["target"]) . "-" . time();
        file_put_contents(LEXICOPIA_PATH . "lexemes/" . $id . ".xml", self::getEntryXml($fieldData, $id));
        //updateTargetJSONFile($lang, $_POST, $id);
        $targetIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/targetIndex.json"), true);
        array_push($targetIndexJSON["targetIndex"], self::getTargetEntry($fieldData["target"], $fieldData["en"], $id));
        file_put_contents(LEXICOPIA_PATH . "cache/targetIndex.json", json_encode($targetIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
        //updateEnglishJSONFile($lang, $_POST, $id);
        $found = false;
        $englishIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/englishIndex.json"), true);
        foreach ($englishIndexJSON["englishIndex"] as $key => $entry) {
            if ($entry["en"] == $fieldData["en"]) {      //existing English entry found so add new Gaelic form
                array_push($englishIndexJSON["englishIndex"][$key]["targets"], array("id" => "{$id}", "form" => "{$fieldData["target"]}"));
                $found = true;
            }
        }
        if (!$found) {    //entry not found so add a new one
            array_push($englishIndexJSON["englishIndex"], self::getEnglishEntry($fieldData["target"], $fieldData["en"], $id));
        }
        file_put_contents(LEXICOPIA_PATH . "cache/englishIndex.json", json_encode($englishIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    private static function getEntryXml($fields, $id) {
        $timestamp = time();
        $xml = <<<XML
<lexeme id="{$id}" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../lexeme.xsd">
  <form>
    <orth>{$fields["target"]}</orth>
  </form>
  <trans resp="{$fields["userEmail"]}">{$fields["en"]}</trans>
  <note>Related forms: {$fields["related"]}</note>
  <note>Note: {$fields["notes"]}</note>
  <note>Contributed by user {$fields["userEmail"]} at {$timestamp}.</note>
</lexeme>
XML;
        return $xml;
    }

    private static function getTargetEntry($target, $en, $id) {
        $entry = array("target" => $target, "id" => "{$id}", "en" => $en);
        return $entry;
    }

    private static function getEnglishEntry($target, $en, $id) {
        $entry = array("en" => $en, "targets" => array(array("id" => "{$id}", "form" => "{$target}")));
        return $entry;
    }

}

?>
