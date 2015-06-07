<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {fetch} plugin
 *
 * Type:     function<br>
 * Name:     fetch<br>
 * Purpose:  fetch file, web or ftp data and display results
 *
 * @link http://www.smarty.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string|null if the assign parameter is passed, Smarty assigns the result to a template variable
 */
function smarty_function_fetch($params, $template)
{
    if (empty($params['file'])) {
        trigger_error("[plugin] fetch parameter 'file' cannot be empty",E_USER_NOTICE);
        return;
    }

    $content = '';
    if (isset($template->smarty->security_policy) && !preg_match('!^(http|ftp)://!i', $params['file'])) {
        if(!$template->smarty->security_policy->isTrustedResourceDir($params['file'])) {
            return;
        }

        // fetch the file
        if($fp = @fopen($params['file'],'r')) {
            while(!feof($fp)) {
                $content .= fgets ($fp,4096);
            }
            fclose($fp);
        } else {
            trigger_error('[plugin] fetch cannot read file \'' . $params['file'] . '\'',E_USER_NOTICE);
            return;
        }
    } else {
        // not a local file
        if(preg_match('!^http://!i',$params['file'])) {
            // http fetch
            if($uri_parts = parse_url($params['file'])) {
                // set defaults
                $host = $server_name = $uri_parts['host'];
                $timeout = 30;
                $accept = "image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*";
                $agent = "Smarty Template Engine ". Smarty::SMARTY_VERSION;
                $referer = "";
                $uri = !empty($uri_parts['path']) ? $uri_parts['path'] : '/';
                $uri .= !empty($uri_parts['query']) ? '?' . $uri_parts['query'] : '';
                $_is_proxy = false;
                if(empty($uri_parts['port'])) {
                    $port = 80;
                } else {
                    $port = $uri_parts['port'];
                }
                if(!empty($uri_parts['user'])) {
                    $user = $uri_parts['user'];
                }
                if(!empty($uri_parts['pass'])) {
                    $pass = $uri_parts['pass'];
                }
                // loop through parameters, setup headers
                foreach($params as $param_key => $param_value) {
                    switch($param_key) {
                        case "file":
                        case "assign":
                        case "assign_headers":
                            break;
                        case "user":
                            if(!empty($param_value)) {
                                $user = $param_value;
                            }
                            break;
                        case "pass":
                            if(!empty($param_value)) {
                                $pass = $param_value;
                            }
                            break;
                        case "accept":
                            if(!empty($param_value)) {
                                $accept = $param_value;
                            }
                            break;
                        case "header":
                            if(!empty($param_value)) {
                                if(!preg_match('![\w\d-]+: .+!',$param_value)) {
                                    trigger_error("[plugin] invalid header format '".$param_value."'",E_USER_NOTICE);
                                    return;
                                } else {
                                    $extra_headers[] = $param_value;
                                }
                            }
                            break;
                        case "proxy_host":
                            if(!empty($param_value)) {
                                $proxy_host = $param_value;
                            }
                            break;
                        case "proxy_port":
                            if(!preg_match('!\D!', $pa