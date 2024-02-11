<?php

namespace localzet;

use Psr\Cache\InvalidArgumentException;
use localzet\WebAnalyzer\Model\Main;

class WebAnalyzer extends Main
{
    use Cache;

    /**
     * Create a new object that contains all the detected information
     *
     * @param array|null $headers Optional, an array with all the headers or a string with just the User-Agent header
     * @param array $options Optional, an array with configuration options
     * @throws InvalidArgumentException
     */

    public function __construct(?array $headers = null, array $options = [])
    {
        parent::__construct();
        $this->analyse($headers, $options);
    }

    /**
     * Analyse the provided headers or User-Agent string
     *
     * @param array|null $headers An array with all the headers or a string with just the User-Agent header
     * @param array $options
     * @throws InvalidArgumentException
     */

    public function analyse(?array $headers = null, array $options = []): void
    {
        if ($this->analyseWithCache($headers, $options)) {
            return;
        }

        $analyser = new Analyser($headers, $options);
        $analyser->setdata($this);
        $analyser->analyse();
    }
}
