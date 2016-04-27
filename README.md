Symfony2 Alipay bundle

This bundle permits you to create, modify and read excel objects.

## Installation

**1**  Add to composer.json to the `require` key

``` yml
    "require" : {
        "simplephp/AlipayBundle": "~1.0",
    }
``` 

**2** Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Simplephp\AlipayBundle\AlipayBundle(),
    );
```

## TL;DR

- Create an empty object:

``` php
$phpExcelObject = $this->get('alipay')->createPHPExcelObject();
```

- Create an object from a file:

``` php
$phpExcelObject = $this->get('alipay')->createPHPExcelObject('file.xls');
```

- Create a Excel5 and write to a file given the object:

```php
$writer = $this->get('alipay')->createWriter($phpExcelObject, 'Excel5');
$writer->save('file.xls');
```

- Create a Excel5 and create a StreamedResponse:

```php
$writer = $this->get('alipay')->createWriter($phpExcelObject, 'Excel5');
$response = $this->get('alipay')->createStreamedResponse($writer);
```


## Example



