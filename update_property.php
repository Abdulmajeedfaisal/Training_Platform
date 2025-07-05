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

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:home.php');
}

if (isset($_POST['update'])) {

    $update_id = $_POST['property_id'];
    $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
    $property_name = $_POST['property_name'];
    $property_name = filter_var($property_name, FILTER_SANITIZE_STRING);
    $address = $_POST['address'];
    $address = filter_var($address, FILTER_SANITIZE_STRING);
    $type = $_POST['type'];
    $type = filter_var($type, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);
    $student_count = $_POST['student_count'];
    $employee_id = $_POST['employee'];
    $employee_id = filter_var($employee_id, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    if (isset($_POST['image_01'])) {
        $image_name = $_FILES['image_01']['name'];
        $image_type = $_FILES['image_01']['type'];
        $image_01_tmp_name = file_get_contents($_FILES['image_01']['tmp_name']);
        $image_01_size = $_FILES['image_01']['size'];
    }
    if (!empty($image_name)) {
        if ($image_01_size > 2000000) {
            $warning_msg[] = 'image 05 size is too large!';
        } else {
            $update_image_01 = $conn->prepare("UPDATE `property` SET image = :images, image_name = :image_name, image_type = :image_type WHERE id = :id");
            $update_image_01->bindParam(':images', $image_01_tmp_name);
            $update_image_01->bindParam(':image_name', $image_name);
            $update_image_01->bindParam(':image_type', $image_type);
            $update_image_01->bindParam(':id', $update_id);
            $update_image_01->execute();
        }
    }

    $update_listing = $conn->prepare("UPDATE `property` SET property_name = :prop_name, address = :prop_address,  employee_id = :employee_id, type = :prop_type, status = :prop_status, student_count = :student_counts, description = :prop_description WHERE id = :update_id");
    $update_listing->bindParam(':prop_name', $property_name);
    $update_listing->bindParam(':prop_address', $address);
    $update_listing->bindParam(':employee_id', $employee_id);
    $update_listing->bindParam(':prop_type', $type);
    $update_listing->bindParam(':prop_status', $status);
    $update_listing->bindParam(':student_counts', $student_count);
    $update_listing->bindParam(':prop_description', $description);
    $update_listing->bindParam(':update_id', $update_id);
    $update_listing->execute();

    $success_msg[] = 'تم تحديث الدورة بنجاح !';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update property</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="property-form">

        <?php
        $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :id ORDER BY date DESC LIMIT 1");
        $select_properties->bindParam(':id', $get_id);
        $select_properties->execute();
        if ($select_properties->rowCount() > 0) {
            while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {
                $property_id = $fetch_property['id'];
                $select_user = $conn->prepare("SELECT * FROM `users` WHERE 	id = :user_id");
                $select_user->bindParam(':user_id', $fetch_property['employee_id']);
                $select_user->execute();
                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);
        ?>
                <form action="" method="POST" enctype="multipart/form-data" style="direction: rtl;">
                    <input type="hidden" name="property_id" value="<?= $property_id; ?>">
                    <h3>تفاصيل الدورة التدريبية</h3>
                    <div class="box">
                        <p>أسم الدورة التدريبة <span>*</span></p>
                        <input type="text" name="property_name" required maxlength="50" placeholder="enter property name" value="<?= $fetch_property['property_name']; ?>" class="input">
                    </div>
                    <div class="flex">

                        <div class="box">
                            <p>موقع الدورة التدريبة<span>*</span></p>
                            <input type="text" name="address" required maxlength="100" placeholder="enter property full address" class="input" value="<?= $fetch_property['address']; ?>">
                        </div>
                        <div class="box">
                            <p>عدد المتدربين <span>*</span></p>
                            <input type="number" name="student_count" required min="0" max="9999999999" maxlength="10" placeholder="إدخل عدد المتدربين" class="input" value="<?= $fetch_property['student_count']; ?>">
                        </div>
                        <div class="box">
                            <p>الفئة المستهدفة من المتدربين<span>*</span></p>
                            <select name="type" required class="input">
                                <option value="<?= $fetch_property['type']; ?>" selected><?= $fetch_property['type']; ?>
                                </option>
                                <option value="house">house</option>
                                <option value="shop">shop</option>
                            </select>
                        </div>
                        <div class="box">
                            <p>حالة الدورة التدريبية<span>*</span></p>
                            <select name="status" required class="input">
                                <?php if ($fetch_property['status'] != "not_available") { ?>
                                    <option value="<?= $fetch_property['status']; ?>" selected>متاحة
                                    </option>
                                    <option value="not_available">غير متاحة</option>
                                <?php } elseif ($fetch_property['status'] == "not_available") { ?>
                                    <option value="<?= $fetch_property['status']; ?>" selected>غير متاحة</option>
                                    <option value="available">متاحة</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <p>مشرف الدورة التدريبية<span>*</span></p>
                        <select name="employee" required class="input">

                            <?php
                            if ($user_type == "company") {
                                $select_firm = $conn->prepare("SELECT * FROM `users` WHERE 	com_id = :id  ORDER BY date DESC");
                                $select_firm->bindParam(':id', $user_id);
                                $select_firm->execute();
                                if ($select_firm->rowCount() > 0) {
                                    while ($fetch_company = $select_firm->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                        <option value="<?= $fetch_company['id']; ?>"><?= $fetch_company['name']; ?></option>
                                    <?php }
                                }
                            } else {
                                $select_firm = $conn->prepare("SELECT * FROM `users` WHERE 	id = :id  ORDER BY date DESC");
                                $select_firm->bindParam(':id', $user_id);
                                $select_firm->execute();
                                if ($select_firm->rowCount() > 0) {
                                    while ($fetch_employee = $select_firm->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                        <option value="<?= $fetch_employee['id']; ?>"><?= $fetch_employee['name']; ?></option>
                            <?php
                                    }
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="box">
                        <p>وصف الدورة التدريبية<span>*</span></p>
                        <textarea name="description" maxlength="1000" class="input" required cols="30" rows="10" placeholder="write about property..."><?= $fetch_property['description']; ?></textarea>
                    </div>
                    <div class="box">
                        <img src="<?= $get_data; ?>" class="image" alt="">
                        <p>صورة عن الدورة<span>*</span></p>
                        <input type="file" name="image_01" class="input" accept="image/*">
                    </div>
                    <input type="submit" value="update property" class="btn" name="update">
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">property not found! <a href="post_property.php" style="margin-top:1.5rem;" class="btn">add new</a></p>';
        }
        ?>

    </section>






    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>