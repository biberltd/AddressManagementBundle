<?php
namespace BiberLtd\Core\Bundles\AddressManagementBundle\Entity;
use BiberLtd\Core\CoreEntity;
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
     */
    private $description;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    private $alias;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_removed;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\AddressManagementBundle\Entity\Address")
     * @ORM\JoinColumn(name="address", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $address;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\AddressManagementBundle\Entity\AddressType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     */
    private $address_type;

    /** 
     * 
     * 
     */
    private $type;

    /**
     * @name            setAddress ()
     *                  Sets the address property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           BiberLtd\Core\Bundles\AddressManagementBundle\Entity\Address $address
     *
     * @return          object                $this
     */
    public function setAddress($address) {
        if(!$this->setModified('address', $address)->isModified()) {
            return $this;
        }
        $this->address = $address;
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
     * @return          BiberLtd\Core\Bundles\AddressManagementBundle\Entity\Address           $this->address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @name            setType ()
     *                  Sets the type property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           BiberLtd\Core\Bundles\AddressManagementBundle\Entity\Type $type
     *
     * @return          object                $this
     */
    public function setType($type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
		return $this;
    }

    /**
     * @name            getType ()
     *                  Returns the value of type property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          BiberLtd\Core\Bundles\AddressManagementBundle\Entity\Type           $this->type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @name            setAlias ()
     *                  Sets the alias property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           string $alias
     *
     * @return          object                $this
     */
    public function setAlias($alias) {
        if(!$this->setModified('alias', $alias)->isModified()) {
            return $this;
        }
        $this->alias = $alias;
		return $this;
    }

    /**
     * @name            getAlias ()
     *                  Returns the value of alias property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string           $this->alias
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @name            setDescription ()
     *                  Sets the description property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           string $description
     *
     * @return          object                $this
     */
    public function setDescription($description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
        $this->description = $description;
		return $this;
    }

    /**
     * @name            getDescription ()
     *                  Returns the value of description property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string           $this->description
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @name            setMember ()
     *                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
        $this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                  Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member           $this->member
     */
    public function getMember() {
        return $this->member;
    }



}