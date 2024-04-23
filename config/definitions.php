<?php

use App\Database;

return [
    Database::class => function(){
        return new Database(host: '127.0.0.1',
                            dbname: 'mijnapi',
                            user: 'Student',
                            password: 'shoarma');
    }
];