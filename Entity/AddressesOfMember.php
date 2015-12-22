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
 *     name="addresses_of_member",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_addresses_of_member_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_addresses_of_member_date_updated", columns={"date_updated"}),
 *         @ORM\Index(name="idx_n_addresses_of_member_date_removed", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_addresses_of_member", columns={"address","member"})}
 * )
 */
class AddressesOfMember extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $description;

    /** 
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $alias;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_removed;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\AddressManagementBundle\Entity\Address")
     * @ORM\JoinColumn(name="address", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\AddressManagementBundle\Entity\Address
     */
    private $address;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType
     */
    private $address_type;

	/**
	 * @param \BiberLtd\Bundle\AddressManagementBundle\Entity\Address $address
	 *
	 * @return $this
	 */
    public function setAddress(\BiberLtd\Bundle\AddressManagementBundle\Entity\Address $address) {
        if(!$this->setModified('address', $address)->isModified()) {
            return $this;
        }
        $this->address = $address;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\Address
	 */
    public function getAddress() {
        return $this->address;
    }

	/**
	 * @param \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $type
	 *
	 * @return $this
	 */
    public function setAddressType(\BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
		return $this;
    }

	/**
	 * @param \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $type
	 *
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressesOfMember
	 */
	public function setType(\BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $type) {
		return $this->setAddressType($type);
	}

	/**
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType
	 */
    public function getAddressType() {
        return $this->address_type;
    }

	/**
	 * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType
	 */
	public function getType(){
		return $this->getAddressType();
	}

	/**
	 * @param string $alias
	 *
	 * @return $this
	 */
    public function setAlias(\string $alias) {
        if(!$this->setModified('alias', $alias)->isModified()) {
            return $this;
        }
        $this->alias = $alias;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getAlias() {
        return $this->alias;
    }

	/**
	 * @param string $description
	 *
	 * @return $this
	 */
    public function setDescription(\string $description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
        $this->description = $description;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getDescription() {
        return $this->description;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
        $this->member = $member;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getMember() {
        return $this->member;
    }
}