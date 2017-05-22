<?php
/**
 * Liquid Outreach_Editor_Role.
 *
 * @since   0.3.8
 * @package Liquid_Outreach
 */


/**
 * Liquid Outreach_Editor_Role class.
 *
 * @since 0.3.8
 */
class LO_Ccb_Outreach_Editor_Role
{

    private $customCaps = array(

        // Permissions for outreach event
        'edit_others_outreach_events' => true,
        'delete_others_outreach_events' => true,
        'delete_private_outreach_events' => true,
        'edit_private_outreach_events' => true,
        'read_private_outreach_events' => true,
        'edit_published_outreach_events' => true,
        'publish_outreach_events' => true,
        'delete_published_outreach_events' => true,
        'edit_outreach_events' => true,
        'delete_outreach_events' => true,
        'edit_outreach_event' => true,
        'read_outreach_event' => true,
        'delete_outreach_event' => true,
        'read_outreach_event' => true,

        // Permissions for outreach partner
        'edit_others_outreach_partners' => true,
        'delete_others_outreach_partners' => true,
        'delete_private_outreach_partners' => true,
        'edit_private_outreach_partners' => true,
        'read_private_outreach_partners' => true,
        'edit_published_outreach_partners' => true,
        'publish_outreach_partners' => true,
        'delete_published_outreach_partners' => true,
        'edit_outreach_partners' => true,
        'delete_outreach_partners' => true,
        'edit_outreach_partner' => true,
        'read_outreach_partner' => true,
        'delete_outreach_partner' => true,
        'read_outreach_partner' => true,

        // Permissions for outreach category
        'manage_event_categories' => true,
        'edit_event_categories' => true,
        'delete_event_categories' => true,
        'assign_event_categories' => true,
    );

    /**
     * add user role for managing plugin functionality
     * @since 0.3.8
     */
    public function add_role()
    {
        add_role( 'outreach_editor', __( 'Outreach Editor', 'liquid-outreach' ), $this->customCaps );
    }

    /**
     * remove capabilities for sme default roles
     * @since 0.3.8
     */
    public function modify_existing_role()
    {
        $roles = array( 'administrator', 'editor' );
        foreach ( $roles as $roleName ) {
            // Get role
            $role = get_role( $roleName );
            // Check role exists
            if ( is_null( $role) ) {
                continue;
            }
            // Iterate through our custom capabilities, adding them
            // to this role if they are enabled
            foreach ( $this->customCaps as $capability => $enabled ) {
                if ( $enabled ) {
                    // Add capability
                    $role->add_cap( $capability );
                }
            }
        }
    }

    /**
     * remove role
     * @since 0.3.8
     */
    public function delete_role()
    {
        remove_role('outreach_editor');

        $roles = array( 'administrator', 'editor' );
        foreach ( $roles as $roleName ) {
            // Get role
            $role = get_role( $roleName );
            // Check role exists
            if ( is_null( $role) ) {
                continue;
            }
            // Iterate through our custom capabilities, adding them
            // to this role if they are enabled
            foreach ( $this->customCaps as $capability => $enabled ) {
                if ( $enabled ) {
                    // Add capability
                    $role->remove_cap( $capability );
                }
            }
        }
    }
}