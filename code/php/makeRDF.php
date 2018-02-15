<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 15/02/2018
 * Time: 10:59
 */

$lang = $argv[1];
/* build the lexicon as an associative array */
$lexicalArray = array();
foreach (scandir("../../" . $lang . "/lexemes") as $nextFile) {
    if (substr($nextFile, -4) === ".xml") {
        $nextLexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextFile, 0, true);
        //$id = substr($nextfile,0,strlen($nextfile)-4); // WHY DOES THIS NOT WORK HERE?
        $id = (string)$nextLexeme["id"];
        $lexicalArray[$id] = $nextLexeme;
    }
}

/* process each entry in turn */
foreach ($lexicalArray as $nextId=>$nextLexeme) {
    $pos = $nextLexeme->getName();
    if ($pos == 'ainmear_fireann') {
        echo "gd:" . $nextId . " a :MasculineNoun ;\n";
    }
    else if ($pos == 'ainmear_boireann') {
        echo "gd:" . $nextId . " a :FeminineNoun ;\n";
    }
    else if ($pos == 'ainmear') {
        echo "gd:" . $nextId . " a :Noun ;\n";
    }
    else if ($pos == 'buadhair') {
        echo "gd:" . $nextId . " a :Adjective ;\n";
    }
    else if ($pos == 'gnìomhair') {
        echo "gd:" . $nextId . " a :Verb ;\n";
    }
    else {
        echo "gd:" . $nextId . " a :Lexeme ;\n";
    }
    echo "    :headword \"" . $nextLexeme->form[0]->orth . "\" ;\n";
    foreach ($nextLexeme->part as $nextPart) {
        echo "    :contains gd:" . $nextPart['ref'] . " ;\n";
    }
    foreach ($nextLexeme->form as $nextForm) {
        echo "    :form [ :orth \"" . $nextForm->orth . "\" ] ;\n";
    }
    foreach ($nextLexeme->trans as $nextTrans) {
        echo "    :en [ :orth \"" . $nextTrans . "\" " ;
        if ($nextTrans['index']) {
            echo "; :index \"" . $nextTrans['index'] . "\" ";
        }
        echo "] ;\n";
    }
    foreach ($nextLexeme->note as $nextNote) {
        $nextNote = str_replace("\"", "'", $nextNote);
        $nextNote = str_replace("\n", " ", $nextNote);
        echo "    :comment [ :content \"" . $nextNote . "\" ] ;\n";
    }
    echo "    :comment \"\" .\n\n";


}
