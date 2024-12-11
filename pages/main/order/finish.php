<?php
  if(isset($_GET['vnp_Amount']))  {
    $Amount=$_GET['vnp_Amount'];
    $BankCode=$_GET['vnp_BankCode'];
    $BankTranNo=$_GET['vnp_BankTranNo'];
    $CardType=$_GET['vnp_CardType'];
    $OrderInfo=$_GET['vnp_OrderInfo'];
    $PayDate=$_GET['vnp_PayDate'];
    $TmnCode=$_GET['vnp_TmnCode'];
    $TransactionNo=$_GET['vnp_TransactionNo'];
    $order_code = $_SESSION['order_code'];
    $insert_vnpay = "INSERT INTO tblvnpay (Amount, BankCode, BankTranNo, CardType, OrderInfo, PayDate, TmnCode, TransactionNo, order_code)
    VALUES ('".$Amount."', '".$BankCode."', '".$BankTranNo."', '".$CardType."', '".$OrderInfo."', '".$PayDate."', '".$TmnCode."', '".$TransactionNo."', $order_code)";
    $query_insert = mysqli_query($mysqli, $insert_vnpay);
  } else if (isset($_GET['partnerCode'])) {
		$order_code = rand(1, 10000);
		$partnerCode = $_GET['partnerCode'];
		$orderId = $_GET['orderId'];
		$amount = $_GET['amount'];
		$orderInfo = $_GET['orderInfo'];
		$orderType = $_GET['orderType'];
		$transId = $_GET['transId'];
		$payType = $_GET['payType'];

		$insert_momo = "INSERT INTO tblmomo (PartnerCode, OrderId, Amount, OrderInfo, OrderType, TransId, PayType, order_code)
		VALUES ('$partnerCode', '$orderId', '$amount', '$orderInfo', '$orderType', '$transId', '$payType', '$order_code')";
		$momo_query = mysqli_query($mysqli, $insert_momo);
    if($momo_query) {
      $user_id = $_SESSION['user_id'];
	    $cart_id = $_SESSION['cart_id'];
      date_default_timezone_set('Asia/Ho_Chi_Minh');
	    $order_created_time = date("Y-m-d H:i:s");
      $order_receiver = $_SESSION['order_receiver'];
      $order_address = $_SESSION['order_address'];
	    $order_value = $_SESSION['total_value'];
      $order_phone = $_SESSION['order_phone'];
      $order_notes = isset($_SESSION['order_notes']) ? $_SESSION['order_notes'] : "";
      $order_payment = 'momo';
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
	}
?>
  <div class="container min-height-100">
    <div class="text-center mt-5">
      <p>Cảm ơn bạn đã đặt hàng, đơn hàng của bạn đang được xét duyệt !</p>
      <a class="btn btn-info" href="index.php?navigate=order_history">Xem lịch sử đơn hàng</a>
    </div>
  </div>