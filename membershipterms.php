<?php

require_once 'membershipterms.civix.php';

use CRM_Membershipterms_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipterms_civicrm_config( &$config ) {
	_membershipterms_civix_civicrm_config( $config );
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipterms_civicrm_xmlMenu( &$files ) {
	_membershipterms_civix_civicrm_xmlMenu( $files );
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipterms_civicrm_install() {
	_membershipterms_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipterms_civicrm_postInstall() {
	_membershipterms_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipterms_civicrm_uninstall() {
	_membershipterms_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipterms_civicrm_enable() {
	_membershipterms_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipterms_civicrm_disable() {
	_membershipterms_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipterms_civicrm_upgrade( $op, CRM_Queue_Queue $queue = null ) {
	return _membershipterms_civix_civicrm_upgrade( $op, $queue );
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipterms_civicrm_managed( &$entities ) {
	_membershipterms_civix_civicrm_managed( $entities );
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipterms_civicrm_caseTypes( &$caseTypes ) {
	_membershipterms_civix_civicrm_caseTypes( $caseTypes );
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function membershipterms_civicrm_angularModules( &$angularModules ) {
	_membershipterms_civix_civicrm_angularModules( $angularModules );
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipterms_civicrm_alterSettingsFolders( &$metaDataFolders = null ) {
	_membershipterms_civix_civicrm_alterSettingsFolders( $metaDataFolders );
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function membershipterms_civicrm_entityTypes( &$entityTypes ) {
	$entityTypes[] = array(
		'name'  => 'MembershipTerms',
		'class' => 'CRM_Membershipterms_DAO_MembershipTerms',
		'table' => 'civicrm_membershipterms',
	);
}

// --- Functions below this ship commented out. Uncomment as required. ---

define( 'CVCRMTERMS', 'civicrm_member_terms' );


/**
 * Implements hook_civicrm_pre() for building the state with which we will create MembershipTerms entities.
 *
 * It monitors object types 'Membership' and 'MembershipPayment'.
 * Nothing is persisted at this point.
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function membershipterms_civicrm_pre( $op, $objectName, $objectId, &$objectRef ) {
	// Associate membership data with the current request.
	// This makes it easy to persist data between hooks
	if ( empty( $_REQUEST[ CVCRMTERMS ] ) ) {
		$_REQUEST[ CVCRMTERMS ] = array(
			'modifier_contact_id' => CRM_Core_Session::singleton()->getLoggedInContactID()
		);
	}
	if ( in_array( $op, array( 'create', 'edit' ) ) ) {
		if ( 'Membership' == $objectName ) {
			$membership_id = $objectId;
			$objectPre     = CRM_Member_BAO_Membership::findById( $membership_id );
			$pre_end_date  = _pd( $objectPre->end_date );
			$post_end_date = _pd( $objectRef['end_date'] );
			if ( ! empty( $objectPre->contact_id ) ) {
				$target_contact_id = $objectPre->contact_id;
			} elseif ( ! empty( $_REQUEST['cid'] ) ) {
				$target_contact_id = $_REQUEST['cid'];
			}

			if ( empty( $_REQUEST['num_terms'] ) ) {
				$terms = 1;
				while ( $terms < 20 ) {
					$dates         = CRM_Member_BAO_MembershipType
						::getRenewalDatesForMembershipType( $membership_id, null, null, $terms );
					$end_date_calc = _pd( $dates['end_date'] );
					if ( $end_date_calc >= $post_end_date ) {
						break;
					}
					$terms ++;
				}
			} else {
				$terms = $_REQUEST['num_terms'];
			}

			$_REQUEST[ CVCRMTERMS ] += array(
				'membership_id'   => $membership_id,
				'start_date'      => $pre_end_date,
				'end_date'        => $post_end_date,
				'number_of_terms' => $terms,
				'contact_id'      => $target_contact_id,
			);

		} elseif ( 'MembershipPayment' == $objectName ) {
			$_REQUEST[ CVCRMTERMS ] += array(
				'contribution_id' => $objectRef['contribution_id']
			);
		}
	}
}

/**
 * Implements hook_civicrm_post().
 *
 * The data garnered in pre-persistence states is now used to create object instances
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function membershipterms_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
	if ( in_array( $op, array( 'create', 'edit' ) ) ) {
		if ( 'Membership' == $objectName ) {
			if ( $op == 'create' ) {
				$_REQUEST[ CVCRMTERMS ] ['membership_id'] = $objectId;
			}
			$instance                     = CRM_Membershipterms_BAO_MembershipTerms::create( $_REQUEST[ CVCRMTERMS ] );
			$_REQUEST[ CVCRMTERMS ]['id'] = $instance->id;
		} elseif ( 'MembershipPayment' == $objectName && ! isset( $_REQUEST[ CVCRMTERMS ]['done'] ) ) {
			CRM_Membershipterms_BAO_MembershipTerms::create( $_REQUEST[ CVCRMTERMS ] );
			$_REQUEST[ CVCRMTERMS ]['done'] = true;
		}
	}
}

/**
 * Alias : CRM_Utils_Date::processDate
 *
 * @param $date
 *
 * @return string
 */
function _pd( $date ) {
	return CRM_Utils_Date::processDate( $date, null, false, 'Ymd' );
}


/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 * function membershipterms_civicrm_preProcess($formName, &$form) {
 *
 * } // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 * function membershipterms_civicrm_navigationMenu(&$menu) {
 * _membershipterms_civix_insert_navigation_menu($menu, NULL, array(
 * 'label' => E::ts('The Page'),
 * 'name' => 'the_page',
 * 'url' => 'civicrm/the-page',
 * 'permission' => 'access CiviReport,access CiviContribute',
 * 'operator' => 'OR',
 * 'separator' => 0,
 * ));
 * _membershipterms_civix_navigationMenu($menu);
 * } // */
