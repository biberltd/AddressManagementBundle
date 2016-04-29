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
 *     name="address_type_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_address_type_localization", columns={"address_type","language"}),
 *         @ORM\UniqueConstraint(
 *             name="idx_u_address_type_localization_url_key",
 *             columns={"address_type","language","url_key"}
 *         )
 *     }
 * )
 */
class AddressTypeLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $url_key;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AddressType", inversedBy="localizations")
     * @ORM\JoinColumn(name="address_type", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType
     */
    private $address_type;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @param \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $address_type
     *
     * @return $this
     */
    public function setAddressType(\BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType $address_type) {
        if(!$this->setModified('address_type', $address_type)->isModified()) {
            return $this;
        }
        $this->address_type = $address_type;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\AddressManagementBundle\Entity\AddressType
     */
    public function getAddressType() {
        return $this->address_type;
    }

    /**
     * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
     *
     * @return $this
     */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
        $this->language = $language;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
	 */
    public function getLanguage() {
        return $this->language;
    }

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
    public function setName(string $name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
        $this->name = $name;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getName() {
        return $this->name;
    }

	/**
	 * @param string $url_key
	 *
	 * @return $this
	 */
    public function setUrlKey(string $url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
        $this->url_key = $url_key;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUrlKey() {
        return $this->url_key;
    }
}