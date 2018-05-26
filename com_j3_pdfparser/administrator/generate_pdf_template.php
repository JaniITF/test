<?php
error_reporting(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
error_reporting(E_ALL ^ E_DEPRECATED);

	define('_JEXEC', 1);
	define('JPATH_BASE', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	require_once JPATH_BASE.DS.'includes'.DS.'defines.php'; 
	require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
	
    include("libraries/mpdf60/mpdf.php");   
	require_once('libraries/mpdf60/qrcode/qrcode.class.php');	
    
	$files = glob("administrator/components/com_lpdfparser/pdf_assets/new_csv_files/*.csv");
	
	if(!empty($files)){
	 
		foreach($files as $row => $fileName){
		
			$csvData = file_get_contents($fileName);
			$lines = explode(PHP_EOL, $csvData);
			$array = array();
			foreach ($lines as $line) {
				$array[] = explode(';',$line);
			}
			
			foreach($array as $key => $value){
				
				$i = 5;
				if($key!=0 && $array[$key]['3']!=''){ 
					
					    $order_n = explode('-',$array[$key]['3']);
						if($order_n['1']!=''){
						    $order_child = $order_n['1']-1;
						}
						else{
							$order_child = '0';
						}
						
						$db = JFactory::getDbo();
						/**THIS IS TO GET ORDER DETAILS */
						$query = $db->getQuery(true);
						$query->select($db->quoteName(array('a.order_number','a.order_id','a.order_created','a.order_user_id','b.dog_name',
										   'b.dog_breed','b.birth_date','b.sex','b.vet_name','b.origin_no','b.id_no','b.savininkoelpatas','b.product_id','t.template_name')));
						$query->from($db->quoteName('#__hikashop_order','a'));
						$query->join('LEFT', $db->quoteName('#__hikashop_order_product', 'b') . ' ON (' . $db->quoteName('a.order_id') . ' = ' . $db->quoteName('b.order_id') . ')');
						$query->join('LEFT', $db->quoteName('#__hikashop_product', 'c') . ' ON (' . $db->quoteName('b.product_id') . ' = ' . $db->quoteName('c.product_id') . ')');
						$query->join('LEFT', $db->quoteName('#__product_templates', 't') . ' ON (' . $db->quoteName('c.product_template_id') . ' = ' . $db->quoteName('t.template_id') . ')');
						
						$query->where($db->quoteName('a.order_number') . ' = '. $db->quote($order_n[0]));
						$db->setQuery($query);
						//echo($query->__toString());
						
						$result = $db->loadObjectList();
						
						$current_date = date('d-m-Y');
						$order_no[$order_n['0']]['result'] = $result;
						$user = $db->getQuery(true);
						$user->select($db->quoteName(array('b.name','b.email')));
						$user->from($db->quoteName('#__hikashop_user','a'));
						$user->join('LEFT', $db->quoteName('#__users', 'b') . ' ON (' . $db->quoteName('a.user_cms_id') . ' = ' . $db->quoteName('b.id') . ')');
						
						$user->where($db->quoteName('a.user_id') . ' = '. $db->quote($result[$order_child]->order_user_id));
						$db->setQuery($user);
						
						
						$user_result = $db->loadObjectList();
						$order_no[$order_n['0']]['user'] = $user_result;						
						$sampling_date =date('d-m-Y',$result[0]->order_created);
						
						$temp_file_name = $result[0]->template_name;
						if($temp_file_name==''){
							$temp_file_name = 'pdf_default_template.php';
						} 
						$temp_file_name = 'pdf_default_template.php';
						$plain_temp_file_name = 'plain_pdf_template.php';
						
						$template = file_get_contents('administrator/components/com_lpdfparser/pdf_assets/pdf_templates/'.$temp_file_name);  
						$plain_template = file_get_contents('administrator/components/com_lpdfparser/pdf_assets/pdf_templates/'.$plain_temp_file_name);  
						
						
				        $animal_data ='<table style="width:100%;border-radius:10px;">
											<tr>
											<td style="font-weight:300; font-size:13px; color:#666;">Name</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[$order_child]->dog_name.'</td>
											</tr>
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Date of Birth</td>
											<td style="font-weight:bold; font-size:13px; color:#58595b;">'.$result[$order_child]->birth_date.'</td>  
											</tr>	
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Sex</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[$order_child]->sex.'</td>	
											</tr>		
											<tr>		
											<td style="font-weight:300; font-size:13px;color:#666;">Breed</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[$order_child]->dog_breed.'</td>	
											</tr>	
											<tr>	
											<td style="font-weight:300; font-size:13px;color:#666;">Id No</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[$order_child]->id_no.'</td>											
											</tr>	
											<tr>	
											<td style="font-weight:300; font-size:13px;color:#666;">Doc No</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[$order_child]->origin_no.'</td>
											</tr>	
										</table>'; 
										
						$qr_animal_data = 'Animal Data %0AName :'.$result[$order_child]->dog_name.' %0ADate of Birth :'.$result[$order_child]->birth_date.' %0ASex :'.$result[$order_child]->sex.' %0ABreed :'.$result[$order_child]->dog_breed;
									  
									  
						$sample_data ='<table style="width:100%;border-radius:10px;margin-left:20px;">
						                    <tr>	
											<td style="font-weight:300; font-size:13px;color:#666;">VHL ID</td>
											<td style="font-weight:bold; font-size:13px;color:#58595b;">'.$array[$key][1].$array[$key][0].'</td>
											</tr>	
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Material</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">Swab</td>
											</tr>
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Order No</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$order_n['0'].'</td>	
											</tr>
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Sampling Date</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b ">'.$sampling_date.'</td>
											</tr>
											<tr>
											<td style="font-weight:300; font-size:13px;color:#666;">Sampled By</td>	
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$user_result[0]->name.'</td>	
											</tr>	
											<tr>	
											<td style="font-weight:300; font-size:13px;color:#666;">Veterinarian Name</td>
											<td style="font-weight:bold; font-size:13px;color:#58595b; ">'.$result[0]->vet_name.'</td>
											</tr>
										</table>';	
											
							if(trim($array[$key][27]) !=''){
							        $test_result =$array[$key][27];
							}
							else{
									$test_result ='Based on DNA Profiles from the samples received the following can be concluded: The DNA/Blood profile from this animal has been determined, the parentage has not been verified.';
							}
											
							$dna_profile = '<table style="width:100%;border-radius:10px;">
							                      <tr>
												  <td style="width:20%;font-weight:300; font-size:13px;color:#666; vertical-align:top">Profile</td>	
												  <td style="width:80%;font-weight:bold; font-size:13px;color:#58595b; ">'.$array[$key][1].$array[$key][2].'</td>
												  </tr>		
												  <tr>	
												  <td style="width:20%;font-weight:300; font-size:13px;color:#666; vertical-align:top">Test Date</td>
												  <td style="width:80%;font-weight:bold; font-size:13px;color:#58595b; ">'.$current_date.'</td>	
												  </tr>	
												  <tr>	
												  <td style="width:20%;font-weight:300; font-size:13px;color:#666; vertical-align:top">Test Result</td>											
												  <td style="width:80%;font-weight:bold; font-size:13px;color:#58595b; ">'.$test_result.'</td>	
												  </tr>	
											</table>';
					   
					   
					    /**ANIMAL DATA PLACEHOLDER REPLACE*/
						 $template=str_replace('{ANIMAL_DATA}',$animal_data,$template);
						 $plain_template=str_replace('{ANIMAL_DATA}',$animal_data,$plain_template);
												
						/**SAMPLE DATA PLACEHOLDER REPLACE*/
						$template=str_replace('{SAMPLE_DATA}',$sample_data,$template);
						$plain_template=str_replace('{SAMPLE_DATA}',$sample_data,$plain_template);
						
					    /**DNA PROFILE PLACEHOLDER REPLACE*/
						$template=str_replace('{DNA_PROFILE}',$dna_profile,$template);
						$plain_template=str_replace('{DNA_PROFILE}',$dna_profile,$plain_template);
												
						/**CERTIFIED PLACEHOLDER REPLACE*/
						$template=str_replace('{CERTIFIED_NO}',$array[$key][1].$array[$key][0].'_'.$array[$key]['3'],$template);
						$template=str_replace('{DATE_OF_ISSUE}',$current_date,$template);
						
						$plain_template=str_replace('{CERTIFIED_NO}',$array[$key][1].$array[$key][0].'_'.$array[$key]['3'],$plain_template);
						$plain_template=str_replace('{DATE_OF_ISSUE}',$current_date,$plain_template);
						
						$tna_markers = '<div style="width:47%;float:left;">';
						
						       $j=1;
						        foreach($value as $kk => $val){
								   $val_v=str_replace(' ',' / ',$val);
								    if($kk < '27'){
										
								    if($i==$kk && $array[0][$i]!=''){
						               $tna_markers .= '<div style="width:47%;float:left;font-weight:300; font-size:13px; line-height:normal; vertical-align:top;color:#666;"> '.$array[0][$i].'</div>

						                                 <div style="width:47%;float:right;font-weight:bold; font-size:13px; line-height:normal; vertical-align:top;color:#58595b;"> '.$val_v.'</div>';
										$i++;
										if($j % 13 == 0){
											$tna_markers .= '</div><div style="width:47%;float:left;font-weight:300; font-size:13px; line-height:normal; vertical-align:top;color:#666;">';
										}
										$j++;	
							        }
									}
							    }
						$tna_markers .= '</div>'; 
										
						$template=str_replace('{DNA_MARKERS_TABLE_DATA}',$tna_markers,$template);
						$plain_template=str_replace('{DNA_MARKERS_TABLE_DATA}',$tna_markers,$plain_template);
						
						$filename = $array[$key]['3'].'_'.$user_result[0]->name.'.pdf';
						//$plian_pdf_filename = $array[$key]['3'].'_'.$user_result[0]->name.'.pdf';
						
						$order_no[$order_n['0']]['owner_pdf_files'][$result[$order_child]->savininkoelpatas][] = $filename;
						$order_no[$order_n['0']]['admin_pdf_files'][] = $filename;
						
						$err = isset($_GET['err']) ? $_GET['err'] : '';
	                    if (!in_array($err, array('L', 'M', 'Q', 'H'))) $err = 'L';
						
						$qrcode = new QRcode(utf8_encode(urlencode($qr_animal_data)), $err);

						$qrcode->displayHTML();

				  	    $QR_code ='<img src="libraries/mpdf60/qrcode/image.php?msg='.$qr_animal_data.'&amp;err='.$err.'" alt="generation qr-code" style="border: solid 1px black; width:300px !important; height:300px !important;">';				  	   
						/* $QR_code ='<img src="libraries/mpdf60/qrcode/image.php?msg=<?php echo urlencode('.$qr_animal_data.'); ?>&amp;err=<?php echo urlencode('.$err.'); ?>" alt="generation qr-code" style="border: solid 1px black; width:250px; height:250px">';                      */

						$template=str_replace('{QR_CODE}',$QR_code,$template);						
						$plain_template=str_replace('{QR_CODE}',$QR_code,$plain_template);
						
						
						$plain_mpdf=new mpdf();
						$plain_mpdf->showImageErrors = true;
						$plain_mpdf->WriteHTML($plain_template);
						//$mpdf->Output('assets/pdf_files/'.$filename,'F');
						$plain_mpdf->Output('administrator/components/com_lpdfparser/helpers/api/lab_reports/plainpdf/'.$filename,'F');
						
						$mpdf=new mpdf();
						$mpdf->showImageErrors = true;
						$mpdf->WriteHTML($template);
						//$mpdf->Output('assets/pdf_files/'.$filename,'F');
						$mpdf->Output('administrator/components/com_lpdfparser/helpers/api/lab_reports/'.$filename,'F');
						
						unset($animal_data);
						unset($sample_data);
						unset($template);
				}
			}
			
			$Newarray = array();
			$i=0;
			
			foreach($order_no as $key => $Datavalue)
			{
				$keyVal = trim($key);
				$Newarray[$keyVal]['owner_pdf_files'][] = $Datavalue['owner_pdf_files'];
				$Newarray[$keyVal]['admin_pdf_files'][] = $Datavalue['admin_pdf_files'];
				$Newarray[$keyVal]['user'][]= $Datavalue['user'][0];
				$Newarray[$keyVal]['result'][] = $Datavalue['result'][0];
				$i++;
			}
			
			
		    foreach($Newarray as $key => $result){
				
				        $baseurl = JUri::root();
						$order_number = $key;
						$user_name    = $result['user'][0]->name;
						$hika_order_id = $result['result'][0]->order_id;
					
					    $params = &JComponentHelper::getParams('com_lpdfparser');
						
						$notify_address=$params->get('notify_address');
						$notify_address_name=$params->get('notify_address_name');
					    $mail_subject=$params->get('cron_mail_subject');
					    $mail_content=$params->get('cron_mail_content');
					    $optional_email=$params->get('cron_optional_email');
					    $hikaorderstatus=$params->get('hikaorderstatus');
					    //$hikaorderstatus='results';
					    $mail_subject=str_replace('{ORDER_NO}',$order_number,$mail_subject);
                        $mail_content=str_replace('{ORDER_NO}',$order_number,$mail_content);
                        					
						$attachments=implode(',',$result['admin_pdf_files']['0']);
						$attach = iconv("UTF-8", "ISO-8859-9//TRANSLIT",$attachments);	
						
						$admin_email =array($optional_email,$result['user'][0]->email);
												
						foreach($result['owner_pdf_files'][0] as $to_mail => $values){
							//$to_mail='janagiraman@itflexsolutions.com';
							$o_mail = new PHPMailer;
							$o_mail->CharSet = 'UTF-8';
							$o_mail->setFrom($notify_address, $notify_address_name);
							$o_mail->addAddress($to_mail);
							$o_mail->Subject  = $mail_subject;
							$o_mail->Body     = $mail_content;
							$o_mail->IsHTML(true);
							$attachments = implode(',',$values);
							
							foreach($values as $kk => $pdf_file){
								$o_mail->AddAttachment($_SERVER['DOCUMENT_ROOT']."/projects/pdfparser/administrator/components/com_lpdfparser/helpers/api/lab_reports/$pdf_file"); 
							}
							$o_mail->send();
							
							pdf_parser_log_update($hika_order_id,$order_number,$user_name,$to_mail,$attachments);
							
						}
						
						foreach($admin_email as $keyy => $toMail){
							
							$a_mail = new PHPMailer;
							$a_mail->CharSet = 'UTF-8';
							$a_mail->setFrom($notify_address, $notify_address_name);
							$a_mail->addAddress($toMail);
							$a_mail->Subject  = $mail_subject;
							$a_mail->Body     = $mail_content;
							$a_mail->IsHTML(true);
							$attach = implode(',',$result['admin_pdf_files'][0]);
							foreach($result['admin_pdf_files'] as $kk => $a_pdf_file){
								$a_mail->AddAttachment($_SERVER['DOCUMENT_ROOT']."/projects/pdfparser/administrator/components/com_lpdfparser/helpers/api/lab_reports/$a_pdf_file"); 
							 }
							 $a_mail->send();
							 
							 pdf_parser_log_update($hika_order_id,$order_number,$user_name,$toMail,$attach);
						}
						
						$created_date=date('Y-m-d H:i:s');
				
			} 
            			
			
			/**This is to move files from new_csv_files folder to completed_csv_files folder */
			 $completedFileName = str_replace('administrator/components/com_lpdfparser/pdf_assets/new_csv_files/', 'administrator/components/com_lpdfparser/pdf_assets/completed_csv_files/', $fileName) ;
			 rename($fileName,$completedFileName); 
	    }	 
          echo "PDF file has been generated and successfully sent email to the users";		
	}
	else{
		echo "There is no files to generate PDF"; 
	}
	
	
	
	function pdf_parser_log_update($hika_order_id,$order_number,$user_name,$to_mail,$attach){
		
		                        $baseurl = JUri::root();
		                        $params = &JComponentHelper::getParams('com_lpdfparser');
						
								$notify_address=$params->get('notify_address');
								$notify_address_name=$params->get('notify_address_name');
								$mail_subject=$params->get('cron_mail_subject');
								$mail_content=$params->get('cron_mail_content');
								$optional_email=$params->get('cron_optional_email');
								$hikaorderstatus=$params->get('hikaorderstatus');
								
								$mail_subject=str_replace('{ORDER_NO}',$order_number,$mail_subject);
                                $mail_content=str_replace('{ORDER_NO}',$order_number,$mail_content);
								
								//$attach = implode(',',$pdf_files);

								$created_date=date('Y-m-d H:i:s');
		
		                        $db1 = JFactory::getDbo();
								$insertQuery = $db1->getQuery(true);
								
								$columns = array('order_number', 'username', 'from_address', 'to_address','subject','mail_body','created_at','downloadlink','hika_order_id');

								// Insert values.
								$values = array($db1->quote($order_number),$db1->quote($user_name), $db1->quote($notify_address), $db1->quote($to_mail),$db1->quote($mail_subject),$db1->quote($mail_content),$db1->quote($created_date),$db1->quote($attach),$db1->quote($hika_order_id));

								// Prepare the insert query.
								$insertQuery
									->insert($db1->quoteName('#__lpdfparser_'))
									->columns($db1->quoteName($columns))
									->values(implode(',', $values));

								// Set the query using our newly populated query object and execute it.
								$db1->setQuery($insertQuery);
								//echo($insertQuery->__toString());
								//echo '<br>';
								$db1->execute();
							
							    $db = JFactory::getDbo();
								$update_query = $db->getQuery(true);
								$update_query = "UPDATE  #__hikashop_order  SET order_status='$hikaorderstatus' WHERE order_id = '".$hika_order_id."'";
								$db->setQuery($update_query);
								//echo($update_query->__toString());
								$db->execute(); 
								unset($attach);
	}
	
 ?>