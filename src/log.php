<?php
namespace fge\nucg\src;
class log{
    public static function escribe($origen,$e){
        error_log(
            sprintf("\n%s\n%s\n%s\n",
            $origen,$e->getMessage(),$e->getTraceAsString())
        );
    }
}