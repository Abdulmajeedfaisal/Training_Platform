<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
   $user_id = $_COOKIE['user_id'];
   $user_type = $_COOKIE['user_type'];
} else {
   $user_id = '';
   $user_type = '';
}

if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_users = $conn->prepare("SELECT * FROM `users` WHERE email = :user_email AND password = :user_pass LIMIT 1");
   $select_users->bindParam(':user_email', $email);
   $select_users->bindParam(':user_pass', $pass);
   $select_users->execute();
   $row = $select_users->fetch(PDO::FETCH_ASSOC);

   if ($select_users->rowCount() > 0) {
      setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
      setcookie('user_type', $row['user_type'], time() + 60 * 60 * 24 * 30, '/');
      header('location:home.php');
   } else {
      $warning_msg[] = 'Incorrect email or password!';
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
   <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- login section starts  -->

   <section class="form-container">

      <form action="" method="post" style="direction: rtl;">
         <h3>أهلآ بعودتك!!</h3>
         <input type="email" name="email" required maxlength="50" placeholder="أدخل بريدك الإلكتروني" class="box">
         <input type="password" name="pass" required maxlength="20" placeholder="أدخل رقمك السري" class="box">
         <p>ليس لديك حساب؟ <a href="register.php">سجل الان</a></p>
         <input type="submit" value="تسجيل دخول" name="submit" class="btn">
      </form>

   </section>

   <!-- login section ends -->










   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>