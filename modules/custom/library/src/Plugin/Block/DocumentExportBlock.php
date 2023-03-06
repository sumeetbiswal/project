<?php
namespace Drupal\library\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'DocumentExportBlock' Block.
 *
 * @Block(
 *   id = "document_generate_block",
 *   admin_label = @Translation("Document Export block")
 * )
 */
class DocumentExportBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $current_path = \Drupal::service('path.current')->getPath();
    $current_path = ltrim($current_path, '/');

    return [
      '#theme' => 'document_export_block',
      '#data' => ['path' => $current_path],
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
