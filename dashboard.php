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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="dashboard" style="direction: rtl;">

        <h1 class="heading">لوحة تحكم</h1>

        <div class="box-container">

            <div class="box">
                <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = :users_id LIMIT 1");
                $select_profile->bindParam(':users_id', $user_id);
                $select_profile->execute();
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <h3>مرحبا!</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href=" update.php" class="btn">تحديث الملف الشخصي</a>
            </div>

            <div class="box">
                <h3>إبحث عن دورة تدريبية</h3>
                <p>إبحث عن دوراتك</p>
                <a href="search.php" class="btn">إبحث الان</a>
            </div>
            <?php if ($user_id != '' && ($user_type == "company" || $user_type == "employee")) { ?>
                <?php if ($user_type == "company") { ?>
                    <div class="box">
                        <?php
                        $number_properties = $conn->prepare("SELECT * FROM `property` WHERE user_id = :id");
                        $number_properties->bindParam(':id', $user_id);
                        $number_properties->execute();
                        $total_properties = $number_properties->rowCount();
                        ?>
                        <h3><?= $total_properties; ?></h3>
                        <p>قائمة الدورات </p>
                        <a href="my_listings.php" class="btn">عرض جميع الدورات</a>
                    </div>
                    <div class="box">
                        <?php
                        $count_properties_company = $conn->prepare("SELECT * FROM `users` WHERE com_id = :id");
                        $count_properties_company->bindParam(':id', $user_id);
                        $count_properties_company->execute();
                        $total_properties = $count_properties_company->rowCount();
                        ?>
                        <h3><?= $total_properties; ?></h3>
                        <p>قائمة المشرفين</p>
                        <a href="employee.php" class="btn">عرض جميع المشرفين</a>
                    </div>
                    <div class="box">
                        <?php
                        $count_requests_sent = $conn->prepare("SELECT * FROM `requests` WHERE activation = '1' AND  receiver_id = :company_id");
                        $count_requests_sent->bindParam(':company_id', $user_id);
                        $count_requests_sent->execute();
                        $total_requests_sent = $count_requests_sent->rowCount();
                        ?>
                        <h3><?= $total_requests_sent; ?></h3>
                        <p>الطلبات تم قبولها</p>
                        <a href="accept_requests.php" class="btn">عرض الطلبات المقبولة</a>
                    </div>
                    <div class="box">
                        <?php
                        $count_requests_received = $conn->prepare("SELECT * FROM `requests` WHERE receiver_id = :id AND activation = '0'");
                        $count_requests_received->bindParam(':id', $user_id);
                        $count_requests_received->execute();
                        $total_requests_received = $count_requests_received->rowCount();
                        ?>
                        <h3><?= $total_requests_received; ?></h3>
                        <p>الطلبات لم يتم قبولها بعد</p>
                        <a href="requests.php" class="btn">عرض جميع الطلبات</a>
                    </div>
                <?php } else { ?>
                    <div class="box">
                        <?php
                        $count_properties_employee = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :id");
                        $count_properties_employee->bindParam(':id', $user_id);
                        $count_properties_employee->execute();
                        $total_properties = $count_properties_employee->rowCount();
                        ?>
                        <h3><?= $total_properties; ?></h3>
                        <p>قائمة الدورات </p>
                        <a href="my_listings.php" class="btn">عرض جميع الدورات</a>
                    </div>
                    <div class="box">
                        <?php
                        $accept_properties_employee = $conn->prepare("SELECT * FROM `property` WHERE employee_id = :employee_id");
                        $accept_properties_employee->bindParam(':employee_id', $user_id);
                        $accept_properties_employee->execute();
                        while ($fetch_employee = $accept_properties_employee->fetch(PDO::FETCH_ASSOC)) {
                            $select_requests_employee = $conn->prepare("SELECT * FROM `requests` WHERE property_id = :id AND activation = '1'");
                            $select_requests_employee->bindParam(':id', $fetch_employee['id']);
                            $select_requests_employee->execute();
                            $total_properties_accept = 0;
                            if ($select_requests_employee->rowCount() > 0) {
                                while ($fetch_request = $select_requests_employee->fetch(PDO::FETCH_ASSOC)) {

                                    $select_sender_for = $conn->prepare("SELECT * FROM `users` WHERE id = :sender_id");
                                    $select_sender_for->bindParam(':sender_id', $fetch_request['sender_id']);
                                    $select_sender_for->execute();
                                    $fetch_sender = $select_sender_for->fetch(PDO::FETCH_ASSOC);
                                    $total_properties_accept =  $select_requests_employee->rowCount() + $accept_properties_employee->rowCount() - 1;
                                }
                            }
                            $total = $total_properties_accept;
                        }
                        ?>
                        <h3><?= $total; ?></h3>
                        <p>الطلبات تم قبولها</p>
                        <a href="accept_requests.php" class="btn">عرض الطلبات المقبولة</a>
                    </div>
            <?php }
            } ?>
            <div class="box">
                <?php
                $count_requests_sent = $conn->prepare("SELECT * FROM `requests` WHERE sender_id = :id AND activation = '0'");
                $count_requests_sent->bindParam(':id', $user_id);
                $count_requests_sent->execute();
                $total_requests_sent = $count_requests_sent->rowCount();
                ?>
                <h3><?= $total_requests_sent; ?></h3>
                <p>الطلبات المرسلة</p>
                <a href="sent.php" class="btn">عرض الطلبات المرسلة</a>
            </div>
            <?php if ($user_type == "client") { ?>
                <div class="box">
                    <?php
                    $count_requests_sent = $conn->prepare("SELECT * FROM `requests` WHERE activation = '1' AND sender_id = :client_id");
                    $count_requests_sent->bindParam(':client_id', $user_id);
                    $count_requests_sent->execute();
                    $total_requests_sent = $count_requests_sent->rowCount();
                    ?>
                    <h3><?= $total_requests_sent; ?></h3>
                    <p>الطلبات تم قبولها</p>
                    <a href="accept_requests.php" class="btn">عرض الطلبات المقبولة</a>
                </div>
            <?php } ?>
            <div class="box">
                <?php
                $count_saved_properties = $conn->prepare("SELECT * FROM `saved` WHERE user_id = :saved_id");
                $count_saved_properties->bindParam(':saved_id', $user_id);
                $count_saved_properties->execute();
                $total_saved_properties = $count_saved_properties->rowCount();
                ?>
                <h3><?= $total_saved_properties; ?></h3>
                <p>الدورات المحفوظة</p>
                <a href="saved.php" class="btn">عرض الدورات المحفوظة</a>
            </div>

        </div>

    </section>






















    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>