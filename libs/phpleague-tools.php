<?php

/*
 * This file is part of the PHPLeague package.
 *
 * (c) Maxime Dizerens <mdizerens@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! class_exists('PHPLeague_Tools')) {

    /**
     * Tools library.
     *
     * @category   Tools
     * @package    PHPLeague
     * @author     Maxime Dizerens
     * @copyright  (c) 2011 Mikaweb Design, Ltd
     */
    class PHPLeague_Tools {

        /**
         * @var  array  preferred order of attributes
         */
        public static $attribute_order = array
        (
            'action',
            'method',
            'type',
            'id',
            'name',
            'value',
            'href',
            'src',
            'width',
            'height',
            'cols',
            'rows',
            'size',
            'maxlength',
            'rel',
            'media',
            'accept-charset',
            'accept',
            'tabindex',
            'accesskey',
            'alt',
            'title',
            'class',
            'style',
            'selected',
            'checked',
            'readonly',
            'disabled',
        );
        
        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}

        /**
         * Handle pagination.
         *
         * @param   integer total items
         * @param   integer items per page
         * @param   integer current page number
         * @param   string  query string
         * @return  string
         */
        public function pagination($total_items, $items_p_page, $page_number, $query = 'p_nb')
        {
            $nb_pages   = ceil($total_items / $items_p_page);
            $page_links = paginate_links(array
            (
                'base'      => add_query_arg($query, '%#%'),
                'format'    => '',
                'prev_text' => __('&laquo;', 'phpleague'),
                'next_text' => __('&raquo;', 'phpleague'),
                'total'     => $nb_pages,
                'current'   => $page_number
            ));

            return $page_links;
        }
        
        /**
         * Valid text input.
         *
         * @param   string  $str
         * @param   integer $length (optional)
         * @param   array   $valid (optional)
         * @return  boolean
         */
        public function valid_text($str, $length = 3, $valid = array())
        {
            if (mb_strlen($str) < $length)
                return FALSE;

            if (empty($valid))
                $valid = array('-', '_', ' ', '.', 'ç', 'é', 'ë', 'è', 'ê', 'à', 'á', 'ä', 'â', 'ö', 'ò', 'ó', 'ô', 'ü', 'ú', 'û', 'ï', 'í', 'î', 'ì', 'ñ', 'ý', 'ß', 'ÿ');
            
            return (bool) ctype_alnum(str_replace($valid, '', $str));
        }
        
        /**
         * Manage a directory (Create/Delete).
         *
         * @param   string $path
         * @param   string $action
         * @return  mixed
         */
        public static function manage_directory($path = NULL, $action = '')
        {
            if ($action === 'create') {
                if ( ! is_dir($path)) { // Path does not exist
                    // Create the directory
                    mkdir($path, 0755, TRUE);
    
                    // Set permissions (must be manually set to fix umask issues)
                    chmod($path, 0755);
                }
            } elseif ($action === 'delete') {
                if (is_dir($path)) { // Path exists
                    $dir_handle = opendir($path);
                    if ( ! $dir_handle)
                        return FALSE;
                    
                    while ($file = readdir($dir_handle)) {
                        if ($file != '.' && $file != '..') {
                            if ( ! is_dir($path.'/'.$file))
                                unlink($path.'/'.$file);
                            else
                                PHPLeague_Tools::manage_directory($path.'/'.$file, 'delete');    
                        }
                    }
                    
                    closedir($dir_handle);
                    rmdir($path);
                    return TRUE;
                }
            }
        }

        /**
         * Return the files in a directory.
         *
         * @param   string path
         * @param   array  extension authorized
         * @return  array
         */
        public function return_dir_files($path = NULL, $extension = array('png'))
        {
            $files = array();
            $list  = array(0 => __('-- Select an image --', 'phpleague'));
            
            if ( ! is_dir($path))
                return $list;
            
            $path = opendir($path);

            while ((FALSE !== $file = readdir($path))) {
                if (in_array(substr($file, -3), $extension))
                    $files[] = trim($file);
            }

            closedir($path);
            sort($files);
            $c = count($files);

            for ($i = 0; $i < $c; $i++) {
                $list[$files[$i]] = $files[$i];
            }

            return $list;
        }

        /**
         * Alternates between two or more strings.
         *
         * Note that using multiple iterations of different strings may produce
         * unexpected results.
         *
         * @param   string  strings to alternate between
         * @return  string
         */
        public function alternate()
        {
            static $i;

            if (func_num_args() === 0) {
                $i = 0;
                return '';
            }

            $args = func_get_args();
            return $args[($i++ % count($args))];
        }
        
        /**
         * Compiles an array of HTML attributes into an attribute string.
         *
         * @param   array  $attributes
         * @return  string
         */
        public static function attributes(array $attributes = NULL)
        {
            if (empty($attributes))
                return '';

            $sorted = array();
            foreach (PHPLeague_Tools::$attribute_order as $key) {
                if (isset($attributes[$key])) {
                    // Add the attribute to the sorted list
                    $sorted[$key] = $attributes[$key];
                }
            }

            // Combine the sorted attributes
            $attributes = $sorted + $attributes;

            $compiled = '';
            foreach ($attributes as $key => $value) {
                if ($value === NULL)
                    continue;

                if (is_int($key))
                    $key = $value;

                // Add the attribute value
                $compiled .= ' '.$key.'="'.esc_html($value).'"';
            }

            return $compiled;
        }
        
        /**
         * Creates a form input. If no type is specified, a "text" type input will
         * be returned.
         *
         * @param   string  input name
         * @param   string  input value
         * @param   array   html attributes
         * @return  string
         */
        public function input($name, $value = NULL, array $attributes = NULL)
        {
            // Set the input name
            $attributes['name'] = $name;

            // Set the input value
            $attributes['value'] = $value;

            if ( ! isset($attributes['type']))
                $attributes['type'] = 'text';

            return '<input'.PHPLeague_Tools::attributes($attributes).' />';
        }
        
        /**
         * Creates a textarea form input.
         *
         * @param   string   textarea name
         * @param   string   textarea body
         * @param   array    html attributes
         * @return  string
         */
        public function textarea($name, $body = '', array $attributes = NULL)
        {
            // Set the input name
            $attributes['name'] = $name;

            // Add default rows and cols attributes (required)
            $attributes += array('rows' => 10, 'cols' => 50);

            return '<textarea'.PHPLeague_Tools::attributes($attributes).'>'.esc_html($body).'</textarea>';
        }

        /**
         * Generates an opening HTML form tag.
         *
         * @param   string  form action
         * @param   array   html attributes
         * @return  string
         */
        public function form_open($action = NULL, array $attributes = NULL)
        {
            if ($action === NULL)
                return wp_die(__('An error occurred! I need to fix this later...', 'phpleague'));

            // Add the form action to the attributes
            $attributes['action'] = $action;

            // Only accept the default character set
            $attributes['accept-charset'] = 'UTF-8';

            if ( ! isset($attributes['method']))
                $attributes['method'] = 'post';

            return '<form'.PHPLeague_Tools::attributes($attributes).'>';
        }

        /**
         * Creates the closing form tag.
         *
         * @param  none
         * @return string
         */
        public function form_close()
        {
            $nonce = '';
            if (is_admin())
                $nonce = wp_nonce_field('phpleague');
            
            return $nonce.'</form>';
        }
        
        /**
         * Creates a select form input.
         *
         * @param   string   input name
         * @param   array    available options
         * @param   mixed    selected option string, or an array of selected options
         * @param   array    html attributes
         * @return  string
         */
        public function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
        {
            // Set the input name
            $attributes['name'] = $name;

            if (is_array($selected))
                $attributes['multiple'] = 'multiple';

            if ( ! is_array($selected)) {
                if ($selected === NULL)
                    $selected = array();
                else
                    $selected = array((string) $selected);
            }

            if (empty($options)) {
                // There are no options
                $options = '';
            }
            else {
                foreach ($options as $value => $name) {
                    if (is_array($name)) {
                        // Create a new optgroup
                        $group = array('label' => $value);

                        // Create a new list of options
                        $_options = array();

                        foreach ($name as $_value => $_name) {
                            // Force value to be string
                            $_value = (string) $_value;

                            // Create a new attribute set for this option
                            $option = array('value' => $_value);

                            if (in_array($_value, $selected))
                                $option['selected'] = 'selected';

                            // Change the option to the HTML string
                            $_options[] = '<option'.PHPLeague_Tools::attributes($option).'>'.esc_html($_name).'</option>';
                        }

                        // Compile the options into a string
                        $_options = "\n".implode("\n", $_options)."\n";

                        $options[$value] = '<optgroup'.PHPLeague_Tools::attributes($group).'>'.$_options.'</optgroup>';
                    } else {
                        // Force value to be string
                        $value = (string) $value;

                        // Create a new attribute set for this option
                        $option = array('value' => $value);

                        if (in_array($value, $selected))
                            $option['selected'] = 'selected';

                        // Change the option to the HTML string
                        $options[$value] = '<option'.PHPLeague_Tools::attributes($option).'>'.esc_html($name).'</option>';
                    }
                }

                // Compile the options into a single string
                $options = "\n".implode("\n", $options)."\n";
            }

            return '<select'.PHPLeague_Tools::attributes($attributes).'>'.$options.'</select>';
        }
    }
}