<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}

$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ? LIMIT 1");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $name_user = trim($name);
   if (!empty($name)) {
      $verify_name = $conn->prepare("SELECT * FROM `admins` WHERE name = :admin_name");
      $verify_name->bindParam(':admin_name', $name);
      $verify_name->execute();
      if ($verify_name->rowCount() > 0) {
         $warning_msg[] = 'أسم المستخدم موجود مسبقا !';
      } else {
         $update_name = $conn->prepare("UPDATE `admins` SET name = :admin_name WHERE id = :admin_id");
         $update_name->bindParam(':admin_name', $name);
         $update_name->bindParam(':admin_id', $admin_id);
         $update_name->execute();
         $success_msg[] = ' تم تحديث أسم المستخدم!';
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $prev_pass = $fetch_profile['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $c_pass = sha1($_POST['c_pass']);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

   if ($old_pass != $empty_pass) {
      if ($old_pass != $prev_pass) {
         $warning_msg[] = 'Old password not matched!';
      } elseif ($c_pass != $new_pass) {
         $warning_msg[] = 'New password not matched!';
      } else {
         if ($new_pass != $empty_pass) {
            $update_password = $conn->prepare("UPDATE `admins` SET password = :admin_pass WHERE id = :admin_id");
            $update_password->bindParam(':admin_pass', $c_pass);
            $update_password->bindParam(':admin_id', $admin_id);
            $update_password->execute();
            $success_msg[] = 'تم تحديث كلمة مرور !';
         } else {
            $warning_msg[] = 'الرجاء إدخال كلمة مرور جديدة !';
         }
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
    <title>Update</title>

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

    <!-- update section starts  -->

    <section class="form-container">

        <form action="" method="POST" style="direction: rtl;">
            <h3>تحديث الحساب !</h3>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="old_pass" placeholder="ادخل كلمة السر القديمة" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" placeholder="أدخل كلمة السر الجديدة" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="c_pass" placeholder=" تأكيد كلمة السر الجديدة" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="تحديث الان" name="submit" class="btn">
        </form>

    </section>

    <!-- update section ends -->


















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>