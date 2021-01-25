<?php
/*
 script om ontruimingstijden uit pdump te vertalen naar ccol format
 Gemeente Den Haag, Jasper Vries, 2020
*/

//check if cli
function detect_cli() {
	if (php_sapi_name() === 'cli') {
        return TRUE;
    }
	if (defined('STDIN')) {
        return TRUE;
    }
	if (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
        return TRUE;
    }
    return false;
}
if (detect_cli() !== TRUE) {
	echo 'Kan alleen uitvoeren vanaf opdrachtregel';
	exit;
}

//usage
function list_help() {
    echo PHP_EOL;
    echo PHP_EOL; 
    echo 'script om ontruimingstijden uit pdump te vertalen naar ccol format' . PHP_EOL;
    echo 'Gemeente Den Haag, Jasper Vries, 2020' . PHP_EOL;
    echo PHP_EOL;
    echo 'Gebruik:' . PHP_EOL;
    echo 'php pdump2ccol.php <inputfile>' . PHP_EOL;
    echo '<inputfile>: bestand met pdump-informatie' . PHP_EOL;
    echo 'uitvoer wordt geschreven naar <inputfile>.ccol';
}

//check if help
if (in_array($argv[1], array('/?', '-h', '--help'))) {
    list_help();
	exit;
}

//check if inputfile is a valid file
if (!is_file($argv[1])) {
    echo 'Geen invoerbestand opgegeven';
    list_help();
	exit;
}

//open file
$pdump = file_get_contents($argv[1]);
$matches = array();
$result = preg_match_all('/(TO) (\d+) (\d+): (-?)(\d+)(\/te)/i', $pdump, $matches, PREG_SET_ORDER);

if (empty($matches[0])) {
    echo 'Geen ontruimingstijden gevonden in bestand';
	exit;
}

$output = '   /* ontruimingstijden */';
$prevfc = null;
foreach ($matches as $match) {
    if ($match[6] == '/te') {
        if (($prevfc != null) && ($prevfc != $match[2])) {
            $output .= PHP_EOL;
        }
        $output .= PHP_EOL . '   ' . $match[1] . '_max[fc' . $match[2] . '][fc' . $match[3] . ']= ' . $match[5] .';';
        $prevfc = $match[2];
    }
}

//write file
echo $output;
$outfile = $argv[1] . '.ccol';
file_put_contents($outfile, $output);
echo PHP_EOL . PHP_EOL . 'Uitvoer geschreven naar ' . $outfile;
?>