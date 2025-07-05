<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
   $user_id = $_COOKIE['user_id'];
   $user_type = $_COOKIE['user_type'];
   if ($user_type == "client" || $user_type == "employee") {
      header("location: ./");
   }
} else {
   $user_id = '';
   $user_type = '';
   header('location:login.php');
}
if (isset($_POST['delete'])) {

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `users` WHERE id = :id ");
   $verify_delete->bindParam(':id', $delete_id);
   $verify_delete->execute();

   if ($verify_delete->rowCount() > 0) {
      $select_employee_property = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :id");
      $select_employee_property->bindParam(':id', $delete_id);
      $select_employee_property->execute();
      while ($fetch_employee_property = $select_employee_property->fetch(PDO::FETCH_ASSOC)) {
         $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE 	property_id =:id");
         $delete_requests->bindParam(':id', $fetch_employee_property['id']);
         $delete_requests->execute();
      }
      $delete_listings = $conn->prepare("DELETE FROM `property` WHERE  employee_id = :id");
      $delete_listings->bindParam(':id', $delete_id);
      $delete_listings->execute();

      $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE sender_id = :sender_id OR receiver_id = :receiver_id");
      $delete_requests->bindParam(':sender_id', $delete_id);
      $delete_requests->bindParam(':receiver_id', $delete_id);
      $delete_requests->execute();
      $delete_saved = $conn->prepare("DELETE FROM `saved` WHERE user_id = :id");
      $delete_saved->bindParam(':id', $delete_id);
      $delete_saved->execute();
      $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = :id AND user_type = 'employee'");
      $delete_user->bindParam(':id', $delete_id);
      $delete_user->execute();
      $success_msg[] = 'تم حذف المشرف بنجاح!';
   } else {
      $warning_msg[] = 'تم حذف المشرف مسبقا !';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/sstyle.css">
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- admins section starts  -->

    <section class="requests" style="direction: rtl;">
        <?php
      $count_properties = $conn->prepare("SELECT * FROM `users` WHERE com_id = :user_id");
      $count_properties->bindParam(':user_id', $user_id);
      $count_properties->execute();
      $total_properties = $count_properties->rowCount();
      if ($total_properties != 0) {
      ?>
        <a href="emp_register.php" style="margin-top:1.5rem; display: inline;" class="btn">إضافة مشرف جديد</a>
        <?php } ?>
        <h1 class="heading">الموظفين</h1>

        <form action="" method="POST" class="search-form">
            <input type="text" name="search_box" placeholder="البحث عن موظف..." maxlength="100" required>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>

        <div class="box-container">

            <?php
         if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            $search_box = $_POST['search_box'];
            $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
            $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = 'employee' AND (name LIKE '%{$search_box}%' OR number LIKE '%{$search_box}%' OR email LIKE '%{$search_box}%')");
            $select_users->execute();
         } else {
            $select_users = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'employee' AND com_id = :company_id");
            $select_users->bindParam(':company_id', $user_id);
            $select_users->execute();
         }
         if ($select_users->rowCount() > 0) {
            while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {

               $count_property = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :id");
               $count_property->bindParam(':id', $fetch_users['id']);
               $count_property->execute();
               $count = 0;
         ?>
            <div class="box">
                <p> الاسم : <span><?= $fetch_users['name']; ?></span></p>
                <p>الجوال : <a href="tel:<?= $fetch_users['number']; ?>"><?= $fetch_users['number']; ?></a></p>
                <p>الايميل : <a href="mailto:<?= $fetch_users['email']; ?>"><?= $fetch_users['email']; ?></a></p>
                <p>الدورات المشرف عليها:
                    <?php if ($count_property->rowCount() > 0) {
                        while ($total_properties = $count_property->fetch(PDO::FETCH_ASSOC)) {
                     ?>
                    <br>
                    <span><span><?= ++$count; ?>-</span><?= $total_properties['property_name']; ?></span>
                    <br>
                    <?php }
                     } else { ?>
                    <span>لا يوجد</span>
                    <?php } ?>
                </p>
                <form action="" method="POST">
                    <input type="hidden" name="delete_id" value="<?= $fetch_users['id']; ?>">
                    <input type="submit" value="delete user" onclick="return confirm('delete this user?');"
                        name="delete" class="btn">
                </form>
            </div>
            <?php
            }
         } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            echo '<p class="empty"> لم يتم العثور على نتائج!</p>';
         } else {
            echo '<p class="empty">لم تتم إضافة حسابات مستخدمين حتى الان ! <a href="emp_register.php" style="margin-top:1.5rem;" class="btn">إضافة مشرف جديد</a></p>';
         }
         ?>

        </div>

    </section>

    <!-- users section ends -->
















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>
    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>