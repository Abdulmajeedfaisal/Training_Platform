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

include 'components/save_send.php';

if (isset($_POST['delete'])) {

    $delete_id = $_POST['property_id'];

    $verify_delete = $conn->prepare("SELECT * FROM `property` WHERE id = :delete_id");
    $verify_delete->bindParam(':delete_id', $delete_id);
    $verify_delete->execute();

    if ($verify_delete->rowCount() > 0) {
        $select_images = $conn->prepare("SELECT * FROM `property` WHERE id = :images_id");
        $select_images->bindParam(':images_id', $delete_id);
        $select_images->execute();
        $delete_information = $conn->prepare("DELETE FROM `files` WHERE property_id = :dele_id AND student_id = :users_id");
        $delete_information->bindParam(':dele_id', $delete_id);
        $delete_information->bindParam(':users_id', $user_id);
        $delete_information->execute();
        $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE property_id = :dele_id AND sender_id = :users_id");
        $delete_requests->bindParam(':dele_id', $delete_id);
        $delete_requests->bindParam(':users_id', $user_id);
        $delete_requests->execute();
        $success_msg[] = 'تم حذف الطلب !';
    } else {
        $warning_msg[] = 'الطلب تم حذفه مسبقا !';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>saved</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <!-- <link rel="stylesheet" href="css/Style1.css"> -->
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

    <section class="listings" style="direction: rtl;">

        <h1 class="heading">الطلبات المرسلة</h1>

        <div class="box-container">
            <?php
            $total_images = 0;
            $select_saved_property = $conn->prepare("SELECT * FROM `requests` WHERE sender_id = :id AND activation = '0'");
            $select_saved_property->bindParam(':id', $user_id);
            $select_saved_property->execute();
            if ($select_saved_property->rowCount() > 0) {
                while ($fetch_saved = $select_saved_property->fetch(PDO::FETCH_ASSOC)) {
                    $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :prop_id ORDER BY date DESC");
                    $select_properties->bindParam(':prop_id', $fetch_saved['property_id']);
                    $select_properties->execute();
                    if ($select_properties->rowCount() > 0) {
                        while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                            $select_user->bindParam(':id', $fetch_property['user_id']);
                            $select_user->execute();
                            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

                            $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);

                            $total_images = 1;

                            $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :property_id and user_id = :user_id");
                            $select_saved->bindParam(':property_id', $fetch_property['id']);
                            $select_saved->bindParam(':user_id', $user_id);
                            $select_saved->execute();

            ?>
            <form action="" method="POST">
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
                        <input type="submit" name="delete" value="إلغاء الطلب !" class="btn">
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






    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>