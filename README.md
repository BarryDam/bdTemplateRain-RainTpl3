bdTemplateRain-RainTpl3
=======================

Extension for RainTPL 3 (https://github.com/rainphp/raintpl3)

RaintTPL3 forces you to store and call template files and cache files in one and the same base directory.

With bdTemplateRain you can easily and quickly execute RainTPL3 and store and call each template file in your specified folder.


Use EXAMPLE QuickRender
=======================

file : X/your/location/template.tpl 
```html
<div>{$test}</div>
```

in php
```php
echo bdTemplateRain::render('X/your/location/template.tpl', array('test'=>'example'));
```

result:
```
<div>example</div>
```

The cache file of the rendered template will be stored in 'X/your/location/cache/template.tpl'


Requires
=======================
In php define 'URL_BASE' your url root (http://myroot.com/) and 'FILE_PATH' , your absolute filepath ('X/var/myroot.com/root/')
```php
	define('URL_BASE', 'http://myroot.com/');
	define('FILE_PATH', 'X/var/myroot.com/root/');
```


$template_info support in RainTPL 3
=======================
In RainTPL 2 you could use the special variable {$template_info} to see all the variables assigned to your template.
Unfortunately, RaintTPL 3 dropped this awesome future. So now with bdTemplateRain I brought back this feature.

