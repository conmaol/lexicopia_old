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

echo "<div class=\"lexicopia-entry\">";
// header:
echo "<h1>";
echo $lexeme->form[0]->orth . " ";
echo "<span>";
$pos = str_replace('_',' ',$lexeme->getName());
if ($pos != 'lexeme'){
    echo " <a href=\"#\" id=\"pos-plus\" onclick=\"showPOS(); return false;\" title=\"" . $pos . "\">[+gnè]</a>";
    echo "<a href=\"#\" id=\"pos-minus\" onclick=\"hidePOS(); return false;\">[-gnè]</a>";
    echo "<span id=\"pos-text\"> " . $pos . "</span>";

}
if ($lexeme->trans) {
    $enstr = makeEnglishString($lexeme);
    echo "<a href=\"#\" id=\"en-plus\" title=\"" . $enstr . "\">[+beurla]</a>";
    echo "<a href=\"#\" id=\"en-minus\" onclick=\"hideEnglish(); return false;\">[-beurla]</a>";
    echo "<span id=\"en-text\"> " . $enstr . "</span>";
}
echo "</span>";
echo "</h1>";
// below header:
echo "<dl>";
echo "<dt>Riochdan:</dt>";
foreach ($lexeme->form as $nextform) {
    echo "<dd>" . $nextform->orth . "</dd>";
}
$file = "../../" . $lang . "/cache/html/" . $id . ".html";
if (file_exists($file)) {
    echo file_get_contents($file);
}
echo "</dl>";

// notes:
echo "<ul>";
foreach ($lexeme->note as $nextnote) {
    echo "<li>";
    echo $nextnote;
    echo "</li>";
}
echo "</ul>";
echo "</div>";

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


/*
function makelink($lexeme) {
    $str = "";
    $str .= "<a class=\"lexicopia-link\" href=\"#\" onclick=\"entryhistory.push('" . $lexeme['id']. "'); updateContent('" . $lexeme['id'] . "');return false;\" title=\"" . makeEnglishString($lexeme) . "\">";
    $str .= $lexeme->form[0]->orth;
    $str .= "</a>";
    return $str;
}
*/
