<?php
namespace ValuFileStorageTest\Service;

use ValuFileStorage\Model\File;

class MongoFileServiceTest extends AbstractServiceTest
{
    protected static $serviceId = 'ValuFileStorageMongoFile';

    protected static $urlScheme = 'mongofs';

    protected static $defaultTarget = 'mongofs:///';

    protected static $urlPattern = '#^mongofs:///[a-zA-Z0-9\-]+/[^/]+$#';

    protected $dm;

    public function setUp()
    {
        parent::setUp();

        $this->serviceBroker->getLoader()->disableService('ValuFileStorageLocalFile');

        $sm = self::$application->getServiceManager();
        $config   = $sm->get('Configuration');
        $this->dm = $sm->get('ValuFileStorageDm');

        if ($this->dm->getConnection()->isConnected()) {
            $this->dm->getConnection()->connect();
        }

        $this->dm->getConnection()->dropDatabase($config['doctrine']['configuration']['odm_default']['default_db']);
        $this->dm->getSchemaManager()->ensureIndexes();
    }

    protected function tearDown()
    {
    	$this->dm->clear();
    	$this->dm = null;

    	parent::tearDown();
    }

    public function testDeleteNonExisting()
    {
        $this->assertFalse(
            $this->service()->delete(static::$urlScheme . ':///does-not-exist')
        );
    }

	public function testInsertRestrictedFile(){

		/**
		 * Set restriction options
		 */
		$this->serviceBroker->getLoader()->load(static::$serviceId)->setOption(
			'blacklist',
			array(
				'hidden' 	=> '/\.[^/]+',
				'exec'		=> '\.(exe|sh|dmg)$'
			)
		);

		$dir = realpath(dirname(__FILE__) . '/../../resources');

		$files = array(
			$dir . '/.htaccess',
			$dir . '/exec/application.exe',
			$dir . '/exec/application.tmp.sh'
		);

		foreach($files as $file){

			$url = 'file://'.$file;

			try{
				$this->service()->insert($url, static::$urlScheme.'://');
			}
			catch (\ValuFileStorage\Service\Exception\RestrictedUrlException $e){
				continue;
			}

			$this->fail('Expected RestrictedUrlException has not been raised');
		}
	}

    /**
     * @expectedException ValuFileStorage\Service\Exception\UnsupportedOperationException
     */
    public function testGetPath()
    {
        $url  = $this->fileUrl('images/lake.jpg');
        $meta = $this->service()->insert($url, static::$defaultTarget);
        $this->service()->getPath($meta['url']);
    }

	/**
	 * @return \ValuFileStorage\Service\MongoFileService
	 */
	protected function service()
	{
	    return $this->serviceBroker->service('FileStorage.File');
	}
}
