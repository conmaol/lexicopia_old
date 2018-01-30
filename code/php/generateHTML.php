<?php

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
/* create the directories for output files if necessary */
$dir = "../../" . $lang . "/cache/html";
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$dir2 = "../../" . $lang . "/cache/temp";
if (!is_dir($dir2)) {
    mkdir($dir2, 0755, true);
}
/* process each entry in turn, creating a new HTML file */
foreach ($lexicalArray as $nextId=>$nextLexeme) {
    $myFile = fopen("../../" . $lang . "/cache/temp/" . $nextId . ".html", "w");
    if ($nextLexeme->part) {
        fwrite($myFile, "<dt>Components:</dt>");
        foreach ($nextLexeme->part as $nextPart) {
            fwrite($myFile, "<dd>");
            $newId = (string)$nextPart['ref'];
            fwrite($myFile, makeLink($newId, $lexicalArray));
            fwrite($myFile, "</dd>");
        }
    }
    $compounds = getCompounds($nextId,$lexicalArray);
    if (count($compounds)>0) {
        fwrite($myFile, "<dt>Compounds:</dt>");
        foreach ($compounds as $nextCompound) {
            fwrite($myFile, "<dd>");
            fwrite($myFile, makeLink($nextCompound, $lexicalArray));
            fwrite($myFile, "</dd>");
        }
    }
    // closely related words: all words with a sense which shares ALL the topics that this word has
    /*
    if ($lexeme->topic) {
        foreach ($lexeme->topic as $nexttopic) {
            fwrite($myfile, "    <dt>");
            fwrite($myfile, $nexttopic['ref']);
            fwrite($myfile, "    </dt>\n");
        }
    }
    */
    fclose($myFile);
}

$files = glob("../../" . $lang . "/cache/html/{,.}*", GLOB_BRACE); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file)) {
        unlink($file); // delete file
    }
}
rmdir("../../" . $lang . "/cache/html");
rename("../../" . $lang . "/cache/temp", "../../" . $lang . "/cache/html");


/* HELPER FUNCTIONS */

function getCompounds($id, &$lexicalArray) {
    $compounds = array();
    foreach ($lexicalArray as $nextId=>$nextLexeme) {
        if (hasPart($nextLexeme,$id)) {
            $compounds[] = $nextId;
        }
    }
    return $compounds;
}

function hasPart($lexeme, $id) {
    $oot = FALSE;
    foreach($lexeme->part as $nextPart) {
        if((string)$nextPart['ref'] == $id) { // THIS IS WHERE THE PROBLEM LIES
            $oot = TRUE;
            break;
        }
    }
    return $oot;
}

function makeLink($id,&$lexicalArray) {
    $lexeme = $lexicalArray[$id];
    $str = "";
    $str .= "<a href=\"#\" class=\"lexicopiaLink\" data-id=\"" . $id . "\" title=\"" . makeEnglishString($lexeme) . "\">";
    $str .= $lexeme->form[0]->orth;
    $str .= "</a>";
    return $str;
}

function makeEnglishString($lexeme) {
    $enStr = "";
    foreach ($lexeme->trans as $nextTrans) {
        if ($nextTrans['index'] != 'only') {
            $enStr .= $nextTrans;
            $enStr .= ', ';
        }
    }
    $enStr = trim($enStr,', ');
    return $enStr;
}
