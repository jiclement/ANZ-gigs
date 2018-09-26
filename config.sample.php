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

class Config {
    // Source URL to retrieve the data file from
    static $SourceURL = 'https://docs.google.com/spreadsheets/d/1-W8XLakqZ3Zlt7tTPfOJ5Gzy7-0Vdxv49FC7PcKHd8I/export?exportFormat=csv';
    
    // The filename including directory where the data file is stored.
    // Either the file or directory must be writable by the web server
    static $CSVFileName = 'gigs.csv';
    
    // How long to cache the datafile for
    static $CacheFileName = 300;
    
    // Is debugging mode in effect? If so, the javascript is included in-line
    static $Debug = false;
}