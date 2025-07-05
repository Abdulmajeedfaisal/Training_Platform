<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
   $user_type = $_COOKIE['user_type'];
} else {
   $user_id = '';
   $user_type = '';
}
function Validate_Name($name)
{
   if (preg_match('/^[\p{L} ]+$/u', $name)) {
      return true;
   } else {
      return false;
   }
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
   $company_id = 0;
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
            $insert_user = $conn->prepare("INSERT INTO `users`(name, number, email, password, user_type, com_id) VALUES(:user_name,:phone_num,:email,:pass,:user_type,:company_id)");
            $insert_user->bindParam(':user_name', $name);
            $insert_user->bindParam(':phone_num', $number);
            $insert_user->bindParam(':email', $email);
            $insert_user->bindParam(':pass', $pass);
            $insert_user->bindParam(':user_type', $user_type);
            $insert_user->bindParam(':company_id', $company_id);
            $insert_user->execute();
            if ($insert_user) {
               $verify_users = $conn->prepare("SELECT * FROM `users` WHERE email =:user_email AND password = :user_pass LIMIT 1");
               $verify_users->bindParam(':user_email', $email);
               $verify_users->bindParam(':user_pass', $pass);
               $verify_users->execute();
               $row = $verify_users->fetch(PDO::FETCH_ASSOC);

               if ($verify_users->rowCount() > 0) {
                  setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
                  setcookie('user_type', $row['user_type'], time() + 60 * 60 * 24 * 30, '/');
                  header('location:home.php');
               } else {
                  $error_msg[] = 'هناك خطأ ما!';
               }
            }
         }
      }
   } else {
      $error_msg[] = 'الاسم يجب أن يحتوي  على الاقل أحرف فقط ';
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
   <!-- <link href="./build/css/demo.css" rel="stylesheet"> -->
   <link href="./build/css/intlTelInput.css" rel="stylesheet">
   <link href="./build/css/intlTelInput.min.css" rel="stylesheet">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/Style1.css">
   <style>
      .iti__country-list {
         left: 0;
      }

      .iti {
         width: 100%;
      }
   </style>
</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- register section starts  -->

   <section class="form-container">

      <form action="" method="post" style="direction: rtl;">
         <h3>إنشاء حساب!</h3>
         <input type="hidden" name="user_type" value="client">
         <input type="text" name="name" required maxlength="50" placeholder="أدخل إسمك" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="أدخل بريدك الإلكتروني" class="box">
         <input type="tel" id="phone" name='number' min="0" max="9999999999" maxlength="10" placeholder="أدخل رقم الجوال" class="box" required>
         <input type="password" name="pass" required maxlength="20" placeholder="أدخل كلمة السر" class="box">
         <input type="password" name="c_pass" required maxlength="20" placeholder="تأكيد كلمة السر" class="box">
         <p>هل لديك حساب ? <a href="login.php">تسجيل دخول الان </a></p>
         <input type="submit" value="إنشاء حساب" name="submit" class="btn">
      </form>

   </section>

   <!-- register section ends -->










   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
   <script src="./build/js/intlTelInput.js"></script>
   <script>
      var input = document.querySelector("#phone");
      window.intlTelInput(input, {});
   </script>
   <?php include 'components/message.php'; ?>

</body>

</html>