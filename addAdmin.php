<?php
    include("./config/connection.php");
    $pass = md5("Tna@1206");
    $sql = "INSERT INTO `tbladmin` (`admin_id`, `admin_loginname`, `admin_password`, `admin_email`, `admin_fullname`, `admin_address`, `admin_phone`) 
            VALUES(2, 'trinhngocanh', '$pass', 'tna@12062003@gmail.com', 'Trịnh Ngọc Anh', 'Hà Nội', '0332572108');";
    $mysqli->query($sql);
    echo "Thành công!";
?>