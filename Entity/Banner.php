<?php

namespace Objects\AdminBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Doctrine\ORM\Mapping as ORM;

/**
 * Objects\AdminBundle\Entity\Banner
 * @Assert\Callback(methods={"isImageCorrect"})
 * @Assert\Callback(methods={"isFlashCorrect"})
 * @Assert\Callback(methods={"isBannerCorrect"})
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Objects\AdminBundle\Entity\BannerRepository")
 */
class Banner {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="text", nullable=true)
     */
    private $code;

    /**
     * @var string
     * @Assert\Url
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfClicks", type="integer")
     */
    private $numberOfClicks = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfViews", type="integer")
     */
    private $numberOfViews = 0;

    /**
     * @var string $fileName
     *
     * @ORM\Column(name="fileName", type="string", length=20, nullable=true)
     */
    private $fileName;

    /**
     * a temp variable for storing the old file name to delete the old file after the update
     * @var string
     */
    private $SWFTemp;

    /**
     * @Assert\File(
     *      mimeTypes = {"application/x-shockwave-flash"},
     *      mimeTypesMessage = "Please upload a valid SWF file"
     * )
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $SWF;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=20, nullable=true)
     */
    private $image;

    /**
     * a temp variable for storing the old image name to delete the old image after the update
     * @var string $temp
     */
    private $temp;

    /**
     * @Assert\Image
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $file;

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set file
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return $this
     */
    public function setFile($file) {
        $this->file = $file;
        //check if we have an old image
        if ($this->image) {
            //store the old name to delete on the update
            $this->temp = $this->image;
            $this->image = NULL;
        } else {
            $this->image = 'initial';
        }
        return $this;
    }

    /**
     * Get file
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * this function is used to delete the current image
     * the deleting of the current object will also delete the image and you do not need to call this function
     * if you call this function before you remove the object the image will not be removed
     */
    public function removeImage() {
        //check if we have an old image
        if ($this->image) {
            //store the old name to delete on the update
            $this->temp = $this->image;
            //delete the current image
            $this->image = NULL;
        }
    }

    /**
     * create the the directory if not found
     * @param string $directoryPath
     * @throws \Exception if the directory can not be created
     */
    private function createDirectory($directoryPath) {
        if (!@is_dir($directoryPath)) {
            $oldumask = umask(0);
            $success = @mkdir($directoryPath, 0755, TRUE);
            umask($oldumask);
            if (!$success) {
                throw new \Exception("Can not create the directory $directoryPath");
            }
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (NULL !== $this->file && (NULL === $this->image || 'initial' === $this->image)) {
            //get the image extension
            $extension = $this->file->guessExtension();
            //generate a random image name
            $img = uniqid();
            //get the image upload directory
            $uploadDir = $this->getUploadRootDir();
            $this->createDirectory($uploadDir);
            //check that the file name does not exist
            while (@file_exists("$uploadDir/$img.$extension")) {
                //try to find a new unique name
                $img = uniqid();
            }
            //set the image new name
            $this->image = "$img.$extension";
        }

        if (NULL !== $this->SWF && (NULL === $this->fileName || 'initial' === $this->fileName)) {
            //get the image extension
            $extension = $this->SWF->guessExtension();
            //generate a random file name
            $fileName = uniqid();
            //get the file upload directory
            $uploadDir = $this->getSWFUploadRootDir();
            $this->createDirectory($uploadDir);
            //check that the file name does not exist
            while (@file_exists("$uploadDir/$fileName.$extension")) {
                //try to find a new unique name
                $fileName = uniqid();
            }
            //set the file new name
            $this->fileName = "$fileName.$extension";
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (NULL !== $this->file) {
            // you must throw an exception here if the file cannot be moved
            // so that the entity is not persisted to the database
            // which the UploadedFile move() method does
            $this->file->move($this->getUploadRootDir(), $this->image);
            //remove the file as you do not need it any more
            $this->file = NULL;
        }
        //check if we have an old image
        if ($this->temp) {
            //try to delete the old image
            @unlink($this->getUploadRootDir() . '/' . $this->temp);
            //clear the temp image
            $this->temp = NULL;
        }
        if (NULL !== $this->SWF) {
            // you must throw an exception here if the file cannot be moved
            // so that the entity is not persisted to the database
            // which the UploadedFile move() method does
            $this->SWF->move($this->getSWFUploadRootDir(), $this->fileName);
            //remove the file as you do not need it any more
            $this->SWF = NULL;
        }
        //check if we have an old file
        if ($this->SWFTemp) {
            //try to delete the old file
            @unlink($this->getSWFUploadRootDir() . '/' . $this->SWFTemp);
            //clear the temp image
            $this->SWFTemp = NULL;
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function postRemove() {
        //check if we have an image
        if ($this->image) {
            //try to delete the image
            @unlink($this->getAbsolutePath());
        }
        //check if we have a file
        if ($this->fileName) {
            //try to delete the file
            @unlink($this->getFileAbsolutePath());
        }
    }

    /**
     * @return string the path of image starting of root
     */
    public function getAbsolutePath() {
        return $this->getUploadRootDir() . '/' . $this->image;
    }

    /**
     * @return string the relative path of image starting from web directory
     */
    public function getWebPath() {
        return NULL === $this->image ? NULL : $this->getUploadDir() . '/' . $this->image;
    }

    /**
     * @return string the path of upload directory starting of root
     */
    public function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * @param $width the desired image width
     * @param $height the desired image height
     * @return string the htaccess file url pattern which map to timthumb url
     */
    public function getSmallImageUrl($width = 50, $height = 50) {
        return NULL === $this->image ? NULL : "banner-image/$width/$height/$this->image";
    }

    /**
     * @return string the document upload directory path starting from web folder
     */
    private function getUploadDir() {
        return 'uploads/banners-images';
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * Set SWF
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return $this
     */
    public function setSWF($file) {
        $this->SWF = $file;
        //check if we have an old file
        if ($this->fileName) {
            //store the old name to delete on the update
            $this->temp = $this->fileName;
            $this->fileName = NULL;
        } else {
            $this->fileName = 'initial';
        }
        return $this;
    }

    /**
     * Get SWF
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getSWF() {
        return $this->SWF;
    }

    /**
     * this function is used to delete the current file
     */
    public function removeFile() {
        //check if we have an old file
        if ($this->fileName) {
            //store the old name to delete on the update
            $this->SWFTemp = $this->fileName;
            //delete the current file
            $this->setFileName(NULL);
        }
    }

    /**
     * @return string the path of file starting of root
     */
    public function getSWFAbsolutePath() {
        return $this->getUploadRootDir() . '/' . $this->fileName;
    }

    /**
     * @return string the relative path of file starting from web directory
     */
    public function getSWFFileWebPath() {
        return NULL === $this->fileName ? NULL : $this->getSWFUploadDir() . '/' . $this->fileName;
    }

    /**
     * @return string the path of upload directory starting of root
     */
    public function getSWFUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getSWFUploadDir();
    }

    /**
     * @return string the document upload directory path starting from web folder
     */
    private function getSWFUploadDir() {
        return 'uploads/banners';
    }

    public function __toString() {
        return "Banner $this->id";
    }

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return array
     */
    public function getValidPositions() {
        return array(
            'top' => 'top'
        );
    }

    /**
     * this function will check if no url with image
     * @param \Symfony\Component\Validator\ExecutionContext $context
     */
    public function isImageCorrect(ExecutionContext $context) {
        if ($this->image && !$this->url) {
            $context->addViolationAt('url', 'You must add url for the image.');
        }
    }

    /**
     * this function will check if no url with flash
     * @param \Symfony\Component\Validator\ExecutionContext $context
     */
    public function isFlashCorrect(ExecutionContext $context) {
        if ($this->fileName && !$this->url) {
            $context->addViolationAt('url', 'You must add url for the flash.');
        }
    }

    /**
     * this function will check if no code, flash or image provided
     * @param \Symfony\Component\Validator\ExecutionContext $context
     */
    public function isBannerCorrect(ExecutionContext $context) {
        if (!$this->fileName && !$this->image && !$this->code) {
            $context->addViolation('You must add image, flash or code.');
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Banner
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Banner
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Banner
     */
    public function setPosition($position) {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Banner
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set numberOfClicks
     *
     * @param integer $numberOfClicks
     * @return Banner
     */
    public function setNumberOfClicks($numberOfClicks) {
        $this->numberOfClicks = $numberOfClicks;

        return $this;
    }

    /**
     * Get numberOfClicks
     *
     * @return integer
     */
    public function getNumberOfClicks() {
        return $this->numberOfClicks;
    }

    /**
     * Set numberOfViews
     *
     * @param integer $numberOfViews
     * @return Banner
     */
    public function setNumberOfViews($numberOfViews) {
        $this->numberOfViews = $numberOfViews;

        return $this;
    }

    /**
     * Get numberOfViews
     *
     * @return integer
     */
    public function getNumberOfViews() {
        return $this->numberOfViews;
    }

}