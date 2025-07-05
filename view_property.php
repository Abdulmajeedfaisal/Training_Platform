<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:home.php');
}

include 'components/save_send.php';
if (isset($_POST['delete'])) {

    $delete_id = $_POST['property_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
    $verify_delete->execute([$delete_id]);

    if ($verify_delete->rowCount() > 0) {
        $select_images = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
        $select_images->execute([$delete_id]);
        while ($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)) {
            $image_01 = $fetch_images['image'];
        }
        $delete_saved = $conn->prepare("DELETE FROM `saved` WHERE property_id = ?");
        $delete_saved->execute([$delete_id]);
        $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE property_id = ?");
        $delete_requests->execute([$delete_id]);
        $delete_listing = $conn->prepare("DELETE FROM `property` WHERE id = ?");
        $delete_listing->execute([$delete_id]);
        $success_msg[] = 'listing deleted successfully!';
    } else {
        $warning_msg[] = 'listing deleted already!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Property</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

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

    <!-- view property section starts  -->

    <section class="view-property">

        <h1 class="heading">تفاصيل الدورة التدريبية</h1>

        <?php
        $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :id ORDER BY date DESC LIMIT 1");
        $select_properties->bindParam(':id', $get_id);
        $select_properties->execute();
        if ($select_properties->rowCount() > 0) {
            while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                $property_id = $fetch_property['id'];
                $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);
                $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                $select_user->bindParam(':id', $fetch_property['user_id']);
                $select_user->execute();
                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :employee_id");
                $select_employee->bindParam(':employee_id', $fetch_property['employee_id']);
                $select_employee->execute();
                $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);

                $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :property_id and user_id = :user_id");
                $select_saved->bindParam(':property_id', $fetch_property['id']);
                $select_saved->bindParam(':user_id', $user_id);
                $select_saved->execute();
        ?>
                <div class="details" style="direction: rtl;">
                    <div class="swiper images-container">
                        <div class="swiper-wrapper">
                            <img src="<?= $get_data; ?>" alt="" class="swiper-slide">
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <h3 class="name"> <?= $fetch_property['property_name']; ?></h3>
                    <p class="location"><i class=" fas fa-map-marker-alt" style="margin-left: 10px;"></i><a href="<?= $fetch_property['address']; ?>" target="_blank"><span><?= $fetch_property['address']; ?></span></a>
                    </p>
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
                            <?php if ($fetch_property['status'] != "not_available") { ?>
                                <p><i>حالة الدورة التدريبية : </i><span>متاحة</span></p>
                            <?php } else { ?>
                                <p><i>حالة الدورة التدريبية : </i><span>غير متاحة</span></p>
                            <?php } ?>
                        </div>
                    </div>
                    <h3 class="title">وصف </h3>
                    <p class="description"> <?= $fetch_property['description']; ?></p>
                    <form action="" method="post" class="flex-btn">
                        <input type="hidden" name="property_id" value="<?= $property_id; ?>">

                        <?php if ($user_id == $fetch_property['user_id'] || $user_id == $fetch_property['employee_id']) { ?>
                            <input type="submit" name="delete" value="حذف الدورة" class="btn" onclick="return confirm('delete this listing?');">
                        <?php } else { ?>
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
                            <a href="ask_for_requset.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تقديم طلب
                            </a>
                        <?php } ?>
                    </form>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">property not found! <a href="post_property.php" style="margin-top:1.5rem;" class="btn">add new</a></p>';
        }
        ?>

    </section>

    <!-- view property section ends -->










    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

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