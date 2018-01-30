<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 10/03/2017
 * Time: 00:18
 */

$lang = $_GET["lang"];
$id = $_GET["id"];

$lexeme = new SimpleXMLElement("../../" . $lang . "/lexemes/" . $id . ".xml", 0, true);

echo "<div class=\"lexicopiaEntry\">";
// header:
echo "<h1><span class=\"lexicopiaHeadWord\">";
echo $lexeme->form[0]->orth . "</span> <span class=\"lexicopiaEnglish\">";
/*
$pos = str_replace('_',' ',$lexeme->getName());
if ($pos != 'lexeme'){
    echo " <a href=\"#\" id=\"posPlus\" title=\"" . $pos . "\">[+gnè]</a>";
    echo "<a href=\"#\" id=\"posMinus\">[-gnè]</a>";
    echo "<span id=\"posText\"> " . $pos . "</span>";

}
*/
if ($lexeme->trans) {
    $enStr = makeEnglishString($lexeme);
    echo "<a href=\"#\" id=\"enPlus\" title=\"" . $enStr . "\">[+en]</a>";
    echo "<a href=\"#\" id=\"enMinus\">[-en]</a>";
    echo "<span id=\"enText\"> ";
    foreach ($lexeme->trans as $nextTrans) {
        echo "<span title=\"Authorised by: " . $nextTrans["resp"] . " \">";
        echo $nextTrans . ", ";
        echo "</span>";
    }
    echo "</span>";
}
echo "</span>";
echo "</h1>";
// below header:
echo "<dl>";
echo "<dt>Forms:</dt>";
foreach ($lexeme->form as $nextForm) {
    echo "<dd>" . $nextForm->orth . "</dd>";
}
$file = "../../" . $lang . "/cache/html/" . $id . ".html";
if (file_exists($file)) {
    echo file_get_contents($file);
}
echo "</dl>";

// notes:
echo "<ul>";
foreach ($lexeme->note as $nextNote) {
    echo "<li>";
    echo $nextNote;
    echo "</li>";
}
echo "<li>[ID: " . $id . "]</li>";
echo "</ul>";
echo "</div>";

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

?>

