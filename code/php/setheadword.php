<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 05/06/2017
 * Time: 21:32
 *
 * Sets the form value of sign with a particular id in a particular language.
 *
 */

$lang = $argv[1];
$id = $argv[2];
$form = $argv[3];

$rootfile =  __DIR__."/../../../lexicopia-xml/" . $lang . "/lexicon-" . $lang .".xml";
$lexiconset = new SimpleXMLElement($rootfile, 0, true);

$includes = $lexiconset->children('http://www.w3.org/2001/XInclude');

$found = FALSE;
foreach ($includes as $nextinclude) {
    $attributes = $nextinclude->attributes();
    $path = __DIR__."/../../../lexicopia-xml/" . $lang . "/" . $attributes['href'];
    $lexicon = new SimpleXMLElement($path, 0, true);
    foreach ($lexicon->sign as $nextsign) {
        if ((string)$nextsign['id'] == $id) {
            $found = TRUE;
            $nextsign->form[0] = str_replace("_", " ", $form);
            break;
        }
    }
    if ($found) {
        $myfile = fopen($path, "w");
        fwrite($myfile,$lexicon->asXML());
        fclose($myfile);
        break;
    }
}








