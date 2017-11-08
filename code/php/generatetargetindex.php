<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 10/03/2017
 * Time: 18:55
 *
 * Creates a JSON target language index for use on Lexicopia systems, of format:
 *
 * { "target_index": [
 *      { "word": "-(a)ich",
 *        "id": "_ich-verb-deriving-suffix",
 *        "en": "[verb-deriving suffix]"
 *      },
 *      ...
 *     ]
 * }
 */

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
        foreach ($lexeme->form as $nextform) {
            $entry = new entry;
            $entry->word = (string)$nextform->orth;
            $entry->id = (string)$lexeme['id'];
            if ($nextform->trans) { // is this really necessary?
                $entry->en = makeEnglishString($nextform);
            }
            else {
                $entry->en = makeEnglishString($lexeme);
            }
            $entries[] = $entry;
        }
    }
}
// sort the lexicon alphabetically, ignoring accents and case
usort($entries,'cmp');
// write out JSON objects to file
$output->target_index = $entries;
$myfile = fopen("../../" . $lang . "/cache/target-index.json", "w");
fwrite($myfile, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
fclose($myfile);
echo $lexemecount . " lexemes\n";
echo $partcount . " parts\n";

class entry {}

function cmp($s, $t) {
    $str1 = (string)$s->word;
    $str2 = (string)$t->word;
    $accentedvowels = array('à','è','ì','ò','ù','À','È','Ì','Ò','Ù','ê','ŷ','ŵ','â');
    $unaccentedvowels = array('a','e','i','o','u','A','E','I','O','U','e','y','w','a');
    $str3 = str_replace($accentedvowels,$unaccentedvowels,$str1);
    $str4 = str_replace($accentedvowels,$unaccentedvowels,$str2);
    return strcasecmp($str3,$str4);
}

function makeEnglishString($thing) {
    $enstr = "";
    foreach ($thing->trans as $nexttrans) {
        if ($nexttrans['index'] != 'only') {
            $enstr .= $nexttrans;
            $enstr .= ', ';
        }
    }
    $enstr = trim($enstr,', ');
    return $enstr;
}
