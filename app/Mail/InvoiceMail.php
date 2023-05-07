<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
        $pdf = PDF::loadView($this->data['page'], $this->data)->setPaper('a4'); 

        return $this->view($this->data['page'])
            ->to($this->data['email'])
            ->subject($this->data['subject'])
            ->attachData($pdf->output(),$this->data['filename'])
            ->with([
                'email_data' => $this->data
            ]);
    }
}
