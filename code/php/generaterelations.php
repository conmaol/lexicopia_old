<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 11/03/2017
 * Time: 01:17
 */

$lang = $argv[1];

$lexicalarray = array();
foreach (scandir("../../" . $lang . "/lexemes") as $nextfile) {
    if (substr($nextfile, -4) === ".xml") {
        $nextlexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $nextfile, 0, true);
        $lexicalarray[(string)$nextlexeme['id']] = $nextlexeme;
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
    echo $id . "\n";
    if ($lexeme->part) {
        fwrite($myfile, "<dt>Buill:</dt>");
        foreach ($lexeme->part as $nextpart) {
            fwrite($myfile, "<dd>");
            if ($nextpart['ref']) {
                fwrite($myfile, makelink($lexicalarray[(string)$nextpart['ref']]));
                //echo makelexemelink2($lexicon2[(string)$nextelement['ref']], $lexicon1[(string)$nextelement['ref']]);
            } else {
                //$str .= display_structure($nextdep->sign, $lexicon);
            }
            fwrite($myfile, "</dd>");
        }
    }

    $compounds = get_compounds($id,$lexicalarray);
    if (count($compounds)>0) {
        fwrite($myfile, "    <dt>Abairtean fillte:</dt>\n");
        foreach ($compounds as $nextcompound) {
            fwrite($myfile, "<dd>");
            fwrite($myfile, makelink($nextcompound));
            fwrite($myfile, "</dd>");
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

function get_compounds($id,&$lexicalarray) {
    $compounds = array();
    foreach ($lexicalarray as $nextid=>$nextlexeme) {
        if (haspart($nextlexeme,$id)) {
            $compounds[] = $nextlexeme;
        }
    }
    return $compounds;
}

function haspart($lexeme,$id) {
    $oot = FALSE;
    foreach($lexeme->part as $nextpart) {
        if((string)$nextpart['ref'] == $id) {
            $oot = TRUE;
            break;
        }
    }
    return $oot;
}


/*
function get_dependent_refs($sign) {
    $dependents = array();
    foreach ($sign->dependent as $nextdependent) {
        if ($nextdependent['ref']) {
            $dependents[] = (string)$nextdependent['ref'];
        }
        elseif ($nextdependent->sign) {
            $newsign = $nextdependent->sign;
            if ($newsign->syntax['ref']) {
                $refs = explode(" ", $newsign->syntax['ref']);
                foreach ($refs as $nextdep) {
                    $dependents[] = $nextdep;
                }
            }
            $newdependents = get_dependent_refs($newsign);
            foreach ($newdependents as $nextdep) {
                $dependents[] = $nextdep;
            }
        }
    }
    return $dependents;
}
*/

function makelink($lexeme) {
    $str = "";
    $str .= "<a class=\"lexicopia-link\" href=\"#\" onclick=\"entryhistory.push('" . $lexeme['id']. "'); updateContent('" . $lexeme['id'] . "');return false;\" title=\"" . makeEnglishString($lexeme) . "\">";
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

function is_a_form_of($sign,$rootid) {
    foreach ($sign->dependent as $nextdependent) {
        if ((string)$nextdependent['relation'] == 'root' || (string)$nextdependent['relation'] == 'stem') {
            if ((string)$nextdependent['ref'] == $rootid) {
                return TRUE;
                break;
            }
            elseif ($nextdependent->sign) {
                if (is_a_form_of($nextdependent->sign,$rootid)) {
                    return TRUE;
                    break;
                }
            }
        }
    }
    return FALSE;
}