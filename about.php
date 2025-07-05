<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- about section starts  -->

    <section class="about" style="direction: rtl;">

        <div class="row">
            <div class="image">
                <img src="images/Profiling-bro.svg" alt="">
            </div>
            <div class="content">
                <h3 style="font-size: 3rem;">لماذا أخترتنا ?</h3>
                <p style="font-size: 2rem;"> هذه المنصة لكونها الفريدة من نوعها في المملكة
                    في تسهيل التقديم والحصول على فرص تدريبية، وكذلك توفير ميزة الإتصال والتواصل مع الجهات التدريبية
                    ومشرفيها وتوفير العناء والتعب بالبحث عن جهه تدريبية تقبل تخصُصي الدراسي?</p>
                <a href="contact.php" class="inline-btn">تواصل معنا</a>
            </div>
        </div>

    </section>

    <!-- about section ends -->



    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>