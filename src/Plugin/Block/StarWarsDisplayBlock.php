<?php

namespace Drupal\star_wars_display\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;


/**
 * Provides a StarWarsDisplayBlock.
 *
 * @Block(
 *   id = "star_wars_display_block",
 *   admin_label = @Translation("Star Wars Display"),
 *   category = @Translation("Content"),
 * )
 */
class StarWarsDisplayBlock extends BlockBase {

  /**
   * Implements build()
   * @return
   *  Associative array that contains theme, API data, and configuration for display/
   */
  public function build() {
    $client = \Drupal::httpClient();
    $url = 'https://swapi.dev/api/films/';
    // Grab data from the SWAPI
    try {
      $request = $client->request('GET', $url, [ 'timeout' => 100 ]);
      $data = json_decode($request->getBody());
    } catch (RequestException $e) {
      watchdog_exception('Star Wars Display', $e);
    }
    $film_info = [];
    foreach ($data->results as $v) {
      $film_info[] = [
        'title' => $v->title,
        'episode' => $v->episode_id,
        'director' => $v->director,
        'producer' => $v->producer,
        'release_date' => date('F j, Y', strtotime($v->release_date))
      ];
    }
    $config = $this->getConfiguration();
    return [
      '#theme' => 'star_wars_display',
      '#film_info' => $film_info,
      '#how_many' => $config['star_wars_display_how_many'] ?? '3',
    ];
  }

  /**
   * Implements blockForm
   * @param $form
   *  The Star Wars form
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *  Form state for the Star Wars form.
   * @return
   *  Newly altered Form object.
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['star_wars_display_how_many'] = [
      '#type' => 'textfield',
      '#title' => $this->t('How many to show (1-6)'),
      '#description' => $this->t('How many films would you like to display?'),
      '#default_value' => $config['star_wars_display_how_many'] ?? '3',
    ];
    return $form;
  }

  /**
   * Implements blockForm
   * @param $form
   *  The Star Wars form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *  Form state for the Star Wars form.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['star_wars_display_how_many'] = $values['star_wars_display_how_many'];
  }
}