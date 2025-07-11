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

$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :user_id LIMIT 1");
$select_user->bindParam(':user_id', $user_id);
$select_user->execute();
$fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   if (!empty($name)) {
      $update_name = $conn->prepare("UPDATE `users` SET name = :user_name WHERE id = :user_id");
      $update_name->bindParam(':user_name', $name);
      $update_name->bindParam(':user_id', $user_id);
      $update_name->execute();
      $success_msg[] = 'name updated!';
   }

   if (!empty($email)) {
      $verify_email = $conn->prepare("SELECT email FROM `users` WHERE email = :emails");
      $verify_email->bindParam(':emails', $email);
      $verify_email->execute();
      if ($verify_email->rowCount() > 0) {
         $warning_msg[] = 'email already taken!';
      } else {
         $update_email = $conn->prepare("UPDATE `users` SET email = :emails WHERE id = :user_id");
         $update_email->bindParam(':emails', $email);
         $update_email->bindParam(':user_id', $user_id);
         $update_email->execute();
         $success_msg[] = 'email updated!';
      }
   }

   if (!empty($number)) {
      $verify_number = $conn->prepare("SELECT number FROM `users` WHERE number = :numbers");
      $verify_number->bindParam(':numbers', $number);
      $verify_number->execute();
      if ($verify_number->rowCount() > 0) {
         $warning_msg[] = 'number already taken!';
      } else {
         $update_number = $conn->prepare("UPDATE `users` SET number = :numbers WHERE id = :user_id");
         $update_number->bindParam(':numbers', $number);
         $update_number->bindParam(':user_id', $user_id);
         $update_number->execute();
         $success_msg[] = 'number updated!';
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $prev_pass = $fetch_user['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $c_pass = sha1($_POST['c_pass']);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

   if ($old_pass != $empty_pass) {
      if ($old_pass != $prev_pass) {
         $warning_msg[] = 'old password not matched!';
      } elseif ($new_pass != $c_pass) {
         $warning_msg[] = 'confirm passowrd not matched!';
      } else {
         if ($new_pass != $empty_pass) {
            $update_pass = $conn->prepare("UPDATE `users` SET password = :pass WHERE id = :user_id");
            $update_pass->bindParam(':pass', $c_pass);
            $update_pass->bindParam(':user_id', $user_id);
            $update_pass->execute();
            $success_msg[] = 'password updated successfully!';
         } else {
            $warning_msg[] = 'please enter new password!';
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
   <title>update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form action="" method="post" style="direction: rtl;">
         <h3>تحديث حسابك !</h3>
         <input type="tel" name="name" maxlength="50" placeholder="<?= $fetch_user['name']; ?>" class="box">
         <input type="email" name="email" maxlength="50" placeholder="<?= $fetch_user['email']; ?>" class="box">
         <input type="number" name="number" min="0" max="9999999999" maxlength="10" placeholder="<?= $fetch_user['number']; ?>" class="box">
         <input type="password" name="old_pass" maxlength="20" placeholder="ادخل كلمة السر القديمة" class="box">
         <input type="password" name="new_pass" maxlength="20" placeholder="أدخل كلمة السر الجديدة" class="box">
         <input type="password" name="c_pass" maxlength="20" placeholder=" تأكيد كلمة السر الجديدة" class="box">
         <input type="submit" value="تحديث الان" name="submit" class="btn">
      </form>

   </section>






   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>
</body>

</html>