 <?php
    class BIC_varLangReplace extends \Rain\Tpl\Plugin
    {
        // hooks
        protected $hooks = array('afterDraw');

        // text that replace the image
        static public $replacement = array();

        /**
        * Function called by the filter beforeParse
        **/
        public function afterDraw(\ArrayAccess $context){
            // get the html
            $html = $context->code;

            // remove the image and set the $context->code
            $context->code = preg_replace_callback(
                '/{\$(.*?)}/i',
                function($matches) {

                    $arr = explode('.', $matches[1]);
                    if(count($arr) > 0) {
                        $output = &BIC_varLangReplace::$replacement;
                        foreach($arr as $key)
                            $output = &$output[$key];
                    }

                    return $output;
                },
                $html
            );

        }

        public static function getReplacement($getReplacement = array()) {
            return self::$replacement[$getReplacement];
        }

        public static function setReplacements($getReplacement = array()) {
            self::$replacement = $getReplacement;
        }

    }
?>