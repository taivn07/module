<?php

use Phalcon\Mvc\User\Component;

/**
 * Description of CommonComponent
 *
 * @author TAIMT
 */
class CommonComponent extends Component {
    /**
     * generate random string with length = 10
     * @param  integer $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}

?>
