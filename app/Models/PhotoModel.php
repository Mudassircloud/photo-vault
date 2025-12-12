<?php

namespace App\Models;

use CodeIgniter\Model;

class PhotoModel extends Model
{
    protected $table = 'photos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'filename', 'original_name', 'created_at'];
    protected $useTimestamps = true;
}
