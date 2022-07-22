<?php

/**
 * @file
 * Contains \Drupal\star_wars_display\Controller\StarWarsDisplayController.
 */

namespace Drupal\star_wars_display\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for Star Wars Display
 */
class StarWarsDisplayController extends ControllerBase {

    /**
     * @return
     *  Array containg theme information.
     */
    public function output() {
        return [
            '#theme' => 'star_wars_display',
        ];
    }

}
