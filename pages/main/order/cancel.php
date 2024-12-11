<?php
    include("../../../admin/config/connection.php");
    session_start();
    $order_id = $_GET['id_cancel'];
    $sql_cancel = "UPDATE tblorder SET order_status = 2 WHERE order_id = $order_id";
    $query_cancel = mysqli_query($mysqli, $sql_cancel);
    $sql_order = "SELECT * FROM tblorder_details 
	where tblorder_details.order_id = $order_id";
	$query_order = mysqli_query($mysqli, $sql_order);
	while ($row = mysqli_fetch_assoc($query_order)) {
		$product_id = $row['product_id'];
		$quantity = $row['quantity'];
		$sql_update = "UPDATE tblproduct set product_quantity = product_quantity + $quantity where product_id = $product_id";
		$query_update = mysqli_query($mysqli, $sql_update);
	}
    header('location: ../../../index.php?navigate=order_history');
?>