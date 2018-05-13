<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.talkingtoys
 *
 * @copyright   Copyright (C) 2018 SSOFB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Plugin to enable talkingtoys into content (e.g. articles)
 * This uses the {talkingtoy} syntax
 *
 * @since  1.5
 */
class PlgContentTalkingtoys extends JPlugin
{

	/**
	 * Plugin that loads talkingtoys within content
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{

		// Expression to search for (positions)
		#$regex = '/{talkingtoy\s(.*?)talkingtoy}/i';
		
		#$regex = "~{talkingtoy}.*?{/talkingtoy}~is";
		#$regex = '/{talkingtoy\s(.*?){/talkingtoy}/i';
		#$regex = '{talkingtoy.*?{/talkingtoy}';

		#$regex = '/{talkingtoy}.*?{\/talkingtoy}/i';
		#$regex = '/{talkingtoy.*?{\/talkingtoy}/i';
		#$regex = '/{talkingtoy.*?{\/talkingtoy}/i';

		$regex = '/{talkingtoy.*?{\/talkingtoy}/i';
		

		$imagepath = $this->params->def('imagepath', 'media/plg_content_talkingtoys/');

		$default_float = $this->params->def('float', 'right');

		// Find all instances of plugin and put in $matches for talkingtoy
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			
			# insert css
			$document = JFactory::getDocument();
			$style = "
			  .speech_box{
				position: relative;
			  }
			  .speech_box img {
				  margin-top: 30px;
			  }
			  div.speech {
				position: absolute;
				top: 0;
				right: 0;
				width: 200px;
				height: 100px;
				text-align: center;
				line-height: 100px;
				background-color: #fff;
				border: 4px solid #888;
				border-radius: 30px;
			  }
			  div.speech:before {
				content: ' ';
				position: absolute;
				width: 0;
				height: 0;
				left: 24px;
				top: 100px;
				border: 20px solid;
				border-color: #888 transparent transparent #888;
			  }
			  div.speech:after {
				content: ' ';
				position: absolute;
				width: 0;
				height: 0;
				left: 28px;
				top: 100px;
				border: 15px solid;
				border-color: #fff transparent transparent #fff;
			  }
			  
			 
			  "; 
			$document->addStyleDeclaration($style);
			
			foreach ($matches as $match)
			{
				$float = $default_float;

				#echo "<pre>match: " . print_r($match, TRUE) . "</pre>";

				$temp_array = preg_split("/[\{}]+/", $match[0]);
				#echo "<pre>temp_array: " . print_r($temp_array, TRUE) . "</pre>";

				$speech_bubble_text = $temp_array[2];
				#echo "<pre>speech_bubble_text: " . $speech_bubble_text . "</pre>";				

				$settings = explode(" ", $temp_array[1]);
				#echo "<pre>settings: " . print_r($settings, TRUE) . "</pre>";

				$filename = "";

				foreach ($settings AS $setting) {
					if ($setting == "L") {
						$float = "left";
					} elseif ( $settings == "R") {
						$float = "right";
					} elseif ( $settings == "N") {
						$float = "none";
					}

					if ( substr_count($setting, ".")) {
						$filename = $imagepath . "/" . $setting;
					}

				}

				if ( !$filename ) {
					$images_array = glob($imagepath . "/*.{jpg,png,gif}", GLOB_BRACE);
					$filename = $images_array[array_rand($images_array)];
				}

				#echo "<pre>float: " . $float . "</pre>";
				#echo "<pre>filename: " . $filename . "</pre>";

				$output = "<div class=\"speech_box\" style=\"float: " . $float . "\">";
				$output .= "<img src=\"" . $filename . "\" />";
				$output .= "<div class=\"speech\">";
				$output .= $speech_bubble_text;
				$output .= "</div>";
				$output .= "</div>";

				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = str_replace($match[0], $output, $article->text);
			}
		}
	}
}
