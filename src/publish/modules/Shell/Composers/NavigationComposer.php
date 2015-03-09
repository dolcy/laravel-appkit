<?php namespace App\Modules\Shell\Composers;

use App;
use Config;
use Lang;
use Modules;
use Menus;
use Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;

class NavigationComposer {

    /**
     * The generated menu.
     *
     * @var \C5\AppKit\Menus\MenuBuilder
     */
    protected $menu;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->generateNavigation(); 
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('menu_admin_left', $this->menu);
    }

	/**
	 * Generate the admin navigation
	 *
	 **/
    protected function generateNavigation() {
    	$this->menu = Menus::make('menu_admin_left', function($menu) {
						$user = Auth::user();
						$modules = Modules::navigable();
						foreach ($modules as $module) {
							if ( $module['requiresAccess']) {
								// User requires access to this module
								if( $user->is($module['requiredAccess']) || $user->can($module['requiredAccess']) ) {
									// User has access to this module - Is in role or has permission
									$item = $menu->add($module['menuText'], ['icon' => $module['icon']]);
									$key = key($module['redirect']);
									$item->add($module['menuText'], ['icon' => $module['icon'], $key => $module['redirect'][$key]]);
									if( isset($module['navigation']) && count($module['navigation']) > 0 ) {
										foreach ($module['navigation'] as $subitem) {
											if ( $subitem['requiresAccess']) {
												// User requires access to this menu item
												if ( $user->is($subitem['requiredAccess']) || $user->can($subitem['requiredAccess'])) {
													// User has access to this menu item - Is in role or has permission
													$key = key($subitem['redirect']);
													$item->add($subitem['menuText'], ['icon' => $subitem['icon'], $key => $subitem['redirect'][$key]]);
												}
											} else {
												$key = key($subitem['redirect']);
												$item->add($subitem['menuText'], ['icon' => $subitem['icon'], $key => $subitem['redirect'][$key]]);
											}
										}
									} else {
										if( isset($module['redirect']) ) {
											$key = key($module['redirect']);
											$item->configureLink([$key => $module['redirect'][$key]]);
										} else {
											$item->configureLink(['url' => $module['slug']]);
										}
									}
								}
							} else {
								// User does not require access to this module
								$item = $menu->add($module['menuText'], ['icon' => $module['icon']]);
								$key = key($module['redirect']);
								$item->add($module['menuText'], ['icon' => $module['icon'], $key => $module['redirect'][$key]]);
								if( isset($module['navigation']) && count($module['navigation']) > 0 ) {
									foreach ($module['navigation'] as $subitem) {
										if ( $subitem['requiresAccess']) {
											// User requires access to this menu item
											if ( $user->is($subitem['requiredAccess']) || $user->can($subitem['requiredAccess'])) {
												// User has access to this menu item - Is in role or has permission
												$key = key($subitem['redirect']);
												$item->add($subitem['menuText'], ['icon' => $subitem['icon'], $key => $subitem['redirect'][$key]]);
											}
										} else {
											$key = key($subitem['redirect']);
											$item->add($subitem['menuText'], ['icon' => $subitem['icon'], $key => $subitem['redirect'][$key]]);
										}
									}
								} else {
									if( isset($module['redirect']) ) {
										$key = key($module['redirect']);
										$item->configureLink([$key => $module['redirect'][$key]]);
									} else {
										$item->configureLink(['url' => $module['slug']]);
									}
								}
							}
						}
					});
    }
}