<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;
    protected $table = "enquiries";
    protected $primaryKey = "enquiryID";

    protected $fillable = [
        'clientID','step','email','name','message','enquiryDate','phone','address','status','course'
    ];
}
