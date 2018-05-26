<?php
/**
 * @version    1.0.0
 * @package    Com_Lpdfparser
 * @author     syed <shabbir4it@gmail.com>
 * @copyright  2017 syed
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_lpdfparser'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

$path = getcwd(); 

chmod($path.'/components/com_lpdfparser/generate_pdf_template.php', 0777);  
chmod($path.'/components/com_lpdfparser/default_bk.php', 0777);  
chmod($path.'/components/com_config/view/component/tmpl/default.php', 0777);  
chmod($path.'/components/com_lpdfparser/component_bk.php', 0777);  
chmod($path.'/components/com_config/model/component.php', 0777);  
chmod($path.'/components/com_ipdfparser/lpdfparser_bk.php', 0777);  
chmod($path.'/components/com_ipdfparser/lpdfparser.php', 0777);  

$file_generate_pdf = file_get_contents($path.'/components/com_lpdfparser/generate_pdf_template.php');
$fp = fopen($path."/../generate_pdf_template.php","w");
fwrite($fp,$file_generate_pdf);
fclose($fp); 

$file_get_contents = file_get_contents($path.'/components/com_lpdfparser/default_bk.php');
$file_put_contents = file_put_contents($path.'/components/com_config/view/component/tmpl/default.php',$file_get_contents);

$file_get_component = file_get_contents($path.'/components/com_lpdfparser/component_bk.php');
$file_put_component = file_put_contents($path.'/components/com_config/model/component.php',$file_get_component);
 
$file_get_parser  = file_get_contents($path.'/components/com_lpdfparser/lpdfparser_bk.php');
$file_put_component  = file_put_contents($path.'/components/com_lpdfparser/lpdfparser.php',$file_get_parser);
 
// Include dependancies
jimport('joomla.application.component.controller'); 

JLoader::registerPrefix('Lpdfparser', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Lpdfparser');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect(); 
