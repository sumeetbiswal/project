<?php

namespace Drupal\login\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Url;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Http\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Session\AccountInterface;

/**
 * Event Subscriber for Drupal Login Class DrupalLoginRedirectSubscriber.
 *
 * @package Drupal\spe_setsounds_login\EventSubscriber
 */
class DrupalLoginRedirectSubscriber implements EventSubscriberInterface {

  /**
   * Request stack.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Redirect code.
   *
   * @var int
   */
  private $redirectCode = 302;

  /**
   * Force SAML Login to anonymous users.
   *
   * @param \Drupal\Core\Http\RequestStack $requestStack
   *   Request stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory service.
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathMatcherInterface $pathMatcher
   *   Path Matcher.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   Current User.
   */
  public function __construct(RequestStack $requestStack,
    ConfigFactoryInterface $configFactory,
    AliasManagerInterface $alias_manager,
    PathMatcherInterface $pathMatcher,
    AccountInterface $currentUser) {
    $this->requestStack = $requestStack;
    $this->configFactory = $configFactory;
    $this->aliasManager = $alias_manager;
    $this->pathMatcher = $pathMatcher;
    $this->currentUser = $currentUser;
  }

  /**
   * Event Subscriber redirection for Drupal login.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Request Event Subscriber.
   */
  public function loginRedirect(RequestEvent $event) {

    if ($this->currentUser->isAnonymous()) {

      $current_path = Url::fromRoute('<current>')->toString();

      $exclude_paths = [
        '/user/logout',
        '/user/password',
        'user/reset',
        '/'
      ];

      $exclude_paths_by_patterns = [
        'user/reset',
      ];

      if (!($is_excluded_path = in_array($current_path, $exclude_paths))) {
        foreach ($exclude_paths_by_patterns as $path) {
          if (strpos($current_path, $path) !== FALSE) {
            $is_excluded_path = TRUE;
            break;
          }
        }
      }

      if (!$is_excluded_path) {

        // Ensuring that homepage will go always to /
        // if system.site.page.front config set with a different page.
        $config = $this->configFactory->get('system.site');
        $front_uri = $config->get('page.front');
        $front_alias = $this->aliasManager->getAliasByPath($front_uri);
        $current_alias = $this->aliasManager->getAliasByPath($current_path);
        if ($this->pathMatcher->isFrontPage() || $front_alias ===
          $current_alias) {
          $current_path = '/';
        }

        // Preserve parameters.
        $current_path_query_params = $this->requestStack->getCurrentRequest()
          ->getQueryString();
        $destination_path = $current_path . '?' . $current_path_query_params;

        $url = Url::fromRoute('<front>', [], [
         // 'query' => [
          //  'destination' => $destination_path,
         // ],
        ])->toString();

        if (!$this->pathMatcher->isFrontPage()){
          $response = new RedirectResponse($url, $this->redirectCode);
          $response->send();
          exit(0);
        }

      }
    }
    else{
      if ($this->pathMatcher->isFrontPage()){
        $response = new RedirectResponse('/home', $this->redirectCode);
        $response->send();
        exit(0);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Listen to kernel.request events and call LoginRedirect.
    $events[KernelEvents::REQUEST][] = ['loginRedirect'];
    return $events;
  }

}
