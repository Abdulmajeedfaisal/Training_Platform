<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
    if ($user_type == "client") {
        header("location: ./");
    }
} else {
    $user_id = '';
    $user_type = '';
    header('location:login.php');
}
if (isset($_GET['get_id'])) {
    $get_property_id = $_GET['get_id'];
} else {
    $get_property_id = '';
    header('location:home.php');
}
if (isset($_POST['delete'])) {

    $delete_id = $_POST['request_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `requests` WHERE id = :id");
    $verify_delete->bindParam(':id', $delete_id);
    $verify_delete->execute();

    if ($verify_delete->rowCount() > 0) {
        $delete_request = $conn->prepare("DELETE FROM `requests` WHERE id = :delete_id");
        $delete_request->bindParam(':delete_id', $delete_id);
        $delete_request->execute();
        $success_msg[] = 'تم حذف الطلب بنجاح !';
    } else {
        $warning_msg[] = 'الطلب تم حذفه مسبقا !';
    }
}
if (isset($_POST['accept'])) {

    $accept_id = $_POST['request_id'];
    $accept_id = filter_var($accept_id, FILTER_SANITIZE_STRING);

    $verify_accept = $conn->prepare("SELECT * FROM `requests` WHERE id = :id");
    $verify_accept->bindParam(':id', $accept_id);
    $verify_accept->execute();

    if ($verify_accept->rowCount() > 0) {
        $accept_request = $conn->prepare("UPDATE `requests` SET activation = '1' WHERE id = :id");
        $accept_request->bindParam(':id', $accept_id);
        $accept_request->execute();
        $success_msg[] = 'تم قبل الطلب!';
    } else {
        $warning_msg[] = 'الطلب تم قبوله مسبقا!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>requests</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">
    <style>
        .btn1 {
            background-color: #198754;
        }

        .flex1 {
            display: flex;
            gap: 20px;
            margin-top: 5rem;
        }

        .pdf {
            height: 250px;
        }

        @media (max-width:568px) {
            .flex1 {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="property-form">
        <?php
        $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE property_id = :property_id AND activation = '0'");
        $select_requests->bindParam(':property_id', $get_property_id);
        $select_requests->execute();
        if ($select_requests->rowCount() > 0) {
            $fetch_request = $select_requests->fetch(PDO::FETCH_ASSOC);

            $select_sender_information = $conn->prepare("SELECT * FROM `files` WHERE property_id = :id");
            $select_sender_information->bindParam(':id', $fetch_request['property_id']);
            $select_sender_information->execute();
            $fetch_sender_information = $select_sender_information->fetch(PDO::FETCH_ASSOC);
            $get_data = "data:" . $fetch_sender_information['pdf_type'] . ";base64," . base64_encode($fetch_sender_information['pdf_file']);
        ?>
            <form action="" method="POST" enctype="multipart/form-data" style="direction: rtl;">
                <input type="hidden" name="request_id" value="<?= $fetch_request['id']; ?>">
                <h3>بيانات الوثيقة</h3>
                <div class="box">
                    <p> اسم مقدم الطلب<span>*</span></p>
                    <input type="text" name="student_name" readonly value="<?= $fetch_sender_information['student_name']; ?>" class="input">
                </div>
                <div class="flex">
                    <div class="box">
                        <p>الرقم الجامعي<span>*</span></p>
                        <input type="text" name="collage_number" readonly value="<?= $fetch_sender_information['collage_number']; ?>" class="input">
                    </div>
                    <div class="box">
                        <p>اسم الجامعة<span>*</span></p>
                        <input type="text" name="collage_name" readonly value="<?= $fetch_sender_information['collage_name']; ?>" class="input">
                    </div>
                    <div class="box">
                        <p>التخصص الجامعي <span>*</span></p>
                        <?php if ($fetch_sender_information['specialization'] == "engineering") { ?>
                            <input type="text" readonly value="هندسة" class="input">
                        <?php } elseif ($fetch_sender_information['specialization'] == "computer") { ?>
                            <input type="text" readonly value="علوم حاسوب" class="input">
                        <?php } elseif ($fetch_sender_information['specialization'] == "doctor") { ?>
                            <input type="text" readonly value="طب عام" class="input">
                        <?php } elseif ($fetch_sender_information['specialization'] == "administration") { ?>
                            <input type="text" readonly value="العلوم الادارية" class="input">
                        <?php } elseif ($fetch_sender_information['specialization'] == "applied") { ?>
                            <input type="text" readonly value="العلوم التطبيقية" class="input">
                        <?php } else { ?>
                            <input type="text" readonly value="التربية" class="input">
                        <?php } ?>
                    </div>
                </div>

                <div class="box pdf">
                    <p style="margin-bottom: 10px;">ملف الوثيقة<span>*</span></p>
                    <embed src="<?= $get_data ?>" frameborder="0" style="border-bottom: var(--border);
                    border-bottom-width: 2px;" scrolling="auto" width="100%" height="100%" />
                </div>
                <div class="flex1">
                    <input type="submit" value="قبول الطلب" class="btn btn1" name="accept">
                    <a href="view_property.php?get_id=<?= $fetch_request['property_id']; ?>" class="btn">عرض الدورة</a>
                    <input type="submit" value="حذف الطلب" class="btn" onclick="return confirm('remove this request?');" name="delete">
                </div>

            </form>
        <?php
        } else {
            echo '<p class="empty">لا يوجد طلب بعد !</p>';
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