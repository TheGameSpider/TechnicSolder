<?php
/*
TOML Parser
Super inefficient, but gets the job done.
Limitation: only gets the first dependency. TODO: split dependency.* into it's own array, and stuff the * into their own id within dependency to prevent overwrite.

MIT License, see LICENSE file, with the difference of:
Copyright 2021 Henry Gross-Hellsen
*/
function p1($inputToml) {
    //remove commented lines
    $lines=array();
    $counter=0;
    foreach (explode("\n", $inputToml) as $line) {
        $line=ltrim($line);
        if ($line[0] == '#') continue;
        $lines[$counter]=$line;
        $counter++;
    }
    // form back into inputToml
    $fileContents=implode("\n", $lines);

    // break inputToml into blocks by scope
    $fileArray = explode("[[", $fileContents);
    $counter=0;
    foreach ($fileArray as $fileElement) {
        if ($counter > 0) {
            $fileArray[$counter]='[['.$fileElement;
        }
        $counter++;
    }
    return $fileArray;
}
function p2($inputArray) {
    // breaks each block line into array of lines, remove empty elements
    $lines=array();
    $cc=0;
    //print_r($inputArray);
    foreach ($inputArray as $block) {
        $block=ltrim($block);
        // if (empty($block)) {
        // unset ($inputArray[$cc]);
        // continue;
        // }
        $tmp=explode("\n", $block);
        $c=0;
        foreach ($tmp as $line) {
            $line=ltrim($line);
            if (empty($line)) {
                unset($tmp[$c]);
                $tmp=array_values($tmp);
                continue;
            }
            $tmp[$c]=$line;
            $c=$c+1;
        }
        array_push($lines, $tmp);
        $cc=$cc+1;
    }

    return $lines;
}
function p3($inputArray) {
    // set the block array key to the first row of the block, if it has a scope value ('[[')
    $c=0;
    //print_r($inputArray);
    foreach ($inputArray as $lineBlock) {
        if (substr($lineBlock[0], 0, 2) == '[[') {
            unset($inputArray[$c][0]);
            $inputArray[trim(trim(trim(explode('#', $lineBlock[0])[0], ' '), ']]'),'[[')] = $inputArray[$c];
            unset($inputArray[$c]);
        }
        $c++;
    }
    return $inputArray;
}

function p4($inputArray) {
    // break each row/line element of each block array into left and right values.
    // these values are then stored as key $right => value $left.
    $keys=array_keys($inputArray);
    $c=0;
    foreach ($inputArray as $block) {
        $keys2=array_keys($block);
        $c2=0;
        foreach ($block as $row) {
            $rowArray=explode('=', $row);
            if (count($rowArray) == 1) { // just push if no key
                array_push($inputArray[$keys[$c]], $rowArray[0]);
                unset($inputArray[$keys[$c]][$keys2[$c2]]);
            } else { // assign value to key
                $left=$rowArray[0];
                $right=end($rowArray);
                $left=explode('#', $left)[0];
                $right=explode('#', $right)[0];
                $left=trim(trim(str_replace("\t", ' ', $left), ' '), '"');
                $right=trim(trim(str_replace("\t", ' ', $right), ' '), '"');
                unset($inputArray[$keys[$c]][$keys2[$c2]]);
                if (ctype_alpha($left)) {
                    // while we already did remove commented lines, and exploded by comment, some new rows/lines MAY still have a comment in them.
                    if ($row[0] == '#') continue;
                    if ($left[0] == '#' || $right[0] == '#') continue;
                    $inputArray[$keys[$c]][$left]=$right;
                }
            }

            $c2++;
        }
        $c++;
    }
    return $inputArray;
}

function p5($inputArray) {
    // handle multiline comments ''' or """..?
    //print_r($inputArray);
    $continuous='';
    $continuousCount=0;
    $continuousKey='';

    $keySet=array_keys($inputArray);
    $counter=0;
    foreach ($inputArray as $block) {
        $blockKeySet=array_keys($block);
        $counter2=0;

        //print_r($block);
        foreach ($block as $row) {
            if (substr($row, 0, 3) == '"""' || substr($row, 0, 3) == "'''") {
                if ($continuousCount==0) { // multi-line begin
                    //echo "BEGIN: ".$row."\n";
                    $continuousCount=1;
                    $continuousKey=$counter2;
                    unset ($inputArray[$keySet[$counter]][$blockKeySet[$counter2]]);
                } else if ($continuousCount==1) { // multi-line end
                    //echo "END: ".$row."\n";
                    $continuousCount=0;
                    $inputArray[$keySet[$counter]][$blockKeySet[$continuousKey]]=$continuous;
                    unset ($inputArray[$keySet[$counter]][$blockKeySet[$counter2]]);
                }
            } else if ($continuousCount == 1) { // append multi-line
                $continuous=$continuous.$row."\n";
                unset ($inputArray[$keySet[$counter]][$blockKeySet[$counter2]]);
                //echo "APPEND: ".$continuous."\n";
            }
            $counter2++;
        }

        $counter++;
    }
    //echo "\n\n";
    //print_r($newArray);
    return $inputArray;
}

function parseToml($tomlData) {
    // one large call to all the functions.
    $tomlData=str_replace("\r", '', $tomlData);
    //error_log(json_encode($tomlData, JSON_PRETTY_PRINT));
    return p5(p4(p3(p2(p1($tomlData)))));
}

// test.toml is a mods.toml pulled from a mod.jar/META-INF.
// parseToml is called with toml file data as an argument..
// if ($_GET['a']=='test') {
// header('content-type: application/json');
// echo json_encode(parseToml(file_get_contents('../test.toml')), JSON_UNESCAPED_SLASHES);
// }

?>