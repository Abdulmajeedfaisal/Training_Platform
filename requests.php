<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
    if ($user_type == "client") {
        header("location: ./");
    }
} else {
    $user_id = '';
    $user_type = '';
    header('location:login.php');
}

if (isset($_POST['delete'])) {

    $delete_id = $_POST['request_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `requests` WHERE id = :id");
    $verify_delete->bindParam(':id', $delete_id);
    $verify_delete->execute();
    $fetch_delete = $verify_delete->fetch(PDO::FETCH_ASSOC);


    if ($verify_delete->rowCount() > 0) {
        $delete_information = $conn->prepare("DELETE FROM `files` WHERE property_id = :dele_id AND student_id = :users_id");
        $delete_information->bindParam(':dele_id', $fetch_delete['property_id']);
        $delete_information->bindParam(':users_id', $fetch_delete['sender_id']);
        $delete_information->execute();
        $delete_request = $conn->prepare("DELETE FROM `requests` WHERE id = :delete_id");
        $delete_request->bindParam(':delete_id', $delete_id);
        $delete_request->execute();
        $success_msg[] = 'تم حذف الطلب بنجاح !';
    } else {
        $warning_msg[] = 'الطلب تم حذفه مسبقا !';
    }
}
if (isset($_POST['accept'])) {

    $accept_id = $_POST['request_id'];
    $accept_id = filter_var($accept_id, FILTER_SANITIZE_STRING);

    $verify_accept = $conn->prepare("SELECT * FROM `requests` WHERE id = :id");
    $verify_accept->bindParam(':id', $accept_id);
    $verify_accept->execute();

    if ($verify_accept->rowCount() > 0) {
        $accept_request = $conn->prepare("UPDATE `requests` SET activation = '1' WHERE id = :id");
        $accept_request->bindParam(':id', $accept_id);
        $accept_request->execute();
        $success_msg[] = 'تم الموافقة على الطلب!';
    } else {
        $warning_msg[] = 'الطلب تم قبوله مسبقا!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>requests</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">
    <style>
        .btn1 {
            background-color: #198754;
        }

        .btn2 {
            background-color: #0488a2d9;
        }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="requests" style="direction: rtl;">

        <h1 class="heading">جميع الطلبات</h1>
        <?php if ($user_type == "company") { ?>
            <div class="box-container">

                <?php
                $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE receiver_id = :user_id AND activation = '0'");
                $select_requests->bindParam(':user_id', $user_id);
                $select_requests->execute();
                if ($select_requests->rowCount() > 0) {
                    while ($fetch_request = $select_requests->fetch(PDO::FETCH_ASSOC)) {

                        $select_sender = $conn->prepare("SELECT * FROM `users` WHERE id = :sender_id");
                        $select_sender->bindParam(':sender_id', $fetch_request['sender_id']);
                        $select_sender->execute();
                        $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);

                        $select_property = $conn->prepare("SELECT * FROM `property` WHERE id = :property_id");
                        $select_property->bindParam(':property_id', $fetch_request['property_id']);
                        $select_property->execute();
                        $fetch_property = $select_property->fetch(PDO::FETCH_ASSOC);
                        $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :employee_id");
                        $select_employee->bindParam(':employee_id', $fetch_property['employee_id']);
                        $select_employee->execute();
                        $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);
                ?>
                        <div class="box">
                            <p>اسم مرسل الطلب: <span><?= $fetch_sender['name']; ?></span></p>
                            <p>رقمة : <a href="tel:<?= $fetch_sender['number']; ?>"><?= $fetch_sender['number']; ?></a></p>
                            <p>ايميل المرسل: <a href="mailto:<?= $fetch_sender['email']; ?>"><?= $fetch_sender['email']; ?></a></p>
                            <p>مشرف الدورة : <span><?= $fetch_employee['name']; ?></span></p>
                            <p>الدورة المطلوبة : <span><?= $fetch_property['property_name']; ?></span></p>
                            <form action="" method="POST">
                                <input type="hidden" name="request_id" value="<?= $fetch_request['id']; ?>">
                                <input type="submit" value="قبول الطلب" class="btn btn1" name="accept">
                                <a href="show_information.php?get_id=<?= $fetch_property['id']; ?>" class="btn btn2">عرض وثيقة
                                    التقديم</a>
                                <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة</a>
                                <input type="submit" value="حذف الطلب" class="btn" onclick="return confirm('remove this request?');" name="delete">
                            </form>
                        </div>
                <?php
                    }
                } else {
                    echo '<p class="empty">لا يوجد طلب بعد !</p>';
                }
                ?>

            </div>
        <?php } else { ?>
            <div class="box-container">

                <?php
                $count_properties = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :employee_id");
                $count_properties->bindParam(':employee_id', $user_id);
                $count_properties->execute();
                while ($fetch_employee = $count_properties->fetch(PDO::FETCH_ASSOC)) {
                    $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE property_id = :request_id AND activation = '0'");
                    $select_requests->bindParam(':request_id', $fetch_employee['id']);
                    $select_requests->execute();
                    if ($select_requests->rowCount() > 0) {
                        while ($fetch_request = $select_requests->fetch(PDO::FETCH_ASSOC)) {

                            $select_sender = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                            $select_sender->bindParam(':id', $fetch_request['sender_id']);
                            $select_sender->execute();
                            $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);

                            $select_property = $conn->prepare("SELECT * FROM `property` WHERE id = :id");
                            $select_property->bindParam(':id', $fetch_request['property_id']);
                            $select_property->execute();
                            $fetch_property = $select_property->fetch(PDO::FETCH_ASSOC);
                ?>
                            <div class="box">
                                <p>اسم مرسل الطلب: <span><?= $fetch_sender['name']; ?></span></p>
                                <p>رقمة : <a href="tel:<?= $fetch_sender['number']; ?>"><?= $fetch_sender['number']; ?></a></p>
                                <p>ايميل المرسل: <a href="mailto:<?= $fetch_sender['email']; ?>"><?= $fetch_sender['email']; ?></a></p>
                                <p>الدورة المطلوبة : <span><?= $fetch_property['property_name']; ?></span></p>
                                <form action="" method="POST">
                                    <input type="hidden" name="request_id" value="<?= $fetch_request['id']; ?>">
                                    <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة</a>
                                    <input type="submit" value="حذف الطلب" class="btn" onclick="return confirm('remove this request?');" name="delete">
                                </form>
                            </div>
                <?php
                        }
                    } else {
                        echo '<p class="empty">لا يوجد طلب بعد !</p>';
                    }
                }
                ?>

            </div>
        <?php } ?>
    </section>






















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>