<?php
$options = [
    'cost' => 12,
];
$pw = password_hash("k-kyudo-2025", PASSWORD_BCRYPT, $options);
// $pw = password_hash("potlem0910", PASSWORD_DEFAULT, $options);
print $pw."<br>";
    //認証処理
    if(password_verify("potlem0910", $pw)){
        print '認証成功';
    }else{
        print '認証失敗';
    }