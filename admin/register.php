<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $name_user = trim($name);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $c_pass = sha1($_POST['c_pass']);
    $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

    $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
    $select_admins->execute([$name]);
    if ($name_user != "") {
        if ($select_admins->rowCount() > 0) {
            $warning_msg[] = 'اسم المستخدم موجود موسبقا!';
        } else {
            if ($pass != $c_pass) {
                $warning_msg[] = 'كلمة المرور غير متطابقة !';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?,?)");
                $insert_admin->execute([$name, $c_pass]);
                $success_msg[] = 'تم إنشاء الحساب بنجاح!';
            }
        }
    } else {
        $error_msg[] = '!الاسم يجب أن يحتوي  على أحرف فقط على الاقل';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

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

    <!-- register section starts  -->

    <section class="form-container" style="direction: rtl;">

        <form action="" method="POST">
            <h3>انشاء حساب</h3>
            <input type="text" name="name" placeholder="إدخل اسم المشرف" maxlength="20" class="box" required
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" placeholder="إدخل كلمة السر" maxlength="20" class="box" required
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="c_pass" placeholder="تأكيد كلمة السر" maxlength="20" class="box" required
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="إنشاء" name="submit" class="btn">
        </form>

    </section>

    <!-- register section ends -->


















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>