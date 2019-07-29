<?php

namespace LifeOnScreen\LaravelQuickBooks;

trait SyncsToQuickBooks
{
    /**
     * @var QuickBooksResource
     */
    protected $quickBooksResourceInstance;

    /**
     * This variable magane if each operation (Insert | Update) return an Object or an Id
     * @var bool
     */
    protected static $returnObject = false;

    /**
     * The data to sync to QuickBooks.
     *
     * @see https://developer.intuit.com/docs/api/accounting
     * @return array
     */
    abstract protected function getQuickBooksArray(): array;

    /**
     * Method to get QuickBooksAttributeIdName
     * @return string
     */
    public function getQuickBooksAttributeIdName()
    {
        return $this->quickBooksIdColumn ?? 'quickbooks_id';
    }

    /**
     * Method to get QuickBooksAttributeSyncTokenName
     * @return string
     */
    public function getQuickBooksAttributeSyncTokenName()
    {
        return $this->quickBooksSyncTokenColumn ?? 'sync_token';
    }

    /**
     * Allows you to use `$model->quickbooks_id` regardless of the actual column being used.
     *
     * @return null|string
     */
    public function getQuickBooksIdAttribute(): ?string
    {
        if (isset($this->attributes[$this->getQuickBooksAttributeIdName()])) {
            return $this->attributes[$this->getQuickBooksAttributeIdName()];
        }
        return null;
    }

    /**
     * Allows you to use `$model->sync_token` regardless of the actual column being used.
     *
     * @return null|int
     */
    public function getQuickBooksSyncTokenAttribute(): ?int
    {
        if (isset($this->attributes[$this->getQuickBooksAttributeSyncTokenName()])) {
            return $this->attributes[$this->getQuickBooksAttributeSyncTokenName()];
        }
        return null;
    }

    /**
     * Allows you to save `$model->quickbooks_id`.
     * @param $value
     * @return boolean
     */
    protected function saveQuickBooksIdAttribute($Id, $SyncToken): bool
    {
        $this->{$this->getQuickBooksAttributeIdName()} = $Id;
        if(static::$returnObject && !is_null($SyncToken)) {
            $this->{$this->getQuickBooksAttributeSyncTokenName()} = $SyncToken;
        }

        return $this->save();
    }

    /**
     * @return null|\QuickBooksOnline\API\Core\HttpClients\FaultHandler
     */
    public function getLastQuickBooksError()
    {
        return $this->getQuickBooksResourceInstance()->getError();
    }

    /**
     * Creates the model in QuickBooks.
     * @return mixed
     * @throws \Exception
     */
    public function insertToQuickBooks()
    {
        $attributes = $this->getQuickBooksArray();
        $resource = $this->getQuickBooksResourceInstance()->create($attributes, static::$returnObject);

        if (!$resource) {
            return false;
        }

        $this->saveQuickBooksIdAttribute($resource->Id, $resource->SyncToken);

        return static::$returnObject && is_object($resource) ? $resource : true;
    }

    /**
     * Updates the model in QuickBooks.
     *
     * @return mixed
     * @throws \QuickBooksOnline\API\Exception\IdsException
     * @throws \Exception
     */
    public function updateToQuickBooks()
    {
        if (empty($this->getQuickBooksIdAttribute())) {
            return false;
        }

        $attributes = $this->getQuickBooksArray();
        $dataSync = $this->getQuickBooksResourceInstance()->update($this->getQuickBooksIdAttribute(), $attributes, static::$returnObject);
        if($dataSync && static::$returnObject){
            $this->saveQuickBooksIdAttribute($dataSync->Id, $dataSync->SyncToken);
        }

        return $dataSync;
    }

    /**
     * Syncs the model to QuickBooks
     * @return bool|mixed
     * @throws \Exception
     */
    public function syncToQuickBooks()
    {
        if (empty($this->getQuickBooksIdAttribute())) {
            return $this->insertToQuickBooks();
        }

        return $this->updateToQuickBooks();
    }

    /**
     * Returns the class name for the QuickBooks resource.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getQuickBooksResource()
    {
        if (empty($this->quickBooksResource)) {
            throw new \Exception('The $quickBooksResource property must be set on the model.');
        }

        return $this->quickBooksResource;
    }

    /**
     * Return an instance of the associated QuickBooks resource.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getQuickBooksResourceInstance()
    {
        if (empty($this->quickBooksResourceInstance)) {
            $this->quickBooksResourceInstance = new $this->quickBooksResource;
        }

        return $this->quickBooksResourceInstance;
    }

    /**
     * Method to get the Resource Name
     * @return mixed
     * @throws \Exception
     */
    public function getResourceName()
    {
        return $this->getQuickBooksResourceInstance()->getResourceName();
    }

    /**
     * Method to get an instance to a synchronize by Batch
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function getQuickBooksInstanceToBatch(array $data = null)
    {
        $facade = $this->getQuickBooksResourceInstance()->getResourceFacade();
        $object = $facade::create($data ?? $this->getQuickBooksArray());
        return $object;
    }
}
