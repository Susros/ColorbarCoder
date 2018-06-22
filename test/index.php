<?php

include "../src/ColorbarCoder.php";

$colorbarCoder = new ColorbarCoder();



// Encode
//$colorbarCoder->encode("aaaaaaaaa");
//$colorbarCoder->encode("Hello, World");
//$colorbarCoder->encode("My name is Kelvin. I am software engineer. It is just awesome!");
//$colorbarCoder->encode("This is awesome!");

// Save photos and print it
//$img = $colorbarCoder->saveColorbarImage(500, 500, "./img");
//$colorbarCoder->printColorbarImage(500, 500);

//echo "<img src='img/". $img ."'/>";

//*/



$img = "aa0eb7a90c85e4773d5b242453a79097.png";	// aaaaaaaaa
$img2 = "9cf75dcd79055c7fc2f879008c99a16d.png";	// Hello, World
$img3 = "fec08aa70709c8992241fe24859504ef.png"; // My name is Kelvin. I am software engineer. It is just awesome!
$img4 = "5603e46d561cef1f6d00bdfebcd77742.png"; // This is awesome!

// Decode
$colorbarCoder->decode("./img/" . $img4);

echo $colorbarCoder->getText();

?>