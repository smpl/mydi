<?php

namespace Smpl\Mydi\Test\Example;

class UserMysqlPDO
{
    public $pdo;

    /**
     * UserMysqlPDO constructor.
     * @param CustomPDO $magic
     * @inject \Smpl\Mydi\Test\Example\MysqlPdo $magic
     */
    public function __construct(CustomPDO $magic)
    {
        $this->pdo = $magic;
    }

}
