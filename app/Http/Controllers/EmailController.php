<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\service\BuscaEmail;

class EmailController extends Controller
{

    private $buscaEmail;

    public function __construct()
    {
        $this->buscaEmail = new BuscaEmail();
    }

    public function index(Request $request)
    {
        return $this->buscaEmail->getMail();
    }

    public function enviaEmail(Request $request)
    {
        return [
            'criar email'
        ];
    }


}
