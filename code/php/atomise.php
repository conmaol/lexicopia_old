<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 20/08/2017
 * Time: 11:21
 */

$lexicon = new SimpleXMLElement("../../../lexicopia-xml/gd/lexicon.xml", 0, true);
foreach ($lexicon->children() as $nextlexeme) {
    $nextlexeme->addAttribute('xmlns:xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
    $nextlexeme->addAttribute('xmlns:xsi:noNamespaceSchemaLocation','../lexeme.xsd');
    $id = (string)$nextlexeme['id'];
    $file = fopen("../../../lexicopia-xml/gd/lexemes/" . $id . ".xml", "w");
    fwrite($file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n" . $nextlexeme->asXML() . "\n");
    fclose($file);
}

