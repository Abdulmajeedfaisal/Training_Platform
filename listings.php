<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
}

include 'components/save_send.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Listings</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/sstyle.css">
    <link rel="stylesheet" href="css/Style1.css">
    <style>
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

    <!-- listings section starts  -->

    <section class="listings" style="direction: rtl;">
        <form action="" method="POST" class="search-form">
            <input type="text" name="search_box" placeholder="بحث عن دورة..." maxlength="100" required>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>
        <h1 class="heading">جميع الفرص التدريبية المتاحة</h1>

        <div class="box-container">

            <?php
            if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                $search_box = $_POST['search_box'];
                $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
                $select_properties = $conn->prepare("SELECT * FROM `property` WHERE property_name LIKE '%{$search_box}%' OR address LIKE '%{$search_box}%' ORDER BY date DESC");
                $select_properties->execute();
            } else {
                $select_properties = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC");
                $select_properties->execute();;
            }
            $total_images = 0;
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
                            <p class="location"><i class=" fas fa-map-marker-alt"></i><a href="<?= $fetch_property['address']; ?>" target="_blank"><span><?= $fetch_property['address']; ?></span></a>

                            </p>
                            <div class="admin">
                                <h3><?= substr($fetch_user['name'], 0, 1); ?></h3>
                                <div>
                                    <p><?= $fetch_user['name']; ?></p>
                                    <span><?= $fetch_property['date']; ?></span>
                                </div>
                            </div>
                            <div class="flex-btn">
                                <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة</a>
                                <a href="ask_for_requset.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تقديم طلب
                                </a>
                            </div>
                        </div>
                    </form>

            <?php
                }
            } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                echo '<p class="empty"> لم يتم العثور على نتائج!</p>';
            } else {
                echo '<p class="empty">لا توجد دورات بعد!</p>';
            }
            ?>

        </div>

    </section>

    <!-- listings section ends -->












    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>