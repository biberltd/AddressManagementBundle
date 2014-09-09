<?php
namespace BiberLtd\Core\Bundles\AddressManagementBundle\Entity;
use BiberLtd\Core\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="address_type",
 *     options={"collate":"utf8_turkish_ci","charset":"utf8","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_address_type_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_address_type_date_updated", columns={"date_updated"}),
 *         @ORM\Index(name="idx_n_address_type_date_removed", columns={"date_removed"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_address_type_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_address_type_code", columns={"code"})
 *     }
 * )
 */
class AddressType extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=5)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", unique=true, length=155, nullable=false)
     */
    private $code;

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
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\AddressManagementBundle\Entity\AddressTypeLocalization",
     *     mappedBy="address_type"
     * )
     */
    protected $localizations;

    /**
     * @name                  setCode ()
     *                                Sets the code property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           string $code
     *
     * @return          object                $this
     */
    public function setCode($code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
        $this->code = $code;
		return $this;
    }

    /**
     * @name            getCode ()
     *                          Returns the value of code property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string           $this->code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @name            getId()
     *                      Returns the value of id property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          integer           $this->id
     */
    public function getId() {
        return $this->id;
    }


}