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
include 'components/save_send.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my listings</title>

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

    <section class="my-listings listings" style="direction: rtl;">
        <?php
        $count_properties = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :employee_id OR user_id = :user_id");
        $count_properties->bindParam(':employee_id', $user_id);
        $count_properties->bindParam(':user_id', $user_id);
        $count_properties->execute();
        $total_properties = $count_properties->rowCount();
        if ($total_properties != 0) {
        ?>
            <a href="post_property.php" style="margin-top:1.5rem; display: inline;" class="btn">إضافة دورة</a>
        <?php } ?>

        <h1 class="heading" style="margin-top:1.5rem;">قائمتي</h1>
        <?php if ($user_type == "company") { ?>
            <div class="box-container">

                <?php
                $total_images = 0;
                $select_properties = $conn->prepare("SELECT * FROM `property` WHERE user_id = :id ORDER BY date DESC");
                $select_properties->bindParam(':id', $user_id);
                $select_properties->execute();
                if ($select_properties->rowCount() > 0) {
                    while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {
                        $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :id ");
                        $select_employee->bindParam(':id', $fetch_property['employee_id']);
                        $select_employee->execute();
                        $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);
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
                                <div class="price">مشرف الدورة <i class="fa-solid fa-chalkboard-user" style="margin-left: 5px;"></i>
                                    :
                                    <span style="color:black; margin-right: 5px;"><?= $fetch_employee['name']; ?></span>
                                </div>
                                <div class="price" style="margin-top: 10px; margin-bottom: 10px;">عدد المتدربين <i class="fa-solid fa-person-circle-check" style="margin-left: 5px;"></i> :
                                    <span style="color:black; margin-right: 5px;"><?= $fetch_property['student_count']; ?></span>
                                </div>
                                <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                                <p class="location"><i class=" fas fa-map-marker-alt" style="margin-left: 10px;"></i><a href="<?= $fetch_property['address']; ?>" target="_blank"><span><?= $fetch_property['address']; ?></span></a>
                                <div class="flex-btn">
                                    <a href="update_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تحديث
                                        البيانات</a>
                                    <input type="submit" name="delete" value="حذف الدورة" class="btn" onclick="return confirm('delete this listing?');">
                                </div>
                                <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة
                                    التدريبية</a>
                            </div>
                        </form>
                <?php
                    }
                } else {
                    echo '<p class="empty">لا يوجد دورات بعد !<a href="post_property.php" style="margin-top:1.5rem;" class="btn">قم بإضافة دورة</a></p>';
                }
                ?>

            </div>
        <?php  } elseif ($user_type == "employee") { ?>
            <div class="box-container">

                <?php
                $total_images = 0;
                $select_properties = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :id ORDER BY date DESC");
                $select_properties->bindParam(':id', $user_id);
                $select_properties->execute();
                if ($select_properties->rowCount() > 0) {
                    while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {
                        $select_employee = $conn->prepare("SELECT * FROM `users` WHERE id = :id ");
                        $select_employee->bindParam(':id', $fetch_property['employee_id']);
                        $select_employee->execute();
                        $fetch_employee = $select_employee->fetch(PDO::FETCH_ASSOC);
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
                                <div class="price">مشرف الدورة <i class="fa-solid fa-chalkboard-user" style="margin-left: 5px;"></i>
                                    :
                                    <span style="color:black; margin-right: 5px;"><?= $fetch_employee['name']; ?></span>
                                </div>
                                <div class="price" style="margin-top: 10px; margin-bottom: 10px;">عدد المتدربين <i class="fa-solid fa-person-circle-check" style="margin-left: 5px;"></i> :
                                    <span style="color:black; margin-right: 5px;"><?= $fetch_property['student_count']; ?></span>
                                </div>
                                <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                                <p class="location"><i class="fas fa-map-marker-alt"></i>
                                    <span><?= $fetch_property['address']; ?></span>
                                </p>
                                <div class="flex-btn">
                                    <a href="update_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تحديث
                                        البيانات</a>
                                    <input type="submit" name="delete" value="حذف الدورة" class="btn" onclick="return confirm('delete this listing?');">
                                </div>
                                <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض الدورة
                                    التدريبية</a>
                            </div>
                        </form>
                <?php
                    }
                } else {
                    echo '<p class="empty">لا يوجد دورات بعد !<a href="post_property.php" style="margin-top:1.5rem;" class="btn">قم بإضافة دورة</a></p>';
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