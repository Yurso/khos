<?php

header('Content-Type: text/csv; charset=UTF-8');		
header('Content-Disposition: attachment; filename="realty-export.csv"');

$sep = ";";
$csv = "";
// headers
$csv .= "Дата" . $sep;                                
$csv .= "Заголовок" . $sep;
//$csv .= "Клиент" . $sep;
$csv .= "Цена" . $sep;

$csv .= "\r\n";

foreach ($items as $key => $item) {
	
	// Выводим данные по каждой линии
	$csv .= date("d.m.y", strtotime($item->date)) . $sep;                                
    $csv .= $item->project_title . ' ' . $item->type_title . ' - ' . $item->title . $sep;
    //$csv .= $item->customer_name . $sep;
    $csv .= $item->price * $item->count . $sep;

    $csv .= "\r\n";

}

$csv = mb_convert_encoding($csv, 'cp1251', 'UTF-8');

echo $csv;	