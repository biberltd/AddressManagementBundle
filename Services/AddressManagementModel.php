<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\AddressManagementBundle\Services;

use BiberLtd\Bundle\AddressManagementBundle\Entity as BundleEntity;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\MemberManagementBundle\Entity as MemberEntity;
use BiberLtd\Bundle\ContactInformationBundle\Entity as ContactEntity;

use BiberLtd\Bundle\MemberManagementBundle\Services as MMBService;
use BiberLtd\Bundle\ContactInformationBundle\Services as CMBService;

use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class AddressManagementModel extends CoreModel {

	/**
	 * AddressManagementModel constructor.
	 *
	 * @param object $kernel
	 * @param string $db_connection
	 * @param string $orm
	 */
	public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
		parent::__construct($kernel, $db_connection, $orm);

		$this->entity = array(
			'a' => array('name' => 'AddressManagementBundle:Address', 'alias' => 'a'),
			'at' => array('name' => 'AddressManagementBundle:AddressType', 'alias' => 'at'),
			'atl' => array('name' => 'AddressManagementBundle:AddressTypeLocalization', 'alias' => 'atl'),
			'aom' => array('name' => 'AddressManagementBundle:AddressesOfMember', 'alias' => 'aom'),
			'm' => array('name' => 'MemberManagementBundle:Member', 'alias' => 'm'),
			'p' => array('name' => 'ContactInformationBundle:PhoneNumber', 'alias' => 'p'),
			'poa' => array('name' => 'AddressManagementBundle:PhoneNumbersOfAddresses', 'alias' => 'poa'),
		);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		foreach ($this as $property => $value) {
			$this->$property = null;
		}
	}

	/**
	 * @param $entry
	 *
	 * @return array
	 */
	public function deleteAddress($entry) {
		return $this->deleteAddresses(array($entry));
	}

	/**
	 * @param $collection
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteAddresses($collection) {
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\Address) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getAddress($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $entry
	 *
	 * @return array
	 */
	public function deleteAddressType($entry) {
		return $this->deleteAddressTypes(array($entry));
	}

	/**
	 * @param $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteAddressTypes($collection) {
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\AddressType) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getAddressType($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed$address
	 * @param bool $bypass
	 *
	 * @return bool
	 */
	public function doesAddressExist($address, $bypass = false)
	{
		$response = $this->getAddress($address);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param mixed$address
	 * @param bool $bypass
	 *
	 * @return bool
	 */
	public function doesAddressTypeExist($address, $bypass = false)
	{
		$response = $this->getAddressType($address);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param $collection
	 *
	 * @return array
	 */
	public function insertAddressType($collection) {
		return $this->insertAddressType(array($collection));
	}

	/**
	 * @param $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertAddressTypes($collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = [];
		$localizations = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\AddressType) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\ProductCategory;
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				foreach ($data as $column => $value) {
					$localeSet = false;
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							$localizations[$countInserts]['localizations'] = $value;
							$localeSet = true;
							$countLocalizations++;
							break;
						default:
							$entity->$set($value);
							break;
					}
					if ($localeSet) {
						$localizations[$countInserts]['entity'] = $entity;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		/** Now handle localizations */
		if ($countInserts > 0 && $countLocalizations > 0) {
			$response = $this->insertAddressTypeLocalizations($localizations);
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $address
	 *
	 * @return array
	 */
	public function insertAddress($address) {
		return $this->insertAddresses(array($address));
	}

	/**
	 * @param $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertAddresses($collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		$lModel = $this->kernel->getContainer()->get('locationmanagement.model');
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Address) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\ProductCategory;
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				foreach ($data as $column => $value) {
					$localeSet = false;
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'city':
							$response = $lModel->getCity($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						case 'state':
							$response = $lModel->getState($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						case 'country':
							$response = $lModel->getCountry($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						case 'district':
							$response = $lModel->getDistrict($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						case 'neighborhood':
							$response = $lModel->getNeighborhood($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						default:
							$entity->$set($value);
							break;
					}
					if ($localeSet) {
						$localizations[$countInserts]['entity'] = $entity;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}

		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $address
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse
	 */
	public function getAddress($address)
	{
		$timeStamp = microtime(true);
		if ($address instanceof BundleEntity\Address) {
			return new ModelResponse($address, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($address) {
			case is_numeric($address):
				$result = $this->em->getRepository($this->entity['a']['name'])->findOneBy(array('id' => $address));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $type
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse
	 */
	public function getAddressType($type)
	{
		$timeStamp = microtime(true);
		if ($type instanceof BundleEntity\AddressType) {
			return new ModelResponse($type, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($type) {
			case is_numeric($type):
				$result = $this->em->getRepository($this->entity['at']['name'])->findOneBy(array('id' => $type));
				break;
			case is_string($type):
				$result = $this->em->getRepository($this->entity['atl']['name'])->findOneBy(array('url_key' => $type));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listAddresses(array $filter = null, array $sortOrder = null, array $limit = null)
	{
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['a']['alias']
			. ' FROM ' . $this->entity['a']['name'].' '.$this->entity['a']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'title':
					case 'zip':
					case 'date_added':
					case 'date_updated':
						$column = $this->entity['a']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listAddressTypes(array $filter = null, array $sortOrder = null, array $limit = null)
	{
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['p']['alias'] . ', ' . $this->entity['pl']['alias']
			. ' FROM ' . $this->entity['pl']['name'] . ' ' . $this->entity['pl']['alias']
			. ' JOIN ' . $this->entity['pl']['alias'] . '.product ' . $this->entity['p']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'code':
					case 'date_added':
					case 'date_updated':
						$column = $this->entity['at']['alias'] . '.' . $column;
						break;
					case 'name':
					case 'url_key':
						$column = $this->entity['atl']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$entities = [];
		foreach ($result as $entry) {
			$id = $entry->getProduct()->getId();
			if (!isset($unique[$id])) {
				$unique[$id] = '';
				$entities[] = $entry->getProduct();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $address
	 *
	 * @return mixed
	 */
	public function updateAddress($address)
	{
		return $this->updateAddresses(array($address));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse
	 */
	public function updateAddresses(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Address) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getProductAttribute($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Address with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				$lModel = $this->kernel->getContainer()->get('locationmanagement.model');
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'district':
							$response = $lModel->getDistrict($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'city':
							$response = $lModel->getCity($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'country':
							$response = $lModel->getCountry($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'neighborhood':
							$response = $lModel->getNeighborhood($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'state':
							$response = $lModel->getState($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							}
							unset($response, $sModel);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $type
	 *
	 * @return mixed
	 */
	public function updateAddressType($type) {
		return $this->updateAddressTypes(array($type));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse
	 */
	public function updateAddressTypes(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		$localizations = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\ProductAttribute) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				if (!property_exists($data, 'site')) {
					$data->site = 1;
				}
				$response = $this->getProductAttribute($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'AddressType with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							foreach ($value as $langCode => $translation) {
								$localization = $oldEntity->getLocalization($langCode, true);
								$newLocalization = false;
								if (!$localization) {
									$newLocalization = true;
									$localization = new BundleEntity\AddressTypeLocalization();
									$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
									$response = $mlsModel->getLanguage($langCode);
									$localization->setLanguage($response->result->set);
									$localization->setAddressType($oldEntity);
								}
								foreach ($translation as $transCol => $transVal) {
									$transSet = 'set' . $this->translateColumnName($transCol);
									$localization->$transSet($transVal);
								}
								if ($newLocalization) {
									$this->em->persist($localization);
								}
								$localizations[] = $localization;
							}
							$oldEntity->setLocalizations($localizations);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertAddressTypeLocalizations(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\AddressTypeLocalization) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else {
				$attribute = $data['entity'];
				foreach ($data['localizations'] as $locale => $translation) {
					$entity = new BundleEntity\ProductAttributeLocalization();
					$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
					$response = $lModel->getLanguage($locale);
					if ($response->error->exist) {
						return $response;
					}
					$entity->setLanguage($response->result->set);
					unset($response);
					$entity->setAddressType($attribute);
					foreach ($translation as $column => $value) {
						$set = 'set' . $this->translateColumnName($column);
						switch ($column) {
							default:
								if (is_object($value) || is_array($value)) {
									$value = json_encode($value);
								}
								$entity->$set($value);
								break;
						}
					}
					$this->em->persist($entity);
					$insertedItems[] = $entity;
					$countInserts++;
				}
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param      $address
	 * @param      $member
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|bool
	 */
	public function isAddressAssociatedWithMember($address, $member, $bypass = false){
		$timeStamp = microtime(true);
		$response = $this->getAddress($address);
		if ($response->error->exist) {
			return $response;
		}
		$address = $response->result->set;

		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;
		$found = false;

		$qStr = 'SELECT COUNT(' . $this->entity['aom']['alias'] . '.address)'
			. ' FROM ' . $this->entity['aom']['name'] . ' ' . $this->entity['aom']['alias']
			. ' WHERE ' . $this->entity['aom']['alias'] . '.member = ' . $member->getId()
			. ' AND ' . $this->entity['aom']['alias'] . '.address = ' . $address->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array $collection
	 * @param       $address
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function addPhoneNumbersToAddress(array $collection, $address)
	{
		$timeStamp = microtime(true);
		$validAttributes = [];
		$response = $this->getAddress($address);
		if ($response->error->exist) {
			return $response;
		}
		$address = $response->result->set;
		$cModel = $this->kernel->getContainer()->get('contactinformation.model');
		foreach ($collection as $tmpContainer) {
			$response = $cModel->getPhoneNumber($tmpContainer['phone_number']);
			if (!$response->error->exist) {
				$validAttributes[] = $response->result->set;
			}
		}
		unset($collection);
		/** issue an error only if there is no valid file entries */
		if (count($validAttributes) < 1) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $collection parameter must be an array collection', 'E:S:001');
		}
		unset($count);

		$poaCcollection = [];
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($validAttributes as $item) {
			/** Check if association exists */
			if (!$this->isPhoneAssociatedWithAddress($item, $address, true)) {
				$poa = new BundleEntity\PhoneNumbersOfAddresses();
				$poa->setAddress($address)->setPhoneNumber($item)->setDateAdded($now);
				$this->em->persist($poa);
				$poaCcollection[] = $poa;
				$count++;
			}
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($poaCcollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param            $member
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return mixed
	 */
	public function listAddressesOfMember($member, array $sortOrder = null, array $limit = null){
		$timeStamp = microtime(true);
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;

		$qStr = 'SELECT '.$this->entity['aom']['alias'].' FROM '.$this->entity['aom']['name'].' '.$this->entity['aom']['alias']
			. ' WHERE ' . $this->entity['aom']['alias'] . '.member = '. $member->getId();


		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);
		$result = $q->getResult();

		$aIds = [];
		if(count($result) < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}

		foreach($result as $aomEntity){
			/**
			 * @var BundleEntity\AddressesOfMember $aomEntity
			 */
			$aIds[] = $aomEntity->getAddress()->getId();
		}

		$column = $this->entity['a']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $aIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		return $this->listAddresses($filter, $sortOrder, $limit);
	}

	/**
	 * @param      $phone
	 * @param      $address
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|bool
	 */
	public function isPhoneAssociatedWithAddress($phone, $address, $bypass = false){
		$timeStamp = microtime(true);
		$response = $this->getAddress($address);
		if ($response->error->exist) {
			return $response;
		}
		$address = $response->result->set;

		$cModel = $this->kernel->getContainer()->get('contactinformation.model');
		$response = $cModel->getPhoneNumber($phone);
		if ($response->error->exist) {
			return $response;
		}
		$phone = $response->result->set;
		$found = false;

		$qStr = 'SELECT COUNT(' . $this->entity['poa']['alias'] . '.address)'
			. ' FROM ' . $this->entity['poa']['name'] . ' ' . $this->entity['poa']['alias']
			. ' WHERE ' . $this->entity['poa']['alias'] . '.phone_number = ' . $phone->getId()
			. ' AND ' . $this->entity['poa']['alias'] . '.address = ' . $address->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param            $phone
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPhoneNumbersOfAddress($phone, array $sortOrder = null, array $limit = null){
		$cModel = $this->kernel->getContainer()->get('contactinformation.model');
		$response = $cModel->getPhoneNumber($phone);
		if ($response->error->exist) {
			return $response;
		}
		$phone = $response->result->set;
		$column = $this->entity['p']['alias'] . '.phone_number';
		$condition = array('column' => $column, 'comparison' => '=', 'value' => $phone->getId());
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		return $this->listAddresses($filter, $sortOrder, $limit);
	}
	/**
	 * @param string     $keyword
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listAddressesWithKeywordMatchingInTitle(string $keyword, array $sortOrder = null, array $limit = null){
		$filter[] = array(
			'glue' => 'or',
			'condition' => array('column' => $this->entity['a']['alias'].'.title', 'comparison' => 'contains', 'value' => $keyword),
		);
		return $this->listAddresses($filter,$sortOrder,$limit);
	}

	/**
	 * @param array $collection
	 * @param $address
	 * @param null $type
	 * @param string|null $alias
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Services\ModelResponse|ModelResponse
	 */
	public function addMembersToAddress(array $collection, $address, $type = null, string $alias = null)
	{
		$timeStamp = microtime(true);
		$membersToAdd = [];
		$response = $this->getAddress($address);
		if ($response->error->exist) {
			return $response;
		}
		if(!is_null($type)){
			$response = $this->getAddressType($type);
			if(!$response->error->exist){
				$type = $response->result->set;
			}
		}
		$address = $response->result->set;
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		foreach ($collection as $aMember) {
			$response = $mModel->getMember($aMember);
			if (!$response->error->exist) {
				$membersToAdd[] = $response->result->set;
			}
		}
		unset($collection);
		/** issue an error only if there is no valid file entries */
		if (count($membersToAdd) < 1) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $collection parameter must be an array collection', 'E:S:001');
		}
		unset($count);

		$aomCollection = [];
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($membersToAdd as $item) {
			/** Check if association exists */
			if (!$this->isAddressAssociatedWithMember($address, $item, true)) {
				$aom = new BundleEntity\AddressesOfMember();
				$aom->setAddress($address)->setMember($item)->setDateAdded($now)->setDateUpdated($now);
				$aom->setAddressType($type)->setAlias($alias);
				$this->em->persist($aom);
				$aomCollection[] = $aom;
				$count++;
			}
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($aomCollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}
}
