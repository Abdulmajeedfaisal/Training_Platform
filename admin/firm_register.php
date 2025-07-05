<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location: login.php');
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $name_user = trim($name);
    $user_type = $_POST['user_type'];
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $c_pass = sha1($_POST['c_pass']);
    $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $address = $_POST['address'];
    $address = filter_var($address, FILTER_SANITIZE_STRING);
    $company_id = 0;
    $image_name = $_FILES['image_01']['name'];
    $image_type = $_FILES['image_01']['type'];
    $image_01_tmp_name = file_get_contents($_FILES['image_01']['tmp_name']);
    $select_users = $conn->prepare("SELECT * FROM `users` WHERE email = :user_email");
    $select_users->bindParam(':user_email', $email);
    $select_users->execute();
    if ($name_user != "") {
        if ($select_users->rowCount() > 0) {
            $warning_msg[] = 'البريد الالكتروني موجود مسبقا!';
        } else {
            if ($pass != $c_pass) {
                $warning_msg[] = 'كلمة المرور غير متطابقة !';
            } else {
                $insert_user = $conn->prepare("INSERT INTO `users`(name, number, email, password, user_type, com_id, address) VALUES(:company_name,:company_number,:company_email,:company_c_pass,:company_user_type,:company_id,:company_address)");
                $insert_user->bindParam(':company_name', $name);
                $insert_user->bindParam(':company_number', $number);
                $insert_user->bindParam(':company_email', $email);
                $insert_user->bindParam(':company_c_pass', $c_pass);
                $insert_user->bindParam(':company_user_type', $user_type);
                $insert_user->bindParam(':company_id', $company_id);
                $insert_user->bindParam(':company_address', $address);
                
                if ($insert_user->execute()) {
                    $verify_users = $conn->prepare("SELECT * FROM `users` WHERE email = :user_email AND password = :user_pass LIMIT 1");
                    $verify_users->bindParam(':user_email', $email);
                    $verify_users->bindParam(':user_pass', $pass);
                    $verify_users->execute();
                    $row = $verify_users->fetch(PDO::FETCH_ASSOC);
                    $firms_insert = $conn->prepare("INSERT INTO `frims`(frim_id, image, image_type, description) VALUES(:id,:images,:image_types,:descriptions)");
                    $firms_insert->bindParam(':id', $row['id']);
                    $firms_insert->bindParam(':images', $image_01_tmp_name);
                    $firms_insert->bindParam(':image_types', $image_type);
                    $firms_insert->bindParam(':descriptions', $description);
                    $firms_insert->execute();
                    if ($firms_insert) {
                        if ($verify_users->rowCount() > 0) {
                            $success_msg[] = 'تم إنشاء الحساب بنجاح !';
                        } else {
                            $error_msg[] = 'هناك خطأ ما !';
                        }
                    }
                }
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
    <link href="../build/css/intlTelInput.css" rel="stylesheet">
    <link href="../build/css/intlTelInput.min.css" rel="stylesheet">
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/admin_new.css">
    <style>
    form .box .input {
        width: 100%;
        border: var(--border);
        margin: 1rem 0;
        padding: 1.4rem;
        color: var(--black);
        font-size: 1.8rem;
    }

    form .box p {
        font-size: 1.6rem;
        color: var(--light-color);
        padding-top: 1rem;
        text-align: right;
    }

    form .box p span {
        color: var(--main-color);
    }

    .iti__country-list {
        left: 0;
    }

    .iti {
        width: 100%;
    }
    </style>

</head>

<body>


    <!-- header section starts  -->
    <?php include '../components/admin_header.php'; ?>
    <!-- header section ends -->

    <!-- register section starts  -->

    <section class="form-container" style="height: fit-content;">

        <form action="" method="post" enctype="multipart/form-data" style="direction: rtl;">
            <h3>إضافة مؤسسة !</h3>
            <input type="hidden" name="user_type" value="company">
            <input type="text" name="name" required maxlength="50" placeholder="ادخل اسم المؤسسة" class="box">
            <input type="email" name="email" required maxlength="50" placeholder="ادخل الايميل " class="box">
            <input type="tel" id="phone" name='number' min="0" max="9999999999" maxlength="10"
                placeholder="أدخل رقم الجوال" class="box" required>
            <input type="text" name="address" required maxlength="100" placeholder="موقع المؤسسة " class="box">
            <input type="password" name="pass" required maxlength="20" placeholder="أدخل كلمة السر" class="box">
            <input type="password" name="c_pass" required maxlength="20" placeholder="تأكيد كلمة السر" class="box">
            <textarea name="description" maxlength="1000" class="input box" required cols="30" rows="10"
                placeholder="إكتب عن المؤسسة ..."></textarea>
            <input type="file" name="image_01" class="input box" accept="image/*" required>
            <p>هل لديك حساب ؟ <a href="/login.php">تسجيل دخول الآن</a></p>
            <input type="submit" value="سجل الان" name="submit" class="btn">
        </form>

    </section>

    <!-- register section ends -->










    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>
    <script src="../build/js/intlTelInput.js"></script>
    <script>
    var input = document.querySelector("#phone");
    window.intlTelInput(input, {});
    </script>
    <?php include '../components/message.php'; ?>

</body>

</html>