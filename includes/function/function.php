<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
if(!defined('_CODE')){
    die('Access denied...');
}

function layouts ($layoutName='header', $title = []){
    if(file_exists(_WEB_PATH_TEMPLATES. '/layout/' .$layoutName. '.php')){
        require_once _WEB_PATH_TEMPLATES. '/layout/' .$layoutName. '.php' ;
        
    }
    
}

//kiểm tra phương thức get 
function isGet(){
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        return true;
    }
    return false;
}
//kiểm tra phương thức post
function isPost(){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        return true;
    }
    return false;
}

//hàm Filter lọc dữ liệu
function filter(){
    $filterArr =[];
    if(isGet()){
        // xử lý cái dữ liệu trươc khi hiển thị ra
        //return $_GET;
        if(!empty($_GET)){
            foreach($_GET as $key => $value){
                $key = strip_tags($key); // Hàm sẽ trả về chuỗi đã loại bỏ hết các thẻ HTML và PHP.
                // FILTER_SANITIZE_SPECIAL_CHARS: Xóa các ký tự đặc biệt. 
                // FILTER_SANITIZE_FULL_SPECIAL_CHARS Có thể tắt báo giá mã hóa bằng cách sử dụng 
                // FILTER_FLAG_NO_ENCODE_QUOTES. FILTER_SANITIZE_STRING: Xóa thẻ / ký tự đặc biệt khỏi chuỗi.8 thg 6, 2021
                if(is_array($value)){
                    $filterArr[$key] = filter_input(INPUT_GET,$key,FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY); 

                }else{
                    $filterArr[$key] = filter_input(INPUT_GET,$key,FILTER_SANITIZE_SPECIAL_CHARS); 
                }
            }
        }
    }
    if(isPost()){   
        if(!empty($_POST)){
            foreach($_POST as $key => $value){
                $key = strip_tags($key); 
                if(is_array($value)){
                    $filterArr[$key] = filter_input(INPUT_POST,$key,FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY); 

                }else{
                    $filterArr[$key] = filter_input(INPUT_POST,$key,FILTER_SANITIZE_SPECIAL_CHARS); 
                }
            }
        }
    }
    return $filterArr;
}

//mail
function sendMail($to ,$subject, $content){

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'nmt15801pgram@gmail.com';                     //SMTP username
    $mail->Password   = 'zxbwtmpraxbiojcr';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('nmt15801pgram@gmail.com', 'Mailer');
    $mail->addAddress($to);     //Add a recipient
    //Content
    $mail->CharSet = "UTF-8";
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';



    //PHPMailer SSL certificare verify failed
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );



    $sendmail = $mail->send();
    if($sendmail){
        return $sendmail;
    }
    //echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}


//Kiểm tra email 
function isEmail($email){
    $checkemail = filter_var($email,FILTER_VALIDATE_EMAIL);
    return $checkemail;
}

//Kiểm tra số nguyên INT
function isNumberInt($number){
    $checknumber = filter_vaR($number,FILTER_VALIDATE_INT);
    return $checknumber;
}

//Kiểm tra số thực 
function isNumberFloat($number){
    $checknumber = filter_vaR($number,FILTER_VALIDATE_FLOAT);
    return $checknumber;
}



//thông báo lỗi
function getSmg($smg,$type ='success'){
    echo '<div id="flash-message" class="alert alert-'.$type.'">';
    echo $smg;
    echo'</div>';
}

// Hàm chuyển hướng
function redirect($path='index.php'){
    header("location: $path");
    exit;
}

//thông báo lỗi ở form 
function form_mess_error($filename,$beforeHTML = '',$afterHTML ='',$errors){
    return (!empty($errors[$filename])) ? $beforeHTML.reset($errors[$filename]).$afterHTML : null ;

}

// hàm hiển thị dữ liệu cũ
function old_data($filename,$old_data,$default = null){
    return (!empty($old_data[$filename])) ? $old_data[$filename] : null;
};

// hàm kiểm tra trạng thái đăng nhập

function slugify($string) {
    // Chuyển về chữ thường
    $string = mb_strtolower($string, 'UTF-8');
    // Thay khoảng trắng, ký tự đặc biệt bằng dấu '-'
    $string = preg_replace('/[^a-z0-9]+/u', '-', $string);
    // Xóa dấu '-' ở đầu và cuối
    return trim($string, '-');
}

function isLogin(){
    $checkLogin = false;
    if(getSession('logintoken')){
        $tokenLogin = getSession('logintoken');
        //Kiểm tra token có giống trong database hay không 
        $queryToken = oneRaw("SELECT user_Id FROM logintoken WHERE token ='$tokenLogin'");
        if(!empty($queryToken)){
            $checkLogin = true;
        }else{
            removeSession('logintoken');
        }
    }
    return $checkLogin;
}
