<?php
/**
 * Created by Lestruviens
 * Date: 2/17/2020
 */

$sql = array();

foreach ($sql as $query){
    if(Db::getInstance()->execute($query) == false){
        return false;
    }
}