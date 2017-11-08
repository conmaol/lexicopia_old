<?php

function addNewEntry($lang, $target, $en, $forms, $notes, $userEmail) {
    $id = str_replace(" ", "_", $target);
    $id = $id . "-" . time();
    $lexeme = getEntryXml($target, $en, $forms, $notes, $userEmail, $id);
    $filename = "../lexicopia/" . $lang . "/lexemes/" . $id . ".xml";
    file_put_contents($filename, $lexeme);
    updateTargetJSONFile($lang, $target, $en, $id);
    updateEnglishJSONFile($lang, $target, $en, $id);
}

function getEntryXml($target, $en, $forms, $notes, $userEmail, $id) {
    $timestamp = time();
    $xml = <<<XML
    <lexeme id="{$id}" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../lexeme.xsd">
      <form>
        <orth>{$target}</orth>
      </form>
      <trans>{$en}</trans>
      <note>Related forms: {$forms}</note>
      <note>Note: {$notes}</note>
      <note>Contributed by user {$userEmail} at {$timestamp}.</note>
    </lexeme>
XML;
    return $xml;
}

function updateTargetJSONFile($lang, $target, $en, $id) {
    $found = false;
    $targetFile = file_get_contents("../lexicopia/" . $lang . "/cache/target-index.json");
    $targetJson = json_decode($targetFile);
    $targetJSONArray = json_decode($targetFile, true); // WHAT?
    array_push($targetJSONArray["target_index"], getTargetEntry($target, $en, $id));
    $targetJson = $targetJSONArray;
    file_put_contents("../lexicopia/" . $lang . "/cache/target-index.json", json_encode($targetJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    return $id;
}

function getTargetEntry($target, $en, $id) {
    $entry = array("word" => $target, "id" => "{$id}", "en" => $en);
    return $entry;
}

function updateEnglishJSONFile($lang, $target, $en, $id) {
    $found = false;
    $englishFile = file_get_contents("../lexicopia/" . $lang . "/cache/english-index.json");
    $englishJson = json_decode($englishFile, true);
    foreach ($englishJson["english_index"] as $key => $entry) {
        if ($entry["en"] == $en) {      //existing English entry found so add new Gaelic form
            array_push($englishJson["english_index"][$key]["gds"], array("id" => "{$id}", "form" => "{$target}"));
            $found = true;
        }
    }
    if (!$found) {    //entry not found so add a new one
        array_push($englishJson["english_index"], getEnglishEntry($target, $en, $id));
    }
    file_put_contents("../lexicopia/" . $lang . "/cache/english-index.json", json_encode($englishJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function getEnglishEntry($target, $en, $id) {
  $entry = array("en" => $en, "gds" => array(array("id" => "{$id}", "form" => "{$target}")));
  return $entry;
}





?>