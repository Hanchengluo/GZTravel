<?php
/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {html_radios} function plugin
 * 
 * File:       function.html_radios.php<br>
 * Type:       function<br>
 * Name:       html_radios<br>
 * Date:       24.Feb.2003<br>
 * Purpose:    Prints out a list of radio input types<br>
 * Params:
 * <pre>
 * - name       (optional) - string default "radio"
 * - values     (required) - array
 * - options    (required) - associative array
 * - checked    (optional) - array default not set
 * - separator  (optional) - ie <br> or &nbsp;
 * - output     (optional) - the output next to each radio button
 * - assign     (optional) - assign the output as an array to this variable
 * - escape     (optional) - escape the content (not value), defaults to true
 * </pre>
 * Examples:
 * <pre>
 * {html_radios values=$ids output=$names}
 * {html_radios values=$ids name='box' separator='<br>' output=$names}
 * {html_radios values=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * 
 * @link http://smarty.php.net/manual/en/language.function.html.radios.php {html_radios}
 *      (Smarty online manual)
 * @author Christopher Kvarme <christopher.kvarme@flashjab.com> 
 * @author credits to Monte Ohrt <monte at ohrt dot com> 
 * @version 1.0
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string 
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_radios($params, $template)
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

    $name = 'radio';
    $values = null;
    $options = null;
    $selected = null;
    $separator = '';
    $escape = true;
    $labels = true;
    $label_ids = false;
    $output = null;
    $extra = '';

    foreach($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'separator':
                $$_key = (string) $_val;
                break;

            case 'checked':
            case 'selected':
                if (is_array($_val)) {
                    trigger_error('html_radios: the "' . $_key . '" attribute cannot be an array', E_USER_WARNING);
                } elseif (is_object($_val)) {
                    if (method_exists($_val, "__toString")) {
                        $selected = smarty_function_escape_special_chars((string) $_val->__toString());
                    } else {
                        trigger_error("html_radios: selected attribute is an object of class '". get_class($_val) ."' without __toString() method", E_USER_NOTICE);
                    }
                } else {
                    $selected = (string) $_val;
                } 
                break;

            case 'escape':
            case 'labels':
            case 'label_ids':
                $$_key = (bool) 