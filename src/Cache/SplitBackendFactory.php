<?php
/**
 * @file
 * Contains \Drupal\cache_split\Cache\SplitBackendFactory.
 */

namespace Drupal\cache_split\Cache;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Factory for cache split backend.
 */
class SplitBackendFactory implements CacheFactoryInterface {

  use ContainerAwareTrait;

  /**
   * @var array
   */
  protected $cache_split_settings = [];

  /**
   * SplitBackendFactory constructor.
   *
   * @param \Drupal\Core\Site\Settings $settings
   */
  public function __construct(Settings $settings) {
    $this->cache_split_settings = $settings->get('cache_split', []);
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $collection = $this->getMatchers($bin);
    return new SplitBackend($collection);
  }

  /**
   * Get collection of matcher items.
   *
   * @param string $bin
   *
   * @return \Drupal\cache_split\Cache\CacheBackendMatcherCollection
   */
  protected function getMatchers($bin) {
    if (empty($this->cache_split_settings[$bin])) {
      return [];
    }

    $collection = new CacheBackendMatcherCollection();
    foreach ($this->cache_split_settings[$bin] as $key => $config) {
      $config += [
        'backend' => $key,
        'includes' => [],
        'excludes' => [],
      ];
      $backend = $this->getCacheBackend($bin, $config);
      $collection->add(new CacheBackendMatcher($backend, $config));
    }

    // Set the default backend to be the database.
    $collection->setDefaultBackend($this->getCacheBackend($bin, ['backend' => 'cache.backend.database']);

    return $collection;
  }

  /**
   *
   * @param $string bin
   * @param array $config
   *
   * @return \Drupal\Core\Cache\CacheBackendInterface
   * @throws \Exception
   */
  protected function getCacheBackend($bin, array $config) {
    $backend = $this->container->get($config['backend']);
    // Check if we got a cache factory here.
    if (!$backend instanceof CacheFactoryInterface) {
      throw new \Exception(sprintf('Services "%s" does not implement CacheFactoryInterface', $config['backend']));
    }

    return $backend->get($bin);
  }


}
