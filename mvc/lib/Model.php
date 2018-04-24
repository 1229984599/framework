<?php
namespace lib;
use Medoo\Medoo;
class Model extends Medoo
{
    public static $conf = null;
    public $medoo;
    public function __construct($options = null)
    {
        if (!self::$conf){
            self::$conf = include BASE_PATH . '/config/databases.php';
        }
        parent::__construct(self::$conf);
    }
}