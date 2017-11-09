<?php

$lang = $argv[1]; //get language code from command line
// create a list of unique English words from the lexicon
$english_words = array();
$lexicalarray = array();
foreach (scandir("../../" . $lang . "/lexemes") as $nextfile) {
    if (substr($nextfile, -4) === ".xml") {
        $nextlexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextfile, 0, true);
        $lexicalarray[substr($nextfile,0,strlen($nextfile)-4)] = $nextlexeme;
        foreach ($nextlexeme->trans as $nexttrans) {
            if ($nexttrans["index"] != "no") {
                $english_words[] = $nexttrans;
            }
        }
    }
}
$newarray = array_unique($english_words); // remove duplicates
$english_words = $newarray;
natcasesort($english_words); // sort it alphabetically
// populate the array with appropriate PHP objects:
$entries = array();
foreach ($english_words as $nexten) {
    $entry = new entry;
    $entry->en = (string)$nexten;
    $gds = array();
    foreach ($lexicalarray as $id=>$lexeme) {
        if (hasEn($lexeme, $nexten)) {
            $gd = new gd;
            $gd->id = $id;
            $gd->form = (string)$lexeme->form[0]->orth;
            $gds[] = $gd;
        }
    }
    $entry->gds = $gds;
    $entries[] = $entry;
}
$output->english_index = $entries;
$myfile = fopen("../../" . $lang . "/cache/english-index.json", "w");
fwrite($myfile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
fclose($myfile);

class entry {}

class gd {}

function hasEn($lexeme,$en) {
    foreach ($lexeme->trans as $nexttrans) {
        if ((string)$nexttrans == $en) {
            return TRUE;
        }
    }
    return FALSE;
}
