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
    <!-- <script>
    function winOpen(url){
      return window.open(url,getWinName(url), 'width=1200px,height=800px,toolbar=yes,location=yes,menubar=yes');
      console.log(getWinName(url))
    }

    function getWinName(url){
      return "win" + url.replace(/[^A-Za-z0-9\-\_]*/g,"");
    }
    </script> -->
</head>

<body>

    <div class="container">
        <div class="header">
        <div class="row">
                <div class="col-md-6">
                <a href="list.php"> <h2 style="color: #fff;">List User</h2> </a>
            
                </div>
                <div class="col-md-6">
                    <form action="search.php" method="get" class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search" name="keyword">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" style="margin-top: 10px">Search</button>
                    </form>
                </div>
            </div>
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
                        
                        <th scope="col">Username
                        <?php if($sort=='desc' && $attr=='username'){?>
                            <a href="?sort=asc&attr=username" class="fa fa-sort-alpha-asc">
                        <?php } else{?>
                            <a href="?sort=desc&attr=username" class="fa fa-sort-alpha-desc">
                        <?php }?>
                        </th>
                
                        <th scope="col">Full name
                        <?php if($sort=='desc' && $attr=='fullname'){?>
                            <a href="?sort=asc&attr=fullname" class="fa fa-sort-alpha-asc">
                        <?php } else{?>
                            <a href="?sort=desc&attr=fullname" class="fa fa-sort-alpha-desc">
                        <?php }?></th>

                        <th scope="col">Email</th>
                        <!-- <th scope="col">Số tiết vắng</th> -->
                        <th scope="col">Detail</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
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
                            <!-- <td><input type="text" style="width: 40px" id="vang" value="<?php echo $arr['vang']?>"></td> -->
                            <td>
                                <form></form>
                                <form action="userdetail.php" method="post">

                                <input type="hidden" name="id" value="<?php echo base64_encode($arr['id'])?>">
                  
                                <button type="submit" name="detail" class="btn btn-primary">
                                <i class="fa fa-eye"></i></button>  
                            </td>

                            <td>
                                <form></form>
                                <form action="edit.php" method="post">

                                <input type="hidden" name="id" 
                                value="<?php echo base64_encode(base64_encode($arr['id']))?>">

                                <button type="submit" name="edit" class="btn btn-primary">
                                <i class="fa fa-pencil-square-o"></i></button>
                            </td>
                        
                           
                            <td>
                                <form></form>
                                <form action="" method="post">
                                <input type="hidden" name="id" 
                                value="<?php echo base64_encode(base64_encode(base64_encode($arr['id'])))?>">
                                <?php 
                                     $token = random(6);
                                     $_SESSION['token' .$arr['id']] = $token;                 
                                ?>
                                <input type="hidden" name="<?php echo 'token' .$arr['id']?>" 
                                value="<?php echo $token ?>">       
                                <button type="submit" name="delete" class="btn btn-primary" 
                                onClick="return confirm('Nhấn oke để xoá')"><i class="fa fa-times"></i></button>
                            </td>
                               
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <div style="text-align: center">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_page; $i++) { ?>
                    <li <?php if ($page == $i) echo "class='active'"; ?>>

                    <?php if($sort == 'desc' && $attr == 'username'){?>

                        <a href="?sort=desc&attr=username&list=<?php echo $i; ?>">

                    <?php }else if($sort == 'asc' && $attr == 'username') {?>

                        <a href="?sort=asc&attr=username&list=<?php echo $i; ?>">

                    <?php }else if($sort == 'desc' && $attr == 'fullname') {?>

                        <a href="?sort=desc&attr=fullname&list=<?php echo $i; ?>">

                    <?php }else if($sort == 'asc' && $attr == 'fullname') {?>

                        <a href="?sort=asc&attr=fullname&list=<?php echo $i; ?>">
                
                    <?php }else{?>
                        <a href="list.php?list=<?php echo $i; ?>">
                    <?php }?>
            
                    <?php echo $i; ?></a></li>
                <?php } ?>
            </ul>
        </div>
       
        <div class="back" style="text-align: center">
            <button type="button" class="btn btn-info" onClick="javascript:history.go(-1)">Back</button>
            <a href="admin.php" class="btn btn-info">Add User ++</a>
            <!-- <a href="excel.php" class="btn btn-info">Export</a> -->
          
            <!-- <form action="list.php" method="post"> -->
                <!-- <button type="submit" class="btn btn-info" name="reset">Reset</button> -->
            <!-- </form> -->
            <!-- <a href="#" onclick="winOpen('list_diemdanh.php');" class="btn btn-info">Open</a> -->
       
        </div>
    </div>
    <script src="./public/js/jquery.js"></script>
    <script src="./public/js/script.js"></script>
</body>
</html>