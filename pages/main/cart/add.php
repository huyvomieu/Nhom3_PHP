<?php
    include("../../../admincp/config/connection.php");
    session_start();
    $cart_id = $_SESSION['cart_id'];
    if (isset($_GET['id'])) {
        $product_id = $_GET['id'];
        $quantity = (int)$_POST['quantity'];

        // Lấy số lượng hiện tại từ database
        $sql_check_stock = "SELECT product_quantity FROM tblproduct WHERE product_id = $product_id";
        $result_stock = mysqli_query($mysqli, $sql_check_stock);

        if ($result_stock) {
            $row_stock = mysqli_fetch_array($result_stock);
            $current_stock = (int)$row_stock['product_quantity'];

            // Kiểm tra tổng số lượng sản phẩm trong giỏ hàng (bao gồm cả số lượng mới thêm)
            $sql_check_cart = "SELECT SUM(quantity) AS total_quantity 
                               FROM tblcart_details 
                               WHERE cart_id = $cart_id AND product_id = $product_id";
            $result_cart = mysqli_query($mysqli, $sql_check_cart);
            $row_cart = mysqli_fetch_array($result_cart);
            $total_quantity_in_cart = (int)$row_cart['total_quantity'] + $quantity;

            // Kiểm tra nếu tổng số lượng yêu cầu trong giỏ lớn hơn số lượng trong kho
            if ($total_quantity_in_cart > $current_stock) {
                echo "<script>
                    alert('Số lượng sản phẩm không đủ trong kho! Vui lòng chọn số lượng nhỏ hơn.');
                    window.history.back();
                </script>";
            } else {
                // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
                $sql_check_product = "SELECT * FROM tblcart_details WHERE cart_id = $cart_id AND product_id = $product_id";
                $result = mysqli_query($mysqli, $sql_check_product);

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        // Cập nhật số lượng nếu sản phẩm đã tồn tại trong giỏ hàng
                        $sql_update_quantity = "UPDATE tblcart_details 
                            SET quantity = quantity + $quantity 
                            WHERE cart_id = $cart_id AND product_id = $product_id";
                        $update_result = mysqli_query($mysqli, $sql_update_quantity);
                    } else {
                        // Thêm sản phẩm mới vào giỏ hàng
                        $sql_addtocart = "INSERT INTO tblcart_details(cart_id, product_id, quantity) 
                            VALUES('$cart_id', '$product_id', '$quantity')";
                        mysqli_query($mysqli, $sql_addtocart);
                    }
                }

                // Chuyển hướng đến trang giỏ hàng
                header('location: ../../../index.php?navigate=cart');
            }
        } else {
            echo "<script>
                alert('Lỗi khi kiểm tra kho sản phẩm!');
                window.history.back();
            </script>";
        }
    }
?>
