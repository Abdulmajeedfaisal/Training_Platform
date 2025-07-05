<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
}

include 'components/save_send.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/sstyle.css">
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>


    <!-- home section starts  -->

    <div class="home">

        <section class="center">

            <form action="search.php" method="POST" class="search-form" style="direction: rtl;">
                <input type="text" name="search_box" placeholder="ابحث عن دورة ..." maxlength="100" required>
                <button type="submit" class="fas fa-search" name="search_btn"></button>
            </form>

        </section>

    </div>

    <!-- home section ends -->

    <!-- services section starts  -->

    <section class="services" style="direction: rtl;">

        <h1 class=" heading">التخصصات المتاحة</h1>

        <div class="box-container">
            <a href="university_majors.php?get_type=engineering">
                <div class="box">
                    <img src="./images/command-line.png" alt="">
                    <h3>الهندسة</h3>
                </div>
            </a>

            <a href="university_majors.php?get_type=computer">
                <div class="box">
                    <img src="./images/computer-science.png" alt="">
                    <h3>علوم حاسوب</h3>
                </div>
            </a>

            <a href="university_majors.php?get_type=doctor">
                <div class="box">
                    <img src="./images/hospital.png" alt="">
                    <h3>طب</h3>
                </div>
            </a>

            <a href="university_majors.php?get_type=administration">
                <div class="box">
                    <img src="./images/blogger.png" alt="">
                    <h3>العلوم الادارية</h3>
                </div>
            </a>

            <a href="university_majors.php?get_type=applied">
                <div class="box">
                    <img src="./images/applied.png" alt="">
                    <h3>العلوم التطبيقية</h3>
                </div>
            </a>

            <a href="university_majors.php?get_type=education">
                <div class="box">
                    <img src="./images/college.png" alt="">
                    <h3>التربية</h3>
                </div>
            </a>


        </div>

    </section>

    <!-- services section ends -->

    <!-- listings section starts  -->

    <section class="listings" style="direction: rtl;">
        <a href="listing_firm.php" style="margin-top:1.5rem; display: inline;" class="btn">عرض المؤسسات المتاحة</a>
        <h1 class="heading" style="margin-top: 30px;">الفرص التدريبية المتاحة</h1>

        <div class="box-container">
            <?php
            $total_images = 0;
            $select_properties = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC LIMIT 6");
            $select_properties->execute();
            if ($select_properties->rowCount() > 0) {
                while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = :id");
                    $select_user->bindParam(':id', $fetch_property['user_id']);
                    $select_user->execute();
                    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                    $get_data = "data:" . $fetch_property['image_type'] . ";base64," . base64_encode($fetch_property['image']);

                    $total_images = 1;

                    $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :property_id and user_id = :user_id");
                    $select_saved->bindParam(':property_id', $fetch_property['id']);
                    $select_saved->bindParam(':user_id', $user_id);
                    $select_saved->execute();

            ?>
            <form action="" method="POST">
                <div class="box">
                    <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
                    <?php
                            if ($select_saved->rowCount() > 0) {
                            ?>
                    <button type="submit" name="save" class="save" style="direction: ltr;"><i
                            class="fas fa-heart"></i><span>تم
                            الحفظ</span></button>
                    <?php
                            } else {
                            ?>
                    <button type="submit" name="save" class="save" style="direction: ltr;"><i
                            class="far fa-heart"></i><span>حفظ</span></button>
                    <?php
                            }
                            ?>
                    <div class="thumb">
                        <p class="total-images" style="direction: ltr;"><i
                                class="far fa-image"></i><span><?= $total_images; ?></span></p>
                        <img src="<?= $get_data; ?>" alt="">
                    </div>
                    <div class="price" style="margin-top: 10px; margin-bottom: 10px;">عدد المتدربين <i
                            class="fa-solid fa-person-circle-check" style="margin-left: 5px;"></i> :
                        <span style="color:black;  margin-right: 5px;"><?= $fetch_property['student_count']; ?></span>
                    </div>
                    <h3 class="name">أسم الدورة : <?= $fetch_property['property_name']; ?></h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i>
                        <span><?= $fetch_property['address']; ?></span>
                    </p>
                    <div class="admin">
                        <h3><?= substr($fetch_user['name'], 0, 1); ?></h3>
                        <div>
                            <p><?= $fetch_user['name']; ?></p>
                            <span><?= $fetch_property['date']; ?></span>
                        </div>
                    </div>
                    <div class="flex-btn">
                        <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">عرض معلومات الدورة
                        </a>
                        <a href="ask_for_requset.php?get_id=<?= $fetch_property['id']; ?>" class="btn">تقديم طلب
                        </a>

                    </div>
                </div>
            </form>
            <?php
                }
            } else {
                echo '<p class="empty">لم يتم إضافة دورات تدريبية بعد ! </p>';
            }
            ?>

        </div>

        <div style="margin-top: 2rem; text-align:center;">
            <a href="listings.php" class="inline-btn">view all</a>
        </div>

    </section>

    <!-- listings section ends -->








    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

    <script>
    let range = document.querySelector("#range");
    range.oninput = () => {
        document.querySelector('#output').innerHTML = range.value;
    }
    </script>

</body>

</html>