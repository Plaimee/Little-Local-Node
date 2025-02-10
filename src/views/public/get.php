<?php
    // echo 'Current PHP version: ' . phpversion();
    require "db.php";

    if (!isset($_GET['noId']) || empty($_GET['noId'])) {
        die("Error: Missing noId parameter.");
    }

    $fakeId = $_GET['noId'];
    $dir_src = "uploads/";
    $img_url_1 = "";
    $img_url_2 = "";
    $img_url_3 = "";

    $smtxt1 = "chaop";
    $smtxt2 = "secop";
    $smtxt3 = "fiop";

    // ใช้ Prepared Statement ป้องกัน SQL Injection
    $sql = "SELECT images FROM lclm_outputs WHERE noId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fakeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $img_temp = $row["images"];
            // echo "ชื่อรูปคือ ".$img_temp;
            // echo "คำสำคัญ ".$smtxt1;
            // echo strpos($img_temp, $smtxt1) !== false;


            // if(strpos("chaop_1739123747112.png", "chaop") !== false) {
            //     echo "พบคำว่า chaop ใน chaop_1739123747112.png";
            // }
            // ใช้ strpos() แทน str_contains()
            if (strpos($img_temp, $smtxt1) !== false) {
                $img_url_1 = $dir_src.$img_temp;
            } else if (strpos($img_temp, $smtxt2) !== false) {
                $img_url_2 = $dir_src.$img_temp;
            } else if (strpos($img_temp, $smtxt3) !== false) {
                $img_url_3 = $dir_src.$img_temp;
            } else {
                echo "Image URL is null";
            }
        }
    } else {
        echo "0 results";
    }

    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Local</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Mali", "sans-serif"],
                    },
                },
            },
        };
        function copyToClipboard(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // สำหรับมือถือ
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("คัดลอกข้อความเรียบร้อย: " + copyText.value);
            }).catch(err => {
                console.error("เกิดข้อผิดพลาดในการคัดลอก:", err);
            });
        }

        function goToGoogleMaps() {
            var mapsUrl = document.getElementById("url").value;
            window.open(mapsUrl, "_blank");
        }
    </script>
    <style type="text/tailwindcss">
        @import url();
        @layer components {
          .pad-main {
            @apply pt-3 px-5;
          }
        }
    </style>
</head>
<body>
    <div class="flex flex-col pad-main justify-center space-y-5 w-full">
        <div class="flex flex-col items-center justify-center space-y-3">
            <img src="./assets/sponsors.png" alt="" class="w-3/4 h-auto">
            <div class="text-3xl font-bold">ดาวน์โหลด</div>
            <div class="text-2xl font-bold">Download</div>
            <div class="text-2xl">กดค้างที่รูปเพื่อบันทึก</div>
        </div>

        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="text-3xl font-bold">รูปที่ 1/3</div>
            <img src="<?php echo $img_url_1; ?>" alt="" class="w-[360px] h-[640px] object-contain border rounded-md">
        </div>
        <hr>
        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="text-3xl font-bold">รูปที่ 2/3</div>
            <img src="<?php echo $img_url_2; ?>" alt="" class="w-[360px] h-[640px] object-contain border rounded-md">
        </div>
        <hr>
        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="text-3xl font-bold">รูปที่ 3/3</div>
            <img src="<?php echo $img_url_3; ?>" alt="" class="w-[360px] h-[640px] object-contain border rounded-md">
        </div>
        <hr>
        <div class="flex flex-col items-center justify-center w-full space-y-3">
            <div class="text-3xl font-bold text-center">ลิตเติ้ลโลคอล ลิตเติ้ลเมมโมรี่</div>
            <div class="text-3xl font-bold">Current Location</div>
            <form class="w-full space-y-3">
                <div class="flex flex-col space-y-2">
                    <label for="location" class="font-bold">ที่อยู่</label>
                    <textarea name="location" id="location" class="border-2 border-black rounded-md p-3" readonly>ถ. มหาไชย แขวงสำราญราษฎร์ เขตพระนคร กรุงเทพมหานตคร 10200</textarea>
                    <button type="button" class="w-full bg-black rounded-md p-3" onclick="copyToClipboard('location')">
                        <div class="text-white font-bold">Coppy</div>
                    </button>
                </div>
                
                <div class="flex flex-col space-y-2">
                    <label for="address" class="font-bold">Address</label>
                    <textarea name="address" id="address" class="border-2 border-black rounded-md p-3" readonly>ถ. มหาไชย แขวงสำราญราษฎร์ เขตพระนคร กรุงเทพ ประเทศไทย</textarea>
                    <button class="w-full bg-black rounded-md p-3" onclick="copyToClipboard('address')">
                        <div class="text-white font-bold">Coppy</div>
                    </button>
                </div>

                <div class="flex flex-col space-y-2">
                    <label for="url" class="font-bold">Google Map URL</label>
                    <textarea name="url" id="url" class="border-2 border-black rounded-md p-3" readonly>https://maps.app.goo.gl/CyKacaPz2aA94xry8</textarea>
                    <button class="w-full bg-black rounded-md p-3" onclick="copyToClipboard('url')">
                        <div class="text-white font-bold">Coppy</div>
                    </button>
                    <button class="w-full bg-black rounded-md p-3" onclick="goToGoogleMaps()">
                        <div class="text-white font-bold">Go to Google Map</div>
                    </button>
                </div>
            </form>
        </div>
        <hr>
        
        <div class="flex flex-col items-center justify-center w-full space-y-3">
            <img src="./assets/sponsors.png" alt="" class="w-3/4 h-auto">
            <div class="font-bold text-center">Credit</div>
            <div class="font-bold text-center">Artwork by jmons (IG @jmons_)</div>
            <div class="font-bold text-center">Interactive by Jiratchaya Ninsuay, Piyanut Plaimee, Silpsupa Sereesuchart and Interactive Application Program Team</div>
        </div>
    </div>
</body>
</html>