<?php
@session_start();

require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


/**
 *
 * 1. Implement code that will handle the file with extension .fdb
 *
 * 2. Implement the parse code for this file and writer.
 * 
 */

//$ddb_name = '../database/sample';
//$db_user = 'ChanceNyasulu';
//$db_password = '12345';
//$collectionName = 'new_collection';
//
//$connect = new \Fdb\Connect\Connect(
//    $ddb_name,
//    $db_user,
//    $db_password
//);
//
///**
// * How to work with transactions.
// */
//
//try{
//    $data = [
//        'id' => time(),
//        'username' => 'John doe',
//        'email' => 'johndoe@gmail.com',
//        'created_at' => time(),
//        'password' => '50780'
//    ];
//
//    $transaction = $connect->transaction;
//
//// Start transaction by giving collections names.
//    $transaction->startTransaction([$collectionName]);
//
//// Transaction insertion
//    foreach ($data as $k=>$v) {
//        $transaction->setData($k, $v);
//    }
//    $transaction->validate($collectionName);
//    $transaction->save($collectionName);
//
//// Transaction updating data.
//    $transaction->setCondition('id',$data['id'],'=');
//    $transaction->validate($collectionName);
//    $transaction->setData('username','JohnDoe');
//    $transaction->update($collectionName);
//
////Transaction deleting data.
//    //$transaction->delete($collectionName);
//    $transaction->commit();
//}catch (Throwable $e){
//    $transaction->rollback();
//    print_r($e->getMessage());
//}
//
//dump($connect->select->all($collectionName));

$dashboard = new \Fdb\dashboard\Dashboard($_POST, $_GET, $_SERVER, $_SESSION);