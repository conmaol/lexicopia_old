<?php

$lang = $argv[1]; //get language code from command line
// create a list of unique English words from the lexicon
$englishWords = array();
$lexicalArray = array();
foreach (scandir("../../" . $lang . "/lexemes") as $nextFile) {
    if (substr($nextFile, -4) === ".xml") {
        $nextLexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextFile, 0, true);
        //$lexicalArray[substr($nextFile,0,strlen($nextFile)-4)] = $nextLexeme;
        $lexicalArray[(string)$nextLexeme["id"]] = $nextLexeme;
        foreach ($nextLexeme->trans as $nextTrans) {
            if ($nextTrans["index"] != "no") {
                $englishWords[] = $nextTrans;
            }
        }
    }
}
$newArray = array_unique($englishWords); // remove duplicates
$englishWords = $newArray;
natcasesort($englishWords); // sort it alphabetically
// populate the array with appropriate PHP objects:
$entries = array();
foreach ($englishWords as $nextEn) {
    $entry = new entry;
    $entry->en = (string)$nextEn;
    $targets = array();
    foreach ($lexicalArray as $nextId=>$nextLexeme) {
        if (hasEn($nextLexeme, $nextEn)) {
            $target = new target;
            $target->id = $nextId;
            $target->form = (string)$nextLexeme->form[0]->orth;
            $targets[] = $target;
        }
    }
    $entry->targets = $targets;
    $entries[] = $entry;
}
$output->englishIndex = $entries;
$myFile = fopen("../../" . $lang . "/cache/englishIndex.json", "w");
fwrite($myFile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
fclose($myFile);

class entry {}

class target {}

function hasEn($lexeme,$en) {
    foreach ($lexeme->trans as $nextTrans) {
        if ((string)$nextTrans == $en) {
            return TRUE;
        }
    }
    return FALSE;
}
