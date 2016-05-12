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
namespace BiberLtd\Bundle\AddressManagementBundle\Entity;
use BiberLtd\Bundle\SiteManagementBundle\Entity\Site;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
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
 *         @ORM\UniqueConstraint(name="idx_u_address_street_address", columns={"address","zip","site"})
 *     }
 * )
 */
class Address extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @var string
     */
    private $title;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $address;

    /** 
     * @ORM\Column(type="string", length=20, nullable=true)
     * @var string
     */
    private $zip;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_removed;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="string", length=5, nullable=true)
     * @var string
     */
    private $nr;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", nullable=false, onDelete="CASCADE") \BiberLtd\Bundle\LocationManagementBundle\Entity\Country
     */
    private $country;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\City")
     * @ORM\JoinColumn(name="city", referencedColumnName="id", nullable=false, onDelete="CASCADE") \BiberLtd\Bundle\LocationManagementBundle\Entity\City
     */
    private $city;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id", onDelete="CASCADE") \BiberLtd\Bundle\LocationManagementBundle\Entity\State
     */
    private $state;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\Neighborhood")
     * @ORM\JoinColumn(name="neighborhood", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\LocationManagementBundle\Entity\Neighborhood
     */
    private $neighborhood;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\District")
     * @ORM\JoinColumn(name="district", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\LocationManagementBundle\Entity\District
     */
    private $district;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title){
        if(!$this->setModified('title', $title)->isModified()){
            return $this;
        }
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress(string $address){
        if(!$this->setModified('address', $address)->isModified()){
            return $this;
        }
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(){
        return $this->address;
    }

    /**
     * @param string $zip
     *
     * @return $this
     */
    public function setZip(string $zip){
        if(!$this->setModified('zip', $zip)->isModified()){
            return $this;
        }
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getZip(){
        return $this->zip;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\Country $country
     *
     * @return $this
     */
    public function setCountry(\BiberLtd\Bundle\LocationManagementBundle\Entity\Country $country){
        if(!$this->setModified('country', $country)->isModified()){
            return $this;
        }
        $this->country = $country;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\Country
     */
    public function getCountry(){
        return $this->country;
    }

    /***
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\City $city
     *
     * @return $this
     */
    public function setCity(\BiberLtd\Bundle\LocationManagementBundle\Entity\City $city){
        if(!$this->setModified('city', $city)->isModified()){
            return $this;
        }
        $this->city = $city;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\City
     */
    public function getCity(){
        return $this->city;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\State $state
     *
     * @return $this
     */
    public function setState(\BiberLtd\Bundle\LocationManagementBundle\Entity\State $state){
        if(!$this->setModified('state', $state)->isModified()){
            return $this;
        }
        $this->state = $state;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\State
     */
    public function getState(){
        return $this->state;
    }

    /**
     * @param string $nr
     *
     * @return $this
     */
    public function setNr(string $nr) {
        if(!$this->setModified('nr', $nr)->isModified()) {
            return $this;
        }
        $this->$nr = $nr;
    }

    /**
     * @return string
     */
    public function getNr() {
        return $this->nr;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\Neighborhood $neighborhood
     *
     * @return $this
     */
    public function setNeighborhood(\BiberLtd\Bundle\LocationManagementBundle\Entity\Neighborhood $neighborhood){
        if(!$this->setModified('neighborhood', $neighborhood)->isModified()){
            return $this;
        }
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\Neighborhood
     */
    public function getNeighborhood(){
        return $this->neighborhood;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\District $district
     *
     * @return $this
     */
    public function setDistrict(\BiberLtd\Bundle\LocationManagementBundle\Entity\District $district){
        if(!$this->setModified('district', $district)->isModified()){
            return $this;
        }
        $this->district = $district;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\District
     */
    public function getDistrict(){
        return $this->district;
    }

    /**
     * @return \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     * @return $this
     */
    public function setSite(Site $site)
    {
        if (!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
        $this->site = $site;
        return $this;
    }

}