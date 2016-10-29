<?php     
/**
 * Export CSV or XLSX direct to browser
**/

require_once '../classes/dbClass.php';
$db = new DB;
require_once '../classes/logging.php';
$log = new Log($dbPre);

$type = $_POST['type'];

$sql = "select c.*, ls.sourceName, lst.statusName, lt.typeName"
     . " from {$dbPre}contacts c, {$dbPre}leadSource ls, {$dbPre}leadStatus lst, {$dbPre}leadType lt"
     . " where c.leadType=lt.typeID and c.leadSource=ls.sourceID and c.lStatus=lst.id"
     . " order by lastName asc";
$contacts = $db->extQuery($sql);

$sql = "select * from {$dbPre}siteSettings";
$siteSettings = $db->extQueryRowObj($sql);

$columns = array(
         'First Name',
         'Last Name',
         $siteSettings->Phone,
         $siteSettings->secondaryPhone,
         $siteSettings->Fax,
         'Email',
         'Secondary Email1',
         'Secondary Email2',
         'Secondary Email3',
         $siteSettings->Address,
         $siteSettings->City,
         $siteSettings->State,
         $siteSettings->Country,
         $siteSettings->Zip,
         'Source',
         'Type',
         'Status',
         "$siteSettings->customField1",
         "$siteSettings->customField2",
         "$siteSettings->customField3",
         'Notes'
);

// PHPExcel_IOFactory
include_once '../classes/PHPExcel/Classes/PHPExcel.php';
// PHPExcel_Writer_Excel2007
include_once '../classes/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
//Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("LCM");
$objPHPExcel->getProperties()->setLastModifiedBy("LCM");
$objPHPExcel->getProperties()->setTitle("Export From LCM");
$objPHPExcel->getProperties()->setSubject("Export of Leads and Contacts");
$objPHPExcel->getProperties()->setDescription("Export from LCM of Leads and Contacts.");

// Sheet 0 //
$objPHPExcel->setActiveSheetIndex(0);
// Header Row
$row = 1;
$col = 0;
foreach ($columns as $val) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val);
    $col++;
}

// Entries
$row = 2;
foreach ($contacts as $key => $val) {
    $noteMerge = '';
    $sql = "select * from {$dbPre}otherEmails where contact='$val->id'";
    $emails = $db->extQuery($sql);
    $sql = "select * from {$dbPre}leadNotes where leadID='$val->id'";
    $notes = $db->extQuery($sql);
    foreach ($notes as $line => $note) {
        $newNote = strip_tags($note->Note);
        $noteMerge = $noteMerge . '--' . $newNote . "\r\n";
    }
    $email1 = isset($emails[0]->email) ? $emails[0]->email : '';
    $email2 = isset($emails[1]->email) ? $emails[1]->email : '';
    $email3 = isset($emails[2]->email) ? $emails[2]->email : '';
    //$col = 0;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $val->firstName);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $val->lastName);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $val->Phone);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $val->secondaryPhone);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $val->Fax);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $val->Email);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $email1);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $email2);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $email3);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $val->Address);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $val->City);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $val->State);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $val->Country);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $val->Zip);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $val->sourceName);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $val->typeName);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $val->statusName);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $val->customField);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $val->customField2);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $val->customField3);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $noteMerge);
    $colStr = PHPExcel_Cell::stringFromColumnIndex(20);
    $objPHPExcel->getActiveSheet()->getStyle($colStr . $row)->getAlignment()->setWrapText(true);
    $row++;
}

// Choose output format 
if ($type == 'csv') {    // Save csv FILE

    $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=LCM-Export.csv');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
                $objWriter->save('php://output');

} elseif ($type == 'excel') {  // Save Excel 2007 file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=LCM-Export.xlsx');
                $objWriter->save('php://output');
}
