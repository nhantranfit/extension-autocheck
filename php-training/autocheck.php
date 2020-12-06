<?php 
session_start();
    require "./functions.php";
    $results = soSanh();
    $results2 = soSanh2();
        $results3 = kiemTra();
        if($results3 != 0){
            foreach($results2 as $result){
                $sql = "UPDATE `users`set `vang` = '0' WHERE id =".$result['id'];
                mysqli_query($conn, $sql);
                setcookie('token2', $result['id'], time() + 30);   
            }
            foreach($results as $result){   
                $sql = "UPDATE `users`set `vang` = '5' WHERE id =".$result['id'];
                mysqli_query($conn, $sql);    
            }
        }