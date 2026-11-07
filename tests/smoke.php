<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

if (!class_exists(\localzet\WebAnalyzer::class)) {
    throw new RuntimeException('WebAnalyzer class cannot be loaded from Composer');
}

$analyzer = new \localzet\WebAnalyzer();
if (!$analyzer->browser instanceof \localzet\WebAnalyzer\Model\Browser) {
    throw new RuntimeException('Browser model is not initialized');
}

if (!$analyzer->isBrowser('Chrome')) {
    // A fresh analyzer with no request headers is not expected to identify a browser.
    echo "Fresh analyzer has no detected browser (expected).\n";
}

echo "WebAnalyzer autoload and model smoke checks: OK\n";
