<?php
namespace Drupal\library\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'ActionBlock' Block.
 *
 * @Block(
 *   id = "document_action_block",
 *   admin_label = @Translation("Document Action block")
 * )
 */
class ActionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $lib = \Drupal::service('library.service');

    $current_path = \Drupal::service('path.current')->getPath();
    $current_path = ltrim($current_path, '/');


    $btn_add = $lib->isPathEnable($current_path . '/add');

    return [
      '#theme' => 'document_action_block',
      '#data' => ['path' => $current_path, 'add' => $btn_add],
    ];
  }

  /**
   * To disable cache for this block
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
