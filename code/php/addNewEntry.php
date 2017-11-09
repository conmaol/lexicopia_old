<?php

$lang = "gd";

$id = str_replace(" ", "_", $_POST["gd"]);
$id = $id . "-" . time();
$lexeme = getEntryXml($_POST, $id);
$fileName = "../../" . $lang . "/lexemes/" . $id . ".xml";
file_put_contents($fileName, $lexeme);
//updateTargetJSONFile($lang, $_POST, $id);
$targetFile = file_get_contents("../../" . $lang . "/cache/target-index.json");
$targetJson = json_decode($targetFile);
$targetJSONArray = json_decode($targetFile, true); // WHAT?
array_push($targetJSONArray["target_index"], getTargetEntry($_POST["gd"], $_POST["en"], $id));
$targetJson = $targetJSONArray;
file_put_contents("../../" . $lang . "/cache/target-index.json", json_encode($targetJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
//updateEnglishJSONFile($lang, $_POST, $id);
$found = false;
$englishFile = file_get_contents("../../" . $lang . "/cache/english-index.json");
$englishJson = json_decode($englishFile, true);
foreach ($englishJson["english_index"] as $key => $entry) {
    if ($entry["en"] == $_POST["en"]) {      //existing English entry found so add new Gaelic form
        array_push($englishJson["english_index"][$key]["gds"], array("id" => "{$id}", "form" => "{$_POST["gd"]}"));
        $found = true;
    }
}
if (!$found) {    //entry not found so add a new one
    array_push($englishJson["english_index"], getEnglishEntry($_POST["gd"], $_POST["en"], $id));
}
file_put_contents("../../" . $lang . "/cache/english-index.json", json_encode($englishJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);

function getEntryXml($fields, $id) {
    $timestamp = time();
    $xml = <<<XML
    <lexeme id="{$id}" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../lexeme.xsd">
      <form>
        <orth>{$fields["gd"]}</orth>
      </form>
      <trans>{$fields["en"]}</trans>
      <note>Related forms: {$fields["related"]}</note>
      <note>Note: {$fields["notes"]}</note>
      <note>Contributed by user {$fields["userEmail"]} at {$timestamp}.</note>
    </lexeme>
XML;
    return $xml;
}

function getTargetEntry($target, $en, $id) {
    $entry = array("word" => $target, "id" => "{$id}", "en" => $en);
    return $entry;
}

function getEnglishEntry($target, $en, $id) {
    $entry = array("en" => $en, "gds" => array(array("id" => "{$id}", "form" => "{$target}")));
    return $entry;
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