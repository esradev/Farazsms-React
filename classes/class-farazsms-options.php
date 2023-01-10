<?php

/**
 * Farazsms Options.
 * 
 * @package Farazsms
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly.


class Farazsms_Options
{
    /**
     * Register settings options.
     * 
     * @since 2.0.0
     */
    public function register_settings_options()
    {
        $farazsms_settings_options = '';
        // [
        //     'show_in_rest' => [
        //         'schema' => [
        //             'type' => 'object',
        //             'properties' => array(),
        //         ],
        //     ],
        // ];
        add_option('farazsms_settings_options', $farazsms_settings_options);
    }

    /**
     * Register login notify options
     *
     * @since 2.0.0
     */
    public function register_login_notify_options()
    {
        $farazsms_login_notify_options = '';
        add_option('farazsms_login_notify_options', $farazsms_login_notify_options);
    }

    /**
     * Register Phonebook options
     *
     * @since 2.0.0
     */
    public function register_phonebook_options()
    {
        $farazsms_phonebook_options = '';
        add_option('farazsms_phonebook_options', $farazsms_phonebook_options);
    }

    /**
     * Register comments options.
     *
     * @since 2.0.0
     */
    public function register_comments_options()
    {
        $farazsms_comments_options = '';
        add_option('farazsms_comments_options', $farazsms_comments_options);
    }

    /**
     * Register newsletter options.
     *
     * @since 2.0.0
     */
    public function register_newsletter_options()
    {
        $farazsms_newsletter_options = '';
        add_option('farazsms_newsletter_options', $farazsms_newsletter_options);
    }
    /**
     * Register WooCommerce options.
     *
     * @since 2.0.0
     */
    public function register_woocommerce_options()
    {
        $farazsms_woocommerce_options = '';
        add_option('farazsms_woocommerce_options', $farazsms_woocommerce_options);
    }
    /**
     * Register EDD options.
     *
     * @since 2.0.0
     */
    public function register_edd_options()
    {
        $farazsms_edd_options = '';
        add_option('farazsms_edd_options', $farazsms_edd_options);
    }

    /**
     * Register Aff options.
     *
     * @since 2.0.0
     */
    public function register_aff_options()
    {
        $farazsms_aff_options = '';
        add_option('farazsms_aff_options', $farazsms_aff_options);
    }

    /**
     * Register Membership options.
     *
     * @since 2.0.0
     */
    public function register_membership_options()
    {
        $farazsms_membership_options = '';
        add_option('farazsms_membership_options', $farazsms_membership_options);
    }
    /**
     * Register Integrations options.
     *
     * @since 2.0.0
     */
    public function register_integrations_options()
    {
        $farazsms_integrations_options = '';
        add_option('farazsms_integrations_options', $farazsms_integrations_options);
    }
}
