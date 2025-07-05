<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_type'])) {
    $user_id = $_COOKIE['user_id'];
    $user_type = $_COOKIE['user_type'];
} else {
    $user_id = '';
    $user_type = '';
}

if (isset($_POST['send'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $message = $_POST['message'];
    $message = filter_var($message, FILTER_SANITIZE_STRING);

    $verify_contact = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
    $verify_contact->execute([$name, $email, $number, $message]);

    if ($verify_contact->rowCount() > 0) {
        $warning_msg[] = 'تم ارسال الرساله مسبقا !';
    } else {
        $send_message = $conn->prepare("INSERT INTO `messages`(name, email, number, message) VALUES(?,?,?,?)");
        $send_message->execute([$name, $email, $number, $message]);
        $success_msg[] = 'تم إرسال الرسالة بنجاح !';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/Style1.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- contact section starts  -->

    <section class="contact">

        <div class="row">
            <div class="image">
                <img src="images/contact-img.svg" alt="">
            </div>
            <form action="" method="post" style="direction: rtl;">
                <h3>إبقى على تواصل</h3>
                <input type="text" name="name" required maxlength="50" placeholder="إدخل إسمك" class="box">
                <input type="email" name="email" required maxlength="50" placeholder="أدخل بريدك الإلكتروني" class="box">
                <input type="number" name="number" required maxlength="10" max="9999999999" min="0" placeholder="إدخل رقمك" class="box">
                <textarea name="message" placeholder="إدخل الرسالة" required maxlength="1000" cols="30" rows="10" class="box"></textarea>
                <input type="submit" value="إرسال" name="send" class="btn">
            </form>
        </div>

    </section>

    <!-- contact section ends -->




    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>