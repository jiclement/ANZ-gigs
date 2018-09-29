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
var app = angular.module("GigsMod", ["ngSanitize", 'ngMaterial']);//['ngMessages']);
app.controller("GigsCtrl", function($scope, $sce, $sanitize) { //,$http, $mdDialog, $mdToast) {
    $scope.Option = {
        All:  true,
        Open: false,
        Pro:  false,
        Both: false,
        Selected: 'All',
        SelectedType: 'All',
        Types: [],
        SelectedGendre: 'All',
        Gendres: [],
        Changed( ) {
            var Set = function( that, all, open, pro, semi ) {
                that.All = all;
                that.Open = open;
                that.Pro = pro;
                that.Both = semi;
            };
            switch( this.Selected ) {
                case 'Open' : 
                    Set( this, false, true, false, false );
                    break;
                case 'Pro' : 
                    Set( this, false, false, true, false );
                    break;
                case 'Both' : 
                    Set( this, false, false, false, true );
                    break;
                default : 
                    Set( this, true, false, false, false );
                    break;
            }
            $scope.filterAreas();
        }
    };
    $scope.AllAreas = global.allAreas;
    $scope.Areas = $scope.AllAreas; // Start with everything visible
    $scope.filterAreas = function() {
        if( $scope.Option.Open )
            $scope.Areas=$scope.AllAreas.filter(x=>x.HasOpenGigs);
        else if( $scope.Option.Pro )
            $scope.Areas=$scope.AllAreas.filter(x=>x.HasProGigs);
        else if( $scope.Option.Both )
            $scope.Areas=$scope.AllAreas.filter(x=>x.HasBothGigs);
        else // Assume All
            $scope.Areas = $scope.AllAreas;
        $scope.Areas.forEach( function(area) {
            area.filterGigs();
        });
        $scope.Areas = $scope.Areas.filter(x=>x.Gigs.length > 0 );
    };
    var workTypes=[];
    var workGendres=[];
    $scope.AllAreas.forEach( function(area) {
        area.filterGigs = function() {
            if( $scope.Option.Open )
                this.Gigs=this.AllGigs.filter(x=>x.Open);
            else if( $scope.Option.Pro)
                this.Gigs=this.AllGigs.filter(x=>x.Pro);
            else if( $scope.Option.Both )
                this.Gigs=this.AllGigs.filter(x=>x.Both);
            else // Assume All
                this.Gigs = this.AllGigs;
            if( $scope.Option.SelectedGendre !=='All' ) {
                this.Gigs=this.Gigs.filter(x=>$scope.Option.SelectedGendre===x.Gendre);
            }
            if( $scope.Option.SelectedType !=='All' ) {
                this.Gigs=this.Gigs.filter(x=>$scope.Option.SelectedType===x.Type);
            }
        };
        area.AllGigs.forEach( function( gig ){
            // Build the Types & Gendres lists
            workTypes[gig.Type] = gig.Type;
            workGendres[gig.Gendre] = gig.Gendre;
            // Set the contact type string
            var contactString = gig.ContactPersonorURL.toLowerCase();
            var urlString = gig.URL.toLowerCase();
            var contactHtml='';
            if( contactString.includes("@")) {
                contactHtml=$sanitize(', Contact: <a href="mailto:'+gig.ContactPersonorURL+'">email</a>');
            } else if( contactString.includes("https://")||contactString.includes("http://")) {
                contactHtml=$sanitize(gig.ContactPersonorURL);
                contactHtml = $sanitize(', Contact: <a href="'+gig.ContactPersonorURL+'">web page</a>');
                if( contactString.includes("https://www.facebook.com/") ||
                    contactString.includes("https://m.facebook.com/")) {
                    if( contactString.includes("https://www.facebook.com/")||
                        contactString.includes("https://m.facebook.com/")) {
                        var messageString=gig.ContactPersonorURL.replace('https://', '');
                        var parts = messageString.split('/');
                        var part = parts[1] === 'pg' ? 'pg/'.  parts[2] : parts[1];
                        if( part && part !== '') {
                            gig.FbMessenger = $sanitize(part);
                            // a bit paranoid, but resanitize in case FbMessenger contains a string that is
                            // harmless on its own but affects the <a tag
                            contactHtml=$sanitize(', Contact: <a href="https://m.me/'+gig.FbMessenger+'">via Facebook messenger</a>');
                        }
                    }
                }
            } else if( contactString.includes("message the page")||contactString.includes("message page")) {
                contactHtml=$sanitize(gig.ContactPersonorURL);
                if( urlString.includes("https://www.facebook.com/")||
                    urlString.includes("https://m.facebook.com/")||
                    urlString.includes("https://facebook.com/")) {
                    var messageString=gig.URL.replace('https://', '');
                    var parts = messageString.split('/');
                    var part = parts[1] === 'pg' ? 'pg/'.  parts[2] : parts[1];
                    if( part && part !== '') {
                        gig.FbMessenger = $sanitize(part);
                        // a bit paranoid, but resanitize in case FbMessenger contains a string that is
                        // harmless on its own but affects the <a tag
                        contactHtml=$sanitize(', Contact: <a href="https://m.me/'+gig.FbMessenger+'">via Facebook messenger</a>');
                    }
                }
            } else if(contactString.length < 1 ) {
                contactHtml = '';
            } else {
                contactHtml = $sanitize(', Contact: '+gig.ContactPersonorURL);
            }
            gig.ContactHtml = $sce.trustAsHtml(contactHtml);
        });
    });
    // Initialise all gigs
    $scope.filterAreas();
    // We've collected a unique list of Types & Gendres
    // Convert to sorted arrays in Option
    for( var x in workTypes) {
        $scope.Option.Types.push(x);
    };
    $scope.Option.Types.sort();
    for( var x in workGendres ) {
        $scope.Option.Gendres.push(x);
    };
    $scope.Option.Gendres.sort();
});
