<?php
namespace Drupal\library\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use \Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Provides a 'ActionBlock' Block.
 *
 * @Block(
 *   id = "document_action_block",
 *   admin_label = @Translation("Document Action block")
 * )
 */
class ActionBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $lib = \Drupal::service('library.service');

        //Get the current page path
        $current_path = \Drupal::service('path.current')->getPath();
        $current_path = explode('/', $current_path)[1];

        //Check the ADD button is enable or not
        $btn_add = $lib->isPathEnable($current_path, 'add');
        $btn_exprt = $lib->isPathEnable($current_path, 'export');

        return [
        '#theme' => 'document_action_block',
        '#data' => ['path' => $current_path, 'add' => $btn_add, 'export' => $btn_exprt ],
        ];
    }

    /**
     * To disable cache for this block
     *
     * @return int
     */
    public function getCacheMaxAge()
    {
        return 0;
    }

}
