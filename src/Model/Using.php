<?php

namespace Triangle\WebAnalyzer\Model;

use Triangle\WebAnalyzer\Model\Primitive\NameVersion;

class Using extends NameVersion
{
    /**
     * Get an array of all defined properties
     *
     * @return array
     * @internal
     *
     */

    public function toArray()
    {
        $result = [];

        if (!empty($this->name) && empty($this->version)) {
            return $this->name;
        }

        if (!empty($this->name)) {
            $result['name'] = $this->name;
        }

        if (!empty($this->version)) {
            $result['version'] = $this->version->toArray();
        }

        return $result;
    }
}
