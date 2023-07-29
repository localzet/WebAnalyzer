<?php

namespace Triangle\WebAnalyzer;

use Psr\Cache\InvalidArgumentException;
use Triangle\WebAnalyzer\Model\Main;

class Parser extends Main
{
    use Cache;

    /**
     * Create a new object that contains all the detected information
     *
     * @param array $options Optional, an array with configuration options
     * @throws InvalidArgumentException
     */

    public function __construct(array $options = [])
    {
        parent::__construct();
        $this->analyse($options);
    }

    /**
     * Analyse the provided headers or User-Agent string
     *
     * @throws InvalidArgumentException
     */

    public function analyse($options = []): void
    {
        $headers = request()->header();

        if ($this->analyseWithCache($headers, $options)) {
            return;
        }

        $analyser = new Analyser($headers, $options);
        $analyser->setdata($this);
        $analyser->analyse();
    }
}
