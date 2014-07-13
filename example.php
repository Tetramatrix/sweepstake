<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2014 Chi Hoang <info@chihoang.de>
*  All rights reserved
*
***************************************************************/  
require ("main.php");

//title = sweepstake title
//uid = unique ID
//day = day of the week
//cycle = weekly or monthly cycle
//end = duration of sweepstake 
//table = exceptions

$table = array( 
                array(
                    "title" => "Sweepstake 1",
                    "uid" => "1",
                    "day" => "2",
                    "cycle" => "0",
                    "end" => "0",
                    "table" =>  array(array
                                (
                                    "ST" => "18 Dezember",
                                    "SD" => 1355785200,
                                    "SUID" => "4",
                                ),
                                array
                                (
                                    "ST" => "24 Dezember",
                                    "SD" => 1356303600,
                                    "SUID" => "5",
                                ),
                                array
                                (
                                    "ST" => "2 Oktober",
                                    "SD" => 1349128800,
                                    "SUID" => "6"
                                )
                          )
                     ),
           array(    
                 "title" => "Sweepstake 2",
                "uid" => "2",
                "day" => "2",
                "cycle" => "0",
                "end" => "0",
                "table" =>  array(array
                            (
                                "ST" => "18 Dezember",
                                "SD" => 1355785200,
                                "SUID" => "4",
                            ),
                            array
                            (
                                "ST" => "24 Dezember",
                                "SD" => 1356303600,
                                "SUID" => "5",
                            ),
                            array
                            (
                                "ST" => "2 Oktober",
                                "SD" => 1349128800,
                                "SUID" => "6"
                            )
                      )
                 ),
            array(      
                "title" => "Sweepstake 3",
                "uid" => "3",
                "day" => "2",
                "cycle" => "0",
                "end" => "1",
                "table" =>  array(array
                    (
                        "ST" => "18 Dezember",
                        "SD" => 1355785200,
                        "SUID" => "4",
                    ),
                    array
                    (
                        "ST" => "24 Dezember",
                        "SD" => 1356303600,
                        "SUID" => "5",
                    ),
                    array
                    (
                        "ST" => "2 Oktober",
                        "SD" => 1349128800,
                        "SUID" => "6"
                    )
                    )
                 ),
                array(
                       "title" => "Sweepstake 4",
                        "uid" => "4",
                        "day" => "2",
                        "cycle" => "0",
                        "end" => "1",
                        "table" =>  array(array
                            (
                                "ST" => "18 Dezember",
                                "SD" => 1355785200,
                                "SUID" => "4",
                            ),
                            array
                            (
                                "ST" => "24 Dezember",
                                "SD" => 1356303600,
                                "SUID" => "5",
                            ),
                            array
                            (
                                "ST" => "2 Oktober",
                                "SD" => 1349128800,
                                "SUID" => "6"
                            )
                        )
                 ),
               array( 
                "title" => "Sweepstake 5",
                "uid" => "5",
                "day" => "2",
                "cycle" => "0",
                "end" => "0",
                "table" =>  array(array
                            (
                                "ST" => "18 Dezember",
                                "SD" => 1355785200,
                                "SUID" => 4,
                            ),
                            array
                            (
                                "ST" => "24 Dezember",
                                "SD" => 1356303600,
                                "SUID" => 5,
                            ),
                            array
                            (
                                "ST" => "2 Oktober",
                                "SD" => 1349128800,
                                "SUID" => "6"
                            )
                       )
                 ),
           array(               
                "title" => "Sweepstake 6",
                "uid" => "6",
                "day" => "2",
                "cycle" => "1",
                "end" => "2",
                "table" => array(
                        array
                        (
                            "ST" => "2 Oktober",
                            "SD" => 1349128800,
                            "SUID" => "6"
                        )
                 )
           )
        );
        
        
$s = new sweepstake();
$result=$s->main($table,31);
echo $result;
?>