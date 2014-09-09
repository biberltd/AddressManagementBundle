<?php

/***
 * AddressManagementModel Class
 *
 * This class acts as a database proxy model for AddressManagementBundle functionalities.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\AccessManagementBundle
 * @subpackage	Services
 * @name	    AccessManagementModel
 *
 * @author		Can Berkol
 * @author      Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.7
 * @date        04.07.2014
 *
 * =============================================================================================================
 * !! INSTRUCTIONS ON IMPORTANT ASPECTS OF MODEL METHODS !!!
 *
 * Each model function must return a $response ARRAY.
 * The array must contain the following keys and corresponding values.
 *
 * $response = array(
 *              'result'    =>   An array that contains the following keys:
 *                               'set'         Actual result set returned from ORM or null
 *                               'total_rows'  0 or number of total rows
 *                               'last_insert_id' The id of the item that is added last (if insert action)
 *              'error'     =>   true if there is an error; false if there is none.
 *              'code'      =>   null or a semantic and short English string that defines the error concanated
 *                               with dots, prefixed with err and the initials of the name of model class.
 *                               EXAMPLE: error.amm.action.not.found success messages have a prefix called success..
 *
 *                               NOTE: DO NOT FORGET TO ADD AN ENTRY FOR ERROR CODE IN BUNDLE'S
 *                               RESOURCES/TRANSLATIONS FOLDER FOR EACH LANGUAGE.
 * =============================================================================================================
 * TODOs
 * 
 *  *
 * A addAddressToMember()
 * A addMemberToAddress()
 * A listAddressesOfMember()
 * A listAddressesOfMemberWithType()
 * A listAddresesWithType()
 * A removeAddressFromMember()
 * A removeMemberFromAddress()
 * 
 * 
 */

namespace BiberLtd\Core\Bundles\AddressManagementBundle\Services;

/*** Entities to be used */
use BiberLtd\Core\Bundles\AddressManagementBundle\Entity as BundleEntity;
use BiberLtd\Core\Bundles\MemberManagementBundle\Entity as MemberEntity;
use BiberLtd\Core\Bundles\ContactInformationBundle\Entity as ContactEntity;
/*** Services to be used */
use BiberLtd\Core\Bundles\MemberManagementBundle\Services as MMBService;
use BiberLtd\Core\Bundles\ContactInformationBundle\Services as CMBService;
/*** Core Service */
use BiberLtd\Core\CoreModel;
use BiberLtd\Core\Services as CoreServices;
use BiberLtd\Core\Exceptions as CoreExceptions;
use Entities\Address;

class AddressManagementModel extends CoreModel {

    /***
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        parent::__construct($kernel, $db_connection, $orm);

        /***
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'address' => array('name' => 'AddressManagementBundle:Address', 'alias' => 'a'),
            'address_type' => array('name' => 'AddressManagementBundle:AddressType', 'alias' => 'at'),
            'address_type_localization' => array('name' => 'AddressManagementBundle:AddressTypeLocalization', 'alias' => 'atl'),
            'addresses_of_member' => array('name' => 'AddressManagementBundle:AddressesOfMember', 'alias' => 'aom'),
            'phone_numbers_of_addresses' => array('name' => 'AddressManagementBundle:PhoneNumbersOfAddresses', 'alias' => 'poa'),
        );
    }

    /***
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /***
     * @name 	deleteFile()
     * Delete adress with a given id or entity.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->deleteAddresses()
     *
     *
     * @param           array           $collection           Collection consists one of the following: 'entity' or entity 'id'
     *                                                  Contains an array with two keys: file, and sortorder
     *
     * @return          array           $response
     */
    public function deleteAddress($collection) {
        return $this->deleteAddresses(array($collection));
    }

    /***
     * @name 	deleteFiles()
     * Delete adress with a given id or entity.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->doesAddressExist()
     * @use             $this->createException()
     *
     * @throws          CoreExceptions\InvalidByOptionException
     * @throws          CoreExceptions\EntityDoesNotExistException
     * @throws          CoreExceptions\InvalidParameterException
     *
     * @param           array           $collection           Collection consists one of the following: 'entity' or entity 'id'
     *                                                  Contains an array with two keys: file, and sortorder
     *
     * @return          array           $response
     */
    public function deleteAddresses($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'error.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\Address) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                $response = $this->getAddress((int) $entry, 'id');
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'error.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'error.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.deleted',
        );
        return $this->response;
    }

    /***
     * @name 	deleteAddressType()
     * Delete adress'type with a given id or entity.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->deleteAddressTypes()
     *
     *
     * @param           array           $collection           Collection consists one of the following: 'entity' or entity 'id'
     *                                                  Contains an array with two keys: file, and sortorder
     * @param           mixed           $by             'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function deleteAddressType($collection, $by = 'id') {
        return $this->deleteAddressTypes($collection, $by);
    }

    /***
     * @name 	deleteAddressTypes()
     * Delete address's type with a given id or entity.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     * @use             $this->doesAddressTypeExist()
     *
     * @throws          CoreExceptions\InvalidByOptionException
     * @throws          CoreExceptions\InvalidParameterException
     *
     * @param           array           $collection           Collection consists one of the following: 'entity' or entity 'id'
     *                                                  Contains an array with two keys: file, and sortorder
     * @param           mixed           $by             'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function deleteAddressTypes($collection, $by) {
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'error.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\AddressType) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                $response = $this->getAddressType($entry, 'id');
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'error.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'error.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.deleted',
        );
        return $this->response;
    }

    /***
     * @name 	doesAddressExist()
     *  	Checks if record exist in db.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->getAddress()
     *
     * @param           array           $address   File collection of entities or post data.
     * @param           string          $by     Entity, post
     *
     * @return          array           $response
     */
    public function doesAddressExist($address, $by = 'entity') {
        $this->resetResponse();
        $exist = false;
        $code = 'error.db.record.notfound';
        $error = false;

        $response = $this->getAddress($address, $by);

        if (!$response['error']) {
            $exist = true;
            $code = 'success.db.record.found';
        } else {
            $error = true;
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => $code,
        );
        return $this->response;
    }

    /***
     * @name 	doesAddressTypeExist()
     *  	Checks if record exist in db.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->getAddressType()
     *
     * @param           array           $addressType   Address's type collection of entities or post data.
     * @param           string          $by     Entity, post
     *
     * @return          array           $response
     */
    public function doesAddressTypeExist($addressType, $by = 'entity') {
        $this->resetResponse();
        $exist = false;
        $code = 'error.db.record.notfound';
        $error = false;

        $response = $this->getAddressType($addressType, $by);

        if (!$response['error']) {
            $exist = true;
            $code = 'success.db.record.found';
        } else {
            $error = true;
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => $code,
        );
        return $this->response;
    }

    /***
     * @name 		insertAddressType()
     *  		Inserts one or more addresses types into database.
     *
     * @since		1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @use             $this->insertAddressTypes()
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertAddressType($collection) {
        $this->resetResponse();
        return $this->insertAddressTypes(array($collection));
    }

    /***
     * @name            insertAddressTypes()
     *  		Updates address types into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     * @use             $this->doesProductExist()
     * @use             \BiberLtd\Core\Bundles\AddressManagementBundle\Entity\AddressType
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function updateAddressTypes($collection) {
        $this->resetResponse();
        /*** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'error.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $countLocalizations = 0;
        $updatedItems = array();
        foreach ($collection as $collection) {
            if ($collection instanceof BundleEntity\AddressType) {
                $entity = $collection;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($collection)) {
                if (!property_exists($collection, 'id') || !is_numeric($collection->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'error.invalid.parameter.collection');
                }
                if (!property_exists($collection, 'date_updated')) {
                    $collection->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($collection, 'date_added')) {
                    unset($collection->date_added);
                }
                $response = $this->getAddressType($collection->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ProductAttribute with id ' . $collection->id, 'error.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($collection as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\AddressTypeLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
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
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.update.done',
        );
        return $this->response;
    }

    /***
     * @name 		insertAddress()
     *  		Inserts one or more addresses into database.
     *
     * @since		    1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @use             $this->insertAddresss()
     *
     * @param           array           $address        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertAddress($address) {
        $this->resetResponse();
        return $this->insertAddresses(array($address));
    }

    /***
     * @name            insertAddresss()
     *  		Inserts one or more files into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertAddresses($collection) {
        $this->resetResponse();
        /*** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'error.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $collection) {
            if ($collection instanceof BundleEntity\Address) {
                $entity = $collection;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($collection)) {
                $entity = new BundleEntity\Address();
                if (isset($collection->id)) {
                    unset($collection->id);
                }
                if (!property_exists($collection, 'date_added')) {
                    $collection->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($collection, 'date_updated')) {
                    $collection->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                $locationModel = $this->kernel->getContainer()->get('locationmanagement.model');
                foreach ($collection as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'city':
                            $response = $locationModel->getCity($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'City', 'error.invalid.city');
                            }
                            unset($response, $sModel);
                            break;
                        case 'state':
                            if ($value != -1) {
                                $response = $locationModel->getState($value, 'id');
                                if (!$response['error']) {
                                    $entity->$set($response['result']['set']);
                                } else {
                                    $this->createException('EntityDoesNotExist', 'State', 'error.invalid.state');
                                }
                                unset($response, $sModel);
                            }
                            break;
                        case 'country':
                            $response = $locationModel->getCountry($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Country', 'error.invalid.country');
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        return $this->response;
    }

    /***
     * @name            getAddress()
     *  		Returns details of a adress.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     * @use             $this->listAddresses()
     * 
     * @throws          InvalidByOptionException
     * @throws          InvalidParameterException
     * @throws          InvalidParameterException
     *
     * @param           mixed           $address               id
     * @param           string          $by                 entity, id
     *
     * @return          mixed           $response
     */
    public function getAddress($address, $by = 'id') {

        $this->resetResponse();
        if (!is_integer($address) && !$address instanceof BundleEntity\Address) {
            return $this->createException('InvalidParameterValue', 'integer', 'error.invalid.parameter.id');
        }

        $result = $this->em->getRepository($this->entity['address']['name'])
            ->findOneBy(array('id' => $address));

        $error = true;
        $code = 'error.db.entry.notexist';
        $found = 0;
        if ($result instanceof BundleEntity\Address) {
            $error = false;
            $code = 'success.db.entity.exist';
            $found = 1;
        }
        if ($error) {
            $result = null;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $found,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => $code,
        );

        return $this->response;
    }

    /***
     * @name            getAddressType()
     *  		Returns details of a type of address.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     * @use             $this->listAddressTypes()
     *
     * @throws          InvalidByOptionException
     * @throws          InvalidParameterException
     * @throws          InvalidParameterException
     * 
     * 
     * @param           mixed           $addressType
     * @param           string          $by                 entity, id
     *
     * @return          mixed           $response
     */
    public function getAddressType($addressType, $by = 'id') {

        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'error.invalid.parameter.by');
        }
        if (!is_object($addressType) && !is_numeric($addressType) && !is_string($addressType)) {
            return $this->createException('InvalidParameter', 'ProductCategory or numeric id', 'error.invalid.parameter.product_category');
        }
        if (is_object($addressType)) {
            if (!$addressType instanceof BundleEntity\AddressType) {
                return $this->createException('InvalidParameter', 'ProductCategory', 'error.invalid.parameter.product_category');
            }
            /***
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $addressType,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'success.db.entry.exist',
            );
            return $this->response;
        }
        $column = '';
        switch ($by) {
            case 'url_key':
                $column = $this->entity['address_type_localization']['alias'] . '.' . $by;
                break;
            default:
                $column = $this->entity['address_type']['alias'] . '.' . $by;
                break;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $addressType),
                )
            )
        );
        $response = $this->listAddressTypes($filter, null, null, null, false);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /***
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.entry.exist',
        );
        return $this->response;
    }

    /***
     * @name            listAddressTypes()
     *                  List address types.
     *
     * @since		    1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepareWhere()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortOrder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $queryStr             If a custom query string needs to be defined.
     * @param                      $returnLocales             
     *
     * @return          array           $response
     */
    public function listAddressTypes($filter = null, $sortOrder = null, $limit = null, $queryStr = null ,$returnLocales = false) {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'error.invalid.parameter.sortorder');
        }
        /***
         * Add filter checks to below to set join_needed to true.
         */
        /***         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /***
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['address_type_localization']['alias'] . ', ' . $this->entity['address_type']['alias']
                . ' FROM ' . $this->entity['address_type_localization']['name'] . ' ' . $this->entity['address_type_localization']['alias']
                . ' JOIN ' . $this->entity['address_type_localization']['alias'] . '.address_type ' . $this->entity['address_type']['alias'];
        }
        /***
         * Prepare ORDER BY section of query.
         */
        if ($sortOrder != null) {
            foreach ($sortOrder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'date_added':
                    case 'date_updated':
                        $column = $this->entity['address_type']['alias'] . '.' . $column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['address_type_localization']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /***
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        if(!is_null($limit)){
            $lqStr = 'SELECT '.$this->entity['address_type']['alias'].' FROM '.$this->entity['address_type']['name'].' '.$this->entity['address_type']['alias'];
            $lqStr .= $where_str.$group_str.$order_str;
            $lQuery = $this->em->createQuery($lqStr);
            $lQuery = $this->addLimit($lQuery, $limit);
            $result = $lQuery->getResult();
            $selectedIds = array();
            foreach($result as $entry){
                $selectedIds[] = $entry->getId();
            }
            $where_str .= ' AND '.$this->entity['address_type_localization']['alias'].'.address_type IN('.implode(',', $selectedIds).')';
        }

        $queryStr .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($queryStr);

        /***
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $addressTypes = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getAddressType()->getId();
            if (!isset($unique[$id])) {
                $addressTypes[$id] = $entry->getAddressType();
                $unique[$id] = $entry->getAddressType();
            }
            $localizations[$id][] = $entry;
        }
        $total_rows = count($addressTypes);
        $responseSet = array();
        if ($returnLocales) {
            foreach ($addressTypes as $key => $addressType) {
                $responseSet[$key]['entity'] = $addressType;
                $responseSet[$key]['localizations'] = $localizations[$key];
            }
        } else {
            $responseSet = $addressTypes;
        }
        $newCollection = array();
        foreach ($responseSet as $item) {
            $newCollection[] = $item;
        }
        unset($responseSet,$addressTypes);

        if ($total_rows < 1) {
            $this->response['code'] = 'error.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $newCollection,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.entry.exist',
        );
        return $this->response;
    }

    /***
     * @name            listAddressTypes()
     *                  List address's types of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepareWhere()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortOrder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $queryStr             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listAddresses($filter = null, $sortOrder = null, $limit = null, $queryStr = null) {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'error.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['address']['alias']
                . ' FROM ' . $this->entity['address']['name'] . ' ' . $this->entity['address']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortOrder != null) {
            foreach ($sortOrder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'city':
                    case 'state':
                    case 'country':
                    case 'date_updated':
                    case 'date_added':
                        $column = $this->entity['address']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $queryStr .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $attributes = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getId();
            if (!isset($unique[$id])) {
                $attributes[] = $entry;
                $unique[$id] = $entry->getId();
            }
        }
        unset($unique);
        $total_rows = count($attributes);
        if ($total_rows < 1) {
            $this->response['code'] = 'error.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $attributes,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            updateAddress()
     *                  Updates single address. The address must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateAddresss()
     * 
     * @param           mixed   $address     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateAddress($address) {
        $this->resetResponse();
        return $this->updateAddresses(array($address));
    }

    /**
     * @name            updateAddresses()
     *                  Updates one or more address  in database.
     * 
     * @since           1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     *
     * @throws          InvalidParameterException
     *
     * @param           array   $collection     Collection of address's entities or array of entity details.
     *
     * @return          array   $response
     *
     */

    public function updateAddresses($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'error.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $collection) {
            if ($collection instanceof BundleEntity\Address) {
                $entity = $collection;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($collection)) {
                if (!property_exists($collection, 'id') || !is_numeric($collection->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'error.invalid.parameter.collection');
                }
                if (!property_exists($collection, 'date_updated')) {
                    $collection->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($collection, 'date_added')) {
                    unset($collection->date_added);
                }
                $response = $this->getAddress($collection->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Address with id ' . $collection->id, 'error.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                $locationModel = $this->kernel->getContainer()->get('locationmanagement.model');
                foreach ($collection as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'city':
                            $response = $locationModel->getCity($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $pModel);
                            break;
                        case 'state':
                            if ($value != -1) {
                                $response = $locationModel->getState($value, 'id');
                                if (!$response['error']) {
                                    $oldEntity->$set($response['result']['set']);
                                } else {
                                    new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                                }
                                unset($response, $lModel);
                            }
                            break;
                        case 'country':
                            $response = $locationModel->getCountry($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $pModel);
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
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.update.done',
        );
        return $this->response;
    }

    /***
     * @name            updateAddressType()
     *                  Updates single address's type.
     * 
     * @since           1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateAddressTypes()
     * 
     * @param           mixed   $address     Entity or Entity id of a address
     * 
     * @return          array   $response
     * 
     */

    public function updateAddressType($address) {
        $this->resetResponse();
        return $this->updateAddressTypes(array($address));
    }

    /***
     * @name            updateAddressTypes()
     *                  Updates one or more address's types details in database.
     * 
     * @since           1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listAddressTypes()
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of address's types entities or array of entity details.
     * 
     * @return          array   $response
     * 
     */

    public function insertAddressTypes($collection) {
        $this->resetResponse();
        /*** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'error.invalid.parameter.collection');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $collection) {
            if ($collection instanceof BundleEntity\AddressType) {
                $entity = $collection;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($collection)) {
                $localizations = array();
                $entity = new BundleEntity\AddressType;
                if (!property_exists($collection, 'date_added')) {
                    $collection->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($collection as $column => $value) {
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
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /*** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertAddressTypeLocalizations($localizations);
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        return $this->response;
    }

    /***
     * @name            insertAddressTypeLocalizations ()
     *                Inserts one or more address type localizations into database.
     *
     * @since            1.0.2
     * @version         1.0.2
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertAddressTypeLocalizations($collection)
    {
        $this->resetResponse();
        /*** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'error.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\AddressTypeLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $collection) {
                    $entity = new BundleEntity\AddressTypeLocalization();
                    $entity->setAddressType($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($collection as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        return $this->response;
    }


    /***
     * @name            addAddressToMember()
     *                  Associates address and member with type.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->insertAddress()
     * 
     * @use             MMBS\MemberManagementModel
     * @use             BundleEntity\AddressesOfMember
     * 
     * @throws          InvalidParameterException
     * 
     * @param           mixed   $address
     * @param           mixed   $member
     * @param           int   $type
     * 
     * @return          array   $response
     * 
     */

    public function addAddressToMember($address,$member, $type = null) {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_numeric($member) && !is_string($member) && !$member instanceof MemberEntity\Member) {
            unset($member);
        }
        /** Validate Address Type */
        if (!is_null($type)) {
            if (!is_numeric($type) && !is_string($type) && !$type instanceof BundleEntity\AddressType) {
                unset($type);
            } else{
                $response = $this->getAddressType($type, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Type', 'error.db.address.notexist');
                }
                $type = $response['result']['set'];
            }
        }
        /** Validate Address */
        if (!is_numeric($address) && !$address instanceof BundleEntity\Address) {
            unset($address);
        }
        /** If no entity is provided as address we need to check if it does exist */
        if (is_numeric($address)) {
            $response = $this->getAddress($address, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Address', 'error.db.address.notexist');
            }
            $address = $response['result']['set'];
        }
        $collection = array();
        $count = 0;
        /** Get Member */
        $memberModel = new MMBService\MemberManagementModel($this->kernel);
        $response = $memberModel->getMember($member);
        if ($response['error']) {
            return $this->createException('EntityDoesNotExist', 'Member', 'error.db.member.notexist');
        }
        $member = $response['result']['set'];

        /** Check if association exists */
        if ($this->isAddressAssociatedWithMember($address,$member,null,true)) {
            new CoreExceptions\DuplicateAssociationException($this->kernel, 'Address => Member');
            $this->response['code'] = 'error.db.entry.exist';
        }

        /** prepare object */
        $aom = new BundleEntity\AddressesOfMember();
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        $aom->setType($type);
        $aom->setAddress($address);
        $aom->setMember($member);
        $aom->setDateAdded($now);
        $aom->setDateUpdated($now);
        /** persist entry */
        $this->em->persist($aom);
        $collection[] = $aom;
        $count++;
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'error.db.insert.failed';
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        unset($count, $collection);
        return $this->response;
    }

    /***
     * @name            addAddressToMember()
     *                  Associates address and member with type.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said Imamoglu
     *
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->insertAddress()
     *
     * @use             MMBS\MemberManagementModel
     * @use             BundleEntity\AddressesOfMember
     *
     * @throws          InvalidParameterException
     *
     * @param   array           $phones
     * @param   mixed           $address
     *
     * @return          array   $response
     *
     */

    public function addPhoneNumbersToAddress($phones,$address) {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        foreach ($phones as $key=>$phone) {
            if (!is_numeric($phone) && !is_string($phone) && !$phone instanceof ContactEntity\PhoneNumber) {
                unset($phones[$key]);
            }
        }

        if (!is_numeric($address) && !is_string($address) && !$address instanceof BundleEntity\Address) {
            unset($address);
        }
        $phoneCollectionForList = array();
        foreach ($phones as $phone) {
            $phoneCollectionForList[] = $phone->getId();
        }


        /** If no entity is provided as address we need to check if it does exist */
        $contactModel = new CMBService\ContactInformationModel($this->kernel);
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' =>$contactModel->getEntityDefinition('phone_number','alias').'.id' , 'comparison' => 'in', 'value' =>$phoneCollectionForList ),
                )
            )
        );
        unset($phoneCollectionForList);
        $response = $contactModel->listPhoneNumbers($filter);
        $phoneCollection = array();
        if (!$response['error']) {
            $phoneCollection = $response['result']['set'];
        }

        $count = 0;
        /** Get Member */
        if (is_int($address)) {
            $response = $this->getAddress($address);
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Address', 'error.db.member.notexist');
            }
            $address = $response['result']['set'];
        }
        $collection = array();
        /** Check if association exists */
        if (!$this->isPhoneAssociatedWithAddress($phoneCollection,$address,true)) {
            /** prepare object */
            foreach ($phoneCollection as $phone) {
                $aom = new BundleEntity\PhoneNumbersOfAddresses();
                $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
                $aom->setAddress($address);
                $aom->setPhone($phone);;
                $aom->setDateAdded($now);
                $aom->setDateUpdated($now);
                /** persist entry */
                $this->em->persist($aom);
                $collection[] = $aom;
                $count++;
            }
        }



        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'error.db.insert.failed';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        unset($count, $collection);
        return $this->response;
    }

    /**
     * @name            listPhoneNumbersOfAddresses()
     *                  Lists records from addresses of member table
     * 
     * @since           1.0.0
     * @version         1.0.6
     *
     * @author          Said Imamoglu
     * @author          Can Berkol
     *
     * @use             $this->type_opts
     * @use             $this->listAddresses()
     * 
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array   $response
     * 
     */

    public function listAddressesOfMember($filter,$sortOrder = null, $limit = null, $queryStr = null) {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'error.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['addresses_of_member']['alias']
                . ' FROM ' . $this->entity['addresses_of_member']['name'] . ' ' . $this->entity['addresses_of_member']['alias'];
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        $queryStr .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $addresses = array();
        foreach ($result as $address) {
            $addresses[] = $address->getAddress();
        }
        unset($result);
        $totalRows = count($addresses);
        if ($totalRows < 1) {
            $this->response['code'] = 'error.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $addresses,
                'total_rows' => $totalRows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name            isAddressAssociatedWithMember ()
     *                  Checks if the product is already associated with the
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @user            $this->createException
     *
     * @param           int $address
     * @param           int $member
     * @param           string $type
     * @param           bool $byPass
     *
     * @return          mixed           bool or $response
     */
    public function isAddressAssociatedWithMember($address, $member, $type = null,$byPass= false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_numeric($address) && !is_string($address) && !$address instanceof BundleEntity\Address) {
            return $this->createException('InvalidParameter', 'Address', 'error.invalid.parameter.product');
        }

        if (!is_numeric($member) && !$member instanceof MemberEntity\Member) {
            return $this->createException('InvalidParameter', 'Member', 'error.invalid.parameter.product_category');
        }
        if (is_numeric($address)) {
            /** If no entity is provided as address we need to check if it does exist */
            $response = $this->getAddress($address, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Address', 'error.db.address.notexist');
            }
            $address = $response['result']['set'];
        }

        if (is_numeric($member)) {
            /** If no entity is provided as member we need to check if it does exist */
            $memberModel = new MMBService\MemberManagementModel($this->kernel);
            $response = $memberModel->getMember($member, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Member', 'error.db.member.notexist');
            }
            $member = $response['result']['set'];
        }

        $q_str = 'SELECT '. $this->entity['addresses_of_member']['alias']
            . ' FROM ' . $this->entity['addresses_of_member']['name'] . ' ' . $this->entity['addresses_of_member']['alias']
            . ' WHERE ' . $this->entity['addresses_of_member']['alias'] . '.member = ' . $member->getId()
            . ' AND ' . $this->entity['addresses_of_member']['alias'] . '.address = ' . $address->getId();

        /** If address type is not null then append this query to $q_str */
        if (!is_null($type)) {
            $response = $this->getAddressType($type, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'AddressType', 'error.db.type.notexist');
            }
            $type = $response['result']['set'];

            $q_str .= ' AND ' . $this->entity['addresses_of_member']['alias'] . '.type = ' .$type->getId();
        }
        $query = $this->em->createQuery($q_str);

        $result = count($query->getResult());

        /** flush all into database */
        $found = false;
        if ($result > 0) {
            $found = true;
            $code = 'success.db.entry.exist';
        } else {
            $code = 'error.member.not.associated.with.address';
        }
        if ($byPass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $found,
                'total_rows' => $result,
                'last_insert_id' => null,
            ),
            'error' => $found == true ? false : true,
            'code' => $code,
        );
        return $this->response;
    }

    /**
     * @name            isPhoneAssociatedWithAddress()
     *                  Checks if the phone is already associated with the address.
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @user            $this->createException
     *
     * @param           array $phones
     * @param           int $address
     * @param           bool $byPass
     *
     * @return          mixed           bool or $response
     */
    public function isPhoneAssociatedWithAddress($phones, $address,$byPass= false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!$address instanceof BundleEntity\Address) {
            return $this->createException('InvalidParameter', 'Address', 'error.invalid.parameter.product');
        }
        foreach ($phones as $key=>$phone) {
            if (!$phone instanceof ContactEntity\PhoneNumber) {
                unset($phones[$key]);
            }
        }
        if (count($phones) < 1) {
            return $this->createException('InvalidParameter', 'Member', 'error.invalid.parameter.product_category');
        }
        $phoneCollection = array();
        foreach($phones as $phone){
            $phoneCollection[] = $phone->getId();
        }

        $q_str = 'SELECT '. $this->entity['phone_numbers_of_addresses']['alias']
            . ' FROM ' . $this->entity['phone_numbers_of_addresses']['name'] . ' ' . $this->entity['phone_numbers_of_addresses']['alias']
            . ' WHERE ' . $this->entity['phone_numbers_of_addresses']['alias'] . '.phone in ('.implode(',',$phoneCollection).') '
            . ' AND ' . $this->entity['phone_numbers_of_addresses']['alias'] . '.address = ' . $address->getId();


        $query = $this->em->createQuery($q_str);

        $result = count($query->getResult());

        /** flush all into database */
        $found = false;
        if ($result > 0) {
            $found = true;
            $code = 'success.db.entry.exist';
        } else {
            $code = 'success.db.entry.noexist';
        }
        if ($byPass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $found,
                'total_rows' => $result,
                'last_insert_id' => null,
            ),
            'error' => $found == true ? false : true,
            'code' => $code,
        );
        return $this->response;
    }

    /**
     * @name            listPhoneNumbersOfAddresses()
     *                  Lists records from phone_numbers_of_address
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array       $response
     *
     */

    public function listPhoneNumbersOfAddresses($filter,$sortOrder = null, $limit = null, $queryStr = null) {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'error.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['phone_numbers_of_addresses']['alias']
                . ' FROM ' . $this->entity['phone_numbers_of_addresses']['name'] . ' ' . $this->entity['phone_numbers_of_addresses']['alias'];
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        $queryStr .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $totalRows = count($result);
        if ($totalRows < 1) {
            $this->response['code'] = 'error.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $totalRows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name            listPhoneNumbersOfAddresses()
     *                  Lists records from phone_numbers_of_address by given address
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $address
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array       $response
     *
     */

    public function listPhoneNumbersOfAddressesByAddress($address,$filter = array(),$sortOrder = null, $limit = null, $queryStr = null) {
        if (!is_int($address) && !$address instanceof \stdClass && !$address instanceof Address) {
            return $this->createException('InvalidParameter', '', 'error.invalid.parameter.address');
        }

        if ($address instanceof Address) {
            $address = $address->getId();
        }
        if ($address instanceof \stdClass) {
            $address = $address->id;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_addresses']['alias'] . '.address', 'comparison' => '=', 'value' => $address),
                )
            )
        );
        return $this->listPhoneNumbersOfAddresses($filter,$sortOrder,$limit,$queryStr);
    }
    /**
     * @name            listPhoneNumbersOfAddressesByPhone()
     *                  Lists records from phone_numbers_of_address by given phone
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $phone
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array       $response
     *
     */

    public function listPhoneNumbersOfAddressesByPhone($phone,$filter = array(),$sortOrder = null, $limit = null, $queryStr = null) {
        if (!is_int($phone) && !$phone instanceof \stdClass && !$phone instanceof Address) {
            return $this->createException('InvalidParameter', '', 'error.invalid.parameter.address');
        }

        if ($phone instanceof Address) {
            $phone = $phone->getId();
        }
        if ($phone instanceof \stdClass) {
            $phone = $phone->id;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_addresses']['alias'] . '.phone', 'comparison' => '=', 'value' => $phone),
                )
            )
        );
        return $this->listPhoneNumbersOfAddresses($filter,$sortOrder,$limit,$queryStr);
    }


    /**
     * @name            arePhoneNumbersAssociatedWithAddress ()
     *                  Checks if the phone numbers are already associated with the address
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @user            $this->createException
     *
     * @param           int $phones
     * @param           int $address
     * @param           bool $byPass
     *
     * @return          mixed           bool or $response
     */
    public function arePhoneNumbersAssociatedWithAddress($phones, $address, $byPass= false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_array($phones)) {
            return $this->createException('InvalidParameter', 'PhoneNumbers Collection', 'error.invalid.parameter.phone.numbers.collection');
        }

        if (!is_numeric($address) && !$address instanceof Address && !$address instanceof \stdClass) {
            return $this->createException('InvalidParameter', 'Address', 'error.invalid.parameter.product_category');
        }
        if ($address instanceof \stdClass) {
            $address = $address->id;
        }
        if ($address instanceof Address) {
            $address = $address->getId();
        }
        $phoneCollection = array();
        foreach ($phones as $phone) {
            if (!is_numeric($phone)  && !$phone instanceof ContactEntity\PhoneNumber && !$phone instanceof \stdClass) {
                return $this->createException('InvalidParameter', 'PhoneNumber', 'error.invalid.parameter.product');
            }
            if ($phone instanceof \stdClass) {
                $phone = $phone->id;
            }
            if ($phone instanceof ContactEntity\PhoneNumber) {
                $phone = $phone->getId();
            }
            $phoneCollection[] = $phone;
        }

        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_addresses']['alias'].'.address', 'comparison' => '=', 'value' => $address),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_addresses']['alias'].'.phone', 'comparison' => 'in', 'value' => $phoneCollection),
                ),
            )
        );
        $found = true;
        $code = 'success.db.entry.exist';
        $response = $this->listPhoneNumbersOfAddresses($filter);
        if ($response['error']) {
            $found = false;
            $code = 'error.db.entry.notexist';
        }
        if ($byPass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => $found == true ? false : true,
            'code' => $code,
        );
        return $this->response;
    }
    /**
     * @name    getAddressOfMember()
     *          Gets given address of given member
     *
     * @version 1.0.6
     * @since   1.0.6
     *
     * @use     $this->listAddressesOfMember()
     *
     * @param   mixed   $member
     * @param   mixed   $filter
     * @param   mixed   $sortOrder
     * @param   mixed   $limit
     * @param   mixed   $queryStr
     *
     * @return   Response
     */
    public function listAddressOfMemberByMember($member,$filter = array(),$sortOrder = null, $limit = null, $queryStr = null){
        if ((!is_int($member) && !$member instanceof \stdClass && !$member instanceof MemberEntity\Member)) {
            return $this->createException('InvalidParameter', 'Member or Address', 'error.invalid.parameter.member.or.address');
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MemberEntity\Member) {
            $member = $member->getId();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['addresses_of_member']['alias'].'.member', 'comparison' => '=', 'value' =>$member ),
                ),
            )
        );
        return $this->listAddressesOfMember($filter,$sortOrder,$limit,$queryStr);
    }

    /**
     * @name    getAddressOfMember()
     *          Gets given address of given member
     *
     * @version 1.0.6
     * @since   1.0.7
     *
     * @use     $this->listAddressesOfMember()
     *
     * @param   mixed   $member
     * @param   mixed   $address
     *
     * @return   Response
     */
    public function getAddressOfMember($member,$address){
        /** We are validating member in listAddressOfMemberByMember method so we dont need to check it again. */
        if (!is_int($address) && !$address instanceof \stdClass && !$address instanceof BundleEntity\Address) {
            return $this->createException('InvalidParameter', 'Address', 'error.invalid.parameter.address');
        }
        if ($address instanceof \stdClass) {
            $address = $address->id;
        }
        if ($address instanceof BundleEntity\Address) {
            $address = $address->getId();
        }
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['addresses_of_member']['alias'].'.address', 'comparison' => '=', 'value' =>$address ),
                ),
            )
        );
        $response = $this->listAddressOfMemberByMember($member,$filter);
        if ($response['error']) {
            return $response;
        }
        $response['result']['set'] = $response['result']['set'][0];
        unset($member,$address);
        return $response;
    }

}

/***
 * Change Log:
 * **************************************
 * v1.0.7                      Said İmamoğlu
 * 04.07.2013
 * **************************************
 * D getAddressOfMemberByMember()
 * A getAddressOfMember()
 * **************************************
 * v1.0.6                      Said İmamoğlu
 * 10.06.2013
 * **************************************
 * A listAddressOfMemberByMember()
 * A getAddressOfMember()
 * U listAddressOfMember()
 * **************************************
 * v1.0.5                      Said İmamoğlu
 * 06.06.2013
 * **************************************
 * A arePhoneNumbersAssociatedWithAddress()
 * **************************************
 * v1.0.4                      Said İmamoğlu
 * 04.06.2013
 * **************************************
 * A listPhoneNumbersOfAddresses()
 * A listPhoneNumbersOfAddressesByAddress()
 * A listPhoneNumbersOfAddressesByPhone()
 * **************************************
 * v1.0.3                      Can Berkol
 * 02.05.2013
 * **************************************
 * U listAddressesOfMember()
 *
 * **************************************
 * v1.0.3                   Said İmamoğlu
 * 02.05.2013
 * **************************************
 * A addAddressToMember()
 * A addPhoneToAddress()
 * A isAddressAssociatedWithMember()
 * A isPhoneAssociatedWithAddress()
 * U deleteAddresses()
 * U deleteAddressTypes()
 *
 * **************************************
 * v1.0.2                   Said İmamoğlu
 * 25.04.2013
 * **************************************
 * U insertAddressTypes()
 * A insertAddressTypesLocalizations()
 * U updateAddressType()
 * U updateAddressTypes()
 *
 * **************************************
 * v1.0.1                   Said İmamoğlu
 * 04.12.2013
 * **************************************
 * A deleteAddress()
 * A deleteAddresses()
 * A doesAddressExist()
 * A insertAddress()
 * A insertAddresses()
 * A getAddress()
 * A listAddresses()
 * A updateAddress()
 * A updateAddresses()
 * A deleteAddressType()
 * A deleteAddressTypes()
 * A doesAddressTypeExist()
 * A insertAddressType()
 * A insertAddressTypes()
 * A getAddressType()
 * A listAddressTypes()
 * A updateAddressType()
 * A updateAddressTypes()

 * 
 * 
 * **************************************
 * v1.0.0                      Can Berkol
 * 03.12.2013
 * **************************************
 * A __construct()
 * A __destruct()
 */