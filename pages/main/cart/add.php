<?php
	include("../../../admincp/config/connection.php");
	session_start();
	$cart_id = $_SESSION['cart_id'];
	if(isset($_GET['id'])){
		$product_id= $_GET['id'];
		$quantity = (int)$_POST['quantity'];
		$sql_check_product = "SELECT * FROM tblcart_details WHERE cart_id = $cart_id AND product_id = $product_id";
		$result = mysqli_query($mysqli, $sql_check_product);
		if ($result) {
			if (mysqli_num_rows($result) > 0) {
				$sql_update_quantity = "UPDATE tblcart_details SET quantity = quantity + $quantity WHERE cart_id = $cart_id AND product_id = $product_id";
			$update_result = mysqli_query($mysqli, $sql_update_quantity);
			} else {
				$sql_addtocart="INSERT INTO tblcart_details(cart_id,product_id,quantity) VALUES('$cart_id','$product_id','$quantity')";
				mysqli_query($mysqli,$sql_addtocart);
			}
		}
		header('location: ../../../index.php?navigate=cart');
	}
?>