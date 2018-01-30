<?php


class EnTrans {

    public static function addEnTrans($fieldData, $path) {
        define("LEXICOPIA_PATH", $path . $fieldData["lang"] . "/");
        $id = $fieldData["id"];
        $trans = $fieldData["trans"];
        $form = $fieldData["form"];
        $fileName = LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
        $lexeme = new SimpleXMLElement($fileName, 0, true);
        $lexeme->addChild("trans", $trans)->addAttribute("resp", $fieldData["userEmail"]);
        $lexeme->addChild("note", "English [{$trans}] contributed by user {$fieldData["userEmail"]} at " . time());
        file_put_contents($fileName, $lexeme->asXML());
        // update englishIndex.json: everything below needs work: None of it actually works!
        $found = false;
        $englishIndexJSON = json_decode(file_get_contents(LEXICOPIA_PATH . "cache/englishIndex.json"), true);
        foreach ($englishIndexJSON["englishIndex"] as $key => $entry) {
            if ($entry["en"] == $trans) {      //existing English entry found so add new Gaelic form
                array_push($englishIndexJSON["englishIndex"][$key]["targets"], array("id" => "{$id}", "form" => "{$form}"));
                $found = true;
            }
        }
        if (!$found) {    //entry not found so add a new one
            array_push($englishIndexJSON["englishIndex"], self::getEnglishEntry($form, $trans, $id));
        }
        //add to target index too!
        file_put_contents(LEXICOPIA_PATH . "cache/englishIndex.json", json_encode($englishIndexJSON, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);

    }

    private static function getEnglishEntry($target, $en, $id) {
        $entry = array("en" => $en, "targets" => array(array("id" => "{$id}", "form" => "{$target}")));
        return $entry;
    }

    public static function authEnTrans($fieldData, $path) {
        define("LEXICOPIA_PATH", $path . $fieldData["lang"] . "/");
        $id = $fieldData["id"];
        $trans = $fieldData["trans"];
        $user = $fieldData["userEmail"];
        $fileName = LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
        $lexeme = new SimpleXMLElement($fileName, 0, true);
        foreach ($lexeme->trans as $nextTrans) {
            if ((string)$nextTrans == $trans) {
                if ($nextTrans["resp"]) {
                    $nextTrans["resp"] .= " ";
                    $nextTrans["resp"] .= $user;
                }
                else {
                    $nextTrans->addAttribute("resp", $user);
                }
                break;
            }
        }
        // write back to file, you idiot!
        file_put_contents($fileName, $lexeme->asXML());
    }

    public static function deAuthEnTrans($fieldData, $path) {
        define("LEXICOPIA_PATH", $path . $fieldData["lang"] . "/");
        $id = $fieldData["id"];
        $trans = $fieldData["trans"];
        $user = $fieldData["userEmail"];
        $fileName = LEXICOPIA_PATH . "lexemes/" . $id . ".xml";
        $lexeme = new SimpleXMLElement($fileName, 0, true);
        foreach ($lexeme->trans as $nextTrans) {
            if ((string)$nextTrans == $trans) {
                $start = strpos((string)$nextTrans["resp"], $user);
                if ($start) {
                    //delete the email from the attribute value
                    $in = $nextTrans["resp"];
                    $nextTrans["resp"] = substr($in,0,$start) . substr($in, $start + strlen($user));
                }
                break;
            }
        }
    }


}

?>

