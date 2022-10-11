<?php

namespace Drupal\dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "change_password_block",
 *   admin_label = @Translation("Change Password block"),
 * )
 */
class ChangePassword extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
	global $base_url;
    return [
			'#theme' => 'change_password_card',
			'#lastchanged'	=>	'30'
    ];
  }

 
}