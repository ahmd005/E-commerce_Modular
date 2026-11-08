<?php

namespace App\Support;

class DbUsage
{
    public array $reads = [];
    public array $writes = [];

    public function summary(): array
    {
        return [
            'read_from'  => array_values(array_unique($this->reads)),
            'write_to'   => array_values(array_unique($this->writes)),
            'read_count' => count($this->reads),
            'write_count'=> count($this->writes),
        ];
    }
}