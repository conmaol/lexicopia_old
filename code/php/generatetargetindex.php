<?php

$lang = $argv[1]; //get language code from command line
$entries = array();
$lexemecount = 0;
$partcount = 0;
foreach (scandir("../../" . $lang . "/lexemes") as $nextfile) {
    if (substr($nextfile,-4) === ".xml") {
        $lexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextfile, 0 , true);
        $lexemecount++;
        foreach ($lexeme->part as $nextpart) {
            $partcount++;
        }
        foreach ($lexeme->form as $nextform) { // do for every form, not just headwords
            $entry = new entry;
            $entry->word = (string)$nextform->orth;
            $entry->id = substr($nextfile,0,strlen($nextfile)-4);
            $entry->en = makeEnglishString($lexeme);
            $entries[] = $entry;
        }
    }
}
// sort the lexicon alphabetically, ignoring accents and case
usort($entries, function ($str1, $str2) {
    return strcasecmp(stripAccents((string)$str1->word),stripAccents((string)$str2->word));
});
// write out JSON objects to file
$output->target_index = $entries;
$myfile = fopen("../../" . $lang . "/cache/target-index.json", "w");
fwrite($myfile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
fclose($myfile);
echo $lexemecount . " lexemes\n";
echo $partcount . " parts\n";

class entry {}

function stripAccents($string) {
    $accentedvowels = array('à','è','ì','ò','ù','À','È','Ì','Ò','Ù','ê','ŷ','ŵ','â');
    $unaccentedvowels = array('a','e','i','o','u','A','E','I','O','U','e','y','w','a');
    return str_replace($accentedvowels, $unaccentedvowels, $string);
}

function makeEnglishString($thing) {
    $enstr = "";
    foreach ($thing->trans as $nexttrans) {
        if ($nexttrans["index"] != "only") {
            $enstr .= $nexttrans;
            $enstr .= ", ";
        }
    }
    $enstr = trim($enstr,", ");
    return $enstr;
}
