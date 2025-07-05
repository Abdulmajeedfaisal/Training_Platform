<?php

include '../components/connect.php';

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = :admin_name AND password = :admin_pass LIMIT 1");
    $select_admins->bindParam(':admin_name', $name);
    $select_admins->bindParam(':admin_pass', $pass);
    $select_admins->execute();
    $row = $select_admins->fetch(PDO::FETCH_ASSOC);

    if ($select_admins->rowCount() > 0) {
        setcookie('admin_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
        header('location:dashboard.php');
    } else {
        $warning_msg[] = 'الإيميل او كلمة السر غير صحيح !';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/admin_new.css">

</head>

<body style="padding-left: 0;">

    <!-- login section starts  -->

    <section class="form-container" style="min-height: 100vh; direction: rtl;">

        <form action="" method="POST">
            <h3>مرحبا بك مرة أخرى</h3>
            <p>الاسم الافتراضي = <span>admin</span> & كلمة المرور = <span>123</span></p>
            <input type="text" name="name" placeholder="إدخل أسم المستخدم" maxlength="20" class="box" required
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" placeholder="إدخل كلمة المرور" maxlength="20" class="box" required
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="تسجيل دخول" name="submit" class="btn">
        </form>

    </section>

    <!-- login section ends -->


















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>