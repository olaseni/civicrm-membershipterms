<?php

use CRM_Membershipterms_ExtensionUtil as E;

class CRM_Membershipterms_BAO_MembershipTerms extends CRM_Membershipterms_DAO_MembershipTerms {

	/**
	 * Create a new MembershipTerms based on array-data
	 *
	 * @param array $params key-value pairs
	 *
	 * @return CRM_Membershipterms_DAO_MembershipTerms|NULL
	 */
	public static function create( $params ) {
		$className  = 'CRM_Membershipterms_DAO_MembershipTerms';
		$entityName = 'MembershipTerms';
		$hook       = empty( $params['id'] ) ? 'create' : 'edit';

		CRM_Utils_Hook::pre( $hook, $entityName, CRM_Utils_Array::value( 'id', $params ), $params );
		$instance = new $className();
		$instance->copyValues( $params );
		$instance->save();
		CRM_Utils_Hook::post( $hook, $entityName, $instance->id, $instance );

		return $instance;
	}

	/**
	 * Convenience method
	 *
	 * @param $id
	 * @param $contact_id
	 * @param $membership_id
	 * @param $start_date
	 * @param $end_date
	 *
	 * @return CRM_Membershipterms_DAO_MembershipTerms|NULL
	 */
	public static function createTerm( $id, $contact_id, $membership_id, $start_date, $end_date ) {
		$params = array(
			'contact_id'    => $contact_id,
			'membership_id' => $membership_id,
			'start_date'    => $start_date,
			'end_date'      => $end_date,
		);
		if ( $id ) {
			$params['id'] = $id;
		}

		return self::create( $params );
	}

}
