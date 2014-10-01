 <?php
    /**
     * Parse vars in lang files
     */
    class BIC_varLangReplace extends \Rain\Tpl\Plugin
    {
        // hooks
        protected $hooks = array('beforeParse', 'afterDraw');

        // replacements
        static public $replacement = array();

        /**
        * Function called by the filter afterDraw
        **/
        public function afterDraw(\ArrayAccess $context){
            // get the html
            $html = $context->code;

            // vervang variables die in een language file staan
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

            
            // vervang variables in tpl $LANG.example.test
            $context->code = preg_replace_callback(
                 '/<!--#BIC_VARLANG_REPLACE#(.*?)#BIC_VARLANG_REPLACE#-->/i',
                 function($arrMatches) {
                     global $LANG;
                     $arr = explode('.', $arrMatches[1]);
                     if (count($arr) > 0) {
                         $arrSearch = $LANG;
                         foreach ($arr as $key) {
                             if (! isset($arrSearch[$key])) 
                                 return;
                             $arrSearch = $arrSearch[$key];
                         }
                         return $arrSearch;  
                     }                         
                 },
                 $context->code
            );
            

        }

        /**
         * beforeParse
         * Example : replaces $LANG.example.test to <!--#BIC_VARLANG_REPLACE#example.test#BIC_VARLANG_REPLACE#-->
         * so that we can process it later in afterDraw method
         */
        public function beforeParse(\ArrayAccess $context)
        {
            $html = $context->code;  
            $context->code = preg_replace_callback(
                '/{\$LANG.(.*?)}/i',
                function($arrMatches) {
                    global $LANG;
                    $arr = explode('.', $arrMatches[1]);
                    if (count($arr) > 0) {
                        $arrSearch = $LANG;
                        foreach ($arr as $key) {
                            if (! isset($arrSearch[$key])) 
                                return;
                            $arrSearch = $arrSearch[$key];
                        }
                        return '<!--#BIC_VARLANG_REPLACE#'.$arrMatches[1].'#BIC_VARLANG_REPLACE#-->';                            
                    }                         
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