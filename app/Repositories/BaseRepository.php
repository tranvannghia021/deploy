<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryInterface
{
    protected $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }



    public function getAll()
    {
        return $this->model->all();
    }


    public function find($id)
    {
        return $this->model->findOrFail($id);
    }


    public function create(array $datas)
    {
        try {
            $model = $this->model->create($datas);
        } catch (\Exception $e) {
            return null;
        }
        return $model;
    }



    public function update($id, array $datas)
    {
        try {
            $model = $this->find($id);
            if ($model) {
                $model = $model->update($datas);
            }
        } catch (\Exception $e) {
            return null;
        }
        return $model;
    }



    public function delete($id)
    {
        try {
            $model = $this->find($id);
            if ($model) {
                return $model->delete();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}