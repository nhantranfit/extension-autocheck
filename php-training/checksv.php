<?php
    require "./functions.php";
    $mssv = $_POST['mssv'];
    if(kiemTraMSSV($mssv) == true){
        echo 0;
    }
    else if(kiemTraMSSV_TonTai($mssv) == false){
        echo 1;
    }
    else{
        if(!isset($_COOKIE['token'])){
                setcookie('token', $mssv, time() + 30);
                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $date = date("Y-m-d");
                $query = "INSERT INTO diemdanh (mssv, time) VALUES('$mssv', '$date')";
                mysqli_query($conn, $query);
                echo 2;
            }
            else{
                echo 3;
            }
    }



    
    
 
