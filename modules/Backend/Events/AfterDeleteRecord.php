<?php

namespace Modules\Backend\Events;

use Illuminate\Queue\SerializesModels;

class AfterDeleteRecord
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data= $data;
    }
}