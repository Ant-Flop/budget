<?php

class DB_connect
{
    public $mysql;
    function __construct()
    {
        //$this->mysql = new mysqli("192.168.50.25", "soe_site", "R7nThBnrppHvyR3w", "budget-edit", "3306");
        //$this->mysql = new mysqli("192.168.50.9", "soe_site", "R7nThBnrppHvyR3w", "budget", "3306");
        $this->mysql = mysqli_connect("127.0.0.1", "root", "", "budget") or die("Ошибка " . mysqli_error($this->mysql));
    }

    function Connect()
    {
        return $this->mysql;
    }
}
