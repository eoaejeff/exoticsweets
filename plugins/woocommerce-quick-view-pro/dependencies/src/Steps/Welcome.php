<?php

/**
 * @package   Barn2\setup-wizard
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Steps;

use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Step;
use Barn2\Plugin\WC_Quick_View_Pro\Dependencies\Barn2\Setup_Wizard\Util;
/**
 * Handles the welcome step of the wizard.
 * Displays a license validation field and validates the license.
 */
class Welcome extends Step
{
    /**
     * Initialize the step.
     */
    public function __construct()
    {
        $this->set_id('welcome');
        $this->set_name(esc_html__('Welcome', 'woocommerce-quick-view-pro'));
    }
    /**
     * {@inheritdoc}
     */
    public function setup_fields()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function submit($values)
    {
        return Api::send_success_response();
    }
}
