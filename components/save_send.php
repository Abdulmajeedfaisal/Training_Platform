<?php

if (isset($_POST['save'])) {
   if ($user_id != '') {

      $property_id = $_POST['property_id'];

      $verify_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = :id and user_id = :user_id");
      $verify_saved->bindParam(':id', $property_id);
      $verify_saved->bindParam(':user_id', $user_id);
      $verify_saved->execute();

      if ($verify_saved->rowCount() > 0) {
         $remove_saved = $conn->prepare("DELETE FROM `saved` WHERE property_id = :id AND user_id = :user_id");
         $remove_saved->bindParam(':id', $property_id);
         $remove_saved->bindParam(':user_id', $user_id);
         $remove_saved->execute();
         $success_msg[] = 'تم إلغاء الحفظ !';
      } else {
         $insert_saved = $conn->prepare("INSERT INTO`saved`(property_id, user_id) VALUES(:id,:user_id)");
         $insert_saved->bindParam(':id', $property_id);
         $insert_saved->bindParam(':user_id', $user_id);
         $insert_saved->execute();
         $success_msg[] = 'تم حفظ الدورة ! ';
      }
   } else {
      $warning_msg[] = 'الرجاء تسجيل الدخول أولا !';
   }
}

if (isset($_POST['send'])) {
   if ($user_id != '') {

      $property_id = $_POST['property_id'];

      $select_receiver = $conn->prepare("SELECT * FROM `property` WHERE id = :property_id LIMIT 1");
      $select_receiver->bindParam(':property_id', $property_id);
      $select_receiver->execute();
      $fetch_receiver = $select_receiver->fetch(PDO::FETCH_ASSOC);
      $receiver = $fetch_receiver['user_id'];
      $status = $fetch_receiver['status'];
      $verify_request = $conn->prepare("SELECT * FROM `requests` WHERE property_id = :property AND sender_id = :sender AND receiver_id = :receiver");
      $verify_request->bindParam(':property', $property_id);
      $verify_request->bindParam(':sender', $user_id);
      $verify_request->bindParam(':receiver', $receiver);
      $verify_request->execute();

      if (($verify_request->rowCount() > 0)) {
         $warning_msg[] = 'الطلب ارسل من قبل !';
      } else {
         if ($status == "available") {
            $student_name = $_POST['student_name'];
            $student_name = filter_var($student_name, FILTER_SANITIZE_STRING);
            $name_user = trim($student_name);
            $collage_number = $_POST['collage_number'];
            $collage_number = filter_var($collage_number, FILTER_SANITIZE_STRING);
            $collage_name = $_POST['collage_name'];
            $collage_name = filter_var($collage_name, FILTER_SANITIZE_STRING);
            $specialization = $_POST['specialization'];
            $specialization = filter_var($specialization, FILTER_SANITIZE_STRING);
            if ($name_user != "") {
               $file_name = $_FILES['file']['name'];
               $file_type = $_FILES['file']['type'];
               $file_01_tmp_name = file_get_contents($_FILES['file']['tmp_name']);
               $send_request = $conn->prepare("INSERT INTO `requests`(property_id, sender_id, receiver_id) VALUES(:property,:sender,:receiver)");
               $send_request->bindParam(':property', $property_id);
               $send_request->bindParam(':sender', $user_id);
               $send_request->bindParam(':receiver', $receiver);
               $send_request->execute();
               $sender_information = $conn->prepare("INSERT INTO `files`(student_id, company_id, student_name, collage_name, collage_number, specialization, pdf_file, pdf_name, pdf_type, property_id) VALUES(:student,:comp_id,:student_name,:collage_name,:collage_number,:spec,:file_data,:file_name,:file_type,:property_id)");
               $sender_information->bindParam(':student', $user_id);
               $sender_information->bindParam(':comp_id', $receiver);
               $sender_information->bindParam(':student_name', $student_name);
               $sender_information->bindParam(':collage_name', $collage_name);
               $sender_information->bindParam(':collage_number', $collage_number);
               $sender_information->bindParam(':spec', $specialization);
               $sender_information->bindParam(':file_data', $file_01_tmp_name);
               $sender_information->bindParam(':file_name', $file_name);
               $sender_information->bindParam(':file_type', $file_type);
               $sender_information->bindParam(':property_id', $property_id);
               $sender_information->execute();
               $success_msg[] = 'تم ارسال طلبك بنجاح !';
            } else {
               $error_msg[] = 'الاسم يجب أن يحتوي  على الاقل أحرف فقط ';
            }
         } else {
            $warning_msg[] = ' الدورة غير متاحة !';
         }
      }
   } else {
      $warning_msg[] = 'الرجاء تسجيل الدخول أولا !';
   }
}
