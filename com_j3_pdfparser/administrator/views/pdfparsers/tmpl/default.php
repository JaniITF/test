<?php
error_reporting(0);
/**
 * @version    1.0.0
 * @package    Com_Lpdfparser
 * @author     syed <shabbir4it@gmail.com>
 * @copyright  2017 syed
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
function ellipsis($text, $max=100, $append='&hellip;') {

    if (strlen($text) <= $max){
		
		
		return $text;


	}

    $replacements = array(
        '|<br /><br />|' => ' ',
        '|&nbsp;|' => ' ',
        '|&rsquo;|' => '\'',
        '|&lsquo;|' => '\'',
        '|&ldquo;|' => '"',
        '|&rdquo;|' => '"',
    );

    $patterns = array_keys($replacements);
    $replacements = array_values($replacements);


    $text = preg_replace($patterns, $replacements, $text); // convert double newlines to spaces
    $text = strip_tags($text); // remove any html.  we *only* want text
    $out = substr($text, 0, $max);
    if (strpos($text, ' ') === false) return $out.$append;
    return preg_replace('/(\W)&(\W)/', '$1&amp;$2', (preg_replace('/\W+$/', ' ', preg_replace('/\w+$/', '', $out)))) . $append;
}
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_lpdfparser/assets/css/lpdfparser.css');
$document->addStyleSheet(JUri::root() . 'media/com_lpdfparser/css/list.css');
$document->addStyleSheet('https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css');

$document->addScript('https://code.jquery.com/ui/1.10.4/jquery-ui.js');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_lpdfparser');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_lpdfparser&task=pdfparsers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'pdfparserList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();

// $params = &JComponentHelper::getParams('com_lpdfparser');
// $no_of_words=$params->get('no_of_words');

?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		// if (order == '<?php echo $listOrder; ?>') {
			// dirn = 'desc';
		// } else {
			dirn = direction.options[direction.selectedIndex].value;
			
		// }
		Joomla.tableOrdering(order, dirn, '');
	};

	jQuery(document).ready(function () {

       jQuery('.resend_mail').on('click', function(){

          	
          	if (confirm("Are you sure you want to resend email")){
				  var row = jQuery(this).attr('id');
				  
				  var prim_key    = jQuery('#prim_key_'+row).val();
				  var user_name    = jQuery('#owner_name_'+row).val();
				  var order_number = jQuery('#order_number_'+row).val();
				  var from_address = jQuery('#from_address_'+row).val();
				  var to_address   = jQuery('#to_address_'+row).val();
				  var subject      = jQuery('#subject_'+row).val();
				  var mail_body    = jQuery('#mail_body_'+row).val();
				  var pdf_files    = jQuery('#pdf_files_'+row).val();

	             jQuery.ajax({
	             	    url:"index.php?option=com_lpdfparser&task=pdfparsers.resend_mail",
	             	    type:'POST',
	             	    data:{'user_name':user_name,'from_address':from_address,'to_address':to_address,'prim_key':prim_key,
	             	          'subject':subject,'mail_body':mail_body,'pdf_files':pdf_files,'submit':'resend_mail','order_number':order_number},
	             	    success:function(resp){
	             	    	var res = resp.split("||").pop();
	             	    	jQuery('#resend_count_'+row).text(res);
	             	    	jQuery('.success_msg').css('display','block');
	             	    	jQuery('.success_msg').text('Mail has been sent successfully');
	             	    	setTimeout(function(){
	             	    		jQuery('.success_msg').hide(1000);
	             	    	},2000);
	             	    	
	             	    } 

	             });
          }
			  
		});
		
		Joomla.tableOrdering(order, 'desc', '');
		
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});

		
	
	});

	window.toggleField = function (id, task, field) {

		var f = document.adminForm,
			i = 0, cbx,
			cb = f[ id ];

		if (!cb) return false;

		while (true) {
			cbx = f[ 'cb' + i ];

			if (!cbx) break;

			cbx.checked = false;
			i++;
		}

		var inputField   = document.createElement('input');
		inputField.type  = 'hidden';
		inputField.name  = 'field';
		inputField.value = field;
		f.appendChild(inputField);

		cb.checked = true;
		f.boxchecked.value = 1;
		window.submitform(task);

		return false;
	};

</script>

<?php

// Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}

?>
<script>


$(function () {
      $(document).tooltip({
		    position: { my: "left+90 center", at: "right center" },
          content: function () {
              return $(this).prop('title');
          }
      });
  });


  </script>
<div class='success_msg'></div>
<form action="<?php echo JRoute::_('index.php?option=com_lpdfparser&view=pdfparsers'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search"
						   class="element-invisible">
						<?php echo JText::_('JSEARCH_FILTER'); ?>
					</label>
					<input type="text" name="filter_search" id="filter_search"
						   placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
						   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
						   title="<?php echo JText::_('JSEARCH_FILTER'); ?>"/>
				</div>
				<div class="btn-group pull-left">
					<button class="btn hasTooltip" type="submit"
							title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i></button>
					<button class="btn hasTooltip" id="clear-search-button" type="button"
							title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
						<i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit"
						   class="element-invisible">
						<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable"
						   class="element-invisible">
						<?php echo JText::_('JFIELD_ORDERING_DESC'); ?>
					</label>
					<?php //$listDirn='desc';?>
					<select name="directionTable" id="directionTable" class="input-medium"
							onchange="Joomla.orderTable()">
						<option value="desc"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
						<option value="asc" <?php echo $listDirn == 'asc' ? 'selected="selected"' : ''; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
						</option>
						<option value="desc" <?php echo $listDirn == 'desc' ? 'selected="selected"' : ''; ?>>
							<?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
						</option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<table class="table table-striped" id="pdfparserList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
					<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value=""
							   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					<?php if (isset($this->items[0]->state)): ?>
						
					<?php endif; ?>

									<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_ORDER_NUMBER', 'a.`order_number`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_USERNAME', 'a.`username`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_FROM_ADDRESS', 'a.`from_address`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_TO_ADDRESS', 'a.`to_address`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_SUBJECT', 'a.`subject`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_MAIL_BODY', 'a.`mail_body`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_CREATED_AT', 'a.`created_at`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_DOWNLOADLINK', 'a.`downloadlink`', $listDirn, $listOrder); ?>
				</th>
				<th class='left title_clm'>
					Resend Count
				</th>
				<th class='left title_clm' >
				Action
				<?php //echo JHtml::_('grid.sort',  'COM_LPDFPARSER_PDFPARSERS_DOWNLOADLINK', 'a.`downloadlink`', $listDirn, $listOrder); ?>
				</th>
				

					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php 
                 $row = 1;
				foreach ($this->items as $i => $item) :
				   
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_lpdfparser');
					$canEdit    = $user->authorise('core.edit', 'com_lpdfparser');
					$canCheckin = $user->authorise('core.manage', 'com_lpdfparser');
					$canChange  = $user->authorise('core.edit.state', 'com_lpdfparser');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if (isset($this->items[0]->state)): ?>
							
						<?php endif; ?>

										<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'pdfparsers.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>

	<a href="<?php echo JRoute::_('index.php?option=com_hikashop&ctrl=order&task=edit&cid[]=' .  $item->hika_order_id); ?>">

							<?php echo $this->escape($item->order_number); ?>
							<input type='hidden' name='order_number_<?echo $row;?>' id='order_number_<?php echo $row;?>'  value='<?php echo $this->escape($item->order_number);?>' />
						</a>



				<?php else : ?>
					<?php echo $this->escape($item->order_number); ?> 
				<?php endif; ?>

 

				</td>				<td>

					<?php echo $item->username; ?>
					<input type='hidden' name='prim_key_<?echo $row;?>' id='prim_key_<?php echo $row;?>'  value='<?php echo $item->id;?>' />
					<input type='hidden' name='owner_name_<?echo $row;?>' id='owner_name_<?php echo $row;?>'  value='<?php echo $item->username;?>' />
				</td>				<td>

					<?php echo $item->from_address; ?>
					<input type='hidden' name='from_address_<?echo $row;?>' id='from_address_<?php echo $row;?>'  value='<?php echo $item->from_address;?>' />
				</td>				<td>

					<?php echo str_replace(',', '<br>', $item->to_address); ?>
					<input type='hidden' name='to_address_<?echo $row;?>' id='to_address_<?php echo $row;?>'  value='<?php echo $item->to_address;?>' />
				</td>				<td>

					<?php echo $item->subject; ?>
					<input type='hidden' name='subject_<?echo $row;?>' id='subject_<?php echo $row;?>'  value='<?php echo $item->subject;?>' />
				</td>				<td>



				
					<?php 
						
						//echo $item->mail_body; 
						 $body_out=trim($item->mail_body);
						
                         ?>
                         <input type='hidden' name='mail_body_<?echo $row;?>' id='mail_body_<?php echo $row;?>'  value='<?php echo $item->mail_body;?>' />
					

			<?php //echo ellipsis($body_out,$no_of_words);
			
			
			?>
<p><a href="#" title="<?php echo htmlspecialchars($body_out); ?>">Read more</a> </p>

				
				
				
				
				
				
				
				</td>				<td>

					<?php echo $item->created_at; ?>
				</td>				<td>
                     <?php if(!empty($item->downloadlink)){
						  $download_link = explode(',',$item->downloadlink);
						  
					 }
					 
					 foreach($download_link as $key => $value){
					 ?>
                       <a target='_blank' href="./components/com_lpdfparser/helpers/api/lab_reports/plainpdf/<?php echo $value; ?>">

							<?php echo $value; ?>
							<?php //echo str_replace(',', '<br>',$item->downloadlink); ?>
						</a>
                        <br>						
					 <?php }?>
					  <input type='hidden' name='pdf_files_<?echo $row;?>' id='pdf_files_<?php echo $row;?>'  value='<?php echo $item->downloadlink;?>' />
						</td>
						
						</td>
						<td>
							<span class='resend_cnt'; id='resend_count_<?php echo $row;?>'><?php if($item->resend_mail > 0){ echo $item->resend_mail; }?></spam>
						</td>
						<td>
							 
							 <input type='button' name='resend' id='<?php echo $row;?>'  value='Resend' class='resend_mail'/>
							
						</td>				
					

					</tr>
				<?php 
				$row++;
				endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>        
<style>
.resend_cnt{
	color: red;
	float: left;
}
.success_msg {
	display: none;
    width: 67%;
    padding: 10px;
    background: lavender;
    margin: auto;
    z-index: 9999;
    text-align: center;
}
.title_clm{
	color: blue;
}
</style>
