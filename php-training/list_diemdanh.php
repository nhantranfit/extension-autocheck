<?php
session_start();
include('functions.php');

if (isAdmin()) {
    
    $results = [];
    $id = isset($_GET['id']) ? $_GET['id'] : '';

    $page = 1; //khởi tạo trang ban đầu
    $limit = 3; //số bản ghi trên 1 trang (2 bản ghi trên 1 trang)


    $arrs_list = mysqli_query($conn, "SELECT id from users");
    $total_record = mysqli_num_rows($arrs_list); //tính tổng số bản ghi có trong bảng khoahoc

    $total_page = ceil($total_record / $limit); //tính tổng số trang sẽ chia

    //xem trang có vượt giới hạn không:
    if (isset($_GET["list"]))
        $page = $_GET["list"]; //nếu biến $_GET["page"] tồn tại thì trang hiện tại là trang $_GET["page"]
    if ($page < 1) $page = 1; //nếu trang hiện tại nhỏ hơn 1 thì gán bằng 1
    if ($page > $total_page) $page = $total_page; //nếu trang hiện tại vượt quá số trang được chia thì sẽ bằng trang cuối cùng

    //tính start (vị trí bản ghi sẽ bắt đầu lấy):
    $start = ($page - 1) * $limit;

    //lấy ra danh sách và gán vào biến $arrs:

    $sort = '';
    $attr = '';
    $arrs = [];

    if(isset($_GET['sort']) && isset($_GET['attr'])){
        $sort = $_GET['sort'];
        $attr = $_GET['attr'];
        if(($sort == 'desc' && ($attr == 'username' || $attr == 'fullname')) || 
        ($sort == 'asc' && ($attr == 'username' || $attr == 'fullname')))
        {
            $sql = "SELECT * FROM users ORDER BY $attr $sort limit $start,$limit";
        }  
        else{
            $sql = "SELECT * from users limit $start,$limit";
        }         
    }
    else{
        $sql = "SELECT * from users limit $start,$limit";
    }
        if(isset($_POST['reset'])){  
                $query = "SELECT * FROM diemdanh";
                $results = mysqli_query($conn, $query);
                if(!isset($_COOKIE['token2'])){
                    foreach($results as $result){
                        $query2 = "DELETE FROM diemdanh WHERE mssv=" .$result['mssv'];
                        mysqli_query($conn,$query2);	
                    }
                    $sql2 = "UPDATE `users`set `vang` = '0'";
                    mysqli_query($conn, $sql2);
                    $sql = "SELECT * from users limit $start,$limit";
                }
                else{
                    array_push($errors, "Reset sau 30s");
                }  
        }
    
 
    $arrs = mysqli_query($conn,$sql);

    if(isset($_POST['delete']) && isset($_POST['id'])){
            $id = base64_decode(base64_decode(base64_decode($_POST['id'])));
            $result = getUserById($id);

            if($result['user_type'] != 'admin'){
                
                if($_SESSION['token' .$id] == $_POST['token' .$id]){
                    deleteUser($id);
                }
                else{
                    header("location: list.php");
                }       
            }
            else{
                $_SESSION['success'] = "Don't delete admin";
            }
    }
}
else{
    header("location: login.php");
}
?>

<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/styles.css">
</head>

<body>

    <div class="container">
        <div class="header">
        <a href="list.php"> <h2 style="color: #fff;">List User</h2> </a>
        </div>

        <form>
            <?php echo display_error(); ?>
            <?php if (isset($_SESSION['success'])) : ?>
                <div class="error success">
                    <h3>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </h3>
                </div>
            <?php endif ?>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">AVT</th>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Số tiết vắng</th>
                     
                    </tr>   
                </thead>
                <tbody>
                    <?php foreach ($arrs as $arr) : ?>
                        <tr scope="row">
                            <td><img src="./public/images/<?php echo $arr['image'];?>" class="img-fluid" alt=""     style="width:50px; height:50px;"></td>
                            <td><?php echo $arr['id']; ?></td>
                            <td><?php echo $arr['username']; ?></td>
                            <td><?php echo $arr['fullname']; ?></td>
                            <td><?php echo $arr['email']; ?></td>
                            <td><input type="text" style="width: 40px" id="vang" value="<?php echo $arr['vang']?>"></td>
                               
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <div class="back" style="text-align: center">
            <a href="excel.php" class="btn btn-info">Export</a>
            <form action="list_diemdanh.php" method="post">
                <button type="submit" class="btn btn-info" name="reset">Reset</button>
            </form>
        </div>
    </div>
   
</body>
</html>