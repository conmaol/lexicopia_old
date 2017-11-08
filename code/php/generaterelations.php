<?php

$lang = $argv[1];
$lexicalarray = array();
foreach (scandir("../../" . $lang . "/lexemes") as $nextfile) {
    if (substr($nextfile, -4) === ".xml") {
        $nextlexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextfile, 0, true);
        $lexicalarray[substr($nextfile,0,strlen($nextfile)-4)] = $nextlexeme;
        //$lexicalarray[(string)$nextlexeme['id']] = $nextlexeme;
    }
}
$dir = '../../' . $lang . '/cache/html';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$dir2 = '../../' . $lang . '/cache/temp';
if (!is_dir($dir2)) {
    mkdir($dir2, 0755, true);
}
foreach ($lexicalarray as $id=>$lexeme) {
    $myfile = fopen("../../" . $lang . "/cache/temp/" . $id . ".html", "w");
    if ($lexeme->part) {
        fwrite($myfile, "<dt>Buill:</dt>");
        foreach ($lexeme->part as $nextpart) {
            fwrite($myfile, "<dd>");
            $newid = (string)$nextpart['ref'];
            fwrite($myfile, makeLink($newid, $lexicalarray));
            //echo makelexemelink2($lexicon2[(string)$nextelement['ref']], $lexicon1[(string)$nextelement['ref']]);
            fwrite($myfile, "</dd>");
        }
    }
    $compounds = get_compounds($id,$lexicalarray);
    if (count($compounds)>0) {
        fwrite($myfile, "    <dt>Abairtean fillte:</dt>\n");
        foreach ($compounds as $nextcompound) {
            fwrite($myfile, "<dd>");
            fwrite($myfile, makeLink($nextcompound, $lexicalarray));
            fwrite($myfile, "</dd>");
        }
    }
    // closely related words: all words with a sense which shares ALL the topics that this word has
    if ($lexeme->topic) {
        foreach ($lexeme->topic as $nexttopic) {
            fwrite($myfile, "    <dt>");
            fwrite($myfile, $nexttopic['ref']);
            fwrite($myfile, "    </dt>\n");
        }
    }
    fclose($myfile);
}

$files = glob("../../" . $lang . "/cache/html/*"); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file)) {
        unlink($file); // delete file
    }
}
rmdir("../../" . $lang . "/cache/html");
rename("../../" . $lang . "/cache/temp", "../../" . $lang . "/cache/html");

function get_compounds($id, &$lexicalarray) {
    $compounds = array();
    foreach ($lexicalarray as $nextid=>$nextlexeme) {
        if (haspart($nextlexeme,$id)) {
            $compounds[] = $nextid;
        }
    }
    return $compounds;
}

function haspart($lexeme, $id) {
    $oot = FALSE;
    foreach($lexeme->part as $nextpart) {
        if((string)$nextpart['ref'] == $id) {
            $oot = TRUE;
            break;
        }
    }
    return $oot;
}

function makeLink($id,&$lexicalarray) {
    $lexeme = $lexicalarray[$id];
    $str = "";
    $str .= "<a href=\"#\" class=\"lexicopia-link\" data-id=\"" . $id . "\" title=\"" . makeEnglishString($lexeme) . "\">";
    $str .= $lexeme->form[0]->orth;
    $str .= "</a>";
    return $str;
}

function makeEnglishString($lexeme) {
    $enstr = "";
    foreach ($lexeme->trans as $nexttrans) {
        if ($nexttrans['index'] != 'only') {
            $enstr .= $nexttrans;
            $enstr .= ', ';
        }
    }
    $enstr = trim($enstr,', ');
    return $enstr;
}
