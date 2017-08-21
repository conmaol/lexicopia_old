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
echo "<div class=\"lexicopia-entry-header\">";
echo "<span class=\"lexicopia-headword\">" . $lexeme->form[0]->orth . "</span>";
$pos = str_replace('_',' ',$lexeme->getName());
if ($pos != 'lexeme'){
    echo " <a class=\"lexicopia-link\" href=\"#\" id=\"pos-plus\" style=\"display:inline\" onclick=\"showPOS(); return false;\" title=\"" . $pos . "\">[+gnè]</a>";
    echo "<a class=\"lexicopia-link\" href=\"#\" id=\"pos-minus\" style=\"display:none\" onclick=\"hidePOS(); return false;\">[-gnè]</a>";
    echo "<span class=\"en-span\" id=\"pos-text\" style=\"display:none;\"> " . $pos . "</span>";

}
if ($lexeme->trans) {
    $enstr = makeEnglishString($lexeme);
    echo " <a class=\"lexicopia-link\" href=\"#\" id=\"en-plus\" style=\"display:inline\" onclick=\"showEnglish(); return false;\" title=\"" . $enstr . "\">[+beurla]</a>";
    echo "<a class=\"lexicopia-link\" href=\"#\" id=\"en-minus\" style=\"display:none\" onclick=\"hideEnglish(); return false;\">[-beurla]</a>";
    echo "<span class=\"en-span\" id=\"en-text\" style=\"display:none;\"> " . $enstr . "</span>";
}
echo "</div>";
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
echo "<ul class=\"lexicopia-entry-notes\">";
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
