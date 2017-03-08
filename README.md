# cache_split

A cache backend for Drupal to split cache items in separate backends.

## Installation

1. `composer require derhasi/cache_split:dev-master`
2. Enable `cache_split` module (e.g. `drush en cache_split`)
3. Change the cache backend for your bin (e.g. `render`) in your _settings.php_
```php
<?php
$settings['cache']['bins']['render'] = 'cache.backend.split';
?>
```
4. Add split configuration for your bin to the _settings.php_:
```php
<?php
$settings['cache_split'] = [
  'render' => [
    // Do not cache render results for paragraphs, as they are only rendered in
    // context of the host entity.
    [
      'backend' => 'cache.backend.null',
      'includes' => [
        'entity_view:paragraph:*'
      ],
      'excludes' => [],
    ],
    [
      'backend' => 'cache.backend.database',
      //...
    ],
  ],
];
?>
```

## Configuration

The configuration for a cache bin has to be defined in the _settings.php_:
`$settings['cache_split']['NAME_OF_CACHE_BIN'] = [...]`.

Each bin holds multiple matcher definitions:

* `backend`: Name of the cache backend service to use (e.g. `cache.backend.database`)
* `includes`: Array of cid patterns this backend should be used for. If this is
   empty all cids are included (except those excluded by `excludes`).
* `excludes`: Array of cid patterns this backend should **not** be used for

Wildcard syntax: A cid pattern may use `*` to match any number of arbitrary characters.
