<?php
namespace App\Repositories\Contracts;

interface DiscountRepositoryInterface
{
    public function getAll(array $filters = []);        

    public function findById($id);               

    public function create(array $data);               

    public function update($id, array $data);      

    public function delete($id);
}