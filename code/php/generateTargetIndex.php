<?php

$lang = $argv[1]; //get language code from command line
$entries = array();
$lexemeCount = 0;
$partCount = 0;
foreach (scandir("../../" . $lang . "/lexemes") as $nextFile) {
    if (substr($nextFile,-4) === ".xml") {
        $lexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextFile, 0 , true);
        $lexemeCount++;
        foreach ($lexeme->part as $nextPart) {
            $partCount++;
        }
        foreach ($lexeme->form as $nextForm) { // do for every form, not just headwords
            $entry = new entry;
            $entry->target = (string)$nextForm->orth;
            //$entry->id = substr($nextFile,0,strlen($nextFile)-4);
            $entry->id = (string)$lexeme["id"];
            $entry->en = makeEnglishString($lexeme);
            $entries[] = $entry;
        }
    }
}
// sort the lexicon alphabetically, ignoring accents and case
usort($entries, function ($str1, $str2) {
    return strcasecmp(stripAccents((string)$str1->target),stripAccents((string)$str2->target));
});
// write out JSON objects to file
$output->targetIndex = $entries;
$myFile = fopen("../../" . $lang . "/cache/targetIndex.json", "w");
fwrite($myFile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
fclose($myFile);
echo $lexemeCount . " lexemes\n";
echo $partCount . " parts\n";

class entry {}

function stripAccents($string) {
    $accentedVowels = array('à','è','ì','ò','ù','À','È','Ì','Ò','Ù','ê','ŷ','ŵ','â');
    $unaccentedVowels = array('a','e','i','o','u','A','E','I','O','U','e','y','w','a');
    return str_replace($accentedVowels, $unaccentedVowels, $string);
}

function makeEnglishString($lexeme) {
    $enStr = "";
    foreach ($lexeme->trans as $nextTrans) {
        if ($nextTrans["index"] != "only") {
            $enStr .= $nextTrans;
            $enStr .= ", ";
        }
    }
    $enStr = trim($enStr,", ");
    return $enStr;
}
