<?php
/**
 * Created by Lestruviens.
 * Date: 2/17/2020
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'. _DB_PREFIX_ . 'ltvsms`(
    `id_ltvsms` int(11) NOT NULL AUTO_INCREMENT,
    `ltvsms_messageOrder` VARCHAR(255),
    `ltvsms_messageInscription` VARCHAR(255),
    PRIMARY KEY (`id_ltvsms`)
)ENGINE='. _MYSQL_ENGINE_ . 'DEFAULT CHARSET=utf8;';

foreach ($sql as $query){
    if(Db::getInstance()->execute($query) == false){
        return false;
    }
}