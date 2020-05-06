<?php
    require_once "config.php";
    require_once "csvimport.php";
?><!DOCTYPE html>
<!--
Copyright (C) 2018 Julia Clement <Julia at Clement dot nz>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.10/angular-material.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular-sanitize.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular-animate.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular-aria.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.4/angular-messages.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.10/angular-material.min.js"></script>
        <style>
            md-radio-button.my-radio .md-off{
                border-color: black ;
            }
            md-radio-button.md-checked.my-radio .md-on{
                background-color: red;
            }

        </style>
        <title>Regular Comedy Gigs: Aotearoa/New Zealand</title>
        <link rel="icon" type="image/png" href="favicon.png">
    </head>
    <body>
        <div ng-app="GigsMod" ng-controller="GigsCtrl" class="md-padding" ng-cloak>
            <h1>Regular Comedy Gigs: Aotearoa/New Zealand</h1>
            <h2>Options</h2>
            <div layout="row"><b>Format:&nbsp;</b>
            <md-radio-group layout="row" ng-model="Option.Selected" ng-change="Option.Changed()" class="my-radio">
                <md-radio-button value="All" class="my-radio">All</md-radio-button>
                <md-radio-button value="Open" class="my-radio">Open Mics</md-radio-button>
                <md-radio-button value="Pro" class="my-radio">Pro</md-radio-button>
                <md-radio-button value="Both" class="my-radio">Pro with open spots</md-radio-button>
                <md-radio-button value="Other" class="my-radio">Other descriptions</md-radio-button>
            </md-radio-group></div><br/>
            <div layout="row"><b>Type:&nbsp;</b>
            <md-radio-group layout="row" ng-model="Option.SelectedType" ng-change="Option.Changed()" class="my-radio">
                <md-radio-button value="All" class="my-radio">All</md-radio-button>
                <md-radio-button value="{{type}}" class="my-radio" ng-repeat="type in Option.Types">{{type}}</md-radio-button>
            </md-radio-group></div><br/>
            <div layout="row"><b>Gendre:&nbsp;</b>
            <md-radio-group layout="row" ng-model="Option.SelectedGendre" ng-change="Option.Changed()" class="my-radio">
                <md-radio-button value="All" class="my-radio">All</md-radio-button>
                <md-radio-button value="{{gendre}}" class="my-radio" ng-repeat="gendre in Option.Gendres">{{gendre}}</md-radio-button>
            </md-radio-group></div></b>
            <div layout="row"><b>Active:&nbsp;</b>
            <md-radio-group layout="row" ng-model="Option.ActiveSel" ng-change="Option.Changed()" class="my-radio">
                <md-radio-button value="All" class="my-radio">All</md-radio-button>
                <md-radio-button value="Yes" class="my-radio">Yes</md-radio-button>
                <md-radio-button value="No" class="my-radio">No</md-radio-button>
            </md-radio-group></div>
            <ng-show ng-show="Areas.length===0"><h2>No areas have events matching your criteria</a></h2></ng-show>
            <ng-show ng-show="Areas.length!==0">
                <h2>Areas</h2>
                <p>Click on an area to show its gigs. Click on a gig to show its details.</p>
                <div ng-repeat="area in Areas">
                    <h3>
                        <div layout-wrap layout-gt-sm="row" >
                          <div flex-gt-sm="50">
                            <md-checkbox ng-model="area.Show" aria-label="{{area.Name}} Checkbox">
                              {{area.Name}} ({{area.Gigs.length}})
                            </md-checkbox>
                          </div>
                        </div>
                    </h3>
                    <div ng-repeat="gig in area.Gigs" ng-show="area.Show">
                        <div flex-gt-sm="40">
                            <p ng-hide="gig.Show">
                                <md-checkbox ng-model="gig.Show" aria-label="{{gig.GigName}} Checkbox">
                                  {{gig.GigName}} {{gig.Frequency}} {{gig.DayofWeek}}
                                </md-checkbox>
                            </p>
                            <div ng-show="!!gig.Show" >
                                <h4>
                                    <md-checkbox ng-model="gig.Show" aria-label="{{gig.GigName}} Checkbox">
                                      {{gig.GigName}} {{gig.Frequency}} {{gig.DayofWeek}}
                                    </md-checkbox>
                                </h4>
                                <p>Format: {{gig.Performers}}<br/>
                                   Type: {{gig.Type}}<br/>
                                   Gendre: {{gig.Gendre}}<br/>
                                   Location: {{gig.Location}}</p>
                                <p>Links: <a href="{{gig.URL}}">Info</a><ng-bind-html ng-bind-html='gig.ContactHtml'></ng-bind-html>
                                    <ng-show ng-show="gig.PerformerSign_up!==''">,
                                        Other: <a href="{{gig.PerformerSign_up}}">Performer Sign-up</a></ng-show></p>
                                    <p ng-show='gig.Notes !== ""'>Notes: {{gig.Notes}}</p>
                            </div>
                        </div>
                    </div>
            </div>
        </ng-show>
        <h2>Fine print</h2>
        <p>This site is provided by <a href="https://juliaclement.com/">Julia Clement</a> as a public service.
            It started as an attempt to build a definitive list of regular comedy shows.
            As such it has failed, but Julia would still like to list as many as possible.
        <p>The data on this page is pulled from a Google Spreadsheet that anyone can add gigs to.
            If you are running a regular comedy gig in New Zealand that is not listed,
            please feel free to add it through <a href="https://ql.nz/add-gigs">this form</a>.
            If your gig is listed but needs updating, please <a href="https://m.me/julia.clement.nz">message Julia</a></p>
        <p>As anyone can add to the list, Julia Clement can not accept any responsibility
            for the content of individual entries. Please exercise due caution on
            the likely veracity of entries.
        <p>The raw spreadsheet can be seen at <a href="http://ql.nz/gigs-sheet">Google Sheets</a>.</p>
        <p>The data may be cached in this web server for up to 5 minutes. Last updated <?=$updateTime?>
        <p>This site does not use cookies. If it is creating any please let me know and I'll try to find out what's causing it.
        <p>Copyright &copy; Julia Clement 2018</p>
<script>
var global={};
global.allAreas = <?=json_encode($areas)?>;
<?php
    // To prevent browsers caching javascript files, we include the 
    // js in-line during development.
    // Would probably benefit from some form of versioning in the file name
    // but this is too trivial a project for that
    if( Config::Debug ) { 
        require "index.js";
    } else { ?>
</script>
<script src="index.js">
<?php
    }
?>
</script>

    </body>
</html>
