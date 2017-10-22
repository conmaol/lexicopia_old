<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 21/10/2017
 * Time: 23:36
 */

$input = $argv[1];

$input = str_replace("à","aa",$input);
$input = str_replace("è","ee",$input);
$input = str_replace("é","eee",$input);
$input = str_replace("ì","ii",$input);
$input = str_replace("ò","oo",$input);
$input = str_replace("ù","uu",$input);
$input = str_replace("’","%",$input);

$input = preg_replace("/\w+/","<w>$0</w>\n",$input);
$input = str_replace(" ","<space/>\n",$input);
$input = str_replace(".","<pc>.</pc>\n",$input);
$input = str_replace(",","<pc>,</pc>\n",$input);
$input = str_replace(":","<pc>:</pc>\n",$input);
$input = str_replace(";","<pc>;</pc>\n",$input);
$input = str_replace("-","<pc>-</pc>\n",$input);
$input = str_replace("%","<pc>’</pc>\n",$input);

$input = str_replace("aa","à",$input);
$input = str_replace("ee","è",$input);
$input = str_replace("eee","é",$input);
$input = str_replace("ii","ì",$input);
$input = str_replace("oo","ò",$input);
$input = str_replace("uu","ù",$input);

echo $input;
echo "\n";
