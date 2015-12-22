<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        13.12.2015
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
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @var \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber
     */
    private $phone;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\AddressManagementBundle\Entity\Address",
     *     inversedBy="phoneNumbersOfAddresses"
     * )
     * @ORM\JoinColumn(name="address", referencedColumnName="id", nullable=false)
     * @ORM\Id
     * @var \BiberLtd\Bundle\AddressManagementBundle\Entity\Address
     */
    private $address;

    /**
     * @param \BiberLtd\Bundle\AddressManagementBundle\Entity\Address $address
     *
     * @return $this
     */
    public function setAddress(\BiberLtd\Bundle\AddressManagementBundle\Entity\Address $address) {
        if($this->setModified('address', $address)->isModified()) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber $phone
     *
     * @return $this
     */
    public function setPhone(\BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber $phone) {
        if($this->setModified('phone', $phone)->isModified()) {
            $this->phone = $phone;
        }

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ContactInformationBundle\Entity\PhoneNumber
     */
    public function getPhone() {
        return $this->phone;
    }

}