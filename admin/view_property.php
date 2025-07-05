<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:dashboard.php');
}

if (isset($_POST['delete'])) {

    $delete_id = $_POST['delete_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `property` WHERE id = :id");
    $verify_delete->bindParam(':id', $delete_id);
    $verify_delete->execute();

    if ($verify_delete->rowCount() > 0) {
        $select_images = $conn->prepare("SELECT * FROM `property` WHERE id = :id");
        $select_images->bindParam(':id', $delete_id);
        $select_images->execute();
        $delete_listings = $conn->prepare("DELETE FROM `property` WHERE id = :id");
        $delete_listings->bindParam(':id', $delete_id);
        $delete_listings->execute();
        $success_msg[] = 'Listing deleted!';
    } else {
        $warning_msg[] = 'Listing deleted already!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>property details</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

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

    <section class="view-property">

        <h1 class="heading">بيانات الدورة </h1>

        <?php
        $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :id ORDER BY date DESC LIMIT 1");
        $select_properties->bindParam(':id', $get_id);
        $select_properties->execute();
        if ($select_properties->rowCount() > 0) {
            while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                $property_id = $fetch_property['id'];
                $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);
                $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :user_id");
                $select_user->bindParam(':user_id', $fetch_property['user_id']);
                $select_user->execute();
                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :user_id");
                $select_employee->bindParam(':user_id', $fetch_property['employee_id']);
                $select_employee->execute();
                $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);
        ?>
                <div class="details" style="direction: rtl;">
                    <div class="swiper images-container">
                        <div class="swiper-wrapper">
                            <img src="<?= $get_data; ?>" alt="" class="swiper-slide">
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <h3 class="name"> <?= $fetch_property['property_name']; ?></h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i><span><?= $fetch_property['address']; ?></span></p>
                    <div class="info">
                        <p><i class="fas fa-user"> </i> <span><?= $fetch_user['name']; ?></span></p>
                        <p><i class="fas fa-phone"></i> <a href="tel:1234567890"> <?= $fetch_user['number']; ?></a></p>
                        <p><i class="fas fa-building"></i><span> <?= $fetch_property['type']; ?></span></p>
                        <p><i class="fas fa-calendar"></i><span> <?= $fetch_property['date']; ?></span></p>
                    </div>
                    <h3 class="title">تفاصيل</h3>
                    <div class="flex">
                        <div class="box">
                            <p><i>اسم مشرف الدورة التدريبية : </i><span><span class="fas" style="margin-right: .5rem;"></span><?= $fetch_employee['name']; ?></span></p>
                            <p><i>حالة الدورة التدريبية : </i><span><?= $fetch_property['status']; ?></span></p>
                        </div>
                    </div>
                    <h3 class="title">وصف </h3>
                    <p class="description"> <?= $fetch_property['description']; ?></p>
                    <form action="" method="post" class="flex-btn">
                        <input type="hidden" name="delete_id" value="<?= $property_id; ?>">
                        <input type="submit" value="حذف الدورة" name="delete" class="delete-btn" onclick="return confirm('delete this listing?');">
                    </form>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">لا يوجد دورة! <a href="listings.php" style="margin-top:1.5rem;" class="option-btn">إذهب الى قائمة المنشورات</a></p>';
        }
        ?>

    </section>


















    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

    <script>
        var swiper = new Swiper(".images-container", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            loop: true,
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 200,
                modifier: 3,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
            },
        });
    </script>

</body>

</html>