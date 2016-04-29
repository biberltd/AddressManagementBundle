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
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
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
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", unique=true, length=155, nullable=false)
     * @var string
     */
    private $code;

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
     * @ORM\OneToMany(targetEntity="AddressTypeLocalization", mappedBy="address_type")
     * @var array
     */
    protected $localizations;

	/**
	 * @param string $code
	 *
	 * @return $this
	 */
    public function setCode(string $code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
        $this->code = $code;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getCode() {
        return $this->code;
    }

	/**
	 * @return int
	 */
    public function getId() {
        return $this->id;
    }
}