<?php
// update_languages.php 1.0 - Copyright (c) 2018, Hellsh Ltd. - https://github.com/hellshltd/L10nUtils

$translation_folder = "lang/";
$primary_language = "en";

echo "[".date("r")."] Reading {$primary_language}.txt...\n";
$format = file($translation_folder.$primary_language.".txt");
$translations = false;
if(file_exists($translation_folder."translations.json"))
{
	$translations = json_decode(file_get_contents($translation_folder."translations.json"), true);
}
foreach(scandir($translation_folder) as $file)
{
	if(!is_dir($translation_folder.$file)&&substr($file,-4)==".txt"&&$file!=$primary_language.".txt")
	{
		$strings = [];
		echo "[".date("r")."] Processing ".$file."...\n";
		foreach(file($translation_folder.$file) as $line)
		{
			$arr = explode("=", str_replace("\n", "", str_replace("\r", "", $line)));
			if(count($arr) == 2)
			{
				$strings[$arr[0]] = $arr[1];
			}
		}
		$cont = $format;
		$total_strings = 0;
		$translated = 0;
		foreach($cont as $i => $line)
		{
			$arr = explode("=", str_replace("\n", "", str_replace("\r", "", $line)));
			if(count($arr) == 2)
			{
				$total_strings++;
				if(!empty($strings[$arr[0]]))
				{
					$cont[$i] = $arr[0]."=".$strings[$arr[0]]."\n";
					$translated++;
				}
				else
				{
					$cont[$i] = "# ".$arr[0]."=".$arr[1]."\n";
				}
			}
		}
		if($file != "new.txt")
		{
			$translations[substr($file,0,-4)]["translated"] = (round(100/$total_strings*$translated*10)/10);
		}
		file_put_contents($translation_folder.$file, join("", $cont));
	}
}
if($translations !== false)
{
	file_put_contents($translation_folder."translations.json", json_encode($translations, JSON_PRETTY_PRINT));
}
