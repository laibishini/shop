<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 12:18
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{

    protected $hidden = [
        "delete_time",
            "update_time"
    ];
}