<?php
	/**
	 * bdTemplateRain 	
	 * @version 2.0
	 *	Last edit 05-03-2014 by Barry
	 */

	/**
	 * 	maakt gebruik van Rain.TPL versie 3 READ > https://github.com/rainphp/raintpl3
	 *	
	 *	Ik heb dit gemaakt zodat de settings afhankelijk zijn van elke template op zichzelf
	 *	rainTPL gebruik maar 1 template + cache dir voor alles!.. alle tpl files zouden dus maar in 1 en dezelfde map kunnen staan.. niet handig dus
	 *
	 *	op de locatie van de template wordt een map cache aangemaakt.
	 *	
	 *	Er zijn 2 manieren waarop je render kan maken  :
	 *
	 *
	 *	manier 1 :
	 *	$tpl = new bdTemplateRain(FILE_PATH.'locatie/van/de/template.tpl');
	 *	$tpl->assign('test','voorbeeld') of $tpl->assign(array('test'=>'voorbeeld','test2'=>'voorbeeld2',)); // functie van RainTPL zelf
	 *	$render = $tpl->render(); // gebruikt RaintTPL->draw functie en geeft de gerenderde template terug ;
	 *	echo $render ; of doe er iets anders mee
	 *	
	 *	manier 2 :
	 *	$render = bdTemplateRain::render(FILE_PATH.'locatie/van/de/template.tpl',array('test'=>'voorbeeld'));
	 *	echo $render;
	 *
	 *	templates opbouwen : https://github.com/rainphp/raintpl3/wiki
	 *
	 */
	final class bdTemplateRain extends \Rain\Tpl {

		private $bdTemplateRainData = array( // all set in __construct function
			'strRender'				=> false,
			'strFileLoc'			=> false,
			'strFileName'			=> false,
		);

		public function __construct($getTemplateLoc=false){
			if(!$getTemplateLoc){
				bdMessage::error('No template passed!');
			}else{
				$file = strtolower($getTemplateLoc);
				if(!file_exists($file)){
					bdMessage::error('File <strong>'.$file.'</strong> does not exist!');
				}else{
					$this->bdTemplateRainData['strRender']		= file_get_contents($file);
					$this->bdTemplateRainData['strFileLoc'] 	= $getTemplateLoc;
					$this->bdTemplateRainData['strFileName'] 	= basename( str_replace("\\", "/", $getTemplateLoc) );
				}
				$this->setRainTPLConfig();			
			}
		}

		/* the raintpl functions will be set according to the template file */
		private function setRainTPLConfig(){
			$strTemplateBaseName 	=	$this->bdTemplateRainData['strFileName'];
			$strTemplateDir 		= 	str_replace($strTemplateBaseName, '', $this->bdTemplateRainData['strFileLoc']);
			self::configure( 'tpl_dir', $strTemplateDir );
			self::configure( 'base_url', URL_BASE );
			self::configure( 'tpl_ext', 'tpl' );
			self::configure( 'cache_dir', $strTemplateDir.'cache/' );
			self::configure( 'auto_escape', false );
		}

		
		/**
		*	@return (string) $strRender rendered template
		**/
		public function render(){
			$strRender = '';
			/* non static */
				if(isset($this) && get_class($this) === 'bdTemplateRain' ){
					/* RainTPL draw function first param is the template name without extension */
						$strTemplateName 	= str_replace('.tpl','',$this->bdTemplateRainData['strFileName'] );
					/* return the rendered template (don't echo it here) */
					
					$strRender =  $this->draw($strTemplateName, $return_string = true);	
					/* SRV DEBUG */
						if (
							( in_array($_SERVER['REMOTE_ADDR'],array('80.101.92.145','84.30.155.204','84.30.159.111')) )
							|| $_SERVER['HTTP_HOST'] == 'srv'
							){
							$strFile = str_replace(FILE_PATH,'',$this->bdTemplateRainData['strFileLoc']);
							$strRender = '
								<!-- Start template '.$strFile.'  -->
								'.$strRender.'
								<!-- End template '.$strFile.'-->
							';
						}
					/* SRV DEBUG END */
				}
			/* static approach */
				else{
					$arguments = func_get_args(); // $arguments[0] = template file loc $arguments[1] = assigns
					if(!$arguments || empty($arguments[0])) return false;
					$tpl = new bdTemplateRain($arguments[0]);
					/* assigns */
						if(!empty($arguments[1])) {
							/**
							 * Rain TPL v3 drops support of $template_info.. so lets bring it back :D
							 */
							$arrTemplateData = $arguments[1];
							$arrTemplateData['template_info'] = 'Template Info:<pre>'.print_r($arrTemplateData, true).'</pre>';
							/* Assign render data */
							$tpl->assign($arrTemplateData);
						} 
					/* return the render*/
					$strRender = $tpl->render();
				}
			/* return the render */	
			return $strRender;						
		}

	}

	class bdMessage{
		public function __construct($getType=false,$getMessage=false,$boolUseParent=false){
			if($getType === 'error'){
				$this->error($getMessage,$boolUseParent);
			}
		}
		public static function error($strMessage=false,$boolUseParent=false){
			$arrDebugBackTrace		= debug_backtrace();
			if($boolUseParent && count($arrDebugBackTrace) > 1){
				$arrFileThrowingError = $arrDebugBackTrace[1];
				$strMessage 			.= ' of CLASS <strong>'.$arrFileThrowingError['class'].'</strong>';
			}else{
				$arrFileThrowingError 	= array_shift($arrDebugBackTrace);
			}
			
			$strFileName			= str_replace(FILE_PATH,'',$arrFileThrowingError['file']);
			$intLine				= $arrFileThrowingError['line'];
			echo 'BD ERROR: '.$strMessage.' in <strong>'.$strFileName.'</strong> on line <strong>'.$intLine.'</strong>';
			echo '<pre>'.print_r($arrDebugBackTrace,true).'</pre>';
			exit();
		}
	};
?>