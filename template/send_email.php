<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "admin@balidiving.com";
    $subject = "New Course Booking: " . $_POST['courseName'];
    
    $body = "New booking received:\n\n";
    $body .= "Course: " . $_POST['courseName'] . "\n";
    $body .= "Date: " . $_POST['courseDate'] . "\n";
    $body .= "Name: " . $_POST['fullName'] . "\n";
    $body .= "Email: " . $_POST['email'] . "\n";
    $body .= "Phone: " . $_POST['phone'] . "\n";

    $headers = "From: noreply@balidiving.com\r\n";
    $headers .= "Reply-To: " . $_POST['email'] . "\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
