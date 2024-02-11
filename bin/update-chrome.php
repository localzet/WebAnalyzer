<?php

include_once __DIR__ . '/bootstrap.php';
include __DIR__ . '/../data/browsers-chrome.php';

echo "Updating chrome versions...\n";

$stable = [
    'desktop' => [],
    'mobile' => []
];


$omaha = explode("\n", file_get_contents("http://omahaproxy.appspot.com/history"));
foreach ($omaha as $i => $line) {
    $items = explode(",", $line);

    if ($items[0] == 'mac' && $items[1] == 'stable') {
        $stable['desktop'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
    }

    if ($items[0] == 'android' && $items[1] == 'stable') {
        $stable['mobile'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
    }
}

$stable['desktop'] = array_unique($stable['desktop']);
$stable['mobile'] = array_unique($stable['mobile']);

sort($stable['desktop']);
sort($stable['mobile']);


foreach ($stable['desktop'] as $i => $version) {
    if (!isset(localzet\WebAnalyzer\Data\Chrome::$DESKTOP[$version])) {
        localzet\WebAnalyzer\Data\Chrome::$DESKTOP[$version] = 'stable';
    }
}

foreach ($stable['mobile'] as $i => $version) {
    if (!isset(localzet\WebAnalyzer\Data\Chrome::$MOBILE[$version])) {
        localzet\WebAnalyzer\Data\Chrome::$MOBILE[$version] = 'stable';
    }
}


$result = "<?php\n\n";
$result .= "namespace localzet\WebAnalyzer\Data;\n\n";
$result .= "Chrome::\$DESKTOP = [\n";
foreach (localzet\WebAnalyzer\Data\Chrome::$DESKTOP as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n\n";
$result .= "Chrome::\$MOBILE = [\n";
foreach (localzet\WebAnalyzer\Data\Chrome::$MOBILE as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n";


file_put_contents(__DIR__ . '/../data/browsers-chrome.php', $result);
