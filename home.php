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
    <style>
    .listings .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        align-items: flex-start;
        justify-content: center;
        gap: 1.5rem;
    }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>


    <!-- home section starts  -->

    <div class="home">

        <section class="center">

            <form action="search.php" method="POST" class="search-form" style="direction: rtl;">
                <input type="text" name="search_box" placeholder="ابحث عن مؤسسة ..." maxlength="100" required>
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
                    <h3>الادارية</h3>
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
        <h1 class="heading">جميع المؤسسات المتاحة</h1>

        <div class="box-container">

            <?php
            $total_images = 0;
            $select_firm = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'company'  ORDER BY date DESC LIMIT 6");
            $select_firm->execute();
            if ($select_firm->rowCount() > 0) {
                while ($fetch_company = $select_firm->fetch(PDO::FETCH_ASSOC)) {
                    $select_user = $conn->prepare("SELECT * FROM `frims` WHERE frim_id = :id LIMIT 1");
                    $select_user->bindParam(':id', $fetch_company['id']);
                    $select_user->execute();
                    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                    $select_property = $conn->prepare("SELECT * FROM `property` WHERE user_id = :id");
                    $select_property->bindParam(':id', $fetch_company['id']);
                    $select_property->execute();
                    $total_properties = $select_property->rowCount();
                    $get_data = "data:" . $fetch_user['image_type'] . ";base64," . base64_encode($fetch_user['image']);

                    $total_images = 1;
            ?>
            <form action="" method="POST" style="direction: rtl;">
                <div class="box">
                    <input type="hidden" name="property_id" value="<?= $fetch_company['id']; ?>">
                    <div class="thumb" style="margin:0;">
                        <p class="total-images" style="direction: ltr;"><i
                                class="far fa-image"></i><span><?= $total_images; ?></span></p>
                        <img src="<?= $get_data; ?>" alt="">
                    </div>
                    <h3 class="name">أسم المؤسسة: <?= $fetch_company['name']; ?></h3>
                    <div class="price">عدد الدورات<i class="fa-solid fa-person-circle-check"></i> :
                        <span style="color:black;"><?= $total_properties; ?></span>
                    </div>
                    <p class="location"><i class=" fas fa-map-marker-alt"></i><a
                            href="<?= $fetch_company['address']; ?>"
                            target="_blank"><span><?= $fetch_company['address']; ?></span></a>

                    </p>
                    <div class="flex-btn">
                        <a href="view_institution.php?get_id=<?= $fetch_company['id']; ?>" class="btn">عرض المؤسسة</a>
                    </div>
                </div>
            </form>

            <?php
                }
            } else {
                echo '<p class="empty">لا توجد مؤسسات  بعد!</p>';
            }
            ?>

        </div>

        <div style="margin-top: 2rem; text-align:center;">
            <a href="listing_firm.php" class="inline-btn">view all</a>
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