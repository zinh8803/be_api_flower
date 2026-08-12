<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function all(...$args);

    /**
     * Get paginated records.
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find record by ID.
     *
     * @param mixed $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail.
     *
     * @param mixed $id
     * @param array $columns
     * @return Model
     */
    public function findById($id, array $columns = ['*']): Model;

    /**
     * Find record by specific field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findByField(string $field, mixed $value, array $columns = ['*']): ?Model;

    /**
     * Create a new record.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing record.
     *
     * @param mixed $id
     * @param array $attributes
     * @return Model|bool
     */
    public function update($id, array $attributes);

    /**
     * Delete a record by ID.
     *
     * @param mixed $id
     * @return bool|int
     */
    public function delete($id);
}
