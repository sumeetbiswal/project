<?php

namespace Drupal\school\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "studentlist_count_block",
 *   admin_label = @Translation("Student List Count block"),
 * )
 */
class StudentlistCount extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        $query->condition('type', 'student');
        $query->condition('status', 1);
        $query->range(0, 1); // only need one
        $query->accessCheck(FALSE);
        $results = $query->execute();
        $count = count($results);

        return [
        '#theme' => 'student_count_card',
        '#student_count' =>    $count
        ];
    }


}
