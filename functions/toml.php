<?php
/*
TOML Parser
Super inefficient, but gets the job done.
Limitation: only gets the first dependency. TODO: split dependency.* into it's own array, and stuff the * into their own id within dependency to prevent overwrite.

MIT License, see LICENSE file, with the difference of:
Copyright 2021 Henry Gross-Hellsen

*/


function p1($inputToml) {
	$fileContents=$inputToml;
	$fileArray = explode("[[", $fileContents);
	$c=0;
	
	foreach ($fileArray as $fileElement) {
	    if ($c > 0) {
               $fileArray[$c]='[['.$fileElement;
           }
           $c=$c+1;
       }
	return $fileArray;
}

function p2($inputArray) {
	$lines=array();
	$cc=0;
	foreach ($inputArray as $block) {
		$block=ltrim($block);
		if (empty($block)) {
			unset ($inputArray[$cc]);
			continue;
		}
		$tmp=explode("\n", $block);

		$c=0;
		foreach ($tmp as $line) {
			$line=ltrim($line);
			if (empty($line)) {
				unset($tmp[$c]); 
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
	$c=0;
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
	$keys=array_keys($inputArray);
	$c=0;
	foreach ($inputArray as $block) {
		$keys2=array_keys($block);
		$c2=0;
		foreach ($block as $row) {
			$rowArray=explode('=', $row);
			$left=$rowArray[0];
			$right=end($rowArray);
			
			$left=explode('#', $left)[0];
			$right=explode('#', $right)[0];
			
			$left=trim(trim(str_replace("\t", ' ', $left), ' '), '"');
			$right=trim(trim(str_replace("\t", ' ', $right), ' '), '"');
			
			
			//echo $left.' = "'.$right.'"<br/>';
			unset($inputArray[$keys[$c]][$keys2[$c2]]);
			if (ctype_alpha($left)) {
				if ($row[0] == '#') continue;
				if ($left[0] == '#' || $right[0] == '#') continue;
				$inputArray[$keys[$c]][$left]=$right;
			}
			$c2++;

		}
		$c++;
	}
	return $inputArray;
}

function parseToml($tomlData) {
	return p4(p3(p2(p1($tomlData))));
}

if ($_GET['a']=='test') {
	header('content-type: application/json');
	echo json_encode(parseToml(file_get_contents('../test.toml')), JSON_UNESCAPED_SLASHES);
}
?>