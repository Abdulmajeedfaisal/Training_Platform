<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/admin_new.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include '../components/admin_header.php'; ?>
    <!-- header section ends -->

    <!-- dashboard section starts  -->

    <section class="dashboard" style="direction: rtl;">

        <h1 class="heading">لوحة تحكم</h1>

        <div class="box-container">

            <div class="box">
                <?php
                $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = :id LIMIT 1");
                $select_profile->bindParam(':id', $admin_id);
                $select_profile->execute();
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <h3>مرحبا!</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href=" update.php" class="btn">تحديث الملف الشخصي</a>
            </div>

            <div class="box">
                <?php
                $select_listings = $conn->prepare("SELECT * FROM `property`");
                $select_listings->execute();
                $count_listings = $select_listings->rowCount();
                ?>
                <h3><?= $count_listings; ?></h3>
                <p>الدورات المنشورة</p>
                <a href="listings.php" class="btn">عرض الدورات</a>
            </div>

            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'company'");
                $select_users->execute();
                $count_users = $select_users->rowCount();
                ?>
                <h3><?= $count_users; ?></h3>
                <p>عدد المؤسسات</p>
                <a href="firm.php" class="btn">عرض المؤسسات</a>
            </div>
            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'client'");
                $select_users->execute();
                $count_users = $select_users->rowCount();
                ?>
                <h3><?= $count_users; ?></h3>
                <p>عدد العملاء</p>
                <a href="users.php" class="btn">عرض العملاء</a>
            </div>

            <div class="box">
                <?php
                $select_admins = $conn->prepare("SELECT * FROM `admins`");
                $select_admins->execute();
                $count_admins = $select_admins->rowCount();
                ?>
                <h3><?= $count_admins; ?></h3>
                <p>عدد مشرفين النظام</p>
                <a href="admins.php" class="btn">عرض المشرفين</a>
            </div>

            <div class="box">
                <?php
                $select_messages = $conn->prepare("SELECT * FROM `messages`");
                $select_messages->execute();
                $count_messages = $select_messages->rowCount();
                ?>
                <h3><?= $count_messages; ?></h3>
                <p>الرسائل</p>
                <a href="messages.php" class="btn">عرض الرسائل</a>
            </div>

        </div>

    </section>


    <!-- dashboard section ends -->




















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>