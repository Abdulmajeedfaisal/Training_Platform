<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
}

if (isset($_POST['delete'])) {

    $delete_id = $_POST['delete_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `property` WHERE id = :delete_id");
    $verify_delete->bindParam(':delete_id', $delete_id);
    $verify_delete->execute();

    if ($verify_delete->rowCount() > 0) {
        $delete_saved = $conn->prepare("DELETE FROM `saved` WHERE property_id = :id");
        $delete_saved->bindParam(':id', $delete_id);
        $delete_saved->execute();
        $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE property_id = :id");
        $delete_requests->bindParam(':id', $delete_id);
        $delete_requests->execute();
        $delete_listings = $conn->prepare("DELETE FROM `property` WHERE id = :id");
        $delete_listings->bindParam(':id', $delete_id);
        $delete_listings->execute();
        $success_msg[] = 'تم حذف الدورة !';
    } else {
        $warning_msg[] = 'الدورة تم حذفها مسبقا !';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings</title>

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

    <section class="listings" style="direction: rtl;">

        <h1 class="heading">جميع الدورات</h1>

        <form action="" method="POST" class="search-form">
            <input type="text" name="search_box" placeholder="بحث عن دورة..." maxlength="100" required>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>

        <div class="box-container">

            <?php
            if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                $search_box = $_POST['search_box'];
                $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
                $select_listings = $conn->prepare("SELECT * FROM `property` WHERE property_name LIKE '%{$search_box}%' OR address LIKE '%{$search_box}%' ORDER BY date DESC");
                $select_listings->execute();
            } else {
                $select_listings = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC");
                $select_listings->execute();
            }
            $total_images = 0;
            if ($select_listings->rowCount() > 0) {
                while ($fetch_property = $select_listings->fetch(PDO::FETCH_ASSOC)) {
                    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :user_id");
                    $select_user->bindParam(':user_id', $fetch_property['user_id']);
                    $select_user->execute();
                    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

                    $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);

                    $total_images = 1;
            ?>
            <form action="" method="POST">
                <div class="box">
                    <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
                    <div class="thumb">
                        <p class="total-images" style="direction: ltr;"><i
                                class="far fa-image"></i><span><?= $total_images; ?></span>
                        </p>
                        <img src="<?= $get_data; ?>" alt="">
                    </div>
                    <div class="price">عدد المتدربين <i class="fa-solid fa-person-circle-check"></i> :
                        <span style="color:black;"><?= $fetch_property['student_count']; ?></span>
                    </div>
                    <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i>
                        <span><?= $fetch_property['address']; ?></span>
                    </p>
                    <form action="" method="POST">
                        <input type="hidden" name="delete_id" value="<?= $fetch_property['id']; ?>">
                        <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة
                            التدريبية</a>
                        <input type="submit" value="حذف الدورة" onclick="return confirm('delete this listing?');"
                            name="delete" class="delete-btn">
                    </form>
                </div>
                <?php
                }
            } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                echo '<p class="empty">لم يتم العثور على نتائج!</p>';
            } else {
                echo '<p class="empty">لا توجد دورات بعد!</p>';
            }
                ?>

        </div>

    </section>



















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>