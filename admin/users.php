<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}

if (isset($_POST['delete'])) {

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
   $verify_delete->bindParam(':id', $delete_id);
   $verify_delete->execute();
   $verify_deletes = $verify_delete->fetch(PDO::FETCH_ASSOC);

   if ($verify_delete->rowCount() > 0) {
      $delete_listings = $conn->prepare("DELETE FROM `property` WHERE user_id = :id");
      $delete_listings->bindParam(':id', $delete_id);
      $delete_listings->execute();
      $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE sender_id = :sender OR receiver_id = :receiver");
      $delete_requests->bindParam(':sender', $delete_id);
      $delete_requests->bindParam(':receiver', $delete_id);
      $delete_requests->execute();
      $delete_saved = $conn->prepare("DELETE FROM `saved` WHERE user_id = :saved_id");
      $delete_saved->bindParam(':saved_id', $delete_id);
      $delete_saved->execute();
      $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = :user_id");
      $delete_user->bindParam(':user_id', $delete_id);
      $delete_user->execute();
      $success_msg[] = ' تم حذف المستخدم !';
   } else {
      $warning_msg[] = 'المستخدم تم حذفه مسبقا !';
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
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/admin_new.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include '../components/admin_header.php'; ?>
    <!-- header section ends -->

    <!-- admins section starts  -->

    <section class="grid" style="direction: rtl;">

        <h1 class="heading">العملاء</h1>

        <form action="" method="POST" class="search-form">
            <input type="text" name="search_box" placeholder="بحث عن عميل..." maxlength="100" required>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>

        <div class="box-container">

            <?php
         if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            $search_box = $_POST['search_box'];
            $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
            $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = 'client' AND (name LIKE '%{$search_box}%' OR number LIKE '%{$search_box}%' OR email LIKE '%{$search_box}%')");
            $select_users->execute();
         } else {
            $select_users = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'client'");
            $select_users->execute();
         }
         if ($select_users->rowCount() > 0) {
            while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {

               $select_saved_property = $conn->prepare("SELECT * FROM `requests` WHERE sender_id = :request_id AND activation = '1'");
               $select_saved_property->bindParam(':request_id', $fetch_users['id']);
               $select_saved_property->execute();
               $total_properties = $select_saved_property->rowCount();
         ?>
            <div class="box">
                <p> الاسم : <span><?= $fetch_users['name']; ?></span></p>
                <p>الجوال : <a href="tel:<?= $fetch_users['number']; ?>"><?= $fetch_users['number']; ?></a></p>
                <p>الايميل : <a href="mailto:<?= $fetch_users['email']; ?>"><?= $fetch_users['email']; ?></a></p>
                <p>عدد الدورات المقبول فيها: <span><?= $total_properties; ?></span></p>
                <form action="" method="POST">
                    <input type="hidden" name="delete_id" value="<?= $fetch_users['id']; ?>">
                    <input type="submit" value="حذف عميل" onclick="return confirm('delete this user?');" name="delete"
                        class="delete-btn">
                </form>
            </div>
            <?php
            }
         } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            echo '<p class="empty"> لم يتم العثور على نتائج!</p>';
         } else {
            echo '<p class="empty">لم تتم إضافة حسابات مستخدمين حتى الان !</p>';
         }
         ?>

        </div>

    </section>

    <!-- users section ends -->
















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>