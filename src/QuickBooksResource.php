<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use QuickBooksOnline\API\Core\HttpClients\FaultHandler;

class QuickBooksResource
{
    /**
     * @var QuickBooksConnection
     */
    protected $connection;

    /**
     * Array from the QuickBookResources class
     *
     * @var array
     */
    protected $facade;

    /**
     * The name of this resource.
     *
     * @var string
     */
    protected $name;

    /**
     * @var FaultHandler
     */
    protected $error;

    /**
     * QuickBooksRequest constructor.
     *
     * @param array $resource Resource constant from QuickBooksResources
     */
    public function __construct(array $resource = null)
    {
        if (!isset($this->facade)) {
            if (empty($resource['facade']) || empty($resource['name'])) {
                throw new \Exception('The provided QuickBooks resource is invalid.');
            }

            $this->facade = $resource['facade'];
            $this->name   = $resource['name'];
        }

        $this->connection = App::make(QuickBooksConnection::class);
    }

    /**
     * Create a resource
     *
     * @param array $attributes
     * @return bool|int
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function create($attributes)
    {
        $object = $this->getResourceFacade()::create($attributes);

        if (!$response = $this->request('Add', $object)) {
            return false;
        }

        return (int) $response->Id;
    }

    /**
     * Update a resource.
     *
     * @param $where
     * @param $attributes
     */
    public function update($id, $attributes)
    {
        $resource = $this->find($id);

        if (!$resource) {
            throw new \Exception("The requested {$this->getResourceName()} could not be found.");
        }

        $object = $this->getResourceFacade()::update($resource, $attributes);

        if (!$response = $this->request('Update', $object)) {
            return false;
        }

        return $response->Id;
    }

    /**
     * Find a resource by ID
     *
     * @param $id
     * @return \QuickBooksOnline\API\Data\IPPIntuitEntity
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function find($id)
    {
        return $this->request('FindById', $this->getResourceName(), $id);
    }

    /**
     * Find a resource by a key/value pair.
     *
     * @param $key
     * @param $value
     * @return \QuickBooksOnline\API\Data\IPPIntuitEntity
     * @throws \Exception
     */
    public function findBy($key, $value)
    {
        return $this->query([$key => $value], 0, 1)->first();
    }

    /**
     * Query the resources by the provided array of column/value pairs.
     *
     * @param array $where
     * @param int $offset
     * @param int $limit
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function query($where = null, $offset = null, $limit = null)
    {
        $query = 'SELECT * FROM ' . $this->getResourceName();

        if (is_array($where)) {
            $query .= $this->buildWhereString($where);
        }

        return collect($this->request('Query', $query, $offset, $limit));
    }

    /**
     * Delete a resource (not supported by all resources)
     *
     * @param $id
     * @return bool
     * @throws \QuickBooksOnline\API\Exception\IdsException
     */
    public function delete($id)
    {
        $entity = $this->find($id);

        if (!$entity) {
            throw new \Exception("The requested {$this->getResourceName()} could not be found for deleting.");
        }

        if (!$this->request('Delete', $entity)) {
            return false;
        }

        return true;
    }

    /**
     * Make a request using the DataService.
     *
     * @param       $method
     * @param mixed ...$params
     * @return bool
     */
    protected function request($method, ...$params)
    {
        $response = $this->connection->getDataService()->$method(...$params);

        if ($this->error = $this->connection->getDataService()->getLastError()) {
            return false;
        }

        return $response;
    }

    /**
     * Get the class for this resource facade
     *
     * @return string
     */
    protected function getResourceFacade()
    {
        return $this->facade;
    }

    /**
     * Get the name of the QuickBooks resource
     *
     * @return string
     */
    protected function getResourceName()
    {
        return $this->name;
    }

    public function hasError()
    {
        return isset($this->error);
    }

    /**
     * Returns the last error message.
     *
     * @return null|string
     */
    public function getError()
    {
        $response = optional($this->error)->getResponseBody();

        if (!$response) {
            return null;
        }

        $parsedXml = new \SimpleXMLElement($response);

        return (string) $parsedXml->Fault->Error->Message ?? null;
    }

    /**
     * Returns the last error code.
     *
     * @return int
     */
    public function getErrorCode()
    {
        return optional($this->error)->getHttpStatusCode();
    }

    /**
     * Builds a where string from the passed in attributes.
     *
     * @param array $attributes
     */
    protected function buildWhereString($attributes)
    {
        $where  = ' WHERE ';

        $where .= collect($attributes)->map(function ($value, $key) {
            return "$key = '" . addslashes($value) . "'";
        })->implode(' AND ');

        return $where;
    }
}