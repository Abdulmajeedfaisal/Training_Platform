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
    <title>All Listings</title>

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

    <!-- services section starts  -->

    <section class="listings" style="direction: rtl;">
        <form action="" method="POST" class="search-form">
            <input type="text" name="search_box" placeholder="بحث عن مؤسسة..." maxlength="100" required>
            <button type="submit" class="fas fa-search" name="search_btn"></button>
        </form>
        <h1 class="heading">جميع المؤسسات المتاحة</h1>

        <div class="box-container">

            <?php
            if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                $search_box = $_POST['search_box'];
                $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
                $select_firm = $conn->prepare("SELECT * FROM `users` WHERE user_type = 'company' AND (name LIKE '%{$search_box}%' OR number LIKE '%{$search_box}%' OR email LIKE '%{$search_box}%')");
                $select_firm->execute();
            } else {
                $select_firm = $conn->prepare("SELECT * FROM `users` WHERE  user_type = 'company' ORDER BY date DESC");
                $select_firm->execute();
            }
            $total_images = 0;
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
            } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
                echo '<p class="empty"> لم يتم العثور على نتائج!</p>';
            } else {
                echo '<p class="empty">لا توجد مؤسسات  بعد!</p>';
            }
            ?>

        </div>
    </section>

    <!-- services section ends -->



    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>