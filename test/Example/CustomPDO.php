<?php

namespace Smpl\Mydi\Test\Example;

class CustomPDO
{
    public $dsn;
    public $username;
    public $password;
    public $options;

    public function __construct(string $db_dsn, string $db_username, string $db_password, array $db_options)
    {
        $this->dsn = $db_dsn;
        $this->username = $db_username;
        $this->password = $db_password;
        $this->options = $db_options;
    }
}
