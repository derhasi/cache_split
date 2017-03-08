<?php
/**
 * @file
 * Contains \Drupal\cache_split\Cache\CacheBackendMatcher.
 */

namespace Drupal\cache_split\Cache;

use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Provides a matcher assoiciatin a Cache Backend with a cache id pattern.
 */
class CacheBackendMatcher {

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $backend;

  /**
   * @var array
   */
  protected $includes = [];

  /**
   * @var array
   */
  protected $excludes = [];

  /**
   * CacheBackendMatcher constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $backend
   * @param array $config
   */
  public function __construct(CacheBackendInterface $backend, array $config) {
    // Provide defaults for config.
    $config += [
      'includes' => [],
      'excludes' => [],
    ];

    $this->backend = $backend;
    $this->includes = $config['includes'];
    $this->excludes = $config['excludes'];
  }

  /**
   * Check if the given Cache ID matches the pattern.
   *
   * @param string $cid
   *
   * @return bool
   */
  public function match($cid) {
    // @todo
    return TRUE;
  }

  /**
   * Filters the given list of cids.
   *
   * @param array $cids
   *
   * @return array
   */
  public function filter(array $cids) {
    return array_filter($cids, [$this, 'match']);
  }

  /**
   * Call method of the cache backend with given set of arguments.
   *
   * @param string $method
   * @param array $args
   *
   * @return mixed
   */
  public function call($method, $args = []) {
    return call_user_func_array([$this->getBackend(), $method], $args);
  }

  /**
   * Get the associated backend.
   *
   * @return \Drupal\Core\Cache\CacheBackendInterface
   */
  public function getBackend() {
    return $this->backend;
  }

}
