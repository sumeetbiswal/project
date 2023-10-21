<?php

namespace Drupal\login\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event Subscriber Class AuthenticatedNoCacheSubscriber.
 *
 * @package Drupal\login\EventSubscriber
 */
class AuthenticatedNoCacheSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public function onRespond(ResponseEvent $event) {
    $response = $event->getResponse();
    $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0');
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onRespond'];
    return $events;
  }

}
