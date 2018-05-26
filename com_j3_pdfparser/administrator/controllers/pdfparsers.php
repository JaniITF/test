<?php
/**
 * @version    1.0.0
 * @package    Com_Lpdfparser
 * @author     syed <shabbir4it@gmail.com>
 * @copyright  2017 syed
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Pdfparsers list controller class.
 *
 * @since  1.6
 */
class LpdfparserControllerPdfparsers extends JControllerAdmin
{
	/**
	 * Method to clone existing Pdfparsers
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_LPDFPARSER_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_LPDFPARSER_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_lpdfparser&view=pdfparsers');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'pdfparser', $prefix = 'LpdfparserModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	public function resend_mail(){

		if($_POST['submit']=='resend_mail'){

            $baseurl = JUri::root();
            $params = &JComponentHelper::getParams('com_lpdfparser');

            $notify_address_name = $params->get('notify_address_name');

			$order_number   = $_POST['order_number'];
			$notify_address = $_POST['from_address'];
			$toMail         = $_POST['to_address'];
			$mail_subject   = $_POST['subject'];
			$mail_content   = $_POST['mail_body'];
			$pdf_files      = explode(',',$_POST['pdf_files']);
			$prim_id        = $_POST['prim_key'];

			//$toMail         ='janagiraman@itflexsolutions.com'; 


			                $resend_mail = new PHPMailer;
							$resend_mail->CharSet = 'UTF-8';
							$resend_mail->setFrom($notify_address, $notify_address_name);
							$resend_mail->addAddress($toMail);
							$resend_mail->Subject  = $mail_subject;
							$resend_mail->Body     = $mail_content;
							$resend_mail->IsHTML(true);
							
							foreach($pdf_files as $kk => $a_pdf_file){
								$resend_mail->AddAttachment($_SERVER['DOCUMENT_ROOT']."/projects/pdfparser/administrator/components/com_lpdfparser/helpers/api/lab_reports/".$a_pdf_file); 
							 }
							 
            if($resend_mail->send()){

							$db = JFactory::getDbo();
							$upquery = $db->getQuery(true);
							$fields = array(
							    $db->quoteName('resend_mail') . ' = resend_mail + 1' 
							);
							$conditions = array(
							    $db->quoteName('id') . ' = '. $prim_id
							);
							$upquery->update($db->quoteName('#__lpdfparser_'))->set($fields)->where($conditions);
							$db->setQuery($upquery);
							//echo($upquery->__toString());  
							$result = $db->execute();

                         	// Create a new query object.
							$query = $db->getQuery(true);
							$query->select($db->quoteName(array('resend_mail')));
							$query->from($db->quoteName('#__lpdfparser_'));
							$query->where($db->quoteName('id' ) . ' = '.$prim_id);
							$db->setQuery($query);
							$results = $db->loadObjectList();
							echo '||'.$results[0]->resend_mail;
							exit;
            }

		}

	}
}
