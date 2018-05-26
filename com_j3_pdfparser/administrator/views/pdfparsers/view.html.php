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

jimport('joomla.application.component.view');

/**
 * View class for a list of Lpdfparser.
 *
 * @since  1.6
 */
class LpdfparserViewPdfparsers extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		LpdfparserHelpersLpdfparser::addSubmenu('pdfparsers');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = LpdfparserHelpersLpdfparser::getActions();

		JToolBarHelper::title(JText::_('COM_LPDFPARSER_TITLE_PDFPARSERS'), 'pdfparsers.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/pdfparser';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('pdfparser.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('pdfparsers.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('pdfparser.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('pdfparsers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('pdfparsers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'pdfparsers.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('pdfparsers.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('pdfparsers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'pdfparsers.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('pdfparsers.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_lpdfparser');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_lpdfparser&view=pdfparsers');

		$this->extra_sidebar = '';
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`order_number`' => JText::_('COM_LPDFPARSER_PDFPARSERS_ORDER_NUMBER'),
			'a.`username`' => JText::_('COM_LPDFPARSER_PDFPARSERS_USERNAME'),
			'a.`from_address`' => JText::_('COM_LPDFPARSER_PDFPARSERS_FROM_ADDRESS'),
			'a.`to_address`' => JText::_('COM_LPDFPARSER_PDFPARSERS_TO_ADDRESS'),
			'a.`subject`' => JText::_('COM_LPDFPARSER_PDFPARSERS_SUBJECT'),
			'a.`mail_body`' => JText::_('COM_LPDFPARSER_PDFPARSERS_MAIL_BODY'),
			'a.`created_at`' => JText::_('COM_LPDFPARSER_PDFPARSERS_CREATED_AT'),
			'a.`downloadlink`' => JText::_('COM_LPDFPARSER_PDFPARSERS_DOWNLOADLINK'),
		);
	}
}
