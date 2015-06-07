<?php
/**
 * Smarty Internal Plugin Smarty Template  Base
 *
 * This file contains the basic shared methodes for template handling
 *
 * @package Smarty
 * @subpackage Template
 * @author Uwe Tews
 */

/**
 * Class with shared template methodes
 *
 * @package Smarty
 * @subpackage Template
 */
abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data {

    /**
     * fetches a rendered Smarty template
     *
     * @param string $template          the resource handle of the template file or template object
     * @param mixed  $cache_id          cache id to be used with this template
     * @param mixed  $compile_id        compile id to be used with this template
     * @param object $parent            next higher level of Smarty variables
     * @param bool   $display           true: display, false: fetch
     * @param bool   $merge_tpl_vars    if true parent template variables merged in to local scope
     * @param bool   $no_output_filter  if true do not run output filter
     * @return string rendered template output
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($template === null && $this instanceof $this->template_class) {
            $template = $this;
        }
        if (!empty($cache_id) && is_object($cache_id)) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if ($parent === null && ($this instanceof Smarty || is_string($template))) {
            $parent = $this;
        }
        // create template object if necessary
        $_template = ($template instanceof $this->template_class)
            ? $template
            : $this->smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
        // if called by Smarty object make sure we use current caching status
        if ($this instanceof Smarty) {
            $_template->caching = $this->caching;
        }
        // merge all variable scopes into template
        if ($merge_tpl_vars) {
            // save local variables
            $save_tpl_vars = $_template->tpl_vars;
            $save_config_vars = $_template->config_vars;
            $ptr_array = array($_template);
            $ptr = $_template;
            while (isset($ptr->parent)) {
                $ptr_array[] = $ptr = $ptr->parent;
            }
            $ptr_array = array_reverse($ptr_array);
            $parent_ptr = reset($ptr_array);
            $tpl_vars = $parent_ptr->tpl_vars;
            $config_vars = $parent_ptr->config_vars;
            while ($parent_ptr = next($ptr_array)) {
                if (!empty($parent_ptr->tpl_vars)) {
                    $tpl_vars = array_merge($tpl_vars, $parent_ptr->tpl_vars);
                }
                if (!empty($parent_ptr->config_vars)) {
                    $config_vars = array_merge($config_vars, $parent_ptr->config_vars);
                }
            }
            if (!empty(Smarty::$global_tpl_vars)) {
                $tpl_vars = array_merge(Smarty::$global_tpl_vars, $tpl_vars);
            }
            $_template->tpl_vars = $tpl_vars;
            $_template->config_vars = $config_vars;
        }
        // dummy local smarty variable
        if (!isset($_template->tpl_vars['smarty'])) {
            $_template->tpl_vars['smarty'] = new Smarty_Variable;
        }
        if (isset($this->smarty->error_reporting)) {
            $_smarty_old_error_level = error_reporting($this->smarty->error_reporting);
        }
        // check URL debugging control
        if (!$this->smarty->debugging && $this->smarty->debugging_ctrl == 'URL') {
            if (isset($_SERVER['QUERY_STRING'])) {
                $_query_string = $_SERVER['QUERY_STRING'];
            } else {
                $_query_string = '';
            }
            if (false !== strpos($_query_string, $this->smarty->smarty_debug_id)) {
                if (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=on')) {
                    // enable debugging for this browser session
                    setcookie('SMARTY_DEBUG', true);
                    $this->smarty->debugging = true;
                } elseif (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=off')) {
                    // disable debugging for this browser session
                    setcookie('SMARTY_DEBUG', false);
                    $this->smarty->debugging = false;
                } else {
                    // enable debugging for this page
                    $this->smarty->debugging = true;
                }
            } else {
                if (isset($_COOKIE['SMARTY_DEBUG'])) {
                    $this->smarty->debugging = true;
                }
            }
        }
        // must reset merge template date
        $_template->smarty->merged_templates_func = array();
        // get rendered template
        // disable caching for evaluated code
        if ($_template->source->recompiled) {
            $_template->caching = false;
        }
        // checks if template exists
        if (!$_template->source->exists) {
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $parent_resource = " in '{$_template->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$_template->source->type} '{$_template->source->name}'{$parent_resource}");
        }
        // read from cache or render
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || !$_template->cached->valid) {
            // render template (not loaded and not in cache)
            if (!$_template->source->uncompiled) {
                $_smarty_tpl = $_template;
                if ($_template->source->recompiled) {
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_compile($_template);
                    }
                    $code = $_template->compiler->compileTemplate($_template);
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::end_compile($_template);
                    }
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        eval("?>" . $code);
                        unset($code);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    if (!$_template->compiled->exists || ($_template->smarty->force_compile && !$_template->compiled->isCompiled)) {
                        $_template->compileTemplateSource();
                    }
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    if (!$_template->compiled->loaded) {
                        include($_template->compiled->filepath);
                        if ($_template->mustCompile) {
                            // recompile and load again
                            $_template->compileTemplateSource();
                            include($_template->compiled->filepath);
                        }
                        $_template->compiled->loaded = true;
                    } else {
                        $_template->decodeProperties($_template->compiled->_properties, false);
                    }
                    try {
                        ob_start();
                        if (empty($_template->properties['unifunc']) || !is_callable($_template->properties['unifunc'])) {
                            throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
                        }
                        $_template->properties['unifunc']($_template);
                        if (isset($_template->_capture_stack[0])) {
                            $_template->capture_error();
                        }
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                }
            } else {
                if ($_template->source->uncompiled) {
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        $_template->source->renderUncompiled($_template);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    throw new SmartyException("Resource '$_template->source->type' must have 'renderUncompiled' method");
                }
            }
            $_output = ob_get_clean();
            if (!$_template->source->recompiled && empty($_template->properties['file_dependency'][$_template->source->uid])) {
                $_template->properties['file_dependency'][$_template->source->uid] = array($_template->source->filepath, $_template->source->timestamp, $_template->source->type);
            }
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $_template->parent->properties['file_dependency'] = array_merge($_template->parent->properties['file_dependency'], $_template->properties['file_dependency']);
                foreach ($_template->required_plugins as $code => $tmp1) {
                    foreach ($tmp1 as $name => $tmp) {
                        foreach ($tmp as $type => $data) {
                            $_template->parent->required_plugins[$code][$name][$type] = $data;
                        }
                    }
                }
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_render($_template);
            }
            // write to cache when nessecary
            if (!$_template->source->recompiled && ($_template->caching == Smarty::CACHING_LIFETIME_SAVED || $_template->caching == Smarty::CACHING_LIFETIME_CURRENT)) {
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::start_cache($_template);
                }
                $_template->properties['has_nocache_code'] = false;
                // get text between non-cached items
                $cache_split = preg_split("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output);
                // get non-cached items
                preg_match_all("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output, $cache_parts);
                $output = '';
                // loop over items, stitch back together
                foreach ($cache_split as $curr_idx => $curr_split) {
                    // escape PHP tags in template content
                    $output .= preg_replace('/(<%|%>|<\?php|<\?|\?>)/', '<?php echo \'$1\'; ?>', $curr_split);
                    if (isset($cache_parts[0][$curr_idx])) {
                        $_template->properties['has_nocache_code'] = true;
                        // remove nocache tags from cache output
                        $output .= preg_replace("!/\*/?%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!", '', $cache_parts[0][$curr_idx]);
                    }
                }
                if (!$no_output_filter && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
                    $output = Smarty_Internal_Filter_Handler::runFilter('output', $output, $_template);
                }
                // rendering (must be done before writing cache file because of {function} nocache handling)
                $_smarty_tpl = $_template;
                try {
                    ob_start();
                    eval("?>" . $output);
                    $_output = ob_get_clean();
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
                // write cache file content
                $_template->writeCachedContent($output);
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::end_cache($_template);
                }
            } else {
                // var_dump('renderTemplate', $_template->has_nocache_code, $_template->template_resource, $_template->properties['nocache_hash'], $_template->parent->properties['nocache_hash'], $_output);
                if (!empty($_template->properties['nocache_hash']) && !empty($_template->parent->properties['nocache_hash'])) {
                    // replace nocache_hash
                    $_output = preg_replace("/{$_template->properties['nocache_hash']}/", $_template->parent->properties['nocache_hash'], $_output);
                    $_template->parent->has_nocache_code = $_template->parent->has_nocache_code || $_template->has_nocache_code;
                }
            }
        } else {
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::start_cache($_template);
            }
            try {
                ob_start();
                $_template->properties['unifunc']($_template);
                if (isset($_template->_capture_stack[0])) {
                    $_template->capture_error();
                }
                $_output = ob_get_clean();
            } catch (Exception $e) {
                ob_get_clean();
                throw $e;
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_cache($_template);
            }
        }
        if ((!$this->caching || $_template->source->recompiled) && !$no_output_filter && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
            $_output = Smarty_Internal_Filter_Handler::runFilter('output', $_output, $_template);
        }
        if (isset($this->error_reporting)) {
            error_reporting($_smarty_old_error_level);
        }
        // display or fetch
        if ($display) {
            if ($this->caching && $this->cache_modified_check) {
                $_isCached = $_template->isCached() && !$_template->has_nocache_code;
                $_last_modified_date = @substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
                if ($_isCached && $_template->cached->timestamp <= strtotime($_last_modified_date)) {
                    switch (PHP_SAPI) {
                        case 'cgi':         // php-cgi < 5.3
                        case 'cgi-fcgi':    // php-cgi >= 5.3
                        case 'fpm-fcgi':    // php-fpm >= 5.3.3
                            header('Status: 304 Not Modified');
                            break;

                        case 'cli':
                            if (/* ^phpunit */!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])/* phpunit$ */) {
                                $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                            }
                            break;

                        default:
                            header('HTTP/1.1 304 Not Modified');
                            break;
                    }
                } else 