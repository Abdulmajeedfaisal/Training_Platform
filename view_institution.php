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
    <link rel="stylesheet" href="css/sstyle.css">
    <link rel="stylesheet" href="css/Style1.css">
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

    .listings .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        align-items: flex-start;
        justify-content: center;
        gap: 1.5rem;
    }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- view property section starts  -->

    <section class="view-property">

        <h1 class="heading">تفاصيل المؤسسة</h1>

        <?php
        $select_firm = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'company' AND id = :id ORDER BY date DESC LIMIT 1");
        $select_firm->bindParam(':id', $get_id);
        $select_firm->execute();
        if ($select_firm->rowCount() > 0) {
            while ($fetch_company = $select_firm->fetch(PDO::FETCH_ASSOC)) {

                $property_id = $fetch_company['id'];
                $select_user = $conn->prepare("SELECT * FROM `frims` WHERE frim_id = :company_id LIMIT 1");
                $select_user->bindParam(':company_id', $fetch_company['id']);
                $select_user->execute();
                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                $select_property = $conn->prepare("SELECT * FROM `property` WHERE user_id = :id");
                $select_property->bindParam(':id', $fetch_company['id']);
                $select_property->execute();
                $total_properties = $select_property->rowCount();
                $get_data = "data:" . $fetch_user['image_type'] . ";base64," . base64_encode($fetch_user['image']);
        ?>
        <div class="details" style="direction: rtl;">
            <div class="swiper images-container">
                <div class="swiper-wrapper">
                    <img src="<?= $get_data; ?>" alt="" class="swiper-slide">
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <h3 class="name"> <?= $fetch_company['name']; ?></h3>
            <p class="location"><i class=" fas fa-map-marker-alt" style="margin-left: 10px;"></i><a
                    href="<?= $fetch_company['address']; ?>"
                    target="_blank"><span><?= $fetch_company['address']; ?></span></a>
            <div class="info">
                <!-- <p><i class="fas fa-user"> </i> <span><?= $fetch_user['name']; ?></span></p> -->
                <p><i class="fas fa-phone"></i> <a href="tel:1234567890"> <?= $fetch_company['number']; ?></a></p>
                <p><i class="fas fa-building"></i><span> <?= $fetch_company['user_type']; ?></span></p>
                <p><i class="fas fa-calendar"></i><span> <?= $fetch_company['date']; ?></span></p>
            </div>
            <h3 class="title">تفاصيل</h3>
            <div class="flex">
                <div class="box">
                    <p><i>عدد الدورات التي تقدمها الشركة</i><span><span class="fas"
                                style="margin-right: .5rem;"></span><?= $total_properties; ?></span></p>
                    <!-- <p><i>حالة الدورة التدريبية : </i><span><?= $fetch_property['status']; ?></span></p> -->
                </div>
            </div>
            <h3 class="title">وصف </h3>
            <p class="description"> <?= $fetch_user['description']; ?></p>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">property not found!</p>';
        }
        ?>

    </section>

    <!-- view property section ends -->

    <section class="my-listings listings" style="direction: rtl;">

        <h1 class="heading">قائمة الدورات</h1>

        <div class="box-container">

            <?php
            $total_images = 0;
            $select_properties = $conn->prepare("SELECT * FROM `property` WHERE user_id = :id ORDER BY date DESC");
            $select_properties->bindParam(':id', $get_id);
            $select_properties->execute();
            if ($select_properties->rowCount() > 0) {
                while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                    $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);

                    $total_images = 1;
                    $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :id ");
                    $select_employee->bindParam(':id', $fetch_property['employee_id']);
                    $select_employee->execute();
                    $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);
                    $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :property_id and user_id = :user_id");
                    $select_saved->bindParam(':property_id', $fetch_property['id']);
                    $select_saved->bindParam(':user_id', $user_id);
                    $select_saved->execute();
            ?>
            <form action="" method="POST" style="padding: 0;">
                <div class="box">
                    <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
                    <?php
                            if ($select_saved->rowCount() > 0) {
                            ?>
                    <button type="submit" name="save" class="save" style="direction: ltr;"><i
                            class="fas fa-heart"></i><span>تم
                            الحفظ</span></button>
                    <?php
                            } else {
                            ?>
                    <button type="submit" name="save" class="save" style="direction: ltr;"><i
                            class="far fa-heart"></i><span>حفظ</span></button>
                    <?php
                            }
                            ?>
                    <div class="thumb">
                        <p class="total-images" style="direction: ltr;"><i
                                class="far fa-image"></i><span><?= $total_images; ?></span></p>
                        <img src="<?= $get_data; ?>" alt="">
                    </div>
                    <div class="price">مشرف الدورة <i class="fa-solid fa-chalkboard-user" style="margin-left: 5px;"></i>
                        :
                        <span style="color:black; margin-right: 5px;"><?= $fetch_employee['name']; ?></span>
                    </div>
                    <div class="price" style="margin-top: 10px; margin-bottom: 10px;">عدد المتدربين <i
                            class="fa-solid fa-person-circle-check" style="margin-left: 5px;"></i> :
                        <span style="color:black; margin-right: 5px;"><?= $fetch_property['student_count']; ?></span>
                    </div>
                    <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                    <p class="location"><i class=" fas fa-map-marker-alt" style="margin-left: 10px;"></i><a
                            href="<?= $fetch_property['address']; ?>"
                            target="_blank"><span><?= $fetch_property['address']; ?></span></a>
                    <div class="flex-btn">
                        <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض معلومات الدورة
                        </a>
                        <a href="ask_for_requset.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تقديم طلب
                        </a>
                    </div>
            </form>
        </div>
        <?php
                }
            } else {
                echo '<p class="empty">لا يوجد لديه دورات بعد ! <a href="listing_firm.php" style="margin-top:1.5rem;" class="btn">ابحث عن مؤسسة اخرى</a></p>';
            }
?>

        </div>

    </section>









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