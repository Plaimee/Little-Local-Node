<?php
    require "db.php";

    $noId = $_POST["NoId"];
    $imageName1 = $_POST['ImageName1'];
    $mainImageData1 = $_POST['Image1'];
    $imageName2 = $_POST['ImageName2'];
    $mainImageData2 = $_POST['Image2'];
    $imageName3 = $_POST['ImageName3'];
    $mainImageData3 = $_POST['Image3'];

    // set 1
    list($type, $mainImageData1) = explode(';', $mainImageData1);
    list(, $mainImageData1)      = explode(',', $mainImageData1);
    $mainImageData1 = base64_decode($mainImageData1);
    $mainImage1 = imagecreatefromstring($mainImageData1);
    $mainWidth1 = imagesx($mainImage1);
    $mainHeight1 = imagesy($mainImage1);
    $imageName1 = $imageName1;
    imagepng($mainImage1, './uploads/' . $imageName1);
    imagedestroy($mainImage1);

    // set 2
    list($type, $mainImageData2) = explode(';', $mainImageData2);
    list(, $mainImageData2)      = explode(',', $mainImageData2);
    $mainImageData2 = base64_decode($mainImageData2);
    $mainImage2 = imagecreatefromstring($mainImageData2);
    $mainWidth2 = imagesx($mainImage2);
    $mainHeight2 = imagesy($mainImage2);
    $imageName2 = $imageName2;
    imagepng($mainImage2, './uploads/' . $imageName2);
    imagedestroy($mainImage2);

    // set 3
    list($type, $mainImageData3) = explode(';', $mainImageData3);
    list(, $mainImageData3)      = explode(',', $mainImageData3);
    $mainImageData3 = base64_decode($mainImageData3);
    $mainImage3 = imagecreatefromstring($mainImageData3);
    $mainWidth3 = imagesx($mainImage3);
    $mainHeight3 = imagesy($mainImage3);
    $imageName3 = $imageName3;
    imagepng($mainImage3, './uploads/' . $imageName3);
    imagedestroy($mainImage3);
    
    
    // echo 'https://funcslash.com/projects/2025/lclm/uploads/'. $imageName;

    $sql = "INSERT INTO lclm_outputs (images, noId) VALUES ('$imageName1', '$noId')";
    if ($conn->query($sql) === TRUE) {
        // echo "Data inserted successfully";
    } else {
        echo "Error image1: " . $sql . "<br>" . $conn->error;
    }

    $sql = "INSERT INTO lclm_outputs (images, noId) VALUES ('$imageName2', '$noId')";
    if ($conn->query($sql) === TRUE) {
        // echo "Data inserted successfully";
    } else {
        echo "Error image2: " . $sql . "<br>" . $conn->error;
    }

    $sql = "INSERT INTO lclm_outputs (images, noId) VALUES ('$imageName3', '$noId')";
    if ($conn->query($sql) === TRUE) {
        // echo "Data inserted successfully";
    } else {
        echo "Error image3: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
?>