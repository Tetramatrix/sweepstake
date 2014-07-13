<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2014 Chi Hoang <info@chihoang.de>
*  All rights reserved
*
***************************************************************/

define("DAY", 1*86400);
define("TEST", 54*86400);
define("SHIFT", 54*86400);
define("ETA30VEVT", 30*86400);
date_default_timezone_set('Europe/Berlin');    

class sweepstake {

    public $fastforward = 0;
    
    public $end =     array ( "0" => "+3 day",
                              "1" => "+6 day",
                              "2" => "-1 day",
                              "null" => ""
                            );
    
    public $day     = array ( "0" => "Mon",
                              "1" => "Tue",
                              "2" => "Wed",
                              "3" => "Thu",
                              "4" => "Fri",
                              "5" => "Sat",
                              "6" => "Sun"
                             );

    public $week = array (  "Mon" => "Mon",
                            "Tue" => "Tue",
                            "Wed" => "Wed",
                            "Thu" => "Thu",
                            "Fri" => "Fri",
                            "Sat" => "Sat",
                            "Sun" => "Sun",
                            "Mon" => "Mon",
                            "Tue" => "Tue",
                            "Wed" => "Wed",
                            "Thu" => "Thu",
                            "Fri" => "Fri",
                            "Sat" => "Sat",
                            "Sun" => "Sun",             
                            );

    public $table;
    
    function overriden_mktime($time)
    {
        if ($time == "today")
        {
            $time = mktime(0,0,0);
            //$time = mktime(0,0,0,2,1,2013);
        }
        return $time+$this->fastforward;
    }
    
    function overriden_date($format,$time=0) 
    {
        if ($time===0)
        {
            $time=$this->overriden_mktime("today");
        }
        return date($format, $time); 
    }
    
    function overriden_strtotime($time) 
    {
        $z = new DateTimeZone("Europe/Berlin");
        $d = new DateTime();
        $d->setTimestamp($this->overriden_mktime("today"));
        //$d = new DateTime($time, $z);
        $d->modify($time);
        $timestamp = $d->format('U');
        return $timestamp;    
    }

    function createMonth($arr, $number)
    {
        if ( $number == 1 && $this->overriden_date("n") != 1)
        {
            $number = 12;
        } elseif ( $number == 12 && $this->overriden_date("n") == 1)
        {
            $number = 1;
        } elseif ( $number == 13 && $this->overriden_date("n") != 1)
        {
            $number = 1;
        }
        return  $arr [ "title" ] . " Month:" . $number;
    }
                                
    function createWeek($arr, $number)
    {
        if ( $number > 52 )
        {
            $number = 1;
        }
        return $arr [ "title" ] . " Week:" . $number;
    }
    
    function convertMonth($arr, $number)
    {
        if ( $number == 1 && $this->overriden_date("n") != 1)
        {
            $number = 12;
        } elseif ( $number == 12 && $this->overriden_date("n") == 1)
        {
            $number = 1;
        } elseif ( $number == 13 && $this->overriden_date("n") != 1)
        {
            $number = 1;
        }
        
        return true;
    }
    
    function convertWeek($arr, $number)
    {
        if ( $number > 52 )
        {
            $number = 1;
        }                            
        return true;        
    }
    
        // return timestamp of last Monday...Friday
    function last_dayofweek($uid, $compare, $day, $end, $week=0)
    {
        $e = $this->end[$end];
        $d = $this->day[$day];
        
        if (date("D", $this->overriden_mktime("today") ) == $d)
        {
            $s = $this->overriden_strtotime("midnight {$e}");
        } else
        {
            $s = $this->overriden_strtotime("last {$d} {$e} {$week}");
        }
 
        foreach ($this->table as $title=>$row)  
        { 
            if ($uid==$row["uid"]) 
            {
                foreach ($row["table"] as $ititle=>$irow) 
                {
                    if ( date("W", $irow["SD"]) == date("W", $this->overriden_mktime("today") ) )
                    {
                        $day = date("D", $irow["SD"]);
                        if (date("D", $this->overriden_mktime("today") ) == $day)
                        {
                            $s = $this->overriden_strtotime("{$day} {$e} {$week}");
                        } else
                        {
                            $s = $this->overriden_strtotime("last {$day} {$e} {$week}");
                        }
                    }
                }
            }
        }     
        if ( $s > $compare )
        {
            $bool = true;    
        } else
        {
            $bool = false;
        }
        return $bool;                                 
    }
    
    /**
    * int nth_day_of_month(int $nbr, str $day, int $mon, int $year)
    *   $nbr = nth weekday to find
    *   $day = full name of weekday, e.g. "Saturday"
    *   $mon = month 1 - 12
    *   $year = year 1970, 2007, etc.
    * returns UNIX time
    */
    function nth_day_of_month($nbr, $day, $mon, $year)
    {
        $date = mktime(0, 0, 0, $mon, 0, $year);
        if($date == 0)
        {
           user_error(__FUNCTION__."(): Invalid month or year", E_USER_WARNING);
           return(FALSE);
        }
        $day = ucfirst(strtolower($day));
        if(!in_array($day, array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat')))
        {
           user_error(__FUNCTION__."(): Invalid day", E_USER_WARNING);
           return(FALSE);
        }
        for($week = 1; $week <= $nbr; $week++)
        {
           $date = strtotime("next $day", $date);
        }
        //return($date);
        return date("d.m.Y", $date);
    }
    
    function getFirstDayMonth ($uid, $compare, $day, $y, $m, $end, $month=0)
    {
        $e = $this->end[$end];
        $d = $this->day[$day];
        
        if ((int)$m > 12)
        {
            $m = (int)1;
            $y = (int)$y+1;    
        }

        $date = $this->nth_day_of_month (3, $d, $m, $y);
        $s = $this->overriden_strtotime($date." {$e} {$month}");            
        $monthNumber1 = $this->overriden_date("n", $s);
        
        foreach ($this->table as $title=>$row)  
        { 
            if ($uid==$row["uid"]) 
            {
                foreach ($row["table"] as $ititle=>$irow) 
                {
                    $monthNumber2 = date("n", $irow["SD"]);
                    
                    if ($monthNumber1 == $monthNumber2)
                    {
                        $s = $irow["SD"]+$this->fastforward;
                    }
                }
            }
        }
        if ( $s > $compare )
        {
            $bool = true;    
        } else
        {
            $bool = false;
        }
        return $bool;
    }
    
    function main($input,$time=7)
    {      
        $this->table=$input;
        $z=array();

        $c = "Timestamp: ".mktime(0,0,0)."<br>";
        $c .= "Time:". date("h:m d.m.Y", mktime(0,0,0))."<br>";

        for ( $loop = 1; $loop <= $time; $loop++)
        {                                    
            foreach ($this->table as $title=>$row)
            {
                    // ist zeit über folgeheft (nächste woche) evt (ohne end)?
                    // die end ist für das Ende der Gewinnspielperiode, die andere Hälfte
                if ( $row["cycle"] === "0" )
                {
                        // Heftnr ist gleich KW!!!
                        // ist heute kleiner evt bzw. wie lange ist es noch bis evt?
                    if ( $this->overriden_strtotime("today") >= $this->overriden_strtotime("monday this week")            
                        &&
                        $this->last_dayofweek(
                            $row["uid"],
                            $this->overriden_mktime("today"),
                            $row["day"],
                           "null",
                            ""
                            ) == false
                        )
                    {
                        if (                                     
                            $this->last_dayofweek(
                                $row["uid"],
                                $this->overriden_mktime("today"),
                                $row["day"],
                               "null",
                                "+1 Week"
                                ) == false )    
                        {
                            // heute ist gleich oder über evt, also KW+1
                            $weeknumber = (int)$this->overriden_date("W")+1;
                                                    
                        } else
                        {
                            if ( ( $this->last_dayofweek(
                                $row["uid"],
                                $this->overriden_strtotime("monday this week"), 
                                $row["day"],
                                "null",
                                ""
                                ) == true )
                                &&
                                ( $this->last_dayofweek(
                                $row["uid"],
                                $this->overriden_mktime("today"),
                                $row["day"],
                                "null",
                                ""
                                ) == false )
                            )
                            {
                                $weeknumber = (int)$this->overriden_date("W")+1;
                                
                            } else
                            {
                                // Woche nach Montag inkl. aber vor EVT
                                $weeknumber = (int)$this->overriden_date("W");
                            }
                        }

                    } else
                    {    
                        $weeknumber = (int)$this->overriden_date("W")+1;
                    }
                                
                    if ( $this->convertWeek($row, $weeknumber) )
                    {
                        $t [ ] = $this->createWeek($row, $weeknumber);            
                    } 
                    
                } elseif ( $row [ "cycle"] === "1" )
                {
                    // dieser monat
                    $monthnumber = (int)$this->overriden_date("m");

                    if ( $this->getFirstDayMonth(
                        $row["uid"],
                        $this->overriden_mktime("today"),
                        $row["day"],
                        $this->overriden_date("Y"),
                        $monthnumber,
                        "null",
                        ""
                        ) == true )
                    {
                        // Heftnr ist gleich KW!!!
                        // ist zeit unter monatsbeginn der nÃ¤chsten monat und Ã¼ber evt?
                            
                        // ist gleich oder über evt?
                        if ( $this->overriden_date("m") == $monthnumber
                             &&
                             $this->getFirstDayMonth(
                                $row["uid"],
                                $this->overriden_mktime("today"),
                                $row["day"],
                                $this->overriden_date("Y"),
                                $monthnumber,
                                "null",
                                ""
                                ) == true )
                        {
                            $monthnumber = (int)$this->overriden_date("m");
                        } else
                        {
                            $monthnumber = (int)$this->overriden_date("m")+1;
                        }
                    } else
                    {
                        $monthnumber = (int)$this->overriden_date("m")+1;
                    }
                    
                    if ($this->convertMonth($row, $monthnumber)
                        &&
                        $this->getFirstDayMonth(
                                $row["uid"],
                                $this->overriden_mktime("today"),
                                $row["day"],
                                $this->overriden_date("Y"),
                                $monthnumber,
                                $row["end"],
                                ""
                                ) == true
                        ) 
                    {
                        $t [ ] = $this->createMonth($row, $monthnumber);
                    }
                }
                
                if ( $row [ "cycle"] === "0" )
                {
                        // Heftnr ist gleich KW!!!
                        // ist heute kleiner evt bzw. wie lange ist es noch bis evt?
                    if ( $this->last_dayofweek(
                            $row["uid"],
                            $this->overriden_mktime("today"),
                            $row["day"],
                            $row["end"],
                            "+1 week"
                            ) == true )
                    {
                        // heute ist gleich oder über evt, also KW+1
                        if ( $this->last_dayofweek(
                            $row["uid"],
                            $this->overriden_strtotime("monday this week"),
                            $row["day"],
                            "null",
                            ""
                            ) == true
                        )
                        {
                            // evt liegt noch in der Zukunft
                            $weeknumber = (int)$this->overriden_date("W");
                            
                        } else
                        {
                            if ($this->overriden_mktime("today") >= $this->overriden_strtotime("monday this week") )
                            {
                                if ( $this->last_dayofweek(
                                    $row["uid"],
                                    $this->overriden_mktime("today"),
                                    $row["day"],
                                    "null",
                                    ""
                                    ) == true )
                                {
                                    $weeknumber = (int)$this->overriden_date("W");
                                }    else if ($this->overriden_mktime("today") >= $this->overriden_strtotime("monday this week") )
                                {
                                    $weeknumber = (int)$this->overriden_date("W")-1;
                                }    else
                                {
                                    // evt liegt noch in der Zukunft
                                    $weeknumber = (int)$this->overriden_date("W");
                                }
                                
                            } else
                            {
                                    // evt liegt noch in der Zukunft
                                $weeknumber = (int)$this->overriden_date("W");    
                            }
                        }
                        
                    } else
                    {
                        if ($this->overriden_mktime("today") >= $this->overriden_strtotime("monday this week")
                            &&
                            $this->last_dayofweek(
                                    $row["uid"],
                                    $this->overriden_mktime("today"),
                                    $row["day"],
                                    $row["end"],
                                    ""
                                ) == false
                            )
                        {
                            if ( $this->last_dayofweek(
                                        $row["uid"],
                                        $this->overriden_strtotime("monday this week"),
                                        $row["day"],
                                        "null",
                                        ""
                                        ) == true
                            )
                            {
                                // evt liegt noch in der Zukunft
                                $weeknumber = (int)$this->overriden_date("W");    
                            } else
                            {
                                // evt liegt noch in der Zukunft
                                $weeknumber = (int)$this->overriden_date("W")-1;    
                            }
                            
                        } else
                        {
                            if ($this->overriden_mktime("today") >= $this->overriden_strtotime("monday this week")
                                &&
                                $this->last_dayofweek(
                                        $row["uid"],
                                        $this->overriden_mktime("today"),
                                        $row["day"],
                                        $row["end"],
                                        ""
                                    ) == false
                                )
                            {
                                if ( $this->last_dayofweek(
                                            $row["uid"],
                                            $this->overriden_strtotime("this week mon"),
                                            $row["day"],
                                            "null",
                                            ""
                                            ) == true
                                )
                                {
                                    // evt liegt noch in der Zukunft
                                    $weeknumber = (int)$this->overriden_date("W");    
                                } else
                                {
                                    // evt liegt noch in der Zukunft
                                    $weeknumber = (int)$this->overriden_date("W")-1;    
                                }
                            }
                        }
                    }
                    
                    if ($this->convertWeek($row, $weeknumber)
                        &&
                        $this->last_dayofweek(
                                $row["uid"],
                                $this->overriden_mktime("today"),
                                $row["day"],
                                $row["end"],
                                ""
                              ) == true
                        )
                       {
                            if ( $weeknumber == 53 )
                            {
                                $weeknumber = 52;
                            } elseif ( $weeknumber == 54)
                            {
                                $weeknumber = 1;
                            } elseif ( $weeknumber == 52 && $this->overriden_date("W") == 2)
                            {
                                $weeknumber = 2;   
                            }
                           $z [ ] = $row [ "title" ] . " Week:" .$weeknumber; 
                       }
                    } elseif ($row ["cycle"] === "1")
                    {
                        $monthnumber = (int)$this->overriden_date("m");
                        
                        if ( $this->getFirstDayMonth(
                                    $row["uid"],
                                    $this->overriden_mktime("today"),
                                    $row["day"],
                                    $this->overriden_date("Y"),
                                    $monthnumber+1,
                                    $row["end"],
                                    ""
                               ) == true
                            )
                        {
                                // ist gleich oder Ã¼ber evt?
                            if ( $this->getFirstDayMonth(
                                    $row["uid"],
                                    $this->overriden_mktime("today"),
                                    $row["day"],
                                    $this->overriden_date("Y"),
                                    $monthnumber,
                                    "null",
                                    ""
                                    ) == true
                                )
                            {
                                $monthnumber = (int)$this->overriden_date("m")-1;
                            } else
                            {
                                $monthnumber = (int)$this->overriden_date("m");
                            }
                            
                            if ( $this->convertMonth($row, $monthnumber)
                                 &&
                                 $this->getFirstDayMonth(
                                    $row["uid"],
                                    $this->overriden_mktime("today"),
                                    $row["day"],
                                    $this->overriden_date("Y"),
                                    $monthnumber,
                                    "null",
                                    ""
                                    ) == true                                    
                                )
                            {
                                $z [ ] = $row [ "title" ] . " Month:" .$monthnumber; 
                            }    
                        }
                    }
                }
        
                $s=$m=$m1=$m2=$m3=array();
                $m1 ["###CONID###"] = $m ["###BRICKID###"] = $loop;
                $m2 ["###TITELBILD###"] = is_array ( $t ) ? implode ( '|', $t ) : 'Please wait, something bad happens.';
                $m3 ["###BRICK###"] = is_array ( $z ) ? implode ( '|', $z ) : '';
                $t=$z=array();

                //$c .= date("h:i d-m-y", $this->overriden_mktime("today"));
                $c .= "<br>Day $loop: ". date("d-m-y", $this->overriden_mktime("today"));
                $c .= "<br>New:".implode (";", $m2 )."<br>Old:".implode (";", $m3 )."<br>";         
                //$c .= "<br>".date("h:i d-m-y", $this->overriden_strtotime("this week mon") );        
                $this->fastforward += DAY;    
            }
            return $c;
        }
}
?>