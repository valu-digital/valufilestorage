<?php
namespace ValuFileStorage\Model;

use Valu\Model\ArrayAdapter\ArrayAdapterTrait;
use Doctrine\MongoDB\GridFSFile;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="valu_file_storage")
 * @ODM\HasLifecycleCallbacks
 */
class File
{
    use ArrayAdapterTrait;

    /**
     * @ODM\Id
     * @var int
     */
    protected $id;

	/**
	 * Physical file location
	 *
	 * @ODM\Field(type="string")
	 * @var string
	 */
	protected $url;

	/**
	 * File
	 *
	 * @ODM\Field(type="file")
	 * @var string
	 */
	protected $file;

	/**
	 * @ODM\Field(type="string")
	 * @var string
	 */
	protected $mimeType;

	/**
	 * @ODM\Field(type="date")
	 * @var DateTime
	 */
	protected $createdAt;

	/**
	 * @ODM\Field(type="date")
	 * @var DateTime
	 */
	protected $modifiedAt;

	/** @ODM\Field */
    private $uploadDate;

	/** @ODM\Field */
    private $length;

	/** @ODM\Field */
    private $chunkSize;

	/** @ODM\Field */
    private $md5;

    /**
     * Default array adapter instance
     *
     * @var \Valu\Model\ArrayAdapter $arrayAdapter
     */
    protected static $defaultArrayAdapter;

	public function __construct($url, array $specs = array()){

		$this->setUrl($url);

		unset($specs['file']);
		unset($specs['bytes']);

		$this->fromArray($specs);
	}

	/**
	 * Return ID for resource
	 *
	 * @return int
	 */
	public function getId(){
	    return $this->id;
	}

	/**
	 * Get physical file location
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	public function getFilename()
	{
	    return $this->file->getFilename();
	}

	/**
	 * Get file
	 *
	 * @return \MongoGridFSFile
	 */
	public function getFile()
	{
		return $this->file;
	}

    /**
     * Retrieve resource handle for reading the file
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->file->getMongoGridFSFile()->getResource();
    }

	/**
	 * Set local file
	 *
	 * @param string $path
	 */
	public function setFile($path)
	{
		if(file_exists($path) && is_file($path) && is_readable($path)){

			$finfo = new \finfo();
			$this->setMimeType($finfo->file($path, FILEINFO_MIME_TYPE));

			$this->file = $path;
		}
		else{
			throw new \Exception('File not found '.$path);
		}
	}

	/**
     * Read file contents
     *
     * @return string
	 */
	public function getBytes()
	{
	    return $this->file->getBytes();
	}

	/**
     * Write to file
     *
     * @param string $bytes
     * @return File
	 */
	public function setBytes($bytes)
	{
	    $finfo = new \finfo();

	    $this->setMimeType($finfo->buffer($bytes, FILEINFO_MIME_TYPE));

	    $path = parse_url($this->getUrl(), PHP_URL_PATH);

	    if (!($this->file instanceof GridFSFile)) {
	        $this->file = new GridFSFile();
	        $this->file->setBytes($bytes);
	    } else {
	        $this->file->setBytes($bytes);
	    }

	    return $this;
	}

	/**
     * Retrieve last modification time
     *
     * @return \DateTime
	 */
	public function getModifiedAt()
	{
	    return $this->modifiedAt;
	}

	/**
	 * Retrieve creation time
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
	    return $this->createdAt;
	}

	/**
	 * Get filesize
	 *
	 * @return int
	 */
	public function getSize()
	{
	    if ($this->file instanceof GridFSFile) {
	        $size = $this->file->getSize();

	        if ($size === null) {
	            $size = filesize($this->file->getFilename());
	        }
	    } else {
	        $size = filesize($this->file);
	    }

		return $size;
	}

	/**
	 * Get mime type
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * Set file mime etpy
	 *
	 * @param string $mimeType
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
	}

	/**
	 * Set physical file location as URL
	 *
	 * @param string $url
	 */
	protected function setUrl($url)
	{
	    $this->url = $url;
	}

	/**
	 * Reset creation timestamp to curren time
	 *
	 * Onche the timestamp is set, it cannot be
	 * changed via this method.
	 *
	 * @ODM\PrePersist
	 */
	public function resetCreationTimestamp()
	{
        $this->createdAt = new \DateTime();
        $this->modifiedAt = $this->createdAt;
	}

	/**
	 * Reset update timestamp to current time
	 *
	 * @ODM\PreUpdate
	 */
	public function resetUpdateTimestamp()
	{
	    $this->modifiedAt = new \DateTime();
	}
}
