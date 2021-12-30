<?php
include '../../globals/config.php';
$bd_name='bip';
$table_name='bip_test';
$affectedRow = 0;
unless(-r "/media/filemaker/Data/Documents/xml_out.xml") {
    system("mount /media/filemaker");
}
shell_exec('cp "/media/filemaker/Data/Documents/xml_out_utf.xml" "/srv/www/html/cms/api/xmlConvert/" ');
$homepage = file_get_contents('xml_out_utf.xml');
$description=iconv('UTF-8', 'UTF-8//IGNORE', $description);
$description='<?xml version="1.0" encoding="UTF-8"?><books>'.$description.'</books>';
$fp = fopen("xml_out.xml","wb");
fwrite($fp,$description);
fclose($fp);
$xml = simplexml_load_file("xml_out_utf.xml") or die("Error: Cannot create object");
$sql = "TRUNCATE ".$table_name;
$result = mysqli_query($link, $sql);
foreach ($xml->children() as $row) {
	    $sql="insert into ".$table_name." set
            zz_webcode='" . $row->zz_web_id . "',
            detail_size='" . $row->Size . "'
            /* other fields hidden for security and privacy reasons */
            ";
    $result = mysqli_query($link, $sql);
    
    if (! empty($result)) {
        $affectedRow ++;
        $arr['error']='1';
    } else {
        $error_message = mysqli_error($result);
        $arr['error']='0';
    }
}
if ($affectedRow > 0) {
    $arr['error_msg']=$affectedRow . " records inserted";
} else {
    $arr['error_msg']="No records inserted";
}
$arr['missing_covers']=$missing_covers;
echo json_encode($arr);
?>