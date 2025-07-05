<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
    header('location:login.php');
}

if (isset($_POST['delete'])) {

    $delete_id = $_POST['request_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `requests` WHERE id = :delete_id");
    $verify_delete->bindParam(':delete_id', $delete_id);
    $verify_delete->execute();

    if ($verify_delete->rowCount() > 0) {
        $delete_request = $conn->prepare("DELETE FROM `requests` WHERE id = :id");
        $delete_request->bindParam(':id', $delete_id);
        $delete_request->execute();
        $success_msg[] = 'تم حذف طلبك بنجاح !';
    } else {
        $warning_msg[] = 'الطلب تم حذفه مسبقا !';
    }
}
if (isset($_POST['accept'])) {

    $accept_id = $_POST['request_id'];
    $accept_id = filter_var($accept_id, FILTER_SANITIZE_STRING);

    $verify_accept = $conn->prepare("SELECT * FROM `requests` WHERE id = :accept_id");
    $verify_accept->bindParam(':accept_id', $accept_id);
    $verify_accept->execute();

    if ($verify_accept->rowCount() > 0) {
        $accept_request = $conn->prepare("UPDATE `requests` SET activation = '1' WHERE id = :accept_id");
        $accept_request->bindParam(':accept_id', $accept_id);
        $accept_request->execute();
        $success_msg[] = 'تم قبل الطلب!';
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
    <link rel="stylesheet" href="css/style.css">
    <style>
        .header .navbar .flex .logo {
            font-size: 2.2rem;
            color: black;
            line-height: 50px;
        }

        .header .navbar .flex ul li ul {
            position: absolute;
            width: 18rem;
            left: 0;
        }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>


    <?php if ($user_type == "client") { ?>
        <section class="listings" style="direction: rtl;">

            <h1 class="heading">الطلبات المقبولة</h1>

            <div class="box-container">
                <?php
                $total_images = 0;
                $select_saved_property = $conn->prepare("SELECT * FROM `requests` WHERE sender_id = :id AND activation = '1'");
                $select_saved_property->bindParam(':id', $user_id);
                $select_saved_property->execute();
                if ($select_saved_property->rowCount() > 0) {
                    while ($fetch_saved = $select_saved_property->fetch(PDO::FETCH_ASSOC)) {
                        $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :id ORDER BY date DESC");
                        $select_properties->bindParam(':id', $fetch_saved['property_id']);
                        $select_properties->execute();
                        if ($select_properties->rowCount() > 0) {
                            while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                                $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                                $select_user->bindParam(':id', $fetch_property['user_id']);
                                $select_user->execute();
                                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

                                $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);

                                $total_images = 1;

                                $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :id_property and user_id = :id_user");
                                $select_saved->bindParam(':id_property', $fetch_property['id']);
                                $select_saved->bindParam(':id_user', $user_id);
                                $select_saved->execute();
                ?>
                                <form action="" method="POST">
                                    <div class="box">
                                        <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
                                        <?php
                                        if ($select_saved->rowCount() > 0) {
                                        ?>
                                            <button type="submit" name="save" class="save" style="direction: ltr;"><i class="fas fa-heart"></i><span>تم
                                                    الحفظ</span></button>
                                        <?php
                                        } else {
                                        ?>
                                            <button type="submit" name="save" class="save" style="direction: ltr;"><i class="far fa-heart"></i><span>حفظ</span></button>
                                        <?php
                                        }
                                        ?>
                                        <div class="thumb">
                                            <p class="total-images" style="direction: ltr;"><i class="far fa-image"></i><span><?= $total_images; ?></span></p>
                                            <img src="<?= $get_data; ?>" alt="">
                                        </div>
                                        <div class="price">عدد المتدربين <i class="fa-solid fa-person-circle-check"></i> :
                                            <span style="color:black;"><?= $fetch_property['student_count']; ?></span>
                                        </div>
                                        <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                                        <p class="location"><i class="fas fa-map-marker-alt"></i>
                                            <span><?= $fetch_property['address']; ?></span>
                                        </p>
                                        <div class="admin">
                                            <h3><?= substr($fetch_user['name'], 0, 1); ?></h3>
                                            <div>
                                                <p><?= $fetch_user['name']; ?></p>
                                                <span><?= $fetch_property['date']; ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-btn">
                                            <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض تفاصيل
                                                الدورة</a>
                                        </div>
                                    </div>
                                </form>
                <?php
                            }
                        } else {
                            echo '<p class="empty">لا يوجد طلب بعد<a href="listings.php" style="margin-top:1.5rem;" class="btn">إطلب الان</a></p>';
                        }
                    }
                } else {
                    echo '<p class="empty">لا يوجد طلب بعد<a href="listings.php" style="margin-top:1.5rem;" class="btn">إطلب الان</a></p>';
                }
                ?>

            </div>

        </section>
    <?php } elseif ($user_type == "company") { ?>
        <section class="requests" style="direction: rtl;">
            <h1 class="heading">الطلبات المقبولة</h1>
            <div class="box-container">

                <?php
                $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE receiver_id = :id AND activation = '1'");
                $select_requests->bindParam(':id', $user_id);
                $select_requests->execute();
                if ($select_requests->rowCount() > 0) {
                    while ($fetch_request = $select_requests->fetch(PDO::FETCH_ASSOC)) {

                        $select_sender = $conn->prepare("SELECT * FROM `users` WHERE id = :sender_id");
                        $select_sender->bindParam(':sender_id', $fetch_request['sender_id']);
                        $select_sender->execute();
                        $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);

                        $select_property = $conn->prepare("SELECT * FROM `property` WHERE id = :id");
                        $select_property->bindParam(':id', $fetch_request['property_id']);
                        $select_property->execute();
                        $fetch_property = $select_property->fetch(PDO::FETCH_ASSOC);
                        $select_emp = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                        $select_emp->bindParam(':id', $fetch_property['employee_id']);
                        $select_emp->execute();
                        $fetch_employees = $select_emp->fetch(PDO::FETCH_ASSOC);
                ?>
                        <div class="box">
                            <p>اسم مرسل الطلب: <span><?= $fetch_sender['name']; ?></span></p>
                            <p>رقمة : <a href="tel:<?= $fetch_sender['number']; ?>"><?= $fetch_sender['number']; ?></a></p>
                            <p>ايميل المرسل: <a href="mailto:<?= $fetch_sender['email']; ?>"><?= $fetch_sender['email']; ?></a></p>
                            <p>الدورة المطلوبة : <span><?= $fetch_property['property_name']; ?></span></p>
                            <p>اسم شرف الدورة: <span><?= $fetch_employees['name']; ?></span></p>
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
                ?>

            </div>
        </section>
    <?php } else {  ?>
        <section class="requests" style="direction: rtl;">
            <h1 class="heading">الطلبات المقبولة</h1>
            <div class="box-container">

                <?php
                $count_properties = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :id");
                $count_properties->bindParam(':id', $user_id);
                $count_properties->execute();
                while ($fetch_employee = $count_properties->fetch(PDO::FETCH_ASSOC)) {
                    $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE property_id = :request_id AND activation = '1'");
                    $select_requests->bindParam(':request_id', $fetch_employee['id']);
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
                    } elseif ($count_properties->rowCount() == 0 || $select_requests->rowCount() == 0) {
                        echo '<p class="empty">لا يوجد طلب بعد !</p>';
                    }
                }
                ?>

            </div>
        </section>
    <?php } ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>