<?php

namespace Academia\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Order
 *
 * @ORM\Table(name="academia_order")
 * @ORM\Entity(repositoryClass="Academia\OrderBundle\Entity\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Order
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderId", type="integer")
     */
    protected $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="discipline", type="string", length=255)
     */
    protected $discipline;

    /**
     * @var integer
     *
     * @ORM\Column(name="pages", type="integer")
     */
    protected $pages;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime")
     */
    protected $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="instructions", type="text")
     */
    protected $instructions;

    /**
     * @var string
     *
     * @ORM\Column(name="citation", type="string", length=255)
     */
    protected $citation;

    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="order_attachment", fileNameProperty="attachmentName")
     *
     * @var File $image
     */
    protected $attachment;

    /**
     * @ORM\Column(name="attachment_name", type="string", length=255, nullable=true)
     *
     * @var string $attachmentName
     */
    protected $attachmentName;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderId = sprintf('%09d', mt_rand(0, 1999999999));
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return Order
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    
        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Order
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set discipline
     *
     * @param string $discipline
     * @return Order
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;
    
        return $this;
    }

    /**
     * Get discipline
     *
     * @return string 
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Set pages
     *
     * @param integer $pages
     * @return Order
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    
        return $this;
    }

    /**
     * Get pages
     *
     * @return integer 
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Order
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    
        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set instructions
     *
     * @param string $instructions
     * @return Order
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
    
        return $this;
    }

    /**
     * Get instructions
     *
     * @return string 
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set citation
     *
     * @param string $citation
     * @return Order
     */
    public function setCitation($citation)
    {
        $this->citation = $citation;
    
        return $this;
    }

    /**
     * Get citation
     *
     * @return string 
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * Set attachmentName
     *
     * @param string $attachmentName
     * @return Order
     */
    public function setAttachmentName($attachmentName)
    {
        $this->attachmentName = $attachmentName;
    
        return $this;
    }

    /**
     * Get attachmentName
     *
     * @return string 
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getAttachment()
    {
        return $this->attachment;
    }


}