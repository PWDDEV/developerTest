<?php

namespace Br\Base;

/*
 * Вывод переменных подробно для админа + backtrace
 * @param mixed[] $mVar переменная для вывода
 * @param int $iToFile сохранить в файл. 1 - сохранить переписать, 2 - сохранить дописать
 * @param int $iMore вывести backtrace
 * @param string путь к файлу относительно корня прим. /mylog.txt, по умолчанию создаст файл printrlog.txt в корне DOCUMENT_ROOT
 */
class Debug {
    public function pr($mVar, $iToFile = 0, $iMore = 0, $sFileName = ''){
        global $USER;
         if($USER->IsAdmin()) {

             if($iToFile > 0){

                 if(strlen($sFileName) <= 0){
                     $sFileName = '/printrlog.txt';
                 }
                 $sFilePath = $_SERVER['DOCUMENT_ROOT'].$sFileName;
                 if(!file_exists( $sFilePath )){
                     fclose(fopen($sFilePath, 'w+'));
                 }

                 ob_start();
                     if($iMore == 1){
                         debug_print_backtrace();
                     }
                 is_array($mVar) ? print_r($mVar) : var_dump($mVar);
                 $sVarResult = ob_get_clean();


                 if($iToFile == 2){
                     file_put_contents($sFilePath, $sVarResult, FILE_APPEND);
                 } else {
                     file_put_contents($sFilePath, $sVarResult);
                 }

             } else {
                 echo '<pre>';
                 if($iMore == 1){
                     echo "<br>########################################################<br>";
                     debug_print_backtrace();
                     echo "########################################################<br>";
                 }
                 is_array($mVar) ? print_r($mVar) : var_dump($mVar);
                 echo "-------<br><br>";
                 echo '</pre>';
             }
        }
     }

}