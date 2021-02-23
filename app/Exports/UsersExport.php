<?php

namespace App\Exports;
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UsersExport implements FromView
{
	private $gender;
    private $status;
    private $from;
    private $to;
   
    public function __construct($gender,$status,$from,$to)
    {
        $this->gender = $gender;
        $this->status = $status;
        $this->from   = $from;
        $this->to     = $to;
    }

    public function view(): View
    {
        if($this->gender ==11 && $this->status ==11)
        {
            $data=User::with(['posts'])->whereBetween('created_at',[$this->from,$this->to])->get();
        }
        elseif(($this->gender ==1 || $this->gender ==2) && $this->status ==11){
           
             $data=User::whereGender($this->gender)->whereBetween('created_at',[$this->from,$this->to])->get();

        }elseif(($this->status ==0 || $this->status ==1) && $this->gender ==11){

             $data=User::whereActive($this->status)->whereBetween('created_at',[$this->from,$this->to])->get();

        }elseif($this->gender !=11 && $this->status !=11){

            $data=User::whereGender($this->gender)->whereActive($this->status)->whereBetween('created_at',[$this->from,$this->to])->get();
        }

        return view('private.export.user', [
            'record' => $data
        ]);
    }
}
