<?php

/* 
 * Copyright (C) 2018 Julia Clement <Julia at Clement dot nz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Area {
    var $Name;
    var $HasOpenGigs = false;
    var $HasProGigs = false;
    var $HasBothGigs = false;
    var $Show = false;
    var $Gigs = [];
    public function __construct($name) {
        $this->Name = $name;
    }
}
define ('CSVFILE',"gigs.csv" );
$filetime = filemtime(CSVFILE);
$age=time() - $filetime;
if($age>300) {
    $ch = curl_init(); 

    // curl options
    curl_setopt($ch, CURLOPT_URL, 'https://docs.google.com/spreadsheets/d/1-W8XLakqZ3Zlt7tTPfOJ5Gzy7-0Vdxv49FC7PcKHd8I/export?exportFormat=csv'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //return the transfer as a string 

    // Get & save the file
    $csvdata = curl_exec($ch);
    file_put_contents(CSVFILE, $csvdata);

    curl_close($ch);
    $lines = preg_split('/\R/', $csvdata );
    $updateTime="just now";
} else {
    $lines = file(CSVFILE);
    $seconds = $age % 60;
    $minutes = ($age-$seconds) / 60;
    $updateTime = sprintf("%d:%02d minutes ago", $minutes, $seconds);
}
$header = str_getcsv(array_shift($lines));
$columns = count($header);
$fields=str_replace( [' ','-'], ['','_'], $header);
$gigs=[];
$rawAreas=[];
$gigsByAreaIn=[];
$allLines=array_map('str_getcsv', $lines );
foreach( array_map('str_getcsv', $lines ) as $line ) {
    $gig = new stdClass();
    for( $i = 0; $i < $columns; ++$i ) {
        $field = $fields[$i];
        $gig->{$field} = $line[$i];
    }
    // Didn't collect this information initially, extract it from a
    // controlled text field
    $gig->Open = stripos( $gig->Performers, "open" ) !== false;
    $gig->Pro = stripos( $gig->Performers, "pro" ) !== false;
    $gig->Both = $gig->Open && $gig->Pro;
    $gigs[]=$gig;
    $area = $gig->Area;
    $rawAreas[$area]=$area;
    if( !array_key_exists( $area, $gigsByAreaIn)) {
        $gigsByAreaIn[$area]=[];
    }
    $gigsByAreaIn[$area][]=$gig;
}
$areas = [];
$areasByName = [];
foreach( $rawAreas as $area ) {
    $areaObject = new Area($area);
    $areas[] = $areaObject;
    $areasByName[ $area ] = $areaObject;
}
usort($areas, function($a,$b) {
        return $a->Name <=> $b->Name;
    });
$gigsByArea=[];
foreach( $gigsByAreaIn as $areaName => $gigsOneArea ) {
    usort($gigsOneArea, function($a,$b) {
        return $a->GigName <=> $b->GigName;
    });
    $area = $areasByName[$areaName];
    $area->AllGigs = $gigsOneArea;
    foreach( $gigsOneArea as $gig ) {
        $area->HasOpenGigs |= $gig->Open;
        $area->HasProGigs |= $gig->Pro;
        $area->HasBothGigs |= $gig->Both;
    }
    $gigsByArea[$area->Name] = $gigsOneArea;
}
