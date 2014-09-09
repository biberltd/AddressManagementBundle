<?php
/**
 * @name        Address
 * @package		BiberLtd\Core\AddressManagementBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        20.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */

namespace BiberLtd\Core\Bundles\AddressManagementBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="address",
 *     options={"collate":"utf8_turkish_ci","charset":"utf8","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_address_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_address_date_updated", columns={"date_updated"}),
 *         @ORM\Index(name="idx_n_address_date_removed", columns={"date_removed"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_address_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_address_street_address", columns={"address","zip","city","state","country","site"})
 *     }
 * )
 */
class Address extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $title;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $address;

    /** 
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zip;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_removed;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\AddressManagementBundle\Entity\PhoneNumbersOfAddresses",
     *     mappedBy="address"
     * )
     */
    private $phoneNumbersOfAddresses;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $nr;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\LocationManagementBundle\Entity\Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $country;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\LocationManagementBundle\Entity\City")
     * @ORM\JoinColumn(name="city", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $city;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\LocationManagementBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id", onDelete="CASCADE")
     */
    private $state;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $site;

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }
    /**
     * @name            setTitle()
     *                  Sets $title property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           string          $title
     *
     * @return          object          $this
     */
    public function setTitle($title){
        if(!$this->setModified('title', $title)->isModified()){
            return $this;
        }
        $this->title = $title;

        return $this;
    }
    /**
     * @name            getTitle()
     *                  Gets $title property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->title
     */
    public function getTitle(){
        return $this->title;
    }
    /**
     * @name            setAddress()
     *                  Sets $address property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           string          $address
     *
     * @return          object          $this
     */
    public function setAddress($address){
        if(!$this->setModified('address', $address)->isModified()){
            return $this;
        }
        $this->address = $address;

        return $this;
    }
    /**
     * @name            getAddress()
     *                  Gets $address property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->address
     */
    public function getAddress(){
        return $this->address;
    }
    /**
     * @name            setZip()
     *                  Sets $zip property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           string          $zip
     *
     * @return          object          $this
     */
    public function setZip($zip){
        if(!$this->setModified('zip', $zip)->isModified()){
            return $this;
        }
        $this->zip = $zip;

        return $this;
    }
    /**
     * @name            getZip()
     *                  Gets $zip property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->zip
     */
    public function getZip(){
        return $this->zip;
    }

    /**
     * @name            setCountry()
     *                  Sets $country property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           BiberLtd\Core\Bundles\LocationManagementBundle\Entity\Country           $country
     *
     * @return          object          $this
     */
    public function setCountry($country){
        if(!$this->setModified('country', $country)->isModified()){
            return $this;
        }
        $this->country = $country;

        return $this;
    }
    /**
     * @name            getCountry()
     *                  Gets $country property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          BiberLtd\Core\Bundles\LocationManagementBundle\Entity\Country           $this->country
     */
    public function getCountry(){
        return $this->country;
    }
    /**
     * @name            setCity()
     *                  Sets $city property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           BiberLtd\Core\Bundles\LocationManagementBundle\Entity\City          $city
     *
     * @return          object          $this
     */
    public function setCity($city){
        if(!$this->setModified('city', $city)->isModified()){
            return $this;
        }
        $this->city = $city;

        return $this;
    }
    /**
     * @name            getCity()
     *                  Gets $city property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          BiberLtd\Core\Bundles\LocationManagementBundle\Entity\City          $this->city
     */
    public function getCity(){
        return $this->city;
    }
    /**
     * @name            setState()
     *                  Sets $state property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           BiberLtd\Core\Bundles\LocationManagementBundle\Entity\State          $state
     *
     * @return          object          $this
     */
    public function setState($state){
        if(!$this->setModified('state', $state)->isModified()){
            return $this;
        }
        $this->state = $state;

        return $this;
    }
    /**
     * @name            getState()
     *                  Gets $state property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          BiberLtd\Core\Bundles\LocationManagementBundle\Entity\State          $this->state
     */
    public function getState(){
        return $this->state;
    }

    /**
     * @name            setNr ()
     *                  Sets the nr property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           string $nr
     *
     * @return          object                $this
     */
    public function setNr($nr) {
        if(!$this->setModified('nr', $nr)->isModified()) {
            return $this;
        }
        $this->$nr = $nr;
    }

    /**
     * @name            getNr ()
     *                  Returns the value of nr property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string           $this->nr
     */
    public function getNr() {
        return $this->nr;
    }


}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 20.09.2013
 * **************************************
 * A getLocalizations()
 * A getAdvertisements()
 * A getHeight()
 * A getId()
 * A getPricePerClick()
 * A getPricePerView()
 * A getWidth()
 *
 * A setLocalizations()
 * A setAdvertisements()
 * A setHeight()
 * A setPricePerClick()
 * A setWidth()
 *
 */