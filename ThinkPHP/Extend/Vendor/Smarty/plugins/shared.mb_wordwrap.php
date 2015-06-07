<?php
/**
 * Smarty shared plugin
 *
 * @package Smarty
 * @subpackage PluginsShared
 */

if(!function_exists('smarty_mb_wordwrap')) {

    /**
     * Wrap a string to a given number of characters
     *
     * @link http://php.net/manual/en/function.wordwrap.php for similarity
     * @param string  $str   the string to wrap
     * @param int     $width the width of the output
     * @param string  $break the character used to break the line
     * @param boolean $cut   ignored parameter, just for the sake of
     * @return string wrapped string
     * @author Rodney Rehm
     */
    function smarty_mb_wordwrap($str, $width=75, $break="\n", $cut=false)
    {
        // break words into tokens using white space as a delimiter
        $tokens = preg_split('!(\s)!uS', $str, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
        $length = 0;
        $t = '';
        $_previous = false;

        foreach ($tokens as $_token) {
            $token_length = mb_strlen($_token, SMARTY_RESOURCE_CHAR_SET);
            $_tokens = array($_token);
            if ($token_length > $width) {
                // remove last space
                $t = mb_substr($t, 0, -1, SMARTY_RESOURCE_CHAR_SET);
                $_previous = false;
                $length = 0;

                if ($cut) {
                    $_tokens = preg_split('!(.{' . $width . '})!uS', $_token, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
             