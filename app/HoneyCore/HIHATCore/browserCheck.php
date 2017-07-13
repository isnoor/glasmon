<?php 
namespace App\HoneyCore\HIHATCore;
/* This file is part of HIHAT v1.1
   ================================
   Copyright (c) 2007 HIHAT-Project                  

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 
*/

/*modify on may 2017 by isnoor.laksana@mail.ugm.ac.id*/

class browserCheck{
  /* identifies browser out of user-agent string
     $http_user_agent : user agent string from $_SERVER array
     return:            short string or thumbnail naming user agent    */
  public static function browserCheckCore( $http_user_agent) {    
      // number of browsers detected
      $browserNr = 27;
      // return string if no browser is detected
      $browser = "unknown";
      
      // array with detection signatures
      /*web browser */

      $detectionArray = array(
        "MSIE"        =>"Internet_Explorer" ,
        "Trident"     =>"Internet_Explorer" ,
        "Opera"       =>"Opera",
        "OPR"         =>"Opera",
        "Chromium"    =>"Chromium",
        "Chrome"      =>"Chrome",
        "CriOS"       =>"Chrome",
        "Fennec"      =>"Fennec",
        "Camino"      =>"Camino",
        "SeaMonkey"   =>"SeaMonkey",
        "Firefox"     =>"Firefox",
        "FxiOS"       =>"Firefox",
        "Safari"      =>"Safari",

        /*BOT*/
        "Msnbot"      =>"Msnbot",
        "Konqueror"   =>"Konqueror",
        "Netscape"    =>"Netscape",
        "Googlebot"   =>"Googlebot",
        "Yahoo"      =>"Yahoo!",
        "VMBot"       =>"VMBot",
        "MaSagool"    =>"MaSagool",
        "sproose"     =>"sproose",
        "Krugle"      =>"Krugle",
        "psbot"       =>"psbot",
        "ichiro"      =>"ichiro",
        "Nutch"       =>"Nutch",
        "WISEnutbot"  =>"WISEnutbot",
        "Speedy"      =>"Speedy",
        "majestic12"  =>"majestic12",
        "Shim"=>"Shim-Crawler",
        "Exabot"      =>"Exabot",
        "WeRelate"    =>"WeRelate",
        "SKIZZLE"     =>"SKIZZLE",
        "Guruji"      =>"Guruji",
        "YahooSeeker" =>"YahooSeeker",
        "VoilaBot"    =>"VoilaBot",

        /*Hacking tools*/
        "Nikto"       =>"Nikto",
        "curl"        =>"Curl",
        "Sqlmap"      =>"Sqlmap",
        "masscan"     =>"masscan",
        "Microsoft-WebDAV"=>"Microsoft-WebDAV",

        "Mozilla"     =>"Mozilla"
        );

      if(strpos($http_user_agent, 'Chromium/') === false && (strpos($http_user_agent, 'Chrome/') !== false || strpos($http_user_agent, 'CriOS/') !== false)){
        $browser =  $detectionArray["Chrome"];
      }else if(strpos($http_user_agent, 'Chromium/') ==! false ){
        $browser =  $detectionArray["Chromium"];
      }else if(strpos($http_user_agent, 'Safari/') ==! false && (strpos($http_user_agent, 'Chrome/') === false && strpos($http_user_agent, 'CriOS/') === false && strpos($http_user_agent, 'FxiOS/') === false)){
        $browser =  $detectionArray["Safari"];
      }else if(strpos($http_user_agent, 'OPR/') ==! false || strpos($http_user_agent, 'Opera/') ==! false  ){
        $browser =  $detectionArray["OPR"];
      }else{
        foreach ($detectionArray as $key => $value) {
          if ( strpos( $http_user_agent, $key ) !== false ) {
            $http_user_agent = htmlentities($http_user_agent);
            if ( browserCheck::isWebSpider( $http_user_agent) )
              $browser = "BOT";
            else $browser = trim($value);
            break;
          }
        }
      }

      if($browser === "unknown" && $http_user_agent !==""){
        $browser = $http_user_agent;
      }      
      return $browser;    
  } 
  
  // returns true if user-agent string contains signature of known web spider
  public static function isWebSpider( $http_user_agent ) { 
       if ( strpos( $http_user_agent, "bot") !== false || 
            stripos( $http_user_agent, "crawler") !== false ||
            stripos( $http_user_agent, 'Yahoo! Slurp' ) !== false ||           
           (stripos( $http_user_agent, 'ichiro' ) !== false & stripos( $http_user_agent, 'goo' ) !== false) ||
           (stripos( $http_user_agent, 'Nutch' ) !== false && ( stripos( $http_user_agent, 'bot' ) !== false ||
                                                                stripos( $http_user_agent, 'crawl' ) !== false)) ||
            stripos( $http_user_agent, 'Speedy Spider' ) !== false || 
            stripos( $http_user_agent, 'SKIZZLE' ) !== false )
          return true;
      else
          return false;
  }
  
  public function getKnownSearchEngines() {
      return array ('lycos.com', 'google.', 'yahoo.', 'altavista.', 'msn.com', 'picsearch', 'krugle', 'ichiro',
                    'entireweb.com', 'nutch', 'WISEnutbot', 'majestic' , 'VMBot', 'Majestic12', 'entireweb.com', 
                    'SKIZZLE', 'fireball.de');
  }
}
?>
