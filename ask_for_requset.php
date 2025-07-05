<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
   $user_id = $_COOKIE['user_id'];
   $user_type = $_COOKIE['user_type'];
} else {
   $user_id = '';
   $user_type = '';
}
if (isset($_GET['get_id'])) {
   $get_id = $_GET['get_id'];
} else {
   $get_id = '';
   header('location:home.php');
}
include 'components/save_send.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send requests</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="property-form">
        <?php
      $select_properties = $conn->prepare("SELECT * FROM `property` WHERE id = :id ORDER BY date DESC LIMIT 1");
      $select_properties->bindParam(':id', $get_id);
      $select_properties->execute();
      if ($select_properties->rowCount() > 0) {
         $fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC);
      ?>
        <form action="" method="POST" enctype="multipart/form-data" style="direction: rtl;">
            <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
            <h3>تقديم طلب</h3>
            <div class="box">
                <p> اسم مقدم الطلب<span>*</span></p>
                <input type="text" name="student_name" required maxlength="50" placeholder="ادخل الاسم" class="input">
            </div>
            <div class="flex">
                <div class="box">
                    <p>الرقم الجامعي<span>*</span></p>
                    <input type="number" name="collage_number" required maxlength="100" placeholder="إدخل الرقم "
                        class="input">
                </div>
                <div class="box">
                    <p>اسم الجامعة التي تدرس فيها<span>*</span></p>
                    <input type="text" name="collage_name" required min="0" max="9999999999" maxlength="10"
                        placeholder="إدخل اسم الجامعة" class="input">
                </div>
                <div class="box">
                    <p>التخصص الجامعي <span>*</span></p>
                    <select name="specialization" required class="input">
                        <option value="engineering">الهندسة</option>
                        <option value="computer">علوم حاسوب</option>
                        <option value="doctor">طب عام</option>
                        <option value="administration">العلوم الادارية</option>
                        <option value="applied">العلوم التطبيقية</option>
                        <option value="education">التربية</option>
                    </select>
                </div>
            </div>
            <div class="box">
                <p>إرفاق الخطاب الجامعي<span>*</span></p>
                <input type="file" name="file" class="input" accept="application/pdf" required>
            </div>
            <input type="submit" value="تقديم طلب" name="send" class="btn">
        </form>
        <?php
      } else {
         echo '<p class="empty">property not found! <a href="post_property.php" style="margin-top:1.5rem;" class="btn">add new</a></p>';
      }
      ?>
    </section>





    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>