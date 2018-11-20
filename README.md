
<p align="center">
	<img src="logo.jpg" width="350" alt="ColorbarCoder" />
</p>

# ColorbarCoder
ColorbarCoder encodes the original text string to color codes and decode color codes from color bar image into its original text.

# Documentation
Full documentation: [Here](http://www.colorbarcoder.com/docs)

# Installation
ColorbarCoder can be downloaded manually or cloneed by using:

```
git clone git@github.com:Susros/ColorbarCoder.git
```

ColorbarCoder is also availabe on Packagist. To install it with Composer, add this line to your ```composer.json``` file:

```
"susros/colorbarcoder" : "~1.0.2"
```

or run

```
composer require susros/colorbarcoder
```

# Basic Usage
Include ColorbarCoder if you are not using autoload.

```php
include_once "path/to/ColorbarCoder.php";
use ColorbarCoder\ColorbarCoder;
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

![alt text](https://github.com/Susros/ColorbarCoder/blob/master/test/img/5603e46d561cef1f6d00bdfebcd77742.png "This is awesome!")

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

For more usages: [See here](http://www.colorbarcoder.com/docs/usage)