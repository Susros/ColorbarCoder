# ColorbarCoder
ColorbarCoder encodes the original text string to color codes and decode color codes from color bar image into its original text.

# Basic Usage
Include ColorbarCoder if you are not using autoload.

```php
include_once "path/to/ColorbarCoder.php"
```

Instantiate ColorbarCoder

```php
$colorbarCoder = new ColorbarCoder();
```

Encode the text,

```php
// Original text 
$text = "This is awesome!"; 

// Encode the text
$colorbarCoder->encode($text);
```

Print the colorbar image,

```php
$colorbarCoder->printColorbarImage(500, 500);
```

This will print out image with 500 x 500 dimention. The default image extension is PNG. JPG format can be created by declaring JPG in the parameter. However, only PNG works for decoding the color bar as JPG will reduce the quality and result in different color code. Therefore, the original text will not be accurate. It is recommended to use PNG instead.

Image here

Decode the color bar image,

```php
// Image file
$image = "./img/colorbar.png";

// Decode the image
$colorbarCoder->decode($image);
```

Print the decoded text,

```php
echo $colorbarCoder->getText();
```

Output

```
This is awesome!
```
