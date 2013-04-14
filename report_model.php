<?php

  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

class Report
{

  private $mysqli;

  public function __construct($mysqli)
  {
    $this->mysqli = $mysqli;
  }

  public function set_settings($userid,$settings)
  {
    $userid = intval($userid);
    $array = json_decode($settings);
    $output = array();

    // Sanitise input
    if (isset($array->histogramFeed)) $output['histogramFeed'] = intval($array->histogramFeed);
    if (isset($array->thresholdA)) $output['thresholdA'] = intval($array->thresholdA);
    if (isset($array->thresholdB)) $output['thresholdB'] = intval($array->thresholdB);
    if (isset($array->defaultPeriod)) $output['defaultPeriod'] = intval($array->defaultPeriod);
    if (isset($array->defaultMode)) $output['defaultMode'] = preg_replace('/[^\/a-z]/','',$array->defaultMode);
    if (isset($array->unitprice)) $output['unitprice'] = floatval($array->unitprice);
    if (isset($array->fixedrate)) $output['fixedrate'] = floatval($array->fixedrate);

    $settings = json_encode($output);

    $result = $this->mysqli->query("SELECT * FROM report WHERE `userid` = '$userid'");
    $row = $result->fetch_array();

    if ($row) {
      $result = $this->mysqli->query("UPDATE report SET `settings`='$settings' WHERE `userid`='$userid'");
    } else {
      $result = $this->mysqli->query("INSERT INTO report (userid,settings) VALUES ('$userid','$settings')");
    }

    return $output;   
  }

  public function get_settings($userid)
  {
    $userid = intval($userid);
    $result = $this->mysqli->query("SELECT settings FROM report WHERE `userid` = '$userid'");
    $row = $result->fetch_array();
    $data = json_decode($row['settings']);
    //if (!$row) return array('settings'=>"");
    return $data;
  }
}
