## Introduction
The mission of this library - provide the easiest way to create and manage a list of hashes.      


## Features
The library is file-based, so it doesn't require any databases. 


## Requirements
* PHP >= 7.0.0
* [Able/Helpers](https://github.com/phpable/helpers)


## Install
There's the simple way to install the ```Eggbe/HashStore``` package via [composer](http://getcomposer.org):
```bash
composer require eggbe/hash-store
```

##Configuration
Please, follow the example below:

```php
$HashStore = new \Eggbe\HashStore\HashStore([
	'path' => 'path-to-storage-directory',
	'sort' => HashStore::BY_DATE | HashStore::BY_DESC,
	'filter' => '^.{0,32}$',
]);
```

The `path` option defines the directory to store all generated files. 
Please, be sure that directory exists and writable, or you risk to get an exception otherwise. 


The `sort` option specifies the sorting and can take the combination of binary flags. 
Currently, you can choose the sorting by alphabet or by creation date in direct and reverse order.


The `filter` option set the regular expression for keywords syntax checking. 
If any given keyword doesn't match this expression, the exception will be thrown.
     
     
## Usage
You have to use the following method to create and add new hash into storage: 
```php
$HashStore->create('keyword');
```
 
Also, you always can view all existing hashes via the following code: 
```php
foreach($HashStore->all() as $keyword => $content){
	echo $keyword . ' ' . $content;
}  
```

The following method helps you to find any hash by a keyword:
```php
$hash = $HashStore->find('keyword');
```

But if you need to get a keyword by a hash so it also possible:
```php
$keyword = $HashStore->search('a2f51b04c9a31cd2defc4d3550eecb71');
```

Of course you can remove any hash if you really need:
```php
$HashStore->remove('keyword');
```

Unfortunately currently this library support only md5 hashes usage but we have plans to extend of the functionality. We will keep you in touch!


##Limitation
Currently, the only md5 hashes are supported.


## License
This package is released under the [MIT license](https://github.com/eggbe/hash-store/blob/master/LICENSE).
