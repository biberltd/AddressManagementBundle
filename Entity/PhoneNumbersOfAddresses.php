<?php
/**
 * @name        PhoneNumbersOfAddresses
 * @package		BiberLtd\Bundle\CoreBundle\AddressManagementBundle
 *
 * @author		Can Berkol
 *
 * @version     1.0.0
 * @date        25.04.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\AddressManagementBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="phone_numbers_of_addresses",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}
 * )
 * 
 */
class PhoneNumbersOfAddresses extends CoreEntity{
    /**
     * 
     * 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber",
     *     inversedBy="phoneNumbersOfAddresses"
     * )
     * @ORM\JoinColumn(name="phone", referencedColumnName="id")
     * @ORM\Id
     */
    private $phone;

    /**
     * 
     * 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\AddressManagementBundle\Entity\Address",
     *     inversedBy="phoneNumbersOfAddresses"
     * )
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     * @ORM\Id
     */
    private $address;

    /**
     * @name                  setAddress ()
     *                                   Sets the address property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $address
     *
     * @return          object                $this
     */
    public function setAddress($address) {
        if($this->setModified('address', $address)->isModified()) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * @name            getAddress ()
     *                  Returns the value of address property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @name            setPhone ()
     *                  Sets the phone property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $phone
     *
     * @return          object                $this
     */
    public function setPhone($phone) {
        if($this->setModified('phone', $phone)->isModified()) {
            $this->phone = $phone;
        }

        return $this;
    }

    /**
     * @name            getPhone ()
     *                  Returns the value of phone property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->phone
     */
    public function getPhone() {
        return $this->phone;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 06.03.2014
 * **************************************
 * A getAddress()
 * A getPhone()
 * A setAddress()
 * A setPhone()
 */