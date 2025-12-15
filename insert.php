<?php
$data = json_decode(file_get_contents("php://input"), true);
$param = $data['param'];

// print_r($param);

//ข้อมูลรูปภาพจับขโมย;
$arrcount =array();
$date = date('Y-m-d');  // ตรวจสอบวันที่ที่เปิดโฟเดอร์ 
$timenow = date('H:i:s');// time bf 30 วินาที  //echo 'ปัจจุบัน '.$timenow.'<br>';
$t1= $timenow;
$dateTimed = new DateTime($t1);
$dateTimed->modify('-30 second');//echo 'ก่อน 30 วินาที '.$dateTimed->format('H:i:s').'<br>';
$t2=$dateTimed->format('H:i:s');
// echo $t2;
$dateTime = new DateTime($t1);
$dateTime->modify('+2 minutes');//echo 'หลัง 2 นาที'.$dateTime->format('H:i:s').'<br><br>';
$t3=$dateTime->format('H:i:s');
//echo 'ข้อมูลรูปภาพจับขโมย  '.$date.'<br><br>';
$i=1;
$img =[];
//echo $date;
$num='pic_001';
// echo $date."\n";
// echo $num."\n";
$dir = "C:\inetpub\wwwroot\Camera_Raw\CAM202412001/{$date}/pic_001";
// echo $dir;
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
    if($file!='..' AND $file!='.'){
     $val =timesp($file);
     $timecheck=substr($val,-6,6); // convert string -> time 
     $ss= substr(   $timecheck,-2,2); // str-> SS
     $ii= substr(   $timecheck,-2,4); // str-> ii
     $time =  $timecheck;
     $formatted_time = substr($time, 0, 2) . ":" . substr($time, 2, 2) . ":" . substr($time, 4, 2);
     $img[] = [
        "value" =>    $file, // Replace with the actual column name for the value
        "timel" =>    date('H:i:s',strtotime($formatted_time))  // Replace with the actual column name for the text
     ];
     $arrcount[]=$file;
     $i++;
      }
    }
    closedir($dh);
  }
}


$arrayfile = array(); 
//t1 = เวลาปัจจุบัน//t2 = เวลา ก่อน 30 วินาที //t3 = เวลาหลัง 2 นาที 
$arrfile1 = [];
$arrfile2 =[];
$arrfile1d = [];
$arrfile2d =[];
foreach($img as $val){   // check //t1 = เวลาปัจจุบัน  //t2 = เวลา ก่อน 30 วินาที 
   // range date 
   if($t1>=$val['timel'] && $t2<=$val['timel']) {
      $arrfile1[]= $val['value'].'-'.$val['timel'];
      $arrfile1d[]= $val['value'];
   }
}



$dateTimex = new DateTime($t1);
$dateTimex->modify('-2 minutes');//echo 'หลัง 2 นาที'.$dateTime->format('H:i:s').'<br><br>';
$timesp=$dateTimex->format('H:i:s');

// echo 'เวลาปัจจุบัน'. $t1.'<br>';
// echo 'เวลา ก่อน 30 วินาที '.$t2.'<br><br>';
// echo 'โดยเริ่มจากเวลาข้อมูลล่าสุด'.$timesp.'<br>';
// echo 'เวลาหลัง 2 นาที'. $t3.'<br><br>';
foreach($img as $val2){ // check //t3 = เวลาหลัง 2 นาที 
   if($timesp>=$val2['timel'] && $t3<=$val2['timel']) {
      $arrfile2[]= $val2['timel'];
      $arrfile2d[]= $val2['value'];
   }
}

//ข้อ 1 คือเวลาที่เริ่มเอา File รูปทั้งหมดมา
//ข้อ 2 คือเวลาที่เริ่มเอา File รูป มา X
//ข้อ 3 คือ เวลาที่ Event Hook 
//ข้อ 4 คือเวลาสิ้นสุดการเอารูป X
//ข้อ 5 คือเวลาที่สิ้นสุดเอา รูปทั้งหมดมา 
//ข้อ 1 คือเวลาที่เริ่มเอา File รูปทั้งหมดมา ลบ 2 นาที 
$dateTimed1 = new DateTime($t1);
$dateTimed1->modify('-2 minutes');
$tl1=$dateTimed1->format('H:i:s');
//ข้อ 2 คือเวลาที่เริ่มเอา File รูป มา X
$dateTime2 = new DateTime($t1);
$dateTime2->modify('-1 minutes');
$tl2=$dateTime2->format('H:i:s');
//ข้อ 3 คือ เวลาที่ Event Hook
$tl3=date('H:i:s');
//ข้อ 4 คือเวลาสิ้นสุดการเอารูป X
$dateTime4=new DateTime($t1);
$dateTime4->modify('+1 minutes');
$tl4=$dateTime4->format('H:i:s');
//ข้อ 5 คือเวลาที่สิ้นสุดเอา รูปทั้งหมดมา
$dateTime5=new DateTime($t1);
$dateTime5->modify('+2 minutes');
$tl5=$dateTime5->format('H:i:s'); //echo '/'.$tl5;

$arrfile3=array();
$arrfile4=array();
$arrfile5=array();
foreach($img as $val3){
//ข้อ 1 คือเวลาที่เริ่มเอา File รูปทั้งหมดมา ลบ 2 นาที  && //ข้อ 5 คือเวลาที่สิ้นสุดเอา รูปทั้งหมดมา + 2
   if($tl5>=$val3['timel'] && $tl1<=$val3['timel']) { $arrfile3[]=$val3['value'];}
}
foreach($img as $val4){
   //ข้อ 1 คือเวลาที่เริ่มเอา File รูปทั้งหมดมา ลบ 1 นาที  && //ข้อ 5 คือเวลาที่สิ้นสุดเอา รูปทั้งหมดมา + 1
   if($tl4>=$val4['timel'] && $tl2<=$val4['timel']){ $arrfile4[]=$val4['value'];}
}
foreach($img as $val5){
   //ข้อ 3 คือ เวลาที่ Event Hook
   if($tl3==$val5['timel']){ $arrfile5[]=$val5['value'];}
}
$fkX=array_merge($arrfile3,$arrfile4,$arrfile5); // FD X กรณมีเวลาตามเงื่อนไขด้านบน 
$fk=array_merge($arrfile1d,$arrfile2d); // FD PIC
  // ถ้า array_merge เป็นค่าว่างจะไม่สร้าง FD -> ใน FTP  
  var_dump($fk);
  if(!empty($fk)){   
    $NAMEFD=$param; 
    echo `C:\FTP/{$NAMEFD}/Pic/X`;
    echo `C:\FTP/{$NAMEFD}/Vdo/`;
    mkdir("C:\FTP/$NAMEFD/Pic/X", 0777, true); // folder Pic and Folder X
    mkdir("C:\FTP/$NAMEFD/Vdo/", 0777, true); // Foloder Vdo in camera name
    foreach($fk as $FdName){  // PIC
    $Filename = "C:\FTP/$NAMEFD/Pic/".$FdName;
    $dirs =$dir.'/'.$FdName;
   //  echo   $dirs;
    copy( $dirs , $Filename);
   } 
   foreach($fkX as $FdxName){
      $FilenameX = "c:\FTP/$NAMEFD/Pic/X/".$FdxName;
      $dirsx =$dir.'/'.$FdxName;
      // echo    $dirsx;
      copy($dirsx,$FilenameX);
   }
   
} 

function make_dir( $path, $permissions = 0777 ) {
   return is_dir( $path ) || mkdir( $path, $permissions, true );
}
function timesp($val){
     $sp=(explode('_',$val,-1));
     $str = $sp[1];
     return $str; 
}



//}  
?>