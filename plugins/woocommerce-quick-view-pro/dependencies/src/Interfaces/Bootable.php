<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Interfaces;

interface Bootable
{
    /**
     * Boot the component.
     *
     * @return void
     */
    public function boot();
}
