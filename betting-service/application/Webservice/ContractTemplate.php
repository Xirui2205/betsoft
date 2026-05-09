<?php

/**
 * ContractTemplate related static methods.
 * @author Pavel Klinger
 * @see Entities_ContractTemplate
 *
 */
class Webservice_ContractTemplate extends Webservice_AbstractWebService {

	const TEMPLATE_VSAZENO_VYPLACENO = 1;
	const TEMPLATE_VSAZENO_PREDEPSANO = 2;
	const TEMPLATE_PAUSAL = 3;
	const TEMPLATE_NABER = 4;

	public static $TABLE 			= "contract_template";
	public static $PARAMETER_TABLE 	= "contract_parameter";
	public static $IDENTITY 		= "id";

	protected static $CONV = array(
		'id'	=> 'templateId',
		'name'	=> 'name',
	);

	protected static $PARAMETER_CONV = array(
		'id'          => 'contractParameterId',
		'name'        => 'name',
		'template_id' => 'templateId',
		'type'        => 'type',
		'mandatory'   => 'isMandatory'
	);

	protected static $ENTITY_NAME = "Entities_ContractTemplate";

	protected static function getDb() {
		return static::getAdminDb();
	}

	/**
	 * Returns all contract templates in the system.
	 * @return array array of the contract template structures
	 * @see Entities_Sport
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find contract template by given identifier.
	 * @param integer $contractId identifier of the contract template
	 * @return struct contractTemplate structure
	 * @see Entities_ContractTemplate
	 */
	public static function getById($contractTemplateId, $extensions = null) {
		return parent::getById($contractTemplateId, $extensions);
	}

	/**
	 * Insert new contractTemplate. Value of the contractTemplate identifier is ignored and new
	 * is generated.
	 * @param struct $contract structure of the contractTemplate
	 * @return integer contract identifier of the created contract
	 * @see Entities_Contract
	 */
	public static function insert($contract) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Update contractTemplate.
	 * @param struct $contract structure of the contract
	 * @return true on success
	 * @see Entities_Contract
	 */
	public static function update($contract) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Delete contractTemplate.
	 * @param struct $contractId idenetifier of the contract.
	 * @return true on success
	 * @see Entities_Contract
	 */
	public static function delete($contractId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	public static function toEntity($contractTemplate, $columns = null) {

		$admindb = static::getDb();

		$ret = parent::toEntity($contractTemplate, $columns);

		try {
			$params = $admindb->select()
				->from( static::$PARAMETER_TABLE )
				->where('template_id = ?', $ret->templateId)
				->query()->fetchAll();


			$contractTemplate['parameters'] = $params;
			return parent::toEntity($contractTemplate);
/*
			$ret->parameters = array();

			foreach ( $params as $param ) {
				$entity = new Entities_ContractParameter();
				$entity = static::mapDbArray2Entity($param, $entity, static::$PARAMETER_CONV);

				$ret->parameters[] = $entity;
			}

			return $ret;
*/

		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}

	}


	/**
	 * Delete contractTemplate.
	 * @param array $templateId idenetifier of the template.
	 * @return struct object with the params for the template with the given identifier
	 */
	public static function getParamsById($templateId){
		$admindb = static::getDb();

		$params = $admindb->select()
			->from(self::$PARAMETER_TABLE)
			->where('template_id=?', $templateId)
			->query()->fetchAll();

		return $params;
	}
}
