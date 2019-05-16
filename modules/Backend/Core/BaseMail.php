<?php

namespace Modules\Backend\Core;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function build()
    {
        $params = $this->params;
        $data = $params['data'];
        return $this->subject($params['subject'])
            ->view($params['template'], compact('data'));
    }
}