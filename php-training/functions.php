<?php
$conn = mysqli_connect('localhost', 'root', '', 'userlogin');

$id = isset($_GET['id']) ? $_GET['id'] : '';
$username = "";
$fullname = "";
$email    = "";
$errors   = array();

function searchUser($keyword)
{
	global $conn;
	$query = ("SELECT * FROM users WHERE username LIKE '%$keyword%' or fullname LIKE '%$keyword%' or email LIKE '%$keyword%'");
	$result = mysqli_query($conn, $query);
	return $result;
}

function register()
{
	global $conn, $errors, $username, $fullname, $email;

	$username    =  escape($_POST['username']);
	$fullname    =  escape($_POST['fullname']);
	$email       =  escape($_POST['email']);
	$password_1  =  escape($_POST['password_1']);
	$password_2  =  escape($_POST['password_2']);

	if (empty($username)) {
		array_push($errors, "Username is required");
	}
	else{
		checkUsername($username);
	}
	if (empty($fullname)) {
		array_push($errors, "Fullname is required");
	}
	if (empty($email)) {
		array_push($errors, "Email is required");
	}
	else{
		checkEmail($email);
	}
	if (empty($password_1)) {
		array_push($errors, "Password is required");
	}
	if ($password_1 != $password_2) {
		array_push($errors, "The two passwords do not match");
	}

	if (count($errors) == 0) {
		$password = md5($password_1);

		if (isset($_POST['user_type']) && $_POST['user_type'] != '') {
			if (!empty($_FILES['image']['name'])) {
				$user_type = escape($_POST['user_type']); 
				$image = 'public/images/' . basename($_FILES['image']['name']);
				$imageType = pathinfo($image, PATHINFO_EXTENSION);
				$allowType = array('jpg', 'png');
				if(in_array($imageType, $allowType) && $_FILES['image']['size'] < 2 * 1024 * 1024){
					if (is_uploaded_file($_FILES['image']['tmp_name']) && move_uploaded_file($_FILES['image']['tmp_name'], $image)){
						$images = basename($image);
						$query = "INSERT INTO users (username,fullname, email, user_type, password, image) 
							  VALUES('$username', '$fullname', '$email', '$user_type', '$password', '$images')";
						mysqli_query($conn, $query);
						
						$_SESSION['success']  = "New user successfully created!!";
						header('location: list.php');
					}
				}
				else{
					$_SESSION['success']  = "Add user failed";
				}
				
			}
			else{
				$_SESSION['success']  = "Select user image";
			}
		} else {
			$query = "INSERT INTO users (username, fullname, email, user_type, password) 
					  VALUES('$username', '$fullname', '$email', 'user', '$password')";
			mysqli_query($conn, $query);

			$logged_in_user_id = mysqli_insert_id($conn);

			$_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
			$_SESSION['success']  = "You are now logged in";
			header('location: index.php');
		}
	}
}


// function edit()
// {
// 	global $conn, $errors, $username, $fullname, $email;
// 	$username    =  escape($_POST['username1']);
// 	$fullname    =  escape($_POST['fullname1']);
// 	$email       =  escape($_POST['email1']);

// 	mysqli_query($conn, "UPDATE `users` SET `username` = '$username', `fullname` = '$fullname', `email`='$email' WHERE `username` = '$username'");

// 	$_SESSION['success']  = "Change successfully";
// 	// // header("Refresh:2; url=page2.php");
// 	if (isset($_COOKIE["user"]) and isset($_COOKIE["pass"])) {
// 		setcookie("user", '', time() - 3600);
// 		setcookie("pass", '', time() - 3600);
// 	}
// 	header('location: home.php');
// }

function editId($id)
{
	global $conn, $id, $errors, $username, $fullname, $email;
	
	$fullname = escape($_POST['fullname']);
	$email = escape($_POST['email']);

	if (empty($fullname)) {
		array_push($errors, "Fullname is required");
	}
	
	if (empty($email)) {
		array_push($errors, "Email is required");
	}
	
	$result = getUserById($id);
	if($email != $result['email']){
		checkEmail($email);
	}
	
	if (count($errors) == 0) {
		if (!empty($_FILES['image']['name'])){
		
			$image = 'public/images/' . basename($_FILES['image']['name']);
			$imageType = pathinfo($image, PATHINFO_EXTENSION);
			$allowType = array('jpg', 'png');
			if(in_array($imageType, $allowType) && $_FILES['image']['size'] < 2 * 1024 * 1024){
				if (is_uploaded_file($_FILES['image']['tmp_name']) && move_uploaded_file($_FILES['image']['tmp_name'], $image)) 
			{
				$images = basename($image);
				mysqli_query($conn, "UPDATE `users` SET `fullname` = '$fullname', `email`='$email', `image` = '$images' WHERE `id` = '$id'");

				$_SESSION['success']  = "Change successfully";
				
				if (isset($_COOKIE["user"]) and isset($_COOKIE["pass"])) {
					setcookie("user", '', time() - 3600);
					setcookie("pass", '', time() - 3600);
				}
				
				header("location: list.php");
			}
			}
			else{
				$_SESSION['success']  = "File isn't in the correct format or file large more 2MB";
			}
			}
			
		else{
			mysqli_query($conn, "UPDATE `users` SET `fullname` = '$fullname', `email`='$email' WHERE `id` = '$id'");

				$_SESSION['success']  = "Change successfully";
		
				if (isset($_COOKIE["user"]) and isset($_COOKIE["pass"])) {
					setcookie("user", '', time() - 3600);
					setcookie("pass", '', time() - 3600);
				}
				header("location: list.php");
		}
	}	
}

function deleteUser($id){
	global $conn;
	$query = "DELETE FROM users WHERE id = '$id'";
	mysqli_query($conn,$query);
	header("location: list.php");
}

function getUsername(){
	global $conn;
	$query = "SELECT username FROM users";
	$results = mysqli_query($conn,$query);
	return $results;
}

function checkUsername($username){
	global $conn, $errors;
	$results = getUsername();
	$flag = false;
	foreach($results as $result){
		if($result['username'] == $username){
			$flag = true;
		}
	}
	if($flag == true){
		array_push($errors, 'Username is already exists');
	}
}

function soSanh(){
	global $conn;
	$query = "SELECT users.id FROM users WHERE (((Exists (SELECT diemdanh.mssv FROM diemdanh WHERE (users.id = diemdanh.mssv)))=false))";
	$results = mysqli_query($conn,$query);
	return $results;
}

function soSanh2(){
	global $conn;
	$query = "SELECT users.id FROM users WHERE (((Exists (SELECT diemdanh.mssv FROM diemdanh WHERE (users.id = diemdanh.mssv)))= true))";
	$results = mysqli_query($conn,$query);
	return $results;
}

function kiemTra(){
	global $conn;
	$query = "SELECT * FROM diemdanh";
	$results = mysqli_query($conn,$query);
	$rowCount = mysqli_num_rows($results);
	return $rowCount;
}
function getMSSV(){
	global $conn;
	$query = "SELECT mssv FROM diemdanh";
	$results = mysqli_query($conn,$query);
	return $results;
}

function kiemTraMSSV($mssv){
	global $conn, $errors;
	$results = getMSSV();
	$flag = false;
	foreach($results as $result){
		if($result['mssv'] == $mssv){
			$flag = true;
			break;
		}
	}
	if($flag == true){
		return true;
	}
	else{
		return false;
	}	
}

function kiemTraMSSV_TonTai($mssv){
	global $conn, $errors;
	$results = getUsers();
	$flag = false;
	foreach($results as $result){
		if($result['id'] == $mssv){
			$flag = true;
			break;
		}
	}
	if($flag == true){
		return true;
	}
	else{
		return false;
	}	
}

function getEmail(){
	global $conn;
	$query = "SELECT email FROM users";
	$results = mysqli_query($conn,$query);
	return $results;
}

function checkEmail($email){
	global $conn, $errors;
	$results = getEmail();
	$flag = false;
	foreach($results as $result){
		if($result['email'] == $email){
			$flag = true;
		}
	}
	if($flag == true){
		array_push($errors, 'Email is already exists');
	}
}

function getUsers(){
	global $conn;
	$query = "SELECT * FROM users";
	$results = mysqli_query($conn,$query);
	return $results;
}

function getUserById($id)
{
	global $conn;
	$query = "SELECT * FROM users WHERE id=" . $id;
	$result = mysqli_query($conn, $query);
	$user = mysqli_fetch_assoc($result);
	return $user;
}


function escape($val)
{
	global $conn;
	return mysqli_real_escape_string($conn, trim($val));
}

function display_error()
{
	global $errors;

	if (count($errors) > 0) {
		echo '<div class="error">';
		foreach ($errors as $error) {
			echo $error . '<br>';
		}
		echo '</div>';
	}
}

function isLoggedIn()
{
	if (isset($_SESSION['user'])) {
		return true;
	} else {
		return false;
	}
}

// log user out if logout button clicked
if (isset($_GET['logout'])) {
	session_destroy();
	unset($_SESSION['user']);

	if (isset($_COOKIE["user"]) and isset($_COOKIE["pass"])) {
		setcookie("user", '', time() - 3600);
		setcookie("pass", '', time() - 3600);
	}

	header("location: login.php");
}
if (isset($_POST['login_btn'])) {
	login();
}

// LOGIN USER
function login()
{
	global $conn, $username, $errors;

	// grap form values
	$username = escape($_POST['username']);
	$password = escape($_POST['password']);

	// make sure form is filled properly
	if (empty($username)) {
		array_push($errors, "Username is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}

	// attempt login if no errors on form
	if (count($errors) == 0) {
		$password = md5($password);

		$query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
		$query2 = "SELECT * FROM users WHERE username='$username' AND password='$password'";
		$results = mysqli_query($conn, $query);
		$results2 = mysqli_query($conn, $query2);
		$row = mysqli_fetch_array($results2);
		if (mysqli_num_rows($results) == 1) { // user found
			// check if user is admin or user
			$logged_in_user = mysqli_fetch_assoc($results);

			if ($logged_in_user['user_type'] == 'admin') {

				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "You are now logged in";

				if (isset($_POST['remember'])) {
					//thiết lập cookie username và password
					setcookie("user", $row['username'], time() + (86400 * 30));
					setcookie("pass", $row['password'], time() + (86400 * 30));
				}
				header('location: home.php');
			} else {
				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "You are now logged in";

				if (isset($_POST['remember'])) {
					//thiết lập cookie username và password
					setcookie("user", $row['username'], time() + (86400 * 30));
					setcookie("pass", $row['password'], time() + (86400 * 30));
				}
				header('location: index.php');
			}
		} else {
			array_push($errors, "Wrong username/password combination");
		}
	}
}

function isAdmin()
{
	if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin') {
		return true;
	} else {
		return false;
	}
}

function random($soKiTu){
	$mang = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',0, 1, 2, 3, 4, 6, 7, 8, 9);
	$kq = '';
	for($i =1; $i <= $soKiTu; $i++){
		$kq = $kq . $mang[rand(0, count($mang) -1)];
	}
	return md5($kq);
}




