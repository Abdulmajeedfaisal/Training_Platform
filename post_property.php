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

if (isset($_POST['post'])) {

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

    $image_name = $_FILES['image_01']['name'];
    $image_type = $_FILES['image_01']['type'];
    $image_01_tmp_name = file_get_contents($_FILES['image_01']['tmp_name']);
    $image_01_size = $_FILES['image_01']['size'];

    if ($image_01_size > 2000000) {
        $warning_msg[] = 'حجم الصورة كبير جيدا !';
    } else {
        if ($user_type == "company") {
            $insert_property = $conn->prepare("INSERT INTO `property`(user_id, property_name, address, employee_id, type, status, student_count, image, image_name, image_type, description) VALUES(:user_id, :property_name, :addresses, :employee_id,:types,  :statuses, :student_count, :image_01, :image_name, :image_type, :descriptions)");
            $insert_property->bindParam(':user_id', $user_id);
            $insert_property->bindParam(':property_name', $property_name);
            $insert_property->bindParam(':addresses', $address);
            $insert_property->bindParam(':employee_id', $employee_id);
            $insert_property->bindParam(':types', $type);
            $insert_property->bindParam(':statuses', $status);
            $insert_property->bindParam(':student_count', $student_count);
            $insert_property->bindParam(':image_01', $image_01_tmp_name);
            $insert_property->bindParam(':image_name', $image_name);
            $insert_property->bindParam(':image_type', $image_type);
            $insert_property->bindParam(':descriptions', $description);
            $insert_property->execute();
            $success_msg[] = 'تم إضافة الدورة بنجاح !';
        } else {
            $select_sender = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
            $select_sender->bindParam(':id', $user_id);
            $select_sender->execute();
            $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);
            $insert_property = $conn->prepare("INSERT INTO `property`(user_id, property_name, address, employee_id, type, status, student_count, image, image_name, image_type, description) VALUES(:user_id,:property_name,:addresses,:employee_id,:types,:statuses,:student_count,:image_01_tmp_name,:image_name,:image_type,:descriptions)");
            $insert_property->bindParam(':user_id', $fetch_sender['com_id']);
            $insert_property->bindParam(':property_name', $property_name);
            $insert_property->bindParam(':addresses', $address);
            $insert_property->bindParam(':employee_id', $employee_id);
            $insert_property->bindParam(':types', $type);
            $insert_property->bindParam(':statuses', $status);
            $insert_property->bindParam(':student_count', $student_count);
            $insert_property->bindParam(':image_01_tmp_name', $image_01_tmp_name);
            $insert_property->bindParam(':image_name', $image_name);
            $insert_property->bindParam(':image_type', $image_type);
            $insert_property->bindParam(':descriptions', $description);
            $insert_property->execute();
            $success_msg[] = 'تم إضافة الدورة بنجاح !';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>post property</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="property-form">

        <form action="" method="POST" enctype="multipart/form-data" style="direction: rtl;">
            <h3>تفاصيل الدورة التدريبية</h3>
            <div class="box">
                <p>أسم الدورة التدريبة <span>*</span></p>
                <input type="text" name="property_name" required maxlength="50" placeholder="ادخل اسم الدورة" class="input">
            </div>
            <div class="flex">
                <div class="box">
                    <p>موقع الدورة التدريبة<span>*</span></p>
                    <input type="text" name="address" required maxlength="100" placeholder="موقع الدورة " class="input">
                </div>
                <div class="box">
                    <p>عدد المتدربين <span>*</span></p>
                    <input type="number" name="student_count" required min="0" max="9999999999" maxlength="10" placeholder="إدخل عدد المتدربين" class="input">
                </div>

                <div class="box">
                    <p>الكلية الخاصة بالدورة التدريبية<span>*</span></p>
                    <select name="type" required class="input">
                        <option value="engineering">الهندسة</option>
                        <option value="computer">علوم حاسوب</option>
                        <option value="doctor">طب عام</option>
                        <option value="administration">العلوم الادارية</option>
                        <option value="applied">العلوم التطبيقية</option>
                        <option value="education">التربية</option>
                    </select>
                </div>
                <div class="box">
                    <p>حالة الدورة التدريبية<span>*</span></p>
                    <select name="status" required class="input">
                        <option value="available">متاحة</option>
                        <option value="not_available">غير متاحة</option>
                    </select>
                </div>
                <div class="box">
                    <p>مشرف الدورة التدريبية<span>*</span></p>
                    <select name="employee" required class="input">

                        <?php
                        if ($user_type == "company") {
                            $select_firm = $conn->prepare("SELECT * FROM `users` WHERE 	com_id = :com_id  ORDER BY date DESC");
                            $select_firm->bindParam(':com_id', $user_id);
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
            </div>
            <div class="box">
                <p>وصف الدورة التدريبية<span>*</span></p>
                <textarea name="description" maxlength="1000" class="input" required cols="30" rows="10" placeholder="إكتب عن الدورة..."></textarea>
            </div>
            <div class="box">
                <p>صورة عن الدورة<span>*</span></p>
                <input type="file" name="image_01" class="input" accept="image/*" required>
            </div>
            <input type="submit" value="إضافة دورة " class="btn" name="post">
        </form>

    </section>





    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>