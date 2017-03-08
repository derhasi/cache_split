<?php
/**
 * @file
 * Contains \Drupal\cache_split\CacheCacheBackendMatcherCollection.
 */

namespace Drupal\cache_split\Cache;

/**
 * Collection for multiple cache backends.
 */
class CacheBackendMatcherCollection {

  /**
   * @var \Drupal\cache_split\Cache\CacheBackendMatcher[]
   */
  protected $matchers = [];

  public function add(CacheBackendMatcher $matcher) {
    $this->matchers[] = $matcher;
  }

  /**
   * @param string $cid
   *
   * @return \Drupal\cache_split\Cache\CacheBackendMatcher
   */
  protected function getMatcher($cid) {
    foreach ($this->matchers as $matcher) {
      if ($matcher->match($cid)) {
        return $matcher;
      }
    }
  }

  public function callSingle($cid, $method, $args = []) {
    return $this->getMatcher($cid)->call($method, $args);
  }

  public function callMultiple($cids, $method, $args = []) {
    return $this->callMultipleByRef($cids, $method, $args);
  }

  public function callMultipleByRef(&$cids, $method, $args = []) {
    $return = [];
    $rest = $cids;
    $unprocessed = [];
    foreach ($this->matchers as $matcher) {
      $filtered = $matcher->filter($rest);
      $ret = $matcher->call($method, [&$filtered] + $args);
      $unprocessed += $filtered;
      if (is_array($ret)) {
        $return += $ret;
      }

      // The rest will be processed by the next matcher.
      $rest = array_diff($rest, $filtered);
      if (empty($rest)) {
        break;
      }
    }
    $cids = $unprocessed;
    return $return;
  }

  public function callAll($method, $args) {
    foreach ($this->matchers as $matcher) {
      return call_user_func_array([$matcher->getBackend(), $method], $args);
    }
  }

}
