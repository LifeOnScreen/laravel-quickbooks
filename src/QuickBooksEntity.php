<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * Class QuickBooksEntity
 * @package LifeOnScreen\LaravelQuickBooks
 * @property string $quickbooks_id
 */
abstract class QuickBooksEntity extends Model implements QuickBookable
{
    /**
     * Database column name
     * @var string
     */
    protected $quickBooksIdColumn = 'quickbooks_id';

    /**
     * LifeOnScreen\LaravelQuickBooks\QuickBooksFacades constant
     * @var array
     */
    protected $quickBooksResource;

    /**
     * @var QuickBooksConnection
     */
    protected $quickBooksConnection;

    /**
     * QuickBooksEntity constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->quickBooksConnection = App::make(QuickBooksConnection::class);
        parent::__construct($attributes);
    }

    abstract protected function getQuickBooksArray(): array;

    /**
     * @return null|string
     */
    public function getQuickBooksIdAttribute(): ?string
    {
        if (isset($this->attributes[$this->quickBooksIdColumn])) {
            return $this->attributes[$this->quickBooksIdColumn];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getQuickBooksResourceNameAttribute()
    {
        return $this->quickBooksResource['name'];
    }

    /**
     * @return string
     */
    public function getQuickBooksResourceFacadeAttribute()
    {
        return $this->quickBooksResource['facade'];
    }

    /**
     * @return array
     */
    public function getQuickBooksResourceAttribute()
    {
        return $this->{$this->quickBooksResource};
    }

    /**
     * @return null|\QuickBooksOnline\API\Core\HttpClients\FaultHandler
     */
    public function getLastQuickBooksError()
    {
        return $this->quickBooksConnection->getDataService()->getLastError();
    }

    /**
     * @param array $insertArray
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    protected function insertToQuickBooks(array $insertArray): bool
    {
        $quickBooksObject = $this->quickBooksResourceFacade::create($insertArray);

        $data = $this->quickBooksConnection->getDataService()->Add($quickBooksObject);

        if ($this->quickBooksConnection->getDataService()->getLastError()) {
            return false;
        }

        $this->quickbooks_id = $data->Id;
        $this->save();

        return true;
    }

    /**
     * @param array $updateArray
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     * @throws \Exception
     */
    protected function updateToQuickBooks(array $updateArray): bool
    {
        $updateArray['Id'] = $this->quickbooks_id;
        $entities = $this->quickBooksConnection->getDataService()->Query(
            "select * from {$this->quickBooksResourceName} where Id='{$updateArray['Id']}'"
        );

        if (!empty($entities) && sizeof($entities) == 1) {
            $quickBooksObject = $this->quickBooksResourceFacade::update(current($entities), $updateArray);
            $this->quickBooksConnection->getDataService()->Add($quickBooksObject);
        } else {
            throw new Exception("{$this->quickBooksResourceName} was not found.");
        }

        if ($this->quickBooksConnection->getDataService()->getLastError()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function syncToQuickbooks(): bool
    {
        $syncArray = $this->getQuickBooksArray();
        if (empty($this->quickbooks_id)) {
            return $this->insertToQuickBooks($syncArray);
        }

        return $this->updateToQuickBooks($syncArray);
    }
}