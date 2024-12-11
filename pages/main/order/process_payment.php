<?php
	include("../../../admincp/config/connection.php");
	require_once('config_vnpay.php');
	session_start();
	$user_id = $_SESSION['user_id'];
	$cart_id = $_SESSION['cart_id'];
    date_default_timezone_set('Asia/Ho_Chi_Minh');
	$order_created_time = date("Y-m-d H:i:s");
    $order_receiver = $_SESSION['order_receiver'];
    $order_address = $_SESSION['order_address'];
	$order_value = $_SESSION['total_value'];
    $order_phone = $_SESSION['order_phone'];
    $order_notes = isset($_SESSION['order_notes']) ? $_SESSION['order_notes'] : "";
    if(isset($_POST['cod'])){
		$order_payment = 'cod';
		$order_code = rand(1, 10000);
		$sql_insert_invoice = "INSERT INTO tblorder(user_id,order_created_time,order_address,order_value,order_phone,order_receiver,order_payment, order_code) VALUES('".$user_id."','".$order_created_time."','".$order_address."','".$order_value."','".$order_phone."', '".$order_receiver."', '".$order_payment."', '$order_code')";
		$insert_invoice_result = mysqli_query($mysqli, $sql_insert_invoice);
		$order_id = mysqli_insert_id($mysqli);
		$_SESSION['order_id'] = $order_id;
		$sql_cart = "SELECT * FROM tblcart_details 
		where tblcart_details.cart_id = $cart_id";
		$query_cart = mysqli_query($mysqli, $sql_cart);
		while ($row = mysqli_fetch_assoc($query_cart)) {
			$product_id = $row['product_id'];
			$quantity = $row['quantity'];
			$sql_product = "SELECT * FROM tblproduct
			where product_id = $product_id";
			$query_product = mysqli_query($mysqli, $sql_product);
			$row_product = mysqli_fetch_assoc($query_product);
			$purchase_price = $row_product['product_price'] * (100-$row_product['product_discount'])/100;
			$sql_insert_order_detail = "INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price) VALUES ('$order_id', '$product_id', '$quantity', '$order_code', '$purchase_price')";
			$insert_detail_result = mysqli_query($mysqli, $sql_insert_order_detail);
			$sql_update = "UPDATE tblproduct set product_quantity = product_quantity - $quantity where product_id = $product_id";
			$query_update = mysqli_query($mysqli, $sql_update);
		}
		$id_delete_cart = $_SESSION['cart_id'];
		$sql_delete_all_products = "DELETE FROM tblcart_details WHERE cart_id = $id_delete_cart";
		$delete_result = mysqli_query($mysqli, $sql_delete_all_products);
		unset($_SESSION['total_value']);
		header('location: ../../../index.php?navigate=finish');
    } else if(isset($_POST['vnpay'])) {
		$order_code = rand(1, 10000);
		$_SESSION['order_code'] = $order_code;
		$vnp_TxnRef = $order_code; //Mã giao dịch thanh toán tham chiếu của merchant
		$vnp_Amount = $order_value; // Số tiền thanh toán
		$vnp_Locale = "vn"; //Ngôn ngữ chuyển hướng thanh toán
		$vnp_BankCode = 'NCB'; //Mã phương thức thanh toán
		$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

		$inputData = array(
			"vnp_Version" => "2.1.0",
			"vnp_TmnCode" => $vnp_TmnCode,
			"vnp_Amount" => $vnp_Amount* 100,
			"vnp_Command" => "pay",
			"vnp_CreateDate" => date('YmdHis'),
			"vnp_CurrCode" => "VND",
			"vnp_IpAddr" => $vnp_IpAddr,
			"vnp_Locale" => $vnp_Locale,
			"vnp_OrderInfo" => "Thanh toan GD:" . (string)$vnp_TxnRef,
			"vnp_OrderType" => "other",
			"vnp_ReturnUrl" => $vnp_Returnurl,
			"vnp_TxnRef" => $vnp_TxnRef,
			"vnp_ExpireDate"=>$expire
		);

		if (isset($vnp_BankCode) && $vnp_BankCode != "") {
			$inputData['vnp_BankCode'] = $vnp_BankCode;
		}

		ksort($inputData);
		$query = "";
		$i = 0;
		$hashdata = "";
		foreach ($inputData as $key => $value) {
			if ($i == 1) {
				$hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
			} else {
				$hashdata .= urlencode($key) . "=" . urlencode($value);
				$i = 1;
			}
			$query .= urlencode($key) . "=" . urlencode($value) . '&';
		}

		$vnp_Url = $vnp_Url . "?" . $query;
		if (isset($vnp_HashSecret)) {
			$vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
			$vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
			$order_payment = 'vnpay';
			$sql_insert_invoice = "INSERT INTO tblorder(user_id,order_created_time,order_address,order_value,order_phone,order_receiver,order_payment, order_code) VALUES('".$user_id."','".$order_created_time."','".$order_address."','".$order_value."','".$order_phone."', '".$order_receiver."', '".$order_payment."', '$order_code')";
			$insert_invoice_result = mysqli_query($mysqli, $sql_insert_invoice);
			$order_id = mysqli_insert_id($mysqli);
			$_SESSION['order_id'] = $order_id;
			$sql_cart = "SELECT * FROM tblcart_details 
			where tblcart_details.cart_id = $cart_id";
			$query_cart = mysqli_query($mysqli, $sql_cart);
			while ($row = mysqli_fetch_assoc($query_cart)) {
				$product_id = $row['product_id'];
				$quantity = $row['quantity'];
				$sql_product = "SELECT * FROM tblproduct
				where product_id = $product_id";
				$query_product = mysqli_query($mysqli, $sql_product);
				$row_product = mysqli_fetch_assoc($query_product);
				$purchase_price = $row_product['product_price'] * (100-$row_product['product_discount'])/100;
				$sql_insert_order_detail = "INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price) VALUES ('$order_id', '$product_id', '$quantity', '$order_code', '$purchase_price')";
				$insert_detail_result = mysqli_query($mysqli, $sql_insert_order_detail);
				$sql_update = "UPDATE tblproduct set product_quantity = product_quantity - $quantity where product_id = $product_id";
				$query_update = mysqli_query($mysqli, $sql_update);
			}
			$id_delete_cart = $_SESSION['cart_id'];
			$sql_delete_all_products = "DELETE FROM tblcart_details WHERE cart_id = $id_delete_cart";
			$delete_result = mysqli_query($mysqli, $sql_delete_all_products);
			unset($_SESSION['total_value']);
		}
		header('Location: ' . $vnp_Url);
		die();
	}
?>