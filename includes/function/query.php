<?php
if(!defined('_CODE')){
    die('Access denied...');
}


function query($sql, $data = [], $check = false) {
    $db = Database::getInstance()->getConnection(); // Lấy kết nối từ singleton
    $result = false;

    try {
        $statement = $db->prepare($sql);
        if (!empty($data)) {
            $result = $statement->execute($data);
        } else {
            $result = $statement->execute();
        }
    } catch (Exception $exp) {
        echo "Lỗi SQL: " . $exp->getMessage() . "<br>";
        echo "File: " . $exp->getFile() . "<br>";
        echo "Dòng: " . $exp->getLine() . "<br>";
        die();
    }

    if ($check) {
        return $statement;
    }

    return $result;
}


function insert($table,$data){
    $key = array_keys($data);
    $list_name_columm = implode(',',$key);
    $list_value = ':'.implode(',:',$key);
    $sql = "INSERT INTO $table($list_name_columm) VALUES ($list_value)";

    $result = query($sql,$data);
    return $result;
}

function update($table,$data,$condition=''){
    $update='';
    foreach($data as $key => $value){
        $update.= $key .'=:'.$key.',';
    }
    
    $update = trim($update,',');

    if(!empty($condition)){
        $sql = "UPDATE $table SET $update WHERE $condition";
    }else{
        $sql = "UPDATE $table SET $update";
    }

    $result = query($sql,$data);
    return $result;
}

function delete($table,$condition=''){
    if(empty($condition)){
        $sql = "DELETE FROM $table";
    }else{
        $sql = "DELETE FROM $table WHERE $condition";
    }
    $result = query($sql);
    return $result;
}

function getRaw ($sql){
    $result = query($sql,'',true);
    if(is_object($result)){
        $dataFetch = $result->fetchAll((PDO::FETCH_ASSOC));
    }
    return $dataFetch;
}

// lấy 1 dòng dữ liệu
function oneRaw ($sql){
    $result = query($sql,'',true);
    if(is_object($result)){
        $dataFetch = $result->fetch((PDO::FETCH_ASSOC));
    }
    return $dataFetch;
}

function getRows ($sql){
    $result = query($sql,'',true);
    if(!empty($result)){
        return $result-> rowCount();
    }
    
}

function isPhone ($phone){
    $checkZero = false;

    //if ky tu dau tien la so 0
    if($phone[0] == '0'){
        $checkZero = true;
        $phone = substr($phone,'1');
    }

    // if phia sau co 9 so
    $checkNumber = false;
    if(isNumberInt($phone) && (strlen($phone) == 9 )){
        $checkNumber =true;
    }

    if($checkNumber && $checkZero){
        return true;
    }
    return false;
}