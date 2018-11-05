<?php

namespace LifeOnScreen\LaravelQuickBooks;

trait SyncsToQuickBooks
{
    /**
     * @var QuickBooksResource
     */
    protected $quickBooksResourceInstance;

    /**
     * The data to sync to QuickBooks.
     *
     * @see https://developer.intuit.com/docs/api/accounting
     * @return array
     */
    abstract protected function getQuickBooksArray(): array;

    /**
     * Allows you to use `$model->quickbooks_id` regardless of the actual column being used.
     *
     * @return null|string
     */
    public function getQuickBooksIdAttribute(): ?string
    {
        $quickBooksIdColumn = $this->quickBooksIdColumn ?? 'quickbooks_id';

        if (isset($this->attributes[$quickBooksIdColumn])) {
            return $this->attributes[$quickBooksIdColumn];
        }

        return null;
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
     *
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function insertToQuickBooks(): bool
    {
        $attributes = $this->getQuickBooksArray();
        $resourceId = $this->getQuickBooksResourceInstance()->create($attributes);

        if (!$resourceId) {
            return false;
        }

        $this->quickbooks_id = $resourceId;
        $this->save();

        return true;
    }

    /**
     * Updates the model in QuickBooks.
     *
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     * @throws \Exception
     */
    public function updateToQuickBooks(): bool
    {
        if (empty($this->quickbooks_id)) {
            return false;
        }

        $attributes = $this->getQuickBooksArray();

        return $this->getQuickBooksResourceInstance()->update($this->quickbooks_id, $attributes);
    }

    /**
     * Syncs the model to QuickBooks.
     *
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function syncToQuickBooks(): bool
    {
        if (empty($this->quickbooks_id)) {
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
}
